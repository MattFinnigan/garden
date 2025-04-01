<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropEntry;
use App\Models\CropEntryImage;

class CropEntryController extends Controller {

  public function store(Request $request, $id) {
    $entry = new CropEntry();
    $entry->crop_id = $id;
    $entry->action = $request->action;
    $entry->stage = $request->stage;
    $entry->notes = $request->notes;
    $entry->datetimestamp = $request->datetimestamp;
    $entry->save();
    foreach ($request->images as $image) {
      $img = new CropEntryImage();
      $img->crop_entry_id = $entry->id;
      $img->name = $image['name'];
      $img->save();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop event added successfully",
      "crop" => Crop::where('id', $entry->crop_id)->first(),
      "crops" => Crop::orderBy('id', 'desc')->get()
    ]);
  }
  public function update(Request $request, $id) {
    $entry = CropEntry::find($id);
    $entry->action = $request->action;
    $entry->stage = $request->stage;
    $entry->notes = $request->notes;
    $entry->datetimestamp = $request->datetimestamp;
    $entry->update();
    $entry->images()->delete();
    foreach ($request->images as $image) {
      $img = new CropEntryImage();
      $img->crop_entry_id = $entry->id;
      $img->name = $image['name'];
      $img->save();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop event updated successfully",
      "crop" => Crop::where('id', $entry->crop_id)->first(),
      "crops" => Crop::orderBy('id', 'desc')->get()
    ]);
  }
  public function destroy($id) {
    $h = CropEntry::find($id);
    $h->delete();
    $crop = Crop::where('id', $h->crop_id)->first();
    return response()->json([
      "status" => "success",
      "message" => "Crop event deleted successfully",
      "crop" => Crop::where('id', $crop->id)->first(),
      "crops" => Crop::orderBy('id', 'desc')->get()
    ]);
  }
}
