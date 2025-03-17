<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Crop;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('crop_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Crop::class)->constrained();
      $table->string('action');
      $table->string('stage');
      $table->longText('notes')->nullable();
      $table->dateTime('datetimestamp');
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
