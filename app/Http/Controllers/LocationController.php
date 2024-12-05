<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Bed;

class LocationController extends Controller {

  public function index() {
    return Location::with('beds')->get();
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
      "data" => Location::where('id', $location->id)->with('beds')->first()
    ]);
  }

  public function show($id) {
    return Location::where('id', $id)->with('beds')->first();
  }

  public function update(Request $request, $id) {
    $location = Location::where('id', $id)->with('beds')->first();
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
    return response()->json([
      "status" => "success",
      "message" => "Location updated successfully",
      "data" => Location::where('id', $id)->with('beds')->first()
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
      "data" => $location
    ]);
  }
}
