<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\CropEntry;
use Illuminate\Support\Carbon;
use App\Models\BedImage;

class Bed extends Model {
  use HasFactory;

  protected $fillable = [
    'name',
    'description',
    'l',
    'w',
    'x',
    'y'
  ];

  protected $with = ['images'];

  public function location () {
    return $this->belongsTo(Location::class);
  }

  public function crop_entries () {
    return $this->hasMany(CropEntry::class);
  }

  public function images () {
    return $this->hasMany(BedImage::class);
  }

  public function areaRemaining ($date) {
    if (!$this->l || !$this->w) {
      return 999999999999;
    }
    // calculate remaining length, width & area from beds
    $area = $this->l * $this->w;
    $crops = $this->crop_entries(function ($q) {
      $q->whereHas('crop', function ($q) {
        $q->whereNotNull('days_to_harvest');
      })->whereNotNull('l')->whereNotNull('w')->whereNotNull('days_to_harvest');
    })->get()->groupBy('crop_id');
    $res = collect([]);
    foreach ($crops as $crop) {
      // last item of array
      $entry = $crop->last();
      $perPlant = $entry->area * $entry->area;
      $firstDate = Carbon::parse($entry->datetimestamp)->subDays(1);
      $lastDate = Carbon::parse($firstDate)->addDays($entry->crop->days_to_harvest);
      if ($date->between($firstDate, $lastDate)) {
        $area -= $perPlant * $entry->qty;
        $res->push([
          'perPlant' => $perPlant,
          'qty' => $entry->qty,
          'removed' => $perPlant * $entry->qty
        ]);
      }
    }
    return $area;
  }
}
