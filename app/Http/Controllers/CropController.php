<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\Plant;

class CropController extends Controller {

  public function index() {
    $crops = Crop::where(function ($query) {
      $query->whereHas('crop_entries');
    })->orderBy('id', 'desc')->get();
    return $crops;
  }

  public function byMonth($month) {
    $crops = Crop::where(function ($query) {
      $query->whereHas('crop_entries');
    })->orderBy('id', 'desc')->get();
    $cropsByMonth = [];
    foreach ($crops as $crop) {
      $crop->crop_entries = $crop->crop_entries->where('datetimestamp', 'like', "%-$month-%");
      if (count($crop->crop_entries) > 0) {
        array_push($cropsByMonth, $crop);
      }
    }
    return $cropsByMonth;
  }

  public function store(Request $request) {
    $crop = new Crop();
    $crop->plant_id = $request->plant_id;
    $crop->days_to_harvest = $request->days_to_harvest;
    $crop->spacing = $request->spacing;
    $crop->qty = $request->qty;
    $crop->x = $request->x;
    $crop->y = $request->y;
    $crop->height = $request->height;
    $crop->width = $request->width;
    if (!$request->days_to_harvest) {
      $crop->days_to_harvest = Plant::find($request->plant_id)->days_to_harvest;
    }
    $crop->save();
    return response()->json([
      "status" => "success",
      "message" => "Crop created successfully",
      "crop" => Crop::where('id', $crop->id)->first(),
      "crops" => Crop::orderBy('id', 'desc')->get()
    ]);
  }

  public function update(Request $request) {
    $c = Crop::where('id', $request->id)->first();
    $c->days_to_harvest = $request->days_to_harvest;
    if (!$request->days_to_harvest) {
      $c->days_to_harvest = Plant::find($request->plant_id)->days_to_harvest;
    }
    $c->update();
  }

  public function show($id) {
    $crop = Crop::where('id', $id)->first();
    foreach ($crop->crop_entries as $entry) {
      $entry->plant_id = $crop->plant_id;
    }
    return $crop;
  }

  public function destroy($id) {
    $crop = Crop::find($id);
    // detach crop entry
    $crop->crop_entries()->delete();
    $crop->delete();
    return response()->json([
      "status" => "success",
      "message" => "Crop deleted successfully",
      "crop" => $crop,
      "crops" => Crop::where(function ($query) {
        $query->whereHas('crop_entries');
      })->orderBy('id', 'desc')->get()
    ]);
  }
}
