<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
class LocationController extends Controller {

  public function index() {
    return Location::all();
  }

  public function mapsIndex (Request $request) {
    // DB::enableQueryLog();
    $date = Carbon::createFromFormat('Y-m-d', $request->date)->startOfDay();
    $locations = Location::with(['beds.crop_entries.crop' => function ($q) {
      $q->whereNotNull('days_to_harvest');
    }, 'beds.crop_entries' => function ($q) {
      $q->whereHas('crop', function ($q) {
        $q->whereNotNull('days_to_harvest');
      });
    }])->get();
    $exludeCrops = [];
    foreach ($locations as $loc) {
      foreach ($loc->beds as $bed) {
        if ($bed->crop_entries->count() === 0) {
          continue;
        }
        $firstDate = Carbon::parse($bed->crop_entries[0]->crop->crop_entries[0]->datetimestamp)->subDays(1);
        $lastDate = Carbon::parse($firstDate)->addDays($bed->crop_entries[0]->crop->days_to_harvest);
        if (!$date->between($firstDate, $lastDate)) {
          $exludeCrops[] = $bed->crop_entries[0]->crop->id;
        }
      }
    }
    $locations = $locations->filter(function ($location) use ($exludeCrops) {
      $location->beds = $location->beds->filter(function ($bed) use ($exludeCrops) {
        $bed->crop_entries = $bed->crop_entries->filter(function ($entry) use ($exludeCrops) {
          return !in_array($entry->crop->id, $exludeCrops);
        });
        return $bed->crop_entries->count() > 0 && $bed->l && $bed->w;
      });
      return $location->beds->count() > 0;
    });
    return $locations;
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
