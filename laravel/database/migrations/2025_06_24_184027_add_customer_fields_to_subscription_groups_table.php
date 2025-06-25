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
        Schema::table('subscription_groups', function (Blueprint $table) {
        $table->string('customer_name')->nullable()->after('participant_count');
        $table->string('phone')->nullable()->after('customer_name');
        $table->string('email')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_groups', function (Blueprint $table) {
            //
        });
    }
};
