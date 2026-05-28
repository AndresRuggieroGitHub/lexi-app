@extends('layouts.site', [
    'title' => __('lexi.meta.profile.title'),
    'description' => __('lexi.meta.profile.description'),
    'robots' => 'noindex',
    'bodyAttributes' => 'data-server-profile="true"',
])

@php
    $flagByLanguage = [
        'ar' => 'icons/flags/saudi_arabia_flag.svg',
        'bg' => 'icons/flags/bulgaria_flag.svg',
        'cs' => 'icons/flags/czech_republic_flag.svg',
        'de' => 'icons/flags/germany_flag.svg',
        'dk' => 'icons/flags/denmark_flag.svg',
        'en' => 'icons/flags/united_kingdom_flag.svg',
        'es' => 'icons/flags/spain_flag.svg',
        'fi' => 'icons/flags/finland_flag.svg',
        'fr' => 'icons/flags/france_flag.svg',
        'gr' => 'icons/flags/greece_flag.svg',
        'he' => 'icons/flags/israel_flag.svg',
        'hi' => 'icons/flags/india_flag.svg',
        'hu' => 'icons/flags/hungary_flag.svg',
        'id' => 'icons/flags/indonesia_flag.svg',
        'it' => 'icons/flags/italy_flag.svg',
        'ja' => 'icons/flags/japan_flag.svg',
        'ko' => 'icons/flags/south_korea_flag.svg',
        'nl' => 'icons/flags/netherlands_flag.svg',
        'no' => 'icons/flags/norway_flag.svg',
        'pl' => 'icons/flags/poland_flag.svg',
        'pt' => 'icons/flags/brazil_flag.svg',
        'ro' => 'icons/flags/romania_flag.svg',
        'ru' => 'icons/flags/russia_flag.svg',
        'sk' => 'icons/flags/slovakia_flag.svg',
        'sv' => 'icons/flags/sweden_flag.svg',
        'th' => 'icons/flags/thailand_flag.svg',
        'tr' => 'icons/flags/turkey_flag.svg',
        'ua' => 'icons/flags/ukraine_flag.svg',
        'vi' => 'icons/flags/vietnam_flag.svg',
        'zh' => 'icons/flags/china_flag.svg',
    ];

    $nativeCode = strtolower((string) $user->mother_tongue_code);
    $nativeFlag = $flagByLanguage[$nativeCode] ?? null;
@endphp

