<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\BedImage;

class BedController extends Controller {

  public function index () {
    $beds = Bed::whereNull('deactivated')->with('images')->get();
    return response()->json([
      "status" => "success",
      "beds" => $beds
    ]);
  }

  public function store (Request $request) {
    $bed = new Bed();
    $bed->name = $request->name;
    $bed->description = $request->description;
    $bed->width = $request->width;
    $bed->height = $request->height;
    $bed->x = $request->x;
    $bed->y = $request->y;
    $bed->save();
    foreach($request->images as $image) {
      $img = new BedImage();
      $img->bed_id = $bed->id;
      $img->name = $image['name'];
      $img->save();
    }
    return response()->json([
      "status" => "success",
      "message" => "Bed created successfully",
      "bed" => Bed::with('images')->find($bed->id)
    ]);
  }

  public function show (Request $request) {
    $bed = Bed::with('images')->find($request->id);
    return response()->json([
      "status" => "success",
      "bed" => $bed
    ]);
  }

  public function update (Request $request, $id) {
    $bed = Bed::find($id);
    $bed->name = $request->name;
    $bed->description = $request->description;
    $bed->width = $request->width;
    $bed->height = $request->height;
    $bed->x = $request->x;
    $bed->y = $request->y;
    $bed->deactivated = $request->deactivated;
    $bed->images()->delete();
    foreach($request->images as $image) {
      $img = new BedImage();
      $img->bed_id = $bed->id;
      $img->name = $image['name'];
      $img->save();
    }
    $bed->save();
    return response()->json([
      "status" => "success",
      "message" => "Bed updated successfully",
      "bed" => $bed
    ]);
  }

  public function destroy ($id) {
    $bed = Bed::find($id);
    $bed->delete();
    return response()->json([
      "status" => "success",
      "message" => "Bed deleted successfully",
      "bed" => $bed
    ]);
  }
}
