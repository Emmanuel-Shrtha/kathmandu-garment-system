<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->integer('bundle_qty');
            $table->string('qr_code')->unique();

            $table->unsignedBigInteger('current_holder_worker_id')->nullable();
            $table->timestamp('last_scanned_at')->nullable();

            $table->unsignedBigInteger('parent_bundle_id')->nullable();
            $table->tinyInteger('rework_level')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
