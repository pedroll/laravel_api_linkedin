<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('userdatas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('nombre');
            $table->string('foto')->nullable();
            $table->string('edad');
            $table->text('acercade');
            $table->string('genero');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userdatas');
    }
};
