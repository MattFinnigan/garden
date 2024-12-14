<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Crop;
use App\Models\Location;
use App\Models\Bed;
use App\Models\Unit;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('crop_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Crop::class)->constrained();
      $table->foreignIdFor(Unit::class)->nullable();
      $table->foreignIdFor(Location::class)->constrained();
      $table->foreignIdFor(Bed::class)->nullable();
      $table->string('action');
      $table->string('stage');
      $table->longText('notes')->nullable();
      $table->string('image')->nullable();
      $table->bigInteger('qty');
      $table->dateTime('datetimestamp');
      $table->bigInteger('spacing_x')->default(1);
      $table->bigInteger('spacing_y')->default(1);
      $table->number('x')->nullable();
      $table->number('y')->nullable();
      $table->longText('plant_pos')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::withoutForeignKeyConstraints(function () {
      Schema::dropIfExists('crop_entries');
    });
  }
};
