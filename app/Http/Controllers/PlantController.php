<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plant;

class PlantController extends Controller {
  public function index() {
    return Plant::orderBy('name')->get();
  }

  public function store(Request $request) {
    $plant = new Plant();
    $plant->name = $request->name;
    $plant->description = $request->description;
    $plant->variety = $request->variety;
    $plant->image = $request->image;
    $plant->days_to_harvest = $request->days_to_harvest;
    $plant->sow_from = $request->sow_from;
    $plant->sow_to = $request->sow_to;
    $plant->spacing = $request->spacing;
    $plant->save();
    return response()->json([
      "status" => "success",
      "message" => "Plant created successfully",
      "plant" => $plant,
      "plants" => Plant::orderBy('name')->get()
    ]);
  }

  public function show($id) {
    return Plant::find($id);
  }

  public function update(Request $request, $id) {
    $plant = Plant::find($id);
    $plant->name = $request->name;
    $plant->description = $request->description;
    $plant->variety = $request->variety;
    $plant->image = $request->image;
    $plant->days_to_harvest = $request->days_to_harvest;
    $plant->sow_from = $request->sow_from;
    $plant->sow_to = $request->sow_to;
    $plant->spacing = $request->spacing;
    $plant->save();
    return response()->json([
      "status" => "success",
      "message" => "Plant updated successfully",
      "plant" => $plant,
      "plants" => Plant::orderBy('name')->get()
    ]);
  }

  public function destroy($id) {
    $plant = Plant::find($id);
    $plant->delete();
    return response()->json([
      "status" => "success",
      "message" => "Plant deleted successfully",
      "plant" => $plant,
      "plants" => Plant::orderBy('name')->get()
    ]);
  }
}