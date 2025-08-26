<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gambling_deposits', function (Blueprint $table) {
            $table->bigInteger('request_id')->nullable()->after('channel_id');
            $table->enum('source_type', ['search', 'request'])
                ->default('search')
                ->after('is_non_member');
        });
    }

    public function down(): void
    {
        Schema::table('gambling_deposits', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
