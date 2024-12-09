<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;
use App\Models\Unit;
use App\Models\Plant;

class CropController extends Controller {

  public function index() {
    $crops = Crop::where(function($query) {
        $query->whereHas('crop_entries');
      })->orderBy('id', 'desc')->get();
    return $crops;
  }

  public function store(Request $request) {
    $crop = new Crop();
    $crop->plant_id = $request->plant_id;
    $crop->days_to_harvest = $request->days_to_harvest;
    if (!$request->days_to_harvest) {
      $crop->days_to_harvest = Plant::find($request->plant_id)->days_to_harvest;
    }
    $crop->save();
    // attach crop entry
    $crop->crop_entries()->create([
      'crop_id' => $crop->id,
      'location_id' => $request->location_id,
      'action' => $request->action,
      'stage' => $request->stage,
      'notes' => $request->notes,
      'image' => $request->image,
      'qty' => $request->qty,
      'bed_id' => $request->bed_id,
      'datetimestamp' => $request->datetimestamp,
      'area' => $request->area
    ]);
    foreach ($request->units as $u) {
      $unit = new Unit();
      $unit->crop_id = $crop->id;
      $unit->name = $u->name;
      $unit->image = $u->image;
      $unit->save();
      foreach ($u->crop_entries as $h) {
        $unit->crop_entries()->create([
          'unit_id' => $unit->id,
          'location_id' => $h->location_id,
          'action' => $h->action,
          'stage' => $h->stage,
          'notes' => $h->notes,
          'image' => $h->image,
          'qty' => 1,
          'bed_id' => $h->bed_id,
          'datetimestamp' => $h->datetimestamp,
          'area' => $h->area
        ]);
      }
    }
    return response()->json([
      "status" => "success",
      "message" => "Crop created successfully",
      "crop" => Crop::where('id', $crop->id)->first(),
      "crops" => Crop::where(function($query) {
        $query->whereHas('crop_entries');
      })->orderBy('id', 'desc')->get()
    ]);
  }

  public function update (Request $request) {
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

  public function destroy ($id) {
    $crop = Crop::find($id);
    // detach crop entry
    $crop->crop_entries()->delete();
    $crop->delete();
    return response()->json([
      "status" => "success",
      "message" => "Crop deleted successfully",
      "crop" => $crop,
      "crops" => Crop::where(function($query) {
        $query->whereHas('crop_entries');
      })->orderBy('id', 'desc')->get()
    ]);
  }
}
