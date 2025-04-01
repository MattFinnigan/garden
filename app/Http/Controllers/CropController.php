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
    return Crop::where(function ($query) use ($month) {
      $query->where('startMonth', '<=', $month)->where('endMonth', '>=', $month);
    })->orderBy('id', 'desc')->with('plant')->get();
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
    $crop->startMonth = $request->startMonth;
    if (!$crop->endMonth) {
      $crop->endMonth = ($crop->startMonth - 1) + $crop->days_to_harvest / 30;
      if ($crop->endMonth > 12) {
        $crop->endMonth = $crop->endMonth - 12;
      }
    }
    $crop->save();
    if ($request->month) {
      $crops = $this->byMonth($request->month);
    } else {
      $crops = Crop::orderBy('id', 'desc')->get();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop created successfully",
      "crop" => Crop::where('id', $crop->id)->first(),
      "crops" => $crops
    ]);
  }

  public function update(Request $request) {
    $c = Crop::where('id', $request->id)->first();
    if ($request->days_to_harvest) {
      $c->days_to_harvest = $request->days_to_harvest;
    }
    if ($request->plant_id) {
      $c->plant_id = $request->plant_id;
    }
    if ($request->height) {
      $c->height = $request->height;
    }
    if ($request->width) {
      $c->width = $request->width;
    }
    if ($request->spacing) {
      $c->spacing = $request->spacing;
    }
    if ($request->qty) {
      $c->qty = $request->qty;
    }
    if ($request->x) {
      $c->x = $request->x;
    }
    if ($request->y) {
      $c->y = $request->y;
    }
    if ($request->startMonth) {
      $c->startMonth = $request->startMonth;
      $c->endMonth = ($c->startMonth - 1) + $c->days_to_harvest / 30;
      if ($c->endMonth > 12) {
        $c->endMonth = $c->endMonth - 12;
      }
    }
    $c->update();
    if ($request->month) {
      $crops = $this->byMonth($request->month);
    } else {
      $crops = Crop::orderBy('id', 'desc')->get();
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop updated successfully",
      "crop" => Crop::where('id', $c->id)->first(),
      "crops" => $crops
    ]);
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
      "message" => "Crop deleted successfully"
    ]);
  }
}
