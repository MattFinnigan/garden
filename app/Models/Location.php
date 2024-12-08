<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Bed;
use App\Models\CropEntry;
use Attribute;

class Location extends Model {
  use HasFactory;
  protected $with = ['beds'];
  public function beds () {
    return $this->hasMany(Bed::class);
  }
  public function crop_entries () {
    return $this->hasMany(CropEntry::class);
  }

  public function areaRemaining () {
    if (!$this->l || !$this->w) {
      return [
        'length' => 999999999999,
        'width' => 999999999999,
        'area' => 999999999999
      ];
    }
    // calculate remaining length, width & area from beds
    $length = $this->l;
    $width = $this->w;
    $area = $length * $width;
    $beds = $this->beds;
    foreach ($beds as $bed) {
      $area -= $bed->l * $bed->w;
      $length -= $bed->l;
      $width -= $bed->w;
    }
    return [
      'length' => $length,
      'width' => $width,
      'area' => $area
    ];
  }
}
