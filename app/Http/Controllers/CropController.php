<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;

class CropController extends Controller {

  public function index() {
    return Crop::with(['crop_history', 'plant', 'crop_history.location'])->get();
  }

  public function store(Request $request) {
    $crop = new Crop();
    $crop->plant_id = $request->plant_id;
    $crop->save();
    // attach crop history
    $crop->crop_history()->create([
      'crop_id' => $crop->id,
      'location_id' => $request->location_id,
      'action' => $request->action,
      'stage' => $request->stage,
      'notes' => $request->notes,
      'image' => $request->image,
      'qty' => $request->qty
    ]);
    return response()->json([
      "status" => "success",
      "message" => "Crop created successfully",
      "data" => Crop::with(['crop_history', 'plant', 'crop_history.location'])->get()
    ]);
  }

  public function show($id) {
    return Crop::find($id)->with('plant')->with('crop_history')->get();
  }

  public function destroy ($id) {
    $crop = Crop::find($id);
    // detach crop history
    $crop->crop_history()->delete();
    $crop->delete();
    return response()->json([
      "status" => "success",
      "message" => "Crop deleted successfully",
      "data" => $crop
    ]);
  }
}
