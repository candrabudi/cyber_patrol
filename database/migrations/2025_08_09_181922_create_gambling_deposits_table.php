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
        Schema::create('gambling_deposits', function (Blueprint $table) {
            $table->id();

            // Website Information
            $table->string('website_name');
            $table->string('website_url');
            $table->boolean('is_confirmed_gambling')->default(false);
            $table->boolean('is_accessible')->default(false);

            // Payment Account Information
            $table->foreignId('channel_id')->constrained('channels')->onDelete('cascade');
            $table->string('account_number');
            $table->string('account_name');

            // Coordination with Authority (Kominfo)
            $table->date('report_date')->nullable();
            $table->text('report_evidence')->nullable();
            $table->date('link_closure_date')->nullable();
            $table->enum('link_closure_status', ['closed', 'not_closed'])->nullable();

            // Account Validation Result
            $table->date('account_validation_date')->nullable();
            $table->enum('account_validation_status', ['closed', 'blocked', 'frozen', 'not_gambling_account'])->nullable();

            // Report Workflow Status
            $table->enum('report_status', ['pending', 'approved', 'rejected'])->default('pending');

            // Additional
            $table->boolean('is_solved')->default(false);
            $table->text('remarks')->nullable();

            // Audit
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambling_deposits');
    }
};
