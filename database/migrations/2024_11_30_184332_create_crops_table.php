<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Plant;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('crops', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Plant::class)->constrained();
      $table->bigInteger('days_to_harvest');
      $table->bigInteger('qty');
      $table->bigInteger('spacing');
      $table->bigInteger('x');
      $table->bigInteger('y');
      $table->bigInteger('height');
      $table->bigInteger('width');
      $table->bigInteger('startMonth');
      $table->bigInteger('endMonth');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::withoutForeignKeyConstraints(function () {
      Schema::dropIfExists('crops');
    });
  }
};
