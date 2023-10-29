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
        Schema::create('admin_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->nestedSet();
            $table->timestamps();
        });

        // 初期のグループを作成
        \Green\AdminBase\Models\AdminGroup::create([
            'name' => __('green::admin_base.admin_group.initial_group'),
        ]);
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_groups');
    }
};
