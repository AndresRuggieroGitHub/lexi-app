<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('exercises')
            ->select('type', 'title', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as total'))
            ->whereNotNull('title')
            ->groupBy('type', 'title')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $duplicateIds = DB::table('exercises')
                ->where('type', $duplicate->type)
                ->where('title', $duplicate->title)
                ->where('id', '!=', $duplicate->keep_id)
                ->pluck('id');

            if ($duplicateIds->isEmpty()) {
                continue;
            }

            DB::table('exercise_attempts')
                ->whereIn('exercise_id', $duplicateIds)
                ->update(['exercise_id' => $duplicate->keep_id]);

            DB::table('exercises')
                ->whereIn('id', $duplicateIds)
                ->delete();
        }

        Schema::table('exercises', function (Blueprint $table) {
            $table->unique(['type', 'title'], 'exercises_type_title_unique');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropUnique('exercises_type_title_unique');
        });
    }
};