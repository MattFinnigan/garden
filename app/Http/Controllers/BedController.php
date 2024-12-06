<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
class BedController extends Controller {
  public function store (Request $request) {
    $bed = new Bed();
    $bed->name = $request->name;
    $bed->description = $request->description;
    $bed->image = $request->image;
    $bed->location_id = $request->location_id;
    $bed->save();
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
    $bed->image = $request->image;
    $bed->location_id = $request->location_id;
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
