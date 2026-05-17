<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.admin', [
            'cards' => [
                ['title' => __('lexi.admin.dashboard.users_title'), 'description' => __('lexi.admin.dashboard.users_description'), 'count' => $this->countLabel('users', __('lexi.admin.dashboard.users_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-people', 'href' => route('admin-users')],
                ['title' => __('lexi.admin.dashboard.languages_title'), 'description' => __('lexi.admin.dashboard.languages_description'), 'count' => $this->countDistinctLabel('words', 'language_code', __('lexi.admin.dashboard.languages_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-translate', 'href' => route('admin-languages')],
                ['title' => __('lexi.admin.dashboard.words_title'), 'description' => __('lexi.admin.dashboard.words_description'), 'count' => $this->countLabel('words', __('lexi.admin.dashboard.words_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-book', 'href' => route('admin-words')],
                ['title' => __('lexi.admin.dashboard.translations_title'), 'description' => __('lexi.admin.dashboard.translations_description'), 'count' => $this->countLabel('translations', __('lexi.admin.dashboard.translations_suffix')), 'status' => __('lexi.admin.dashboard.review'), 'statusClass' => 'admin-status--review', 'icon' => 'bi-arrow-left-right', 'href' => route('admin-translations')],
                ['title' => __('lexi.admin.dashboard.collections_title'), 'description' => __('lexi.admin.dashboard.collections_description'), 'count' => $this->countLabel('collections', __('lexi.admin.dashboard.collections_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-collection', 'href' => route('admin-collections')],
                ['title' => __('lexi.admin.dashboard.exercises_title'), 'description' => __('lexi.admin.dashboard.exercises_description'), 'count' => $this->countLabel('exercises', __('lexi.admin.dashboard.exercises_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-grid', 'href' => route('admin-exercises')],
                ['title' => __('lexi.admin.dashboard.categories_title'), 'description' => __('lexi.admin.dashboard.categories_description'), 'count' => $this->countLabel('categories', __('lexi.admin.dashboard.categories_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-tags', 'href' => route('admin-categories')],
                ['title' => __('lexi.admin.dashboard.roles_title'), 'description' => __('lexi.admin.dashboard.roles_description'), 'count' => $this->countLabel('roles', __('lexi.admin.dashboard.roles_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-shield-lock', 'href' => route('admin-roles')],
                ['title' => __('lexi.admin.dashboard.billing_title'), 'description' => __('lexi.admin.dashboard.billing_description'), 'count' => $this->countLabel('subscriptions', __('lexi.admin.dashboard.billing_suffix')), 'status' => Schema::hasTable('subscriptions') ? __('lexi.admin.dashboard.active') : __('lexi.admin.dashboard.pending'), 'statusClass' => Schema::hasTable('subscriptions') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-credit-card', 'href' => route('admin-billing')],
                ['title' => __('lexi.admin.dashboard.payments_title'), 'description' => __('lexi.admin.dashboard.payments_description'), 'count' => $this->countLabel('payments', __('lexi.admin.dashboard.payments_suffix')), 'status' => Schema::hasTable('payments') ? __('lexi.admin.dashboard.active') : __('lexi.admin.dashboard.pending'), 'statusClass' => Schema::hasTable('payments') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-cash-coin', 'href' => route('admin-billing') . '#payments'],
                ['title' => __('lexi.admin.dashboard.usage_title'), 'description' => __('lexi.admin.dashboard.usage_description'), 'count' => $this->countLabel('user_usage', __('lexi.admin.dashboard.usage_suffix')), 'status' => Schema::hasTable('user_usage') ? __('lexi.admin.dashboard.active') : __('lexi.admin.dashboard.pending'), 'statusClass' => Schema::hasTable('user_usage') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-speedometer2', 'href' => route('admin-billing') . '#usage'],
                ['title' => __('lexi.admin.dashboard.plan_features_title'), 'description' => __('lexi.admin.dashboard.plan_features_description'), 'count' => $this->countLabel('plan_features', __('lexi.admin.dashboard.plan_features_suffix')), 'status' => Schema::hasTable('plan_features') ? __('lexi.admin.dashboard.active') : __('lexi.admin.dashboard.pending'), 'statusClass' => Schema::hasTable('plan_features') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-sliders2', 'href' => route('admin-billing') . '#features'],
                ['title' => __('lexi.admin.dashboard.analytics_title'), 'description' => __('lexi.admin.dashboard.analytics_description'), 'count' => $this->countLabel('exercise_attempts', __('lexi.admin.dashboard.analytics_suffix')), 'status' => __('lexi.admin.dashboard.active'), 'statusClass' => 'admin-status--active', 'icon' => 'bi-graph-up-arrow', 'href' => route('admin-analytics')],
                ['title' => __('lexi.admin.dashboard.ai_title'), 'description' => __('lexi.admin.dashboard.ai_description'), 'count' => $this->countLabel('ai_generations', __('lexi.admin.dashboard.ai_suffix')), 'status' => Schema::hasTable('ai_generations') ? __('lexi.admin.dashboard.active') : __('lexi.admin.dashboard.pending'), 'statusClass' => Schema::hasTable('ai_generations') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-robot', 'href' => route('admin-ai')],
            ],
        ]);
    }

    private function countLabel(string $table, string $suffix): string
    {
        if (!Schema::hasTable($table)) {
            return __('lexi.admin.dashboard.pending');
        }

        return DB::table($table)->count() . ' ' . $suffix;
    }

    private function countDistinctLabel(string $table, string $column, string $suffix): string
    {
        if (!Schema::hasTable($table)) {
            return __('lexi.admin.dashboard.pending');
        }

        return DB::table($table)->distinct()->count($column) . ' ' . $suffix;
    }
}