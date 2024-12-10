<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\CropEntry;
use App\Models\Bed;
use Illuminate\Support\Carbon;

class CropEntryController extends Controller {

  public function store(Request $request, $id) {
    if ($request->bed_id) {
      $date = Carbon::parse($request->datetimestamp)->startOfDay();
      $bed = Bed::find($request->bed_id);
      if ($bed->areaRemaining($date) < (($request->area * 2) * ($request->area * 2) * $request->qty)) {
        return response()->json([
          "status" => "error",
          "message" => "Bed area is not enough for this crop"
        ]);
      }
    }
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
    if ($request->days_to_harvest) {
      $c = Crop::where('id', $entry->crop_id)->first();
      $c->days_to_harvest = $request->days_to_harvest;
      $c->update();
    }
    $entry->save();
    return response()->json([
      "status" => "success",
      "message" => "Crop event added successfully",
      "crop" => Crop::where('id', $entry->crop_id)->first(),
      "crops" => Crop::where(function($query) {
          $query->whereHas('crop_entries');
        })->orderBy('id', 'desc')->get()
    ]);
  }
  public function update (Request $request, $id) {
    $entry = CropEntry::find($id);
    if ($request->bed_id) {
      $date = Carbon::parse($request->datetimestamp)->startOfDay();
      $bed = Bed::find($request->bed_id);
      $oldArea = ($entry->area * $entry->area) * $entry->qty;
      $replacementArea = ($request->area * $request->area) * $request->qty;
      if ($replacementArea > $oldArea) {
        if ($bed->areaRemaining($date) < $replacementArea - $oldArea) {
          return response()->json([
            "status" => "error",
            "message" => "Bed area is not enough for this crop",
            "areaRemaining" => $bed->areaRemaining($date),
            "replacementArea" => $replacementArea,
            "oldArea" => $oldArea,
            "compared" => $replacementArea - $oldArea
          ]);
        }
      }
    }
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
    if ($request->days_to_harvest) {
      $c = Crop::where('id', $entry->crop_id)->first();
      $c->days_to_harvest = $request->days_to_harvest;
      $c->update();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop event updated successfully",
      "crop" => Crop::where('id', $entry->crop_id)->first(),
      "crops" => Crop::where(function($query) {
        $query->whereHas('crop_entries');
      })->orderBy('id', 'desc')->get()
    ]);
  }
  public function destroy($id) {
    $h = CropEntry::find($id);
    $h->delete();
    $crop = Crop::where('id', $h->crop_id)->first();
    if ($crop->crop_entries->count() == 0) {
      $crop->delete();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop event deleted successfully",
      "crop" => $crop->crop_entries->count() > 0 ? $crop : null,
      "crops" => Crop::where(function($query) {
        $query->whereHas('crop_entries');
      })->orderBy('id', 'desc')->get()
    ]);
  }
}
