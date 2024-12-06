<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Bed;

class LocationController extends Controller {

  public function index() {
    return Location::all();
  }

  public function store(Request $request) {
    $location = new Location();
    $location->name = $request->name;
    $location->description = $request->description;
    $location->image = $request->image;
    $location->save();
    foreach ($request->beds as $bed) {
      $location->beds()->create($bed);
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
    $location->save();
    foreach ($request->beds as $bed) {
      if (!isset($bed['id'])) {
        $location->beds()->create($bed);
      } else {
        $b = Bed::find($bed['id']);
        $b->name = $bed['name'];
        $b->description = $bed['description'];
        $b->image = $bed['image'];
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
