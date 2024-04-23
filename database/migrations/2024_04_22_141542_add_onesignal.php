<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('userdatas', function (Blueprint $table) {
            // add onesigna_id
            $table->string('onesignal_id')->after('genero')->nullable();


        });
    }

    public function down(): void
    {
        Schema::table('userdatas', function (Blueprint $table) {
            // drop
            $table->dropColumn('onesignal_id');
        });
    }
};
