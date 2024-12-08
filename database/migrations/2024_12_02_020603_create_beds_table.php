<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Location;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('beds', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Location::class)->constrained();
      $table->string('name');
      $table->string('description')->nullable();
      $table->string('image')->nullable();
      $table->number('l')->nullable();
      $table->number('w')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::withoutForeignKeyConstraints(function () {
      Schema::dropIfExists('beds');
    });
  }
};
