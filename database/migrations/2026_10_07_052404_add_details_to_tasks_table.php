<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('deadline')->nullable();
            $table->tinyInteger('priority')->default(1);
            $table->tinyInteger('status')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');

            $table->dropColumn([
                'deadline',
                'priority',
                'status',
            ]);
        });
    }
};
