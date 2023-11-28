<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーションを実行する
     */
    public function up(): void
    {
        Schema::create('admin_user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Green\AdminAuth\Models\AdminUser::class)->constrained();
            $table->foreignIdFor(\Green\AdminAuth\Models\AdminRole::class)->constrained();
        });

        // 初期のロールを割当
        \Green\AdminAuth\Models\AdminUser::find(1)->roles()->sync([1]);
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user_role');
    }
};
