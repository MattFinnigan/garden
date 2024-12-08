<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller {

  public function index() {
    return Location::all();
  }

  public function mapsIndex (Request $request) {
    // DB::enableQueryLog();
    $date = $request->date;
    $loc = Location::with(['beds.crop_entries.crop', 'beds.crop_entries' => function ($q) use ($date) {
      $q->whereDate('datetimestamp', $date);
    }])->get();
    // // filter out beds with no crop entries
    // $loc = $loc->filter(function ($l) {
    //   $l->beds = $l->beds->filter(function ($b) {
    //     return $b->crop_entries->count() > 0;
    //   });
    //   return $l->beds->count() > 0;
    // });
    // // filter out locations with no beds
    // $loc = $loc->filter(function ($l) {
    //   return $l->beds->count() > 0;
    // });
    return $loc;
  }

  public function store(Request $request) {
    $location = new Location();
    $location->name = $request->name;
    $location->description = $request->description;
    $location->image = $request->image;
    $location->l = $request->l;
    $location->w = $request->w;
    $location->save();
    foreach ($request->beds as $bed) {
      $b = new Bed();
      $b->name = $bed['name'];
      $b->description = $bed['description'];
      $b->image = $bed['image'];
      $b->l = $bed['l'];
      $b->w = $bed['w'];
      $b->location_id = $location->id;
      $b->save();
    }
    return response()->json([
      "status" => "success",
      "message" => "Location created successfully",
      "locations" => Location::all(),
      "location" => Location::where('id', $location->id)->first()
    ]);
  }

  public function show($id) {
    return Location::where('id', $id)->first();
  }

  public function update(Request $request, $id) {
    $location = Location::where('id', $id)->first();
    $location->name = $request->name;
    $location->description = $request->description;
    $location->image = $request->image;
    $location->l = $request->l;
    $location->w = $request->w;
    $location->save();
    foreach ($request->beds as $bed) {
      if (!isset($bed['id'])) {
        $location->beds()->create($bed);
      } else {
        $b = Bed::find($bed['id']);
        $b->name = $bed['name'];
        $b->description = $bed['description'];
        $b->image = $bed['image'];
        $b->l = $bed['l'];
        $b->w = $bed['w'];
        $b->save();
      }
    }
    // delete beds that werent included in the request
    $location->beds()->whereNotIn('id', collect($request->beds)->pluck('id'))->delete();
    return response()->json([
      "status" => "success",
      "message" => "Location updated successfully",
      "locations" => Location::all(),
      "location" => Location::where('id', $location->id)->first()
    ]);
  }

  public function destroy ($id) {
    $location = Location::find($id);
    if ($location->crop_entries()->count() > 0) {
      return response()->json([
        "status" => "error",
        "message" => "Cannot delete location with crop entries"
      ]);
    }
    $location->beds()->delete();
    $location->delete();
    return response()->json([
      "status" => "success",
      "message" => "Location deleted successfully",
      "locations" => Location::all(),
      "location" => $location
    ]);
  }
}
