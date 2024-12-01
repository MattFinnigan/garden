<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropHistory;

class CropHistoryController extends Controller {
  public function create(Request $request, $id) {
    $crop = Crop::find($id);
    $history = $crop->crop_history()->create($request->all());
    return response()->json([
      "status" => "success",
      "message" => "Crop event added successfully",
      "data" => CropHistory::where('id', $history->id)->with('location')->first()
    ]);
  }
  public function update (Request $request, $id) {
    $history = CropHistory::where('id', $id)->with('location')->first();
    $history->update($request->all());
    return response()->json([
      "status" => "success",
      "message" => "Crop event updated successfully",
      "data" => $history
    ]);
  }
  public function destroy($id) {
    $h = CropHistory::find($id);
    $h->delete();
    $crop = Crop::where('id', $h->crop_id)->with('crop_history')->first();
    if ($crop->crop_history->count() == 0) {
      $crop->delete();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop event deleted successfully",
      "data" => $h
    ]);
  }
}