@section('content')
<main id="mainContent" class="profile-main">
    <div class="profile-hero">
        <div class="profile-avatar" id="profileAvatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
        <div class="profile-hero-info">
            <div class="profile-name-wrap">
                <h1 class="profile-name" id="profileName">{{ $user->name }}</h1>
                <button class="profile-edit-btn" id="btnEditName" aria-label="{{ __('lexi.profile.edit_name') }}">
                    <i class="bi bi-pencil"></i>
                </button>
            </div>
            <p class="profile-since" id="profileSince">{{ __('lexi.profile.member_since', ['date' => optional($user->created_at)->translatedFormat('F Y')]) }}</p>
            <a href="{{ route('progreso') }}" class="profile-progress-link">
                <i class="bi bi-bar-chart-fill"></i>
                {{ __('lexi.profile.view_progress') }}
            </a>
        </div>
    </div>

    <section class="profile-section">
        <h2 class="profile-section-title">{{ __('lexi.profile.account_information') }}</h2>
        <div class="profile-settings-card">
            @if (session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf

                <div class="profile-setting-row">
                    <span class="profile-setting-label">{{ __('lexi.profile.username') }}</span>
                    <div class="profile-setting-control">
                        <span class="profile-setting-value" id="displayName">{{ $user->name }}</span>
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="btnEditName2">{{ __('lexi.common.edit') }}</button>
                    </div>
                </div>

                <div class="profile-setting-row" id="editNameRow" hidden>
                    <label class="profile-setting-label" for="inputName">{{ __('lexi.profile.new_name') }}</label>
                    <div class="profile-setting-control">
                        <input class="profile-input" type="text" id="inputName" name="name" maxlength="30" value="{{ old('name', $user->name) }}" placeholder="{{ __('lexi.auth.name_placeholder') }}">
                        <button class="btn btn-primary btn-sm" id="btnSaveName" type="submit">{{ __('lexi.common.save') }}</button>
                        <button class="btn btn-outline-secondary btn-sm" id="btnCancelName" type="button">{{ __('lexi.common.cancel') }}</button>
                    </div>
                </div>

                <div class="profile-setting-row">
                    <span class="profile-setting-label">{{ __('lexi.profile.email') }}</span>
                    <div class="profile-setting-control">
                        <span class="profile-setting-value">{{ $user->email }}</span>
                        <span class="profile-verified-badge">
                            <i class="bi bi-patch-check-fill"></i>
                            {{ $user->email_verified_at ? __('lexi.profile.verified') : __('lexi.profile.pending') }}
                        </span>
                    </div>
                </div>

                <div class="profile-setting-row">
                    <span class="profile-setting-label">{{ __('lexi.profile.native_language') }}</span>
                    <div class="profile-setting-control">
                        <span class="profile-native-pill profile-native-pill--locked">
                            @if ($nativeFlag)
                                <img src="{{ asset($nativeFlag) }}" alt="" aria-hidden="true" draggable="false">
                            @endif
                            <i class="bi bi-lock-fill profile-native-lock-icon" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
            </form>

            <div class="profile-setting-row" style="flex-direction:column;align-items:stretch;gap:0.75rem">
                <span class="profile-setting-label">{{ __('lexi.profile.studied_languages') }}</span>
                <div id="langChips" class="profile-studied-list">
                    @forelse ($studiedLanguages as $studiedLanguage)
                        @php
                            $studiedCode = strtolower((string) $studiedLanguage['code']);
                            $studiedFlag = $flagByLanguage[$studiedCode] ?? null;
                        @endphp
                        <div class="profile-studied-row{{ $studiedLanguage['is_active'] ? ' is-active' : '' }}">
                            <div class="profile-studied-left">
                                @if ($studiedFlag)
                                    <img src="{{ asset($studiedFlag) }}" alt="" aria-hidden="true" draggable="false">
                                @endif
                                <span>{{ $studiedLanguage['label'] }}</span>
                            </div>
                            @if ($studiedLanguage['is_active'])
                                <span class="profile-studied-badge">
                                    <i class="bi bi-check-circle-fill"></i>
                                    {{ __('lexi.profile.active') }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <span class="profile-setting-hint" style="font-style:italic">{{ __('lexi.profile.no_studied_languages') }}</span>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <h2 class="profile-section-title">{{ __('lexi.profile.session') }}</h2>
        <div class="profile-settings-card">
            <div class="profile-setting-row">
                <span class="profile-setting-label">{{ __('lexi.profile.active_session') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" type="submit">{{ __('lexi.profile.logout') }}</button>
                </form>
            </div>
        </div>
    </section>

    <section class="profile-section">
        <h2 class="profile-section-title" style="color:#dc3545">{{ __('lexi.profile.danger_zone') }}</h2>
        <div class="profile-settings-card profile-danger-card">
            <div class="profile-setting-row profile-setting-danger">
                <div>
                    <span class="profile-setting-label">{{ __('lexi.profile.delete_account') }}</span>
                    <p class="profile-setting-hint">{{ __('lexi.profile.delete_account_hint') }}</p>
                </div>
                <button class="btn btn-danger btn-sm" type="button" id="btnOpenDeleteAccountModal">{{ __('lexi.profile.delete_account') }}</button>
            </div>
        </div>
    </section>

    <div class="profile-delete-modal" id="deleteAccountModal" hidden aria-hidden="true">
        <div class="profile-delete-modal__backdrop" data-delete-modal-close></div>
        <div class="profile-delete-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="deleteAccountModalTitle" aria-describedby="deleteAccountModalText">
            <h3 class="profile-delete-modal__title" id="deleteAccountModalTitle">{{ __('lexi.profile.delete_account') }}</h3>
            <p class="profile-delete-modal__text" id="deleteAccountModalText">{{ __('lexi.profile.delete_account_confirm') }}</p>

            <form id="deleteAccountForm" method="POST" action="{{ route('profile.destroy') }}" class="profile-delete-modal__actions">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-secondary btn-sm" type="button" id="btnCancelDeleteAccount" data-delete-modal-close>{{ __('lexi.common.cancel') }}</button>
                <button class="btn btn-danger btn-sm" type="submit" id="btnConfirmDeleteAccount">{{ __('lexi.profile.delete_account') }}</button>
            </form>
        </div>
    </div>
</main>
@endsection

@section('inlineScripts')
<script>
(function () {
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    const deleteAccountModal = document.getElementById('deleteAccountModal');
    const openDeleteAccountModalButton = document.getElementById('btnOpenDeleteAccountModal');
    const closeDeleteAccountModalControls = document.querySelectorAll('[data-delete-modal-close]');
    let lastFocusedElement = null;

    const openDeleteAccountModal = () => {
        if (!deleteAccountModal) {
            return;
        }

        lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
        deleteAccountModal.hidden = false;
        deleteAccountModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('profile-modal-open');

        const confirmButton = document.getElementById('btnConfirmDeleteAccount');
        if (confirmButton) {
            confirmButton.focus();
        }
    };

    const closeDeleteAccountModal = () => {
        if (!deleteAccountModal) {
            return;
        }

        deleteAccountModal.hidden = true;
        deleteAccountModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('profile-modal-open');

        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    };

    if (openDeleteAccountModalButton) {
        openDeleteAccountModalButton.addEventListener('click', openDeleteAccountModal);
    }

    closeDeleteAccountModalControls.forEach((control) => {
        control.addEventListener('click', closeDeleteAccountModal);
    });

    if (deleteAccountModal) {
        deleteAccountModal.addEventListener('click', (event) => {
            if (event.target === deleteAccountModal) {
                closeDeleteAccountModal();
            }
        });

        deleteAccountModal.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeDeleteAccountModal();
            }
        });
    }

    if (deleteAccountForm) {
        deleteAccountForm.addEventListener('submit', () => {
            const confirmButton = document.getElementById('btnConfirmDeleteAccount');
            if (confirmButton) {
                confirmButton.disabled = true;
            }
        });
    }

    const showEdit = (show) => {
        document.getElementById('editNameRow').hidden = !show;
        document.querySelectorAll('[id="btnEditName"],[id="btnEditName2"]').forEach((button) => {
            button.hidden = show;
        });

        if (show) {
            document.getElementById('inputName').focus();
        }
    };

    document.getElementById('btnEditName').addEventListener('click', () => showEdit(true));
    document.getElementById('btnEditName2').addEventListener('click', () => showEdit(true));
    document.getElementById('btnCancelName').addEventListener('click', () => showEdit(false));
})();
</script>
@endsection
