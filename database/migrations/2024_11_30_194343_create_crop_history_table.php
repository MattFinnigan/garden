<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Crop;
use App\Models\Location;
use App\Models\Bed;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('crop_history', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Crop::class)->constrained();
      $table->foreignIdFor(Location::class)->constrained();
      $table->foreignIdFor(Bed::class)->nullable();
      $table->string('action');
      $table->string('stage');
      $table->longText('notes')->nullable();
      $table->string('image')->nullable();
      $table->bigInteger('qty');
      $table->dateTime('datetimestamp');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::withoutForeignKeyConstraints(function () {
      Schema::dropIfExists('crop_history');
    });
  }
};
