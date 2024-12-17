<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\BedImage;

class LocationController extends Controller {

  public function index() {
    return Location::all();
  }

  public function mapsIndex (Request $request) {
    // DB::enableQueryLog();
    $date = Carbon::createFromFormat('Y-m-d', $request->date)->startOfDay();

    $locations = Location::with(['beds' => function ($q) {
      $q->whereNotNull('l')
        ->whereNotNull('w')
        ->whereNotNull('x')
        ->whereNotNull('y')
        ->whereNull('deactivated');
    }, 'beds.crop_entries.crop' => function ($q) {
      $q->whereNotNull('days_to_harvest');
    }, 'beds.crop_entries' => function ($q) {
      $q->whereHas('crop', function ($q) {
        $q->whereNotNull('days_to_harvest')
          ->whereNotNull('plant_pos');
      })->where('stage', '!=', 'completed');
    }])->get();
    
    $filteredLocations = $locations->map(function ($location) use ($date) {
      $location->beds = $location->beds->map(function ($bed) use ($date) {
        // Skip beds with no crop entries
        if ($bed->crop_entries->isEmpty()) {
          return $bed;
        }
        $crop_entries = clone($bed->crop_entries);
        unset($bed->crop_entries);
        // Filter out crops that don't fall within the date range
        $validEntries = $crop_entries->filter(function ($entry) use ($date) {
          $firstDate = Carbon::parse($entry->datetimestamp)->subDays(1);
          $lastDate = $firstDate->copy()->addDays($entry->crop->days_to_harvest);
          return $date->between($firstDate, $lastDate);
        });
    
        // Group by crop_id and pick only the latest entry per group
        $latestEntries = $validEntries
          ->groupBy('crop_id')
          ->map(fn($entries) => $entries->sortByDesc('datetimestamp')->first())
          ->values();
    
        // // Replace the crop_entries with the latest single entries
        $bed->crop_entries = $latestEntries;
    
        return $bed;
      });
    
      return $location;
    });    

    return $filteredLocations;
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
      $b->l = $bed['l'];
      $b->w = $bed['w'];
      $b->x = $bed['x'];
      $b->y = $bed['y'];
      $b->location_id = $location->id;
      $b->save();
      foreach($bed['images'] as $image) {
        $img = new BedImage();
        $img->bed_id = $b->id;
        $img->name = $image['name'];
        $img->save();
      }
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
        $b = $location->beds()->create($bed);
        foreach($bed['images'] as $image) {
          $img = new BedImage();
          $img->bed_id = $b->id;
          $img->name = $image['name'];
          $img->save();
        }
      } else {
        $b = Bed::find($bed['id']);
        $b->name = $bed['name'];
        $b->description = $bed['description'];
        $b->l = $bed['l'];
        $b->w = $bed['w'];
        $b->x = $bed['x'];
        $b->y = $bed['y'];
        $b->deactivated = $bed['deactivated'];
        $b->images()->delete();
        foreach($bed['images'] as $image) {
          $img = new BedImage();
          $img->bed_id = $b->id;
          $img->name = $image['name'];
          $img->save();
        }
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
