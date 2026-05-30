@extends('layouts.admin', [
    'title' => __('lexi.admin.users.meta_title'),
    'description' => __('lexi.admin.users.meta_description'),
    'sidebarNoteTitle' => __('lexi.admin.users.sidebar_title'),
    'sidebarNoteText' => __('lexi.admin.users.sidebar_text'),
])

@section('content')
<section class="admin-page-head">
    <div>
        <h1>{{ __('lexi.admin.users.heading') }}</h1>
        <p>{{ __('lexi.admin.users.intro') }}</p>
    </div>
    <div class="admin-page-actions">
        <a class="admin-btn admin-btn--ghost" href="{{ route('admin') }}"><i class="bi bi-arrow-left"></i> {{ __('lexi.admin.users.back_panel') }}</a>
    </div>
</section>

<section class="admin-stats">
    <article class="admin-card admin-stat">
        <div class="admin-stat__row">
            <span class="admin-chip admin-chip--violet">{{ __('lexi.admin.users.users') }}</span>
            <div class="admin-stat__icon"><i class="bi bi-people"></i></div>
        </div>
        <p class="admin-stat__value">{{ number_format($stats['users']) }}</p>
        <p class="admin-stat__label">{{ __('lexi.admin.users.registered_accounts') }}</p>
        <span class="admin-stat__meta"><i class="bi bi-database"></i> {{ __('lexi.admin.users.real_users_table') }}</span>
    </article>

    <article class="admin-card admin-stat">
        <div class="admin-stat__row">
            <span class="admin-chip admin-chip--green">{{ __('lexi.admin.users.active') }}</span>
            <div class="admin-stat__icon"><i class="bi bi-check2-circle"></i></div>
        </div>
        <p class="admin-stat__value">{{ number_format($stats['active']) }}</p>
        <p class="admin-stat__label">{{ __('lexi.admin.users.verified_users') }}</p>
        <span class="admin-stat__meta"><i class="bi bi-lightning-charge"></i> {{ __('lexi.admin.users.verified_email') }}</span>
    </article>

    <article class="admin-card admin-stat">
        <div class="admin-stat__row">
            <span class="admin-chip admin-chip--amber">{{ __('lexi.admin.users.pending') }}</span>
            <div class="admin-stat__icon"><i class="bi bi-hourglass-split"></i></div>
        </div>
        <p class="admin-stat__value">{{ number_format($stats['pending']) }}</p>
        <p class="admin-stat__label">{{ __('lexi.admin.users.verification_pending') }}</p>
        <span class="admin-stat__meta"><i class="bi bi-envelope"></i> {{ __('lexi.admin.users.requires_review') }}</span>
    </article>

    <article class="admin-card admin-stat">
        <div class="admin-stat__row">
            <span class="admin-chip admin-chip--violet">{{ __('lexi.admin.users.teachers') }}</span>
            <div class="admin-stat__icon"><i class="bi bi-mortarboard"></i></div>
        </div>
        <p class="admin-stat__value">{{ number_format($stats['teachers']) }}</p>
        <p class="admin-stat__label">{{ __('lexi.admin.users.teacher_accounts') }}</p>
        <span class="admin-stat__meta"><i class="bi bi-person-workspace"></i> {{ __('lexi.admin.users.teacher_role') }}</span>
    </article>
</section>

