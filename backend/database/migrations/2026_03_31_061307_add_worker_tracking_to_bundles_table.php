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
        Schema::table('bundles', function (Blueprint $table) {
            // This tracks who currently has the bundle in the factory
            $table->unsignedBigInteger('current_holder_worker_id')->nullable();
            
            // This tracks the exact time of the last successful scan
            $table->timestamp('last_scanned_at')->nullable();

            // Optional: If you want to be strict, link it to the workers table
            // $table->foreign('current_holder_worker_id')->references('id')->on('workers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn(['current_holder_worker_id', 'last_scanned_at']);
        });
    }
};