<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * マイグレーションを実行する
     */
    public function up(): void
    {
        Schema::create('admin_auths', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Green\AdminAuth\Models\AdminUser::class);
            $table->string('driver')->nullable();
            $table->text('token')->nullable();
            $table->dateTime('token_expires_at')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('uid')->nullable();
            $table->string('avatar_hash')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_auths');
    }
};