<section class="admin-card">
    <div class="admin-card__inner">
        <div class="admin-card__head">
            <div>
                <h2>{{ __('lexi.admin.users.directory_title') }}</h2>
                <p>{{ __('lexi.admin.users.directory_text') }}</p>
            </div>
        </div>

        <form class="row g-3 mb-4" method="get" action="{{ route('admin-users') }}">
            <div class="col-md-10">
                <label class="form-label" for="userSearch">{{ __('lexi.admin.users.search_user') }}</label>
                <input class="form-control" id="userSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('lexi.admin.users.search_placeholder') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> {{ __('lexi.admin.users.filter') }}</button>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('lexi.admin.users.table_user') }}</th>
                        <th>{{ __('lexi.admin.users.table_email') }}</th>
                        <th>{{ __('lexi.admin.users.table_role') }}</th>
                        <th>{{ __('lexi.admin.users.table_native') }}</th>
                        <th>{{ __('lexi.admin.users.table_learning') }}</th>
                        <th>{{ __('lexi.admin.users.table_status') }}</th>
                        <th>{{ __('lexi.admin.users.table_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php($fullName = trim(($user->name ?? '') . ' ' . ($user->surname ?? '')))
                        @php($rolesCollection = $user->roles->pluck('name'))
                        @php($roles = $rolesCollection->map(fn ($role) => ucfirst($role))->join(', '))
                        @php($learningCodes = $user->userLanguages->pluck('language_code'))
                        @php($learning = $learningCodes->map(fn ($code) => $languageLabels[$code] ?? strtoupper($code))->join(', '))
                        @php($isCurrentUser = (int) auth()->id() === (int) $user->id)
                        @php($isAdminRow = $rolesCollection->contains('admin'))
                        @php($canDelete = !$isCurrentUser && !$isAdminRow)
                        <tr>
                            <td>{{ $fullName ?: __('lexi.admin.users.no_name') }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $roles ?: __('lexi.admin.users.no_role') }}</td>
                            <td>{{ $languageLabels[$user->mother_tongue_code] ?? strtoupper((string) $user->mother_tongue_code) ?: __('lexi.admin.users.na') }}</td>
                            <td>{{ $learning ?: __('lexi.admin.users.no_languages') }}</td>
                            <td>
                                <span class="admin-status {{ $user->email_verified_at ? 'admin-status--active' : 'admin-status--review' }}">
                                    {{ $user->email_verified_at ? __('lexi.admin.users.status_active') : __('lexi.admin.users.status_pending') }}
                                </span>
                            </td>
                            <td>
                                <div class="admin-actions-inline">
                                    <form method="post" action="{{ route('admin-users.verification.toggle', $user->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit">
                                            {{ $user->email_verified_at ? __('lexi.admin.users.unverify_email') : __('lexi.admin.users.verify_email') }}
                                        </button>
                                    </form>

                                    @if ($canDelete)
                                        <button
                                            type="button"
                                            class="admin-action-danger"
                                            data-delete-user
                                            data-delete-user-name="{{ $fullName ?: $user->email }}"
                                            data-delete-url="{{ route('admin-users.destroy', $user->id) }}"
                                        >
                                            {{ __('lexi.admin.users.delete_user') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">{{ __('lexi.admin.users.no_users_filter') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="d-flex justify-content-between align-items-center pt-3">
                <p class="mb-0 text-muted small">{{ __('lexi.admin.users.showing_rows', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}</p>
                <div>{{ $users->onEachSide(1)->links() }}</div>
            </div>
        @endif
    </div>
</section>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content admin-confirm-modal">
                        <div class="modal-header">
                                <h2 class="h5 mb-0">{{ __('lexi.admin.users.delete_modal_title') }}</h2>
                                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                                <p class="mb-2">{{ __('lexi.admin.users.delete_modal_text') }}</p>
                                <p class="admin-confirm-modal__user mb-0" id="deleteUserNamePreview"></p>
                        </div>
                        <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{ __('lexi.common.cancel') }}</button>
                                <button class="btn btn-danger" id="confirmDeleteUserBtn" type="button">{{ __('lexi.admin.users.delete_modal_confirm') }}</button>
                        </div>
                </div>
        </div>
</div>

<form method="post" id="deleteUserForm" class="d-none">
        @csrf
        @method('DELETE')
</form>
@endsection

@section('inlineScripts')
<script>
    (() => {
        const modalElement = document.getElementById('deleteUserModal');
        const triggerButtons = document.querySelectorAll('[data-delete-user]');
        const namePreview = document.getElementById('deleteUserNamePreview');
        const confirmButton = document.getElementById('confirmDeleteUserBtn');
        const deleteForm = document.getElementById('deleteUserForm');
        if (!modalElement || !confirmButton || !deleteForm || !window.bootstrap) return;

        const modal = new window.bootstrap.Modal(modalElement);
        let selectedUrl = '';

        triggerButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const userName = button.getAttribute('data-delete-user-name') || '';
                selectedUrl = button.getAttribute('data-delete-url') || '';
                namePreview.textContent = userName;
                modal.show();
            });
        });

        confirmButton.addEventListener('click', () => {
            if (!selectedUrl) return;
            deleteForm.setAttribute('action', selectedUrl);
            deleteForm.submit();
        });
    })();
</script>
@endsection
