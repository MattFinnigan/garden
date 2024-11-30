<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

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
    return response()->json([
      "status" => "success",
      "message" => "Location created successfully",
      "data" => $location
    ]);
  }

  public function show($id) {
    return Location::find($id);
  }

  public function update(Request $request, $id) {
    $location = Location::find($id);
    $location->name = $request->name;
    $location->description = $request->description;
    $location->image = $request->image;
    $location->save();
    return response()->json([
      "status" => "success",
      "message" => "Location updated successfully",
      "data" => $location
    ]);
  }

  public function destroy ($id) {
    $location = Location::find($id);
    $location->delete();
    return response()->json([
      "status" => "success",
      "message" => "Location deleted successfully",
      "data" => $location
    ]);
  }
}
