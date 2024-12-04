<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crop;

class CropController extends Controller {

  public function index() {
    return Crop::with(['plant', 'crop_history.location', 'crop_history'])->get();
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
      'qty' => $request->qty,
      'bed_id' => $request->bed_id,
      'datetimestamp' => $request->datetimestamp
    ]);
    return response()->json([
      "status" => "success",
      "message" => "Crop created successfully",
      "data" => Crop::where('id', $crop->id)->with(['crop_history', 'plant', 'crop_history.location', 'crop_history.bed'])->first()
    ]);
  }

  public function show($id) {
    $crop = Crop::where('id', $id)->with(['crop_history', 'plant', 'crop_history.location', 'crop_history.bed'])->first();
    foreach ($crop->crop_history as $history) {
      $history->plant_id = $crop->plant_id;
    }
    return $crop;
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
