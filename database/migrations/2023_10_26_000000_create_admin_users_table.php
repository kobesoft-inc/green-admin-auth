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
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('exist')->nullable()
                ->storedAs('case when deleted_at is null then 1 else null end');
            $table->unique(['email', 'exist']);
            $table->unique(['username', 'exist']);
        });

        // 初期の管理ユーザーを作成
        \Green\AdminBase\Models\AdminUser::create([
            'name' => 'Administrator',
            'email' => 'admin@example.net',
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
