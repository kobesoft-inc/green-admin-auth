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
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->json('permissions')->nullable();
            $table->bigInteger('sort_order')->nullable()->index();
            $table->timestamps();
        });

        // 初期のロールを作成
        \Green\AdminAuth\Models\AdminRole::create([
            'name' => __('green::admin_base.admin_role.initial_role'),
            'permissions' => [\Green\AdminAuth\Permissions\Super::class],
            'sort_order' => 1,
        ]);
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_roles');
    }
};
