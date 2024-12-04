<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropHistory;

class CropHistoryController extends Controller {
  public function create(Request $request, $id) {
    $history = new CropHistory();
    $history->crop_id = $id;
    $history->location_id = $request->location_id;
    $history->action = $request->action;
    $history->stage = $request->stage;
    $history->notes = $request->notes;
    $history->image = $request->image;
    $history->qty = $request->qty;
    $history->bed_id = $request->bed_id;
    $history->datetimestamp = $request->datetimestamp;
    $history->area = $request->area;
    $history->unit_id = $request->unit_id;
    $history->save();
    return response()->json([
      "status" => "success",
      "message" => "Crop event added successfully",
      "data" => CropHistory::where('id', $history->id)->with('location', 'bed', 'unit', 'unit.crop_history')->first()
    ]);
  }
  public function update (Request $request, $id) {
    $history = CropHistory::where('id', $id)->first();
    $history->location_id = $request->location_id;
    $history->action = $request->action;
    $history->stage = $request->stage;
    $history->notes = $request->notes;
    $history->image = $request->image;
    $history->qty = $request->qty;
    $history->bed_id = $request->bed_id;
    $history->datetimestamp = $request->datetimestamp;
    $history->area = $request->area;
    $history->unit_id = $request->unit_id;
    $history->update();
    return response()->json([
      "status" => "success",
      "message" => "Crop event updated successfully",
      "data" => CropHistory::where('id', $id)->with('location', 'bed', 'unit', 'unit.crop_history')->first()
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
