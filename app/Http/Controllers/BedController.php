<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Location;
use App\Models\BedImage;

class BedController extends Controller {
  public function store (Request $request) {
    $bed = new Bed();
    $bed->name = $request->name;
    $bed->description = $request->description;
    $bed->location_id = $request->location_id;
    $bed->l = $request->l;
    $bed->w = $request->w;
    $bed->x = $request->x;
    $bed->y = $request->y;
    $location = Location::find($request->location_id);
    $remainingSpace = $location->areaRemaining();
    if ($bed->w * $bed->l > $remainingSpace['area']) {
      return response()->json([
        "status" => "error",
        "message" => "Bed area exceeds location area"
      ]);
    } else if ($bed->w > $remainingSpace['w']) {
      return response()->json([
        "status" => "error",
        "message" => "Bed width exceeds location width"
      ]);
    } else if ($bed->l > $remainingSpace['l']) {
      return response()->json([
        "status" => "error",
        "message" => "Bed length exceeds location length"
      ]);
    }
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
      "bed" => $bed
    ]);
  }

  public function update (Request $request, $id) {
    $bed = Bed::find($id);
    $bed->name = $request->name;
    $bed->description = $request->description;
    $bed->location_id = $request->location_id;
    $bed->l = $request->l;
    $bed->w = $request->w;
    $bed->x = $request->x;
    $bed->y = $request->y;
    $bed->images()->delete();
    foreach($request->images as $image) {
      $img = new BedImage();
      $img->bed_id = $bed->id;
      $img->name = $image['name'];
      $img->save();
    }
    $location = Location::find($request->location_id);
    $remainingSpace = $location->areaRemaining();
    if ($bed->w * $bed->l > $remainingSpace['area']) {
      return response()->json([
        "status" => "error",
        "message" => "Bed area exceeds location area"
      ]);
    } else if ($bed->w > $remainingSpace['w']) {
      return response()->json([
        "status" => "error",
        "message" => "Bed width exceeds location width"
      ]);
    } else if ($bed->l > $remainingSpace['l']) {
      return response()->json([
        "status" => "error",
        "message" => "Bed length exceeds location length"
      ]);
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
