<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plant;

class PlantController extends Controller {
  public function index() {
    return Plant::all();
  }

  public function store(Request $request) {
    $plant = new Plant();
    $plant->name = $request->name;
    $plant->description = $request->description;
    $plant->image = $request->image;
    $plant->save();
    return $plant;
  }

  public function show($id) {
    return Plant::find($id);
  }

  public function update(Request $request, $id) {
    $plant = Plant::find($id);
    $plant->name = $request->name;
    $plant->description = $request->description;
    $plant->image = $request->image;
    $plant->save();
    return $plant;
  }

  public function destroy($id) {
    $plant = Plant::find($id);
    $plant->delete();
    return $plant;
  }
}