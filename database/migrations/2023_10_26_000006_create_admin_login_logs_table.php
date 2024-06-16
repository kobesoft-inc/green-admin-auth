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
        Schema::create('admin_login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Green\AdminAuth\Models\AdminUser::class);
            $table->string('languages')->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_login_logs');
    }
};
