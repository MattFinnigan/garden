<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropEntry;

class CropEntryController extends Controller {
  public function create(Request $request, $id) {
    $entry = new CropEntry();
    $entry->crop_id = $id;
    $entry->location_id = $request->location_id;
    $entry->action = $request->action;
    $entry->stage = $request->stage;
    $entry->notes = $request->notes;
    $entry->image = $request->image;
    $entry->qty = $request->qty;
    $entry->bed_id = $request->bed_id;
    $entry->datetimestamp = $request->datetimestamp;
    $entry->area = $request->area;
    $entry->unit_id = $request->unit_id;
    $entry->save();
    return response()->json([
      "status" => "success",
      "message" => "Crop event added successfully",
      "data" => CropEntry::where('id', $entry->id)->with('location', 'bed', 'unit', 'unit.crop_entries')->first()
    ]);
  }
  public function update (Request $request, $id) {
    $entry = CropEntry::where('id', $id)->first();
    $entry->location_id = $request->location_id;
    $entry->action = $request->action;
    $entry->stage = $request->stage;
    $entry->notes = $request->notes;
    $entry->image = $request->image;
    $entry->qty = $request->qty;
    $entry->bed_id = $request->bed_id;
    $entry->datetimestamp = $request->datetimestamp;
    $entry->area = $request->area;
    $entry->unit_id = $request->unit_id;
    $entry->update();
    return response()->json([
      "status" => "success",
      "message" => "Crop event updated successfully",
      "data" => CropEntry::where('id', $id)->with('location', 'bed', 'unit', 'unit.crop_entries')->first()
    ]);
  }
  public function destroy($id) {
    $h = CropEntry::find($id);
    $h->delete();
    $crop = Crop::where('id', $h->crop_id)->with('crop_entries')->first();
    if ($crop->crop_entries->count() == 0) {
      $crop->delete();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop event deleted successfully",
      "data" => $h
    ]);
  }
}
