<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminExercisesController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));

        $attemptCounts = DB::table('exercise_attempts')
            ->select('exercise_id', DB::raw('count(*) as total'))
            ->groupBy('exercise_id');

        $answerStats = DB::table('exercise_attempts')
            ->leftJoin('attempt_answers', 'attempt_answers.attempt_id', '=', 'exercise_attempts.id')
            ->select(
                'exercise_attempts.exercise_id',
                DB::raw('count(attempt_answers.id) as answers_total'),
                DB::raw('coalesce(sum(case when attempt_answers.is_correct = 1 then 1 else 0 end), 0) as correct_answers_total')
            )
            ->groupBy('exercise_attempts.exercise_id');

        $exercises = DB::table('exercises')
            ->leftJoinSub($attemptCounts, 'attempt_counts', fn ($join) => $join->on('exercises.id', '=', 'attempt_counts.exercise_id'))
            ->leftJoinSub($answerStats, 'answer_stats', fn ($join) => $join->on('exercises.id', '=', 'answer_stats.exercise_id'))
            ->when($search !== '', fn ($query) => $query->where('exercises.title', 'like', '%' . $search . '%'))
            ->select(
                'exercises.id',
                'exercises.title',
                'exercises.type',
                'exercises.source',
                'exercises.created_at',
                DB::raw('coalesce(attempt_counts.total, 0) as attempts_total'),
                DB::raw('coalesce(answer_stats.answers_total, 0) as answers_total'),
                DB::raw('coalesce(answer_stats.correct_answers_total, 0) as correct_answers_total')
            )
            ->orderByDesc('attempts_total')
            ->orderBy('exercises.title')
            ->paginate(20)
            ->withQueryString();

        $templateItemCounts = DB::table('exercise_items')
            ->select('template_id', DB::raw('count(*) as items_total'))
            ->groupBy('template_id');

        $templateOptionCounts = DB::table('exercise_items')
            ->join('exercise_options', 'exercise_options.item_id', '=', 'exercise_items.id')
            ->select('exercise_items.template_id', DB::raw('count(exercise_options.id) as options_total'))
            ->groupBy('exercise_items.template_id');

        $templateInstanceCounts = DB::table('exercise_instances')
            ->select('template_id', DB::raw('count(*) as instances_total'))
            ->groupBy('template_id');

        $templates = DB::table('exercise_templates')
            ->leftJoin('users', 'users.id', '=', 'exercise_templates.created_by')
            ->leftJoinSub($templateItemCounts, 'template_item_counts', fn ($join) => $join->on('exercise_templates.id', '=', 'template_item_counts.template_id'))
            ->leftJoinSub($templateOptionCounts, 'template_option_counts', fn ($join) => $join->on('exercise_templates.id', '=', 'template_option_counts.template_id'))
            ->leftJoinSub($templateInstanceCounts, 'template_instance_counts', fn ($join) => $join->on('exercise_templates.id', '=', 'template_instance_counts.template_id'))
            ->when($search !== '', fn ($query) => $query->where('exercise_templates.title', 'like', '%' . $search . '%'))
            ->select(
                'exercise_templates.id',
                'exercise_templates.title',
                'exercise_templates.type',
                'exercise_templates.source',
                'exercise_templates.schema_version',
                'exercise_templates.payload',
                'users.name as author_name',
                'users.surname as author_surname',
                DB::raw('coalesce(template_item_counts.items_total, 0) as items_total'),
                DB::raw('coalesce(template_option_counts.options_total, 0) as options_total'),
                DB::raw('coalesce(template_instance_counts.instances_total, 0) as instances_total')
            )
            ->orderByDesc('instances_total')
            ->orderBy('exercise_templates.title')
            ->limit(12)
            ->get();

        $templateOptions = DB::table('exercise_templates')
            ->select('id', 'title', 'type')
            ->orderBy('title')
            ->get();

        $itemOptionCounts = DB::table('exercise_options')
            ->select('item_id', DB::raw('count(*) as options_total'))
            ->groupBy('item_id');

        $templateItems = DB::table('exercise_items')
            ->join('exercise_templates', 'exercise_templates.id', '=', 'exercise_items.template_id')
            ->leftJoinSub($itemOptionCounts, 'item_option_counts', fn ($join) => $join->on('exercise_items.id', '=', 'item_option_counts.item_id'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('exercise_templates.title', 'like', '%' . $search . '%')
                        ->orWhere('exercise_items.question_text', 'like', '%' . $search . '%');
                });
            })
            ->select(
                'exercise_items.id',
                'exercise_items.template_id',
                'exercise_items.item_order',
                'exercise_items.item_type',
                'exercise_items.question_text',
                'exercise_items.correct_answer',
                'exercise_items.payload',
                'exercise_templates.title as template_title',
                DB::raw('coalesce(item_option_counts.options_total, 0) as options_total')
            )
            ->orderBy('exercise_templates.title')
            ->orderBy('exercise_items.item_order')
            ->limit(20)
            ->get();

        $itemOptionsByItem = DB::table('exercise_options')
            ->whereIn('item_id', $templateItems->pluck('id'))
            ->orderBy('option_order')
            ->get()
            ->groupBy('item_id');

        $templateItems = $templateItems->map(function ($item) use ($itemOptionsByItem) {
                $payload = json_decode((string) ($item->payload ?? ''), true);
                $options = $itemOptionsByItem->get($item->id, collect());
                $item->hint = is_array($payload) ? ($payload['hint'] ?? null) : null;
                $item->min_words = is_array($payload) ? ($payload['min_words'] ?? null) : null;
                $item->options_text = $options->pluck('option_text')->implode("\n");
                $item->correct_option_order = optional($options->firstWhere('is_correct', true))->option_order;

                return $item;
            });

        $templateInstances = DB::table('exercise_instances')
            ->join('exercise_templates', 'exercise_templates.id', '=', 'exercise_instances.template_id')
            ->join('users', 'users.id', '=', 'exercise_instances.user_id')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery
                        ->where('exercise_templates.title', 'like', '%' . $search . '%')
                        ->orWhere('users.email', 'like', '%' . $search . '%')
                        ->orWhere('users.name', 'like', '%' . $search . '%');
                });
            })
            ->select(
                'exercise_instances.id',
                'exercise_instances.assigned_language_code',
                'exercise_instances.generated_payload',
                'exercise_instances.created_at',
                'exercise_templates.title as template_title',
                'exercise_templates.type as template_type',
                'users.email',
                'users.name',
                'users.surname'
            )
            ->orderByDesc('exercise_instances.id')
            ->limit(20)
            ->get()
            ->map(function ($instance) {
                $payload = json_decode((string) ($instance->generated_payload ?? ''), true);
                $instance->payload_summary = is_array($payload)
                    ? collect($payload)->map(fn ($value, $key) => $key . ': ' . (is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)))->implode(' · ')
                    : null;

                return $instance;
            });

        $answersTotal = (int) DB::table('attempt_answers')->count();
        $correctAnswersTotal = (int) DB::table('attempt_answers')->where('is_correct', true)->count();
        $accuracyRate = $answersTotal > 0 ? (int) round(($correctAnswersTotal / $answersTotal) * 100) : null;

        return view('pages.admin-exercises', [
            'exercises' => $exercises,
            'templates' => $templates,
            'templateOptions' => $templateOptions,
            'templateItems' => $templateItems,
            'templateInstances' => $templateInstances,
            'filters' => ['q' => $search],
            'stats' => [
                'published' => DB::table('exercises')->count(),
                'drafts' => 0,
                'attempts' => DB::table('exercise_attempts')->count(),
                'answers' => $answersTotal,
                'accuracy_rate' => $accuracyRate,
                'templates' => DB::table('exercise_templates')->count(),
                'template_items' => DB::table('exercise_items')->count(),
                'instances' => DB::table('exercise_instances')->count(),
            ],
        ]);
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:reading,listening,speaking,writing,mix'],
            'source' => ['nullable', 'in:manual,ai'],
            'schema_version' => ['nullable', 'integer', 'min:1', 'max:99'],
            'topic' => ['nullable', 'string', 'max:80'],
            'difficulty' => ['nullable', 'string', 'max:10'],
        ]);

        DB::table('exercise_templates')->insert([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'source' => $validated['source'] ?? 'manual',
            'schema_version' => $validated['schema_version'] ?? 1,
            'payload' => json_encode(array_filter([
                'topic' => $validated['topic'] ?? null,
                'difficulty' => $validated['difficulty'] ?? null,
            ], fn ($value) => $value !== null && $value !== ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_by' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin-exercises')->with('status', __('lexi.admin.exercises.status_template_created'));
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $this->validateItem($request);

        $itemId = $this->persistItem($validated);

        return redirect()->route('admin-exercises')->with('status', __('lexi.admin.exercises.status_item_added'));
    }

    public function updateTemplate(Request $request, int $templateId): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:reading,listening,speaking,writing,mix'],
            'source' => ['nullable', 'in:manual,ai'],
            'schema_version' => ['nullable', 'integer', 'min:1', 'max:99'],
            'topic' => ['nullable', 'string', 'max:80'],
            'difficulty' => ['nullable', 'string', 'max:10'],
        ]);

        DB::table('exercise_templates')
            ->where('id', $templateId)
            ->update([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'source' => $validated['source'] ?? 'manual',
                'schema_version' => $validated['schema_version'] ?? 1,
                'payload' => json_encode(array_filter([
                    'topic' => $validated['topic'] ?? null,
                    'difficulty' => $validated['difficulty'] ?? null,
                ], fn ($value) => $value !== null && $value !== ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);

        return redirect()->route('admin-exercises')->with('status', __('lexi.admin.exercises.status_template_updated'));
    }

    public function destroyTemplate(int $templateId): RedirectResponse
    {
        DB::table('exercise_templates')->where('id', $templateId)->delete();

        return redirect()->route('admin-exercises')->with('status', __('lexi.admin.exercises.status_template_deleted'));
    }

    public function updateItem(Request $request, int $itemId): RedirectResponse
    {
        $validated = $this->validateItem($request, $itemId);

        $this->persistItem($validated, $itemId);

        return redirect()->route('admin-exercises')->with('status', __('lexi.admin.exercises.status_item_updated'));
    }

    public function destroyItem(int $itemId): RedirectResponse
    {
        DB::table('exercise_items')->where('id', $itemId)->delete();

        return redirect()->route('admin-exercises')->with('status', __('lexi.admin.exercises.status_item_deleted'));
    }

    private function validateItem(Request $request, ?int $itemId = null): array
    {
        $templateId = (int) $request->input('template_id');

        return $request->validate([
            'template_id' => ['required', 'integer', 'exists:exercise_templates,id'],
            'item_order' => [
                'required',
                'integer',
                'min:1',
                'max:999',
                Rule::unique('exercise_items', 'item_order')
                    ->where(fn ($query) => $query->where('template_id', $templateId))
                    ->ignore($itemId),
            ],
            'item_type' => ['required', 'in:question,prompt,instruction,choice,translate,fillin,pronounce'],
            'question_text' => ['required', 'string', 'max:2000'],
            'correct_answer' => ['nullable', 'string', 'max:4000'],
            'hint' => ['nullable', 'string', 'max:500'],
            'min_words' => ['nullable', 'integer', 'min:0', 'max:500'],
            'options_text' => ['nullable', 'string', 'max:4000'],
            'correct_option_order' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);
    }

    private function persistItem(array $validated, ?int $itemId = null): int
    {
        return DB::transaction(function () use ($validated, $itemId) {
            $attributes = [
                'template_id' => $validated['template_id'],
                'item_order' => $validated['item_order'],
                'item_type' => $validated['item_type'],
                'question_text' => $validated['question_text'],
                'correct_answer' => $validated['correct_answer'] ?? null,
                'payload' => json_encode(array_filter([
                    'hint' => $validated['hint'] ?? null,
                    'min_words' => $validated['min_words'] ?? null,
                ], fn ($value) => $value !== null && $value !== ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ];

            if ($itemId === null) {
                $itemId = DB::table('exercise_items')->insertGetId([
                    ...$attributes,
                    'created_at' => now(),
                ]);
            } else {
                DB::table('exercise_items')->where('id', $itemId)->update($attributes);
                DB::table('exercise_options')->where('item_id', $itemId)->delete();
            }

            $options = collect(preg_split('/\r\n|\r|\n/', (string) ($validated['options_text'] ?? '')))
                ->map(fn ($option) => trim($option))
                ->filter()
                ->values();

            if ($options->isNotEmpty()) {
                DB::table('exercise_options')->insert($options->map(function (string $option, int $index) use ($validated, $itemId) {
                    $order = $index + 1;

                    return [
                        'item_id' => $itemId,
                        'option_text' => $option,
                        'is_correct' => isset($validated['correct_option_order']) && (int) $validated['correct_option_order'] === $order,
                        'option_order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all());
            }

            return $itemId;
        });
    }
}