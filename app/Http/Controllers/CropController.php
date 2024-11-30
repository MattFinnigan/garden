<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;

class CropController extends Controller {

  public function index() {
    return Crop::all();
  }

  public function store(Request $request) {
    $crop = new Crop();
    $crop->name = $request->name;
    $crop->plant_id = $request->plant_id;
    $crop->save();
    return $crop;
  }

  public function show($id) {
    return Crop::find($id);
  }

  public function destroy ($id) {
    $crop = Crop::find($id);
    $crop->delete();
    return $crop;
  }
}
