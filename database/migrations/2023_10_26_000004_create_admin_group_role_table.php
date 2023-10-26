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
        Schema::create('admin_group_role', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Green\AdminBase\Models\AdminGroup::class)->constrained();
            $table->foreignIdFor(\Green\AdminBase\Models\AdminRole::class)->constrained();
        });
    }

    /**
     * マイグレーションを戻す
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_group_role');
    }
};
