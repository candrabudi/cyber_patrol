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
        Schema::create('gambling_deposit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gambling_deposit_id')->constrained('gambling_deposits')->onDelete('cascade');

            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('field_changed');

            $table->text('old_value')->nullable();

            $table->text('new_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambling_deposit_logs');
    }
};
