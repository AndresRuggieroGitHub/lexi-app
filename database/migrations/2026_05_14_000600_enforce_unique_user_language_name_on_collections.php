<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('collections')
            ->select('user_id', 'language_code', 'name', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as total'))
            ->groupBy('user_id', 'language_code', 'name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $duplicateIds = DB::table('collections')
                ->where('user_id', $duplicate->user_id)
                ->where('language_code', $duplicate->language_code)
                ->where('name', $duplicate->name)
                ->where('id', '!=', $duplicate->keep_id)
                ->pluck('id');

            if ($duplicateIds->isEmpty()) {
                continue;
            }

            foreach ($duplicateIds as $duplicateId) {
                $wordIds = DB::table('collection_words')
                    ->where('collection_id', $duplicateId)
                    ->pluck('word_id');

                foreach ($wordIds as $wordId) {
                    DB::table('collection_words')->updateOrInsert(
                        ['collection_id' => $duplicate->keep_id, 'word_id' => $wordId],
                        ['updated_at' => now(), 'created_at' => now()]
                    );
                }

                DB::table('collection_words')->where('collection_id', $duplicateId)->delete();
            }

            DB::table('collections')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('collections', function (Blueprint $table) {
            $table->unique(['user_id', 'language_code', 'name'], 'collections_user_language_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropUnique('collections_user_language_name_unique');
        });
    }
};