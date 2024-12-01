<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropHistory;

class CropHistoryController extends Controller {
  public function index($id) {
    $history = Crop::find($id)->crossJoin('crop_history')->get();
    return response()->json($history);
  }
  public function add(Request $request, $id) {
    $crop = Crop::find($id);
    $crop->history()->create($request->all());
    return response()->json([
      "status" => "success",
      "message" => "Crop event added successfully",
      "data" => $crop
    ]);
  }
  public function destroy($id) {
    $crop = CropHistory::find($id);
    $crop->delete();
    return response()->json([
      "status" => "success",
      "message" => "Crop event deleted successfully",
      "data" => $crop
    ]);
  }
}
