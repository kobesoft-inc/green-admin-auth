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
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('username')->nullable()->unique();
            $table->string('password')->nullable();
            $table->dateTime('password_expire_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 初期の管理ユーザーを作成
        \Green\AdminBase\Models\AdminUser::create([
            'name' => __('green::admin_base.admin_user.initial_user'),
            'username' => 'admin',
            'password' => 'admin',
        ]);
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
