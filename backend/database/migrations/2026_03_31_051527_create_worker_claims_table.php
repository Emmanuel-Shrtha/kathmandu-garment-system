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
        Schema::create('worker_claims', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bundle_id');
            $table->unsignedBigInteger('worker_id');

            $table->char('client_uuid', 36)->unique();

            $table->integer('claimed_qty');
            $table->integer('passed_qty')->default(0);
            $table->integer('wasted_qty')->default(0);
            $table->integer('repaired_qty')->default(0);

            $table->enum('status', [
                'provisional_offline',
                'provisional',
                'conflicted',
                'passed',
                'rejected',
                'wasted'
            ])->default('provisional');

            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamp('qc_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_claims');
    }
};
