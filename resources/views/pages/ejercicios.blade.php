@extends('layouts.site', ['title' => __('lexi.meta.exercises.title'), 'description' => __('lexi.meta.exercises.description'), 'robots' => 'index,follow', 'activeNav' => 'exercises', 'showFooter' => false])

@section('content')
<main id="mainContent" class="exercises page-main">

  <div id="exerciseMenu">
    <div class="exercises-hero">
      <h1 class="exercises-title">{{ __('lexi.exercises.title') }}</h1>
      <p class="exercises-subtitle">{{ __('lexi.exercises.subtitle') }}</p>
      <div class="exercise-source-toggle" role="tablist" aria-label="{{ __('lexi.exercises.source_label') }}">
        <button class="exercise-source-toggle__btn is-active" type="button" data-source-tab="catalog" aria-pressed="true"><i class="bi bi-grid-1x2-fill"></i><span>{{ __('lexi.exercises.catalog') }}</span></button>
        <button class="exercise-source-toggle__btn" type="button" data-source-tab="saved" aria-pressed="false"><i class="bi bi-bookmarks-fill"></i><span>{{ __('lexi.exercises.your_lists') }}</span></button>
      </div>
      <div class="exercise-list-picker">
        <div class="exercise-list-picker__loading" id="exerciseSourceLoading" hidden aria-live="polite">
          <span class="exercise-list-picker__spinner" aria-hidden="true"></span>
          <span id="exerciseSourceLoadingText">{{ __('lexi.exercises.loading_options') }}</span>
        </div>
        <div class="exercise-source-panel" data-source-panel="catalog">
          <div class="exercise-list-picker__grid">
            <select id="exerciseCatalogLevelSelect" class="exercise-list-picker__select"></select>
            <select id="exerciseCatalogTopicSelect" class="exercise-list-picker__select"></select>
          </div>
        </div>
        <div class="exercise-source-panel" data-source-panel="saved" hidden>
          <div class="exercise-list-picker__row">
            <select id="exerciseCollectionSelect" class="exercise-list-picker__select"></select>
          </div>
        </div>
      </div>
    </div>
    <section class="exercise-grid">
      <article class="exercise-card" data-mode="reading" role="button" tabindex="0" aria-label="{{ __('lexi.exercises.card_reading_aria') }}" style="--card-color:#4f8ef7">
        <div class="exercise-card-icon"><i class="bi bi-book-half"></i></div>
        <img src="images/reading_small.webp" srcset="images/reading_small.webp 300w, images/reading_medium.webp 600w, images/reading_large.webp 900w" sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 20vw" loading="lazy" alt="{{ __('lexi.exercises.reading_image_alt') }}">
        <p class="exercise-card-label">{{ __('lexi.exercises.reading_label') }}</p>
        <span class="exercise-card-badge">{{ __('lexi.exercises.reading_label') }}</span>
      </article>
      <article class="exercise-card" data-mode="listening" role="button" tabindex="0" aria-label="{{ __('lexi.exercises.card_listening_aria') }}" style="--card-color:#f76b4f">
        <div class="exercise-card-icon"><i class="bi bi-headphones"></i></div>
        <img src="images/listening_small.webp" srcset="images/listening_small.webp 300w, images/listening_medium.webp 600w, images/listening_large.webp 900w" sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 20vw" loading="lazy" alt="{{ __('lexi.exercises.listening_image_alt') }}">
        <p class="exercise-card-label">{{ __('lexi.exercises.listening_label') }}</p>
        <span class="exercise-card-badge">{{ __('lexi.exercises.listening_label') }}</span>
      </article>
      <article class="exercise-card" data-mode="speaking" role="button" tabindex="0" aria-label="{{ __('lexi.exercises.card_speaking_aria') }}" style="--card-color:#2dc98b">
        <div class="exercise-card-icon"><i class="bi bi-mic-fill"></i></div>
        <img src="images/speaking_small.webp" srcset="images/speaking_small.webp 300w, images/speaking_medium.webp 600w, images/speaking_large.webp 900w" sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 20vw" loading="lazy" alt="{{ __('lexi.exercises.speaking_image_alt') }}">
        <p class="exercise-card-label">{{ __('lexi.exercises.speaking_label') }}</p>
        <span class="exercise-card-badge">{{ __('lexi.exercises.speaking_label') }}</span>
      </article>
      <article class="exercise-card" data-mode="writing" role="button" tabindex="0" aria-label="{{ __('lexi.exercises.card_writing_aria') }}" style="--card-color:#a855f7">
        <div class="exercise-card-icon"><i class="bi bi-pencil-fill"></i></div>
        <img src="images/writing_small.webp" srcset="images/writing_small.webp 300w, images/writing_medium.webp 600w, images/writing_large.webp 900w" sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 20vw" loading="lazy" alt="{{ __('lexi.exercises.writing_image_alt') }}">
        <p class="exercise-card-label">{{ __('lexi.exercises.writing_label') }}</p>
        <span class="exercise-card-badge">{{ __('lexi.exercises.writing_label') }}</span>
      </article>
      <article class="exercise-card" data-mode="mix" role="button" tabindex="0" aria-label="{{ __('lexi.exercises.card_mix_aria') }}" style="--card-color:#f9b233">
        <div class="exercise-card-icon"><i class="bi bi-shuffle"></i></div>
        <img src="images/challenge_small.webp" srcset="images/challenge_small.webp 300w, images/challenge_medium.webp 600w, images/challenge_large.webp 900w" sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 20vw" loading="lazy" alt="{{ __('lexi.exercises.mix_image_alt') }}">
        <p class="exercise-card-label">{{ __('lexi.exercises.mix_label') }}</p>
        <span class="exercise-card-badge">{{ __('lexi.exercises.mix_label') }}</span>
      </article>
    </section>
  </div>

  <div id="exercisePanel" class="ex-panel" hidden>
    <div class="ex-panel-header">
      <button class="ex-back-btn" id="btnBack">
        <i class="bi bi-arrow-left"></i> {{ __('lexi.exercises.back') }}
      </button>
      <div class="ex-panel-meta">
        <h2 class="ex-panel-title" id="exPanelTitle"></h2>
        <span class="ex-panel-progress" id="exProgress"></span>
      </div>
    </div>
    <div class="ex-progress-bar-wrap">
      <div class="ex-progress-bar" id="exProgressBar"></div>
    </div>
    <div id="exerciseContent" class="ex-content"></div>
    <div class="ex-nav-btns">
      <button class="btn btn-outline-secondary" id="btnPrevEx" disabled>
        <i class="bi bi-arrow-left"></i> {{ __('lexi.exercises.previous') }}
      </button>
      <button class="btn btn-primary" id="btnNextEx">
        {{ __('lexi.exercises.next') }} <i class="bi bi-arrow-right"></i>
      </button>
    </div>
  </div>

  <div id="exCompleteModal" class="ex-complete-modal" hidden>
    <div class="ex-complete-box">
      <div class="ex-complete-icon">🎉</div>
      <h2>{{ __('lexi.exercises.completed') }}</h2>
      <p id="exCompleteMsg"></p>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
        <button class="btn btn-outline-secondary" id="btnRepeat">{{ __('lexi.exercises.repeat') }}</button>
        <button class="btn btn-primary" id="btnBackFromComplete">{{ __('lexi.exercises.choose_other_mode') }}</button>
      </div>
    </div>
  </div>

</main>
@endsection

@section('inlineScripts')
<script type="application/json" id="lexiExerciseSharedConfig">{!! json_encode([
  'cefr_levels' => config('lexi.cefr_levels', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']),
  'topic_options' => config('lexi.catalog_topics', []),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/json" id="lexiExerciseTemplateCatalog">{!! json_encode($normalizedTemplates ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script>
(function () {
  const EXERCISE_I18N = (window.lexiTranslations && window.lexiTranslations.js && window.lexiTranslations.js.exercise_runtime) || {};
  const t = (key, replacements = {}) => {
    const template = EXERCISE_I18N[key];
    if (typeof template !== 'string') return key;

    return template.replace(/:([a-zA-Z_]+)/g, (_, token) => replacements[token] ?? `:${token}`);
  };
  const tx = (key, fallback) => {
    const value = t(key);
    return value === key ? fallback : value;
  };
  const sharedConfigNode = document.getElementById('lexiExerciseSharedConfig');
  const sharedConfig = sharedConfigNode ? JSON.parse(sharedConfigNode.textContent || '{}') : {};
  const templateCatalogNode = document.getElementById('lexiExerciseTemplateCatalog');
  const TEMPLATE_CATALOG = templateCatalogNode ? JSON.parse(templateCatalogNode.textContent || '{}') : {};
  const SHARED_CEFR_LEVELS = Array.isArray(sharedConfig.cefr_levels) ? sharedConfig.cefr_levels : [];
  const SHARED_TOPIC_OPTIONS = Array.isArray(sharedConfig.topic_options) ? sharedConfig.topic_options : [];
  const SKILL_LABELS = {
    en: ['Reading', 'Listening', 'Speaking', 'Writing', 'Challenge'],
    fr: ['Lecture', 'Écoute', 'Expression', 'Écriture', 'Challenge'],
    de: ['Lesen', 'Hören', 'Sprechen', 'Schreiben', 'Challenge'],
    it: ['Lettura', 'Ascolto', 'Parlare', 'Scrittura', 'Challenge'],
    no: ['Lesing', 'Lytting', 'Snakking', 'Skriving', 'Challenge'],
    dk: ['Læsning', 'Lytning', 'Tale', 'Skrivning', 'Challenge'],
    fi: ['Lukeminen', 'Kuuntelu', 'Puhuminen', 'Kirjoitus', 'Challenge'],
    ko: ['읽기', '듣기', '말하기', '쓰기', '챌린지'],
    zh: ['阅读', '听力', '口语', '写作', '挑战'],
    ru: ['Чтение', 'Слушание', 'Говорение', 'Письмо', 'Челлендж'],
    ua: ['Читання', 'Слухання', 'Говоріння', 'Письмо', 'Виклик'],
    gr: ['Ανάγνωση', 'Ακρόαση', 'Ομιλία', 'Γραφή', 'Challenge'],
    es: ['Lectura', 'Escucha', 'Habla', 'Escritura', 'Desafio'],
  };
  const BADGE_MODES = ['reading', 'listening', 'speaking', 'writing', 'mix'];
  const EXERCISE_SOURCE_KEY = 'lexiExerciseSource';
  const EXERCISE_COLLECTION_KEY = 'lexiExerciseCollection';
  const EXERCISE_CATALOG_LEVEL_KEY = 'lexiExerciseCatalogLevel';
  const EXERCISE_CATALOG_TOPIC_KEY = 'lexiExerciseCatalogTopic';
  const CEFR_LEVELS = Array.isArray(SHARED_CEFR_LEVELS) && SHARED_CEFR_LEVELS.length ? SHARED_CEFR_LEVELS : ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
  let serverVocabularyState = { library: { id: 'library', name: t('saved_name'), items: [] }, collections: [], catalog: [] };
  let exerciseSourceSwitchInFlight = false;

  function setExerciseCollectionsLoading(isLoading, message = t('loading_options')) {
    const select = document.getElementById('exerciseCollectionSelect');
    const levelSelect = document.getElementById('exerciseCatalogLevelSelect');
    const topicSelect = document.getElementById('exerciseCatalogTopicSelect');
    const loading = document.getElementById('exerciseSourceLoading');
    const loadingText = document.getElementById('exerciseSourceLoadingText');
    if (!select) return;

    if (loading) loading.hidden = !isLoading;
    if (loadingText) loadingText.textContent = message;

    if (isLoading) {
      select.innerHTML = '<option selected disabled>' + t('loading_lists') + '</option>';
      select.disabled = true;
      if (levelSelect) levelSelect.disabled = true;
      if (topicSelect) topicSelect.disabled = true;
      return;
    }

    select.disabled = false;
    if (levelSelect) levelSelect.disabled = false;
    if (topicSelect) topicSelect.disabled = false;
  }
  const CATALOG_TOPIC_ORDER = SHARED_TOPIC_OPTIONS.map(topic => topic.value);
  const TOPIC_OPTION_LABELS = SHARED_TOPIC_OPTIONS.reduce((labels, topic) => {
    labels[topic.value] = topic.label;
    return labels;
  }, { cultura: '🎭 Cultura' });
  const CATALOG_VOCABULARY = [
    { id: 'w-heritage', lang: 'en', cefr: 'B2', topic: 'culture', text: 'to preserve heritage', translation: 'preservar el patrimonio' },
    { id: 'w-growth', lang: 'en', cefr: 'B1', topic: 'business', text: 'sustainable growth', translation: 'crecimiento sostenible' },
    { id: 'w-checkin', lang: 'en', cefr: 'A2', topic: 'travel', text: 'check in at the hotel', translation: 'hacer el check-in en el hotel' },
    { id: 'w-energy', lang: 'en', cefr: 'B1', topic: 'science', text: 'renewable energy sources', translation: 'fuentes de energía renovable' },
    { id: 'w-maison', lang: 'fr', cefr: 'A1', topic: 'home', text: 'la maison', translation: 'la casa' },
    { id: 'w-breakfast', lang: 'en', cefr: 'A1', topic: 'food', text: 'have breakfast', translation: 'desayunar' },
    { id: 'w-appointment', lang: 'en', cefr: 'A2', topic: 'health', text: 'book an appointment', translation: 'pedir una cita' },
    { id: 'w-deadline', lang: 'en', cefr: 'B2', topic: 'work', text: 'meet a deadline', translation: 'cumplir un plazo de entrega' },
    { id: 'w-gare', lang: 'fr', cefr: 'A2', topic: 'travel', text: 'la gare', translation: 'la estación de tren' },
    { id: 'w-critical', lang: 'en', cefr: 'B1', topic: 'education', text: 'critical thinking', translation: 'pensamiento crítico' },
    { id: 'w-art', lang: 'en', cefr: 'B2', topic: 'culture', text: 'art exhibition', translation: 'exposición de arte' },
    { id: 'w-negotiate', lang: 'en', cefr: 'C1', topic: 'business', text: 'to negotiate terms', translation: 'negociar las condiciones' },
    { id: 'w-biodiversity', lang: 'en', cefr: 'C1', topic: 'science', text: 'protect biodiversity', translation: 'proteger la biodiversidad' },
    { id: 'w-commute', lang: 'en', cefr: 'B1', topic: 'work', text: 'daily commute', translation: 'desplazamiento diario' },
    { id: 'w-fluent', lang: 'en', cefr: 'B2', topic: 'education', text: 'become fluent', translation: 'llegar a tener fluidez' },
    { id: 'w-recipe', lang: 'en', cefr: 'A2', topic: 'food', text: 'follow a recipe', translation: 'seguir una receta' },
    { id: 'w-library', lang: 'en', cefr: 'A1', topic: 'education', text: 'public library', translation: 'biblioteca pública' },
    { id: 'w-treaty', lang: 'en', cefr: 'C1', topic: 'politics', text: 'sign a treaty', translation: 'firmar un tratado' },
    { id: 'w-freelance', lang: 'en', cefr: 'B2', topic: 'work', text: 'freelance designer', translation: 'diseñador freelance' },
    { id: 'w-boulangerie', lang: 'fr', cefr: 'A1', topic: 'food', text: 'la boulangerie', translation: 'la panadería' },
    { id: 'w-zeitgeist', lang: 'en', cefr: 'C2', topic: 'culture', text: 'capture the zeitgeist', translation: 'capturar el espíritu de la época' },
    { id: 'w-empathy', lang: 'en', cefr: 'B2', topic: 'health', text: 'show empathy', translation: 'mostrar empatía' },
    { id: 'w-haus', lang: 'de', cefr: 'A1', topic: 'home', text: 'das Haus', translation: 'la casa' },
    { id: 'w-arbeit', lang: 'de', cefr: 'A2', topic: 'work', text: 'die Arbeit', translation: 'el trabajo' },
    { id: 'w-reise', lang: 'de', cefr: 'B1', topic: 'travel', text: 'die Reise', translation: 'el viaje' },
    { id: 'w-vaer', lang: 'no', cefr: 'A1', topic: 'nature', text: 'været', translation: 'el tiempo meteorológico' },
    { id: 'w-koulu', lang: 'fi', cefr: 'A2', topic: 'education', text: 'koulu', translation: 'la escuela' },
    { id: 'w-annyeong', lang: 'ko', cefr: 'A1', topic: 'social', text: '안녕하세요', translation: 'hola / buenos días' },
    { id: 'w-rabota', lang: 'ru', cefr: 'A2', topic: 'work', text: 'работа', translation: 'trabajo' },
    { id: 'w-mangiare', lang: 'it', cefr: 'A1', topic: 'food', text: 'mangiare', translation: 'comer' },
    { id: 'w-hej', lang: 'dk', cefr: 'A1', topic: 'social', text: 'hej', translation: 'hola' },
    { id: 'w-thalassa', lang: 'gr', cefr: 'B1', topic: 'culture', text: 'Θάλασσα', translation: 'el mar' },
    { id: 'w-gongzuo', lang: 'zh', cefr: 'A2', topic: 'work', text: '工作', translation: 'trabajo' },
    { id: 'w-praia', lang: 'pt', cefr: 'A1', topic: 'travel', text: 'a praia', translation: 'la playa' },
    { id: 'w-hej-sv', lang: 'sv', cefr: 'A1', topic: 'social', text: 'hej', translation: 'hola' },
    { id: 'w-arigato', lang: 'ja', cefr: 'A1', topic: 'social', text: 'ありがとう', translation: 'gracias' },
    { id: 'w-zdravey', lang: 'bg', cefr: 'A1', topic: 'social', text: 'Здравей', translation: 'hola' },
    { id: 'w-buna-ro', lang: 'ro', cefr: 'A1', topic: 'social', text: 'buna ziua', translation: 'buenos días' },
    { id: 'w-dobry-cs', lang: 'cs', cefr: 'A1', topic: 'social', text: 'dobrý den', translation: 'buenos días' },
    { id: 'w-dobry-sk', lang: 'sk', cefr: 'A1', topic: 'social', text: 'dobrý deň', translation: 'buenos días' },
    { id: 'w-jonapot', lang: 'hu', cefr: 'A1', topic: 'social', text: 'jó napot', translation: 'buenos días' },
    { id: 'w-marhaba', lang: 'ar', cefr: 'A1', topic: 'social', text: 'مرحبا', translation: 'hola' },
    { id: 'w-xinchao', lang: 'vi', cefr: 'A1', topic: 'social', text: 'xin chào', translation: 'hola' },
    { id: 'w-tr-toplanti', lang: 'tr', cefr: 'B1', topic: 'work', text: 'toplantı', translation: 'reunión' },
    { id: 'w-id-pengetahuan', lang: 'id', cefr: 'B2', topic: 'education', text: 'pengetahuan', translation: 'conocimiento' },
    { id: 'w-he-tarbut', lang: 'he', cefr: 'B2', topic: 'culture', text: 'תַּרְבּוּת', translation: 'cultura' },
    { id: 'w-hi-vyapar', lang: 'hi', cefr: 'B2', topic: 'work', text: 'व्यापार', translation: 'negocio / comercio' },
    { id: 'w-th-thongthiao', lang: 'th', cefr: 'B1', topic: 'travel', text: 'ท่องเที่ยว', translation: 'viajar / turismo' }
  ];

  const getActiveLang = () => localStorage.getItem('lexiLang') || 'en';

  const getXsrfToken = () => {
    const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
  };

  const exerciseApiFetch = (url, options = {}) => {
    const headers = new Headers(options.headers || {});
    if (!headers.has('Accept')) headers.set('Accept', 'application/json');
    if (options.body && !headers.has('Content-Type')) headers.set('Content-Type', 'application/json');
    const xsrfToken = getXsrfToken();
    if (xsrfToken && !headers.has('X-XSRF-TOKEN')) headers.set('X-XSRF-TOKEN', xsrfToken);

    return fetch(url, {
      credentials: 'same-origin',
      ...options,
      headers,
    });
  };

  function normalizeTopicKey(topic) {
    const normalized = String(topic || '').trim().toLowerCase();
    return normalized === 'cultura' ? 'culture' : normalized;
  }

  function getTopicOptions() {
    return SHARED_TOPIC_OPTIONS;
  }

  async function loadVocabularySources(message = t('loading_options'), showSpinner = false) {
    if (showSpinner) {
      setExerciseCollectionsLoading(true, message);
    }

    try {
      const response = await exerciseApiFetch('/api/library/state');
      if (!response.ok) throw new Error('library-state-error');
      const payload = await response.json();
      serverVocabularyState = {
        library: {
          id: 'library',
          name: t('saved_name'),
          items: Array.isArray(payload.items) ? payload.items : [],
        },
        collections: Array.isArray(payload.collections) ? payload.collections : [],
        catalog: Array.isArray(payload.catalog) ? payload.catalog : [],
      };
    } catch {
      serverVocabularyState = {
        library: { id: 'library', name: t('saved_name'), items: [] },
        collections: [],
        catalog: [],
      };
    } finally {
      if (showSpinner) {
        setExerciseCollectionsLoading(false);
      }
    }
  }

  function getVocabularySources() {
    const activeLang = getActiveLang();
    const libraryItems = (serverVocabularyState.library?.items || []).filter(item => !item.language || item.language === activeLang);
    const collections = (serverVocabularyState.collections || []).map(collection => ({
      id: collection.id,
      name: collection.name,
      items: (collection.items || []).filter(item => !item.language || item.language === activeLang),
    }));

    return {
      activeLang,
      library: {
        id: 'library',
        name: t('saved_name'),
        items: libraryItems,
      },
      collections,
    };
  }

  function getCatalogVocabulary() {
    const activeLang = getActiveLang();
    const serverItems = (serverVocabularyState.catalog || []).map(item => ({
      id: item.id,
      lang: item.language,
      cefr: item.cefr,
      topic: normalizeTopicKey(item.topic),
      text: item.label,
      translation: item.translation,
    }));

    const catalogSource = serverItems.length ? serverItems : CATALOG_VOCABULARY;
    const items = catalogSource.filter(item => item.lang === activeLang);
    return items.length ? items : catalogSource.filter(item => item.lang === 'en');
  }

  function getCatalogLevels(items) {
    const presentLevels = new Set(items.map(item => String(item.cefr || '').toUpperCase()).filter(level => CEFR_LEVELS.includes(level)));
    return CEFR_LEVELS.filter(level => presentLevels.has(level) || true);
  }

  function getCatalogTopics(items) {
    const presentTopics = new Set(items.map(item => normalizeTopicKey(item.topic)).filter(Boolean));
    const orderedTopics = CATALOG_TOPIC_ORDER.filter(topic => presentTopics.has(topic));
    return orderedTopics.length ? orderedTopics : CATALOG_TOPIC_ORDER;
  }

  function getSelectedSourceType() {
    return localStorage.getItem(EXERCISE_SOURCE_KEY) || 'catalog';
  }

  function getSelectedCatalogSource() {
    const items = getCatalogVocabulary();
    const levels = getCatalogLevels(items);
    const topics = getCatalogTopics(items);
    const selectedLevel = localStorage.getItem(EXERCISE_CATALOG_LEVEL_KEY) || '';
    const selectedTopic = localStorage.getItem(EXERCISE_CATALOG_TOPIC_KEY) || '';
    const filteredItems = items.filter(item => {
      const matchesLevel = !selectedLevel || item.cefr === selectedLevel;
      const matchesTopic = !selectedTopic || item.topic === selectedTopic;
      return matchesLevel && matchesTopic;
    });

    return {
      id: 'catalog',
      name: [selectedLevel, selectedTopic].filter(Boolean).join(' · ') || t('catalog_name'),
      items: filteredItems,
      levels,
      topics,
      selectedLevel,
      selectedTopic,
    };
  }

  function aggregateSavedItems(library, collections) {
    const merged = [...(library.items || []), ...collections.flatMap(collection => collection.items || [])];
    const byKey = new Map();

    merged.forEach(item => {
      const key = String(item.id || item.clientKey || item.text || '').trim().toLowerCase();
      if (!key) return;
      if (!byKey.has(key)) byKey.set(key, item);
    });

    return Array.from(byKey.values());
  }

  function getSelectedVocabularySource() {
    if (getSelectedSourceType() === 'catalog') {
      return getSelectedCatalogSource();
    }

    const { library, collections } = getVocabularySources();
    const selectedId = localStorage.getItem(EXERCISE_COLLECTION_KEY) || 'all_saved';
    if (selectedId === 'all_saved') {
      return {
        id: 'all_saved',
        name: tx('all_collections', 'Todas las colecciones'),
        items: aggregateSavedItems(library, collections),
      };
    }
    return collections.find(collection => String(collection.id) === selectedId) || (selectedId === 'library' ? library : { id: 'saved', name: t('saved_lists_name'), items: [] });
  }

  function syncSourcePanels() {
    const sourceType = getSelectedSourceType();
    document.querySelectorAll('[data-source-tab]').forEach(button => {
      const isActive = button.dataset.sourceTab === sourceType;
      button.classList.toggle('is-active', isActive);
      button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
    document.querySelectorAll('[data-source-panel]').forEach(panel => {
      panel.hidden = panel.dataset.sourcePanel !== sourceType;
    });
  }

  function setupExerciseSourceTabs() {
    document.querySelectorAll('[data-source-tab]').forEach(button => {
      button.addEventListener('click', async () => {
        if (exerciseSourceSwitchInFlight) return;
        exerciseSourceSwitchInFlight = true;
        localStorage.setItem(EXERCISE_SOURCE_KEY, button.dataset.sourceTab);
        localStorage.setItem(EXERCISE_CATALOG_LEVEL_KEY, '');
        localStorage.setItem(EXERCISE_CATALOG_TOPIC_KEY, '');
        localStorage.setItem(EXERCISE_COLLECTION_KEY, 'all_saved');
        try {
          await loadVocabularySources(button.dataset.sourceTab === 'saved' ? t('loading_saved_lists') : t('loading_catalog'), true);
          syncSourcePanels();
          setupExerciseCatalogSelects();
          setupExerciseCollectionSelect();
        } finally {
          exerciseSourceSwitchInFlight = false;
        }
      });
    });

    syncSourcePanels();
  }

  function setupExerciseCatalogSelects() {
    const levelSelect = document.getElementById('exerciseCatalogLevelSelect');
    const topicSelect = document.getElementById('exerciseCatalogTopicSelect');
    if (!levelSelect || !topicSelect) return;

    const catalogSource = getSelectedCatalogSource();
    const levelOptionLabel = (level) => {
      if (level === 'A1') return '▮▯▯▯▯ ' + level;
      if (level === 'A2') return '▮▮▯▯▯ ' + level;
      if (level === 'B1') return '▮▮▮▯▯ ' + level;
      if (level === 'B2' || level === 'C1') return '▮▮▮▮▯ ' + level;
      return '▮▮▮▮▮ ' + level;
    };
    levelSelect.innerHTML = '<option value="">' + tx('all_levels', 'Todos los niveles') + '</option>' + catalogSource.levels.map(level => '<option value="' + level + '">' + levelOptionLabel(level) + '</option>').join('');
    topicSelect.innerHTML = '<option value="">' + tx('all_categories', 'Todas las categorias') + '</option>' + getTopicOptions().map(topic => '<option value="' + topic.value + '">' + topic.label + '</option>').join('');

    levelSelect.value = catalogSource.selectedLevel;
    topicSelect.value = catalogSource.selectedTopic;

    levelSelect.onchange = () => {
      localStorage.setItem(EXERCISE_CATALOG_LEVEL_KEY, levelSelect.value);
    };
    topicSelect.onchange = () => {
      localStorage.setItem(EXERCISE_CATALOG_TOPIC_KEY, topicSelect.value);
    };
  }

  function setupExerciseCollectionSelect() {
    const select = document.getElementById('exerciseCollectionSelect');
    if (!select) return;

    const { library, collections } = getVocabularySources();
    const selectedId = localStorage.getItem(EXERCISE_COLLECTION_KEY) || 'all_saved';
    const options = [library, ...collections];
    const allCount = aggregateSavedItems(library, collections).length;

    select.innerHTML = '<option value="all_saved">' + tx('all_collections', 'Todas las colecciones') + ' (' + allCount + ')</option>' + options.map(source => {
      const count = source.items.length;
      const label = source.name + ' (' + count + ')';
      return '<option value="' + String(source.id) + '">' + label + '</option>';
    }).join('');

    const hasSelected = selectedId === 'all_saved' || options.some(source => String(source.id) === selectedId);
    select.value = hasSelected ? selectedId : 'all_saved';
    localStorage.setItem(EXERCISE_COLLECTION_KEY, select.value);

    select.onchange = () => {
      localStorage.setItem(EXERCISE_COLLECTION_KEY, select.value);
    };
  }

  function resetExerciseSelectionState() {
    localStorage.setItem(EXERCISE_SOURCE_KEY, 'catalog');
    localStorage.setItem(EXERCISE_CATALOG_LEVEL_KEY, '');
    localStorage.setItem(EXERCISE_CATALOG_TOPIC_KEY, '');
    localStorage.setItem(EXERCISE_COLLECTION_KEY, 'all_saved');
  }

  function resolveDifficultyLevel(source, items) {
    const selectedLevel = String(source?.selectedLevel || '').toUpperCase();
    if (['A1', 'A2', 'B1', 'B2', 'C1', 'C2'].includes(selectedLevel)) {
      return selectedLevel;
    }

    const counts = new Map();
    items.forEach(item => {
      const level = String(item?.cefr || '').toUpperCase();
      if (!['A1', 'A2', 'B1', 'B2', 'C1', 'C2'].includes(level)) return;
      counts.set(level, (counts.get(level) || 0) + 1);
    });

    if (!counts.size) return 'B1';

    return Array.from(counts.entries()).sort((left, right) => right[1] - left[1])[0][0];
  }

  function buildCustomSpeakingItems(items, difficultyLevel = 'B1') {
    return items
      .map(item => ({
        type: 'pronounce',
        word: item.text || item.word || '',
        hint: buildSpeakingHint(item, difficultyLevel),
      }))
      .filter(item => item.word)
      .slice(0, 5);
  }

  function buildCustomReadingItems(items, difficultyLevel = 'B1') {
    const wordBank = items
      .map(item => item.text || item.word || '')
      .filter(Boolean);
    const optionsLimit = ['A1', 'A2'].includes(difficultyLevel) ? 3 : 4;
    const question = ['A1', 'A2'].includes(difficultyLevel)
      ? 'Choose the correct word to complete the sentence.'
      : ['B1', 'B2'].includes(difficultyLevel)
        ? 'Choose the best word to complete the gap naturally and accurately.'
        : 'Choose the most precise option to complete the gap while preserving register and collocation.';

    return items
      .filter(item => item.text && item.translation)
      .slice(0, 5)
      .map(item => {
        const correctAnswer = item.text;
        const distractors = selectReadingDistractors(wordBank, correctAnswer, Math.max(2, optionsLimit - 1));
        const options = shuffleArray([correctAnswer, ...distractors]).slice(0, optionsLimit);
        const topic = formatTopicLabel(item.topic);

        return {
          type: 'mcq',
          passage: `Exam-style multiple-choice cloze (${item.cefr || 'B2'}).\n\nPart 1 task. Context: ${topic}. Intended meaning: "${item.translation}". Gap: The candidate must ________ this idea in accurate formal English.`,
          question,
          options,
          correct: options.indexOf(correctAnswer),
        };
      });
  }

  function buildCustomWritingItems(items, difficultyLevel = 'B1') {
    const prompt = ['A1', 'A2'].includes(difficultyLevel)
      ? 'Translate into clear everyday English.'
      : ['B1', 'B2'].includes(difficultyLevel)
        ? 'Cambridge-style sentence transformation. Keep meaning and register.'
        : 'Cambridge-style transformation. Preserve meaning, formal register, and lexical precision.';

    return items
      .map(item => ({
        type: 'translate',
        prompt,
        sentence: `Rewrite in English (${formatTopicLabel(item.topic)} context): ${item.translation || item.meaning || ''}`,
        answer: item.text || item.word || '',
      }))
      .filter(item => item.sentence && item.answer)
      .slice(0, 4);
  }

  function buildCustomListeningItems(items, difficultyLevel = 'B1') {
    const question = ['A1', 'A2'].includes(difficultyLevel)
      ? 'Write the missing word from the audio.'
      : 'Complete the sentence with the exact word from the recording.';

    return items
      .filter(item => item.text && item.translation)
      .slice(0, 3)
      .map(item => ({
        type: 'fillin',
        transcript: ['C1', 'C2'].includes(difficultyLevel)
          ? `You are listening to a high-level exam briefing about ${String(formatTopicLabel(item.topic)).toLowerCase()}. The speaker states: "Candidates are expected to ${item.text} before the final task so that register, collocation and precision remain consistent throughout the response."`
          : ['A1', 'A2'].includes(difficultyLevel)
            ? `You hear a short classroom instruction about ${String(formatTopicLabel(item.topic)).toLowerCase()}. The speaker says: "Please ${item.text} before the final task."`
            : `You are listening to a short exam briefing about ${String(formatTopicLabel(item.topic)).toLowerCase()}. The speaker says: "Candidates should ${item.text} before the final task because this reflects professional register and lexical precision."`,
        question,
        sentence: `In the ${String(formatTopicLabel(item.topic)).toLowerCase()} briefing, candidates should ________ before the final task (${item.translation}).`,
        answer: item.text,
      }));
  }

  function buildCustomMixItems(items, difficultyLevel = 'B1') {
    const matchingItems = buildCustomMatchingItems(items, difficultyLevel).slice(0, 1);
    const memoryItems = buildCustomMemoryItems(items, difficultyLevel).slice(0, 1);

    return [...matchingItems, ...memoryItems].filter(item => item && item.type);
  }

  function buildCustomFlashcardItems(items) {
    return items
      .filter(item => item.text && item.translation)
      .slice(0, 12)
      .map(item => ({
        type: 'flashcard',
        front: item.text,
        back: item.translation,
        hint: item.topic || '',
        reveal_ms: 1200,
      }));
  }

  function buildCustomMatchingItems(items, difficultyLevel = 'B1') {
    const pairs = items
      .filter(item => item.text && item.translation)
      .slice(0, 12)
      .map(item => ({ left: item.text, right: item.translation }));

    if (pairs.length < 3) return [];

    const timePerPair = ['A1', 'A2'].includes(difficultyLevel) ? 8 : difficultyLevel === 'B1' ? 7 : difficultyLevel === 'B2' ? 6 : 5;

    return [{
      type: 'match',
      question: tx('matching_question', 'Match the pairs as fast as possible. Faster time means better score.'),
      pairs,
      time_limit_seconds: Math.max(35, Math.min(95, pairs.length * timePerPair)),
    }];
  }

  function buildCustomMemoryItems(items, difficultyLevel = 'B1') {
    const pairs = items
      .filter(item => item.text && item.translation)
      .slice(0, 18)
      .map(item => ({ front: item.text, back: item.translation }));

    if (pairs.length < 4) return [];

    const previewMs = ['A1', 'A2'].includes(difficultyLevel) ? 1500 : difficultyLevel === 'B1' ? 1200 : difficultyLevel === 'B2' ? 900 : 700;

    return [{
      type: 'memory',
      question: tx('memory_question', 'Memory Matrix: find all translation pairs with the fewest moves.'),
      pairs,
      grid_columns: pairs.length >= 12 ? 6 : 4,
      preview_ms: previewMs,
    }];
  }

  function shuffleArray(items) {
    const clone = [...items];
    for (let index = clone.length - 1; index > 0; index -= 1) {
      const randomIndex = Math.floor(Math.random() * (index + 1));
      [clone[index], clone[randomIndex]] = [clone[randomIndex], clone[index]];
    }
    return clone;
  }

  function selectReadingDistractors(wordBank, correctWord, limit) {
    const correct = String(correctWord || '').trim().toLowerCase();
    if (!correct) return [];

    return Array.from(new Set(
      (wordBank || [])
        .map(word => String(word || '').trim())
        .filter(Boolean)
        .filter(word => word.toLowerCase() !== correct)
    ))
      .map(word => {
        const lower = word.toLowerCase();
        const sameInitial = lower[0] === correct[0] ? 3 : 0;
        const lengthDistance = Math.abs(lower.length - correct.length);
        const lengthScore = Math.max(0, 3 - lengthDistance);
        const editScore = Math.max(0, 8 - levenshteinDistance(correct, lower));
        return { word, score: sameInitial + lengthScore + editScore };
      })
      .sort((left, right) => right.score - left.score)
      .slice(0, limit)
      .map(item => item.word);
  }

  function levenshteinDistance(a, b) {
    const rows = a.length + 1;
    const cols = b.length + 1;
    const matrix = Array.from({ length: rows }, () => Array(cols).fill(0));

    for (let row = 0; row < rows; row += 1) matrix[row][0] = row;
    for (let col = 0; col < cols; col += 1) matrix[0][col] = col;

    for (let row = 1; row < rows; row += 1) {
      for (let col = 1; col < cols; col += 1) {
        const cost = a[row - 1] === b[col - 1] ? 0 : 1;
        matrix[row][col] = Math.min(
          matrix[row - 1][col] + 1,
          matrix[row][col - 1] + 1,
          matrix[row - 1][col - 1] + cost
        );
      }
    }

    return matrix[rows - 1][cols - 1];
  }

  function formatTopicLabel(topic) {
    const value = String(topic || '').trim();
    if (!value) return 'General English';

    return value
      .replace(/[_-]+/g, ' ')
      .replace(/\s+/g, ' ')
      .replace(/\b\w/g, char => char.toUpperCase());
  }

  function buildSpeakingHint(item, difficultyLevel = 'B1') {
    const topic = formatTopicLabel(item.topic);
    const meaning = item.translation || item.meaning || '';

    if (['A1', 'A2'].includes(difficultyLevel)) {
      return `Say the word clearly and use it in one short sentence.${meaning ? ' Meaning: ' + meaning : ''}`;
    }

    if (['C1', 'C2'].includes(difficultyLevel)) {
      return `Exam speaking (${topic}): produce clear stress and connected speech, then use the word in a precise C-level sentence.${meaning ? ' Meaning: ' + meaning : ''}`;
    }

    return `Exam speaking (${topic}): pronounce clearly, stress key syllables, then use it in one formal sentence.${meaning ? ' Meaning: ' + meaning : ''}`;
  }

  function normalizeTemplateExerciseItem(templateType, item, templateTitle) {
    const payload = item && item.payload && typeof item.payload === 'object' ? item.payload : {};
    const optionList = Array.isArray(item.options) ? [...item.options].sort((left, right) => (left.order || 0) - (right.order || 0)) : [];
    const fallbackType = templateType === 'reading' ? 'mcq'
      : templateType === 'listening' ? 'fillin'
      : templateType === 'speaking' ? 'pronounce'
      : 'translate';
    const mappedType = item.item_type === 'choice' ? 'mcq'
      : ['fillin', 'translate', 'pronounce'].includes(item.item_type) ? item.item_type
      : fallbackType;

    if (mappedType === 'mcq') {
      const correctIndex = optionList.findIndex(option => option.is_correct) >= 0
        ? optionList.findIndex(option => option.is_correct)
        : Math.max(0, optionList.findIndex(option => option.text === item.correct_answer));

      return {
        itemId: item.id,
        type: 'mcq',
        passage: payload.passage || templateTitle || t('custom_template'),
        question: item.question_text || t('select_correct_answer'),
        options: optionList.map(option => option.text),
        correct: correctIndex >= 0 ? correctIndex : 0,
      };
    }

    if (mappedType === 'fillin') {
      return {
        itemId: item.id,
        type: 'fillin',
        transcript: payload.transcript || item.question_text || '',
        question: payload.prompt || t('complete_sentence'),
        sentence: payload.sentence || item.question_text || t('complete_blank'),
        answer: item.correct_answer || '',
      };
    }

    if (mappedType === 'pronounce') {
      return {
        itemId: item.id,
        type: 'pronounce',
        word: item.question_text || item.correct_answer || '',
        hint: payload.hint || item.correct_answer || t('pronounce_hint'),
      };
    }

    return {
      itemId: item.id,
      type: 'translate',
      prompt: payload.prompt || t('translate_to_english'),
      sentence: item.question_text || '',
      answer: item.correct_answer || '',
    };
  }

  function getTemplateModeData(mode) {
    const templates = Array.isArray(TEMPLATE_CATALOG[mode]) ? TEMPLATE_CATALOG[mode] : [];
    const selectedTemplate = templates.find(template => Array.isArray(template.items) && template.items.length > 0);
    if (!selectedTemplate) return null;

    const items = selectedTemplate.items
      .map(item => normalizeTemplateExerciseItem(mode, item, selectedTemplate.title || 'Plantilla personalizada'))
      .filter(item => {
        if (mode === 'mix') return ['match', 'memory'].includes(item.type);
        if (item.type === 'mcq') return Array.isArray(item.options) && item.options.length >= 2;
        if (item.type === 'pronounce') return Boolean(item.word);
        return Boolean(item.answer || item.sentence || item.question);
      });

    if (!items.length) return null;

    return {
      title: selectedTemplate.title || EXERCISES[mode].title,
      items,
    };
  }

  function getModeData(mode) {
    const base = EXERCISES[mode];
    const source = getSelectedVocabularySource();
    const templateModeData = getTemplateModeData(mode);
    const difficultyLevel = resolveDifficultyLevel(source, source.items || []);

    if (templateModeData) {
      return templateModeData;
    }

    if (!source.items.length) {
      return { title: base.title, items: base.items };
    }

    if (mode === 'reading') {
      const customItems = buildCustomReadingItems(source.items, difficultyLevel);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'speaking') {
      const customItems = buildCustomSpeakingItems(source.items, difficultyLevel);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'listening') {
      const customItems = buildCustomListeningItems(source.items, difficultyLevel);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'writing') {
      const customItems = buildCustomWritingItems(source.items, difficultyLevel);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'mix') {
      const customItems = buildCustomMixItems(source.items, difficultyLevel);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'flashcards') {
      const customItems = buildCustomFlashcardItems(source.items);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'matching') {
      const customItems = buildCustomMatchingItems(source.items, difficultyLevel);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    return { title: base.title, items: base.items };
  }

  async function registerExerciseAttempt() {
    try {
      const source = getSelectedVocabularySource();
      const items = (currentModeData && currentModeData.items) ? currentModeData.items : (getModeData(currentMode).items || []);
      const itemCount = items.length;
      const timeSpentSeconds = sessionStartedAt ? Math.max(0, Math.round((Date.now() - sessionStartedAt) / 1000)) : null;
      const score = itemCount > 0 ? Math.round((sessionCorrectItems / itemCount) * 100) : null;

      await exerciseApiFetch('/api/exercise-attempts', {
        method: 'POST',
        body: JSON.stringify({
          mode: currentMode,
          source_type: getSelectedSourceType(),
          source_name: source?.name || null,
          result_status: score !== null && score >= 60 ? 'passed' : 'completed',
          score,
          time_spent_seconds: timeSpentSeconds,
          item_count: itemCount,
          correct_count: sessionCorrectItems,
          answers: sessionAnswerRecords,
        }),
      });
    } catch {
    }
  }

  (function updateBadges() {
    const lang = getActiveLang();
    const labels = SKILL_LABELS[lang] || SKILL_LABELS['en'];
    document.querySelectorAll('.exercise-card[data-mode]').forEach(card => {
      const idx = BADGE_MODES.indexOf(card.dataset.mode);
      if (idx === -1) return;
      const badge = card.querySelector('.exercise-card-badge');
      if (badge) badge.textContent = labels[idx];
    });
  })();

  async function bootstrapExercises() {
    if (window.lexiSessionReady) {
      try {
        await window.lexiSessionReady;
      } catch {}
    }

    resetExerciseSelectionState();
    await loadVocabularySources();
    setupExerciseSourceTabs();
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
  }

  bootstrapExercises();

  window.addEventListener('lexi-lang-changed', async () => {
    await loadVocabularySources();
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
    syncSourcePanels();
  });

  const EXERCISES = {
    reading: {
      title: "Reading",
      items: [
        {
          type: "mcq",
          passage: "Every morning, Sarah wakes up at 6am and goes for a run before work. She believes that starting the day with exercise helps her focus and feel more energetic throughout the day. Her colleagues often ask her secret, and she always says the same thing: consistency.",
          question: "What does Sarah believe helps her focus during the day?",
          options: ["Drinking coffee", "Morning exercise", "Reading the news", "A cold shower"],
          correct: 1
        },
        {
          type: "mcq",
          passage: "The company announced that it would be expanding to three new countries next year, creating over 500 new jobs in the process. The CEO stated that this move was part of a long-term strategy to become a global leader in the industry.",
          question: "According to the announcement, how many new jobs will be created?",
          options: ["Over 300", "Over 400", "Over 500", "Over 600"],
          correct: 2
        },
        {
          type: "mcq",
          passage: "Deadlines can be stressful, but they also push us to manage our time more effectively. Many professionals say that working under pressure actually helps them produce better results, as long as the deadlines are realistic.",
          question: "What do many professionals say about working under pressure?",
          options: ["It always lowers quality", "It helps them produce better results", "It causes burnout", "It is never productive"],
          correct: 1
        }
      ]
    },
    listening: {
      title: "Listening",
      items: [
        {
          type: "fillin",
          transcript: "Good morning everyone. Today's meeting has been moved from the main conference room to room 204 on the second floor. We will start at half past nine instead of nine o'clock. Please bring your project updates.",
          question: t('complete_sentence'),
          sentence: "Today's meeting has been moved to room 204 on the ________ floor.",
          answer: "second"
        },
        {
          type: "fillin",
          transcript: "Welcome to the Lexi podcast. Today we're going to talk about the importance of vocabulary in language learning. Studies show that knowing the most common two thousand words in a language allows you to understand about eighty percent of everyday conversations.",
          question: t('complete_sentence'),
          sentence: "Knowing the most common two thousand words allows you to understand about ________ percent of everyday conversations.",
          answer: "eighty"
        },
        {
          type: "fillin",
          transcript: "The flight to London has been delayed by approximately forty minutes due to heavy fog at the destination airport. Passengers are advised to remain in the departure lounge and listen for further announcements.",
          question: t('complete_sentence'),
          sentence: "The flight has been delayed due to heavy ________ at the destination airport.",
          answer: "fog"
        }
      ]
    },
    speaking: {
      title: "Speaking",
      items: [
        { type: "pronounce", word: "meet a deadline", hint: "cumplir un plazo de entrega" },
        { type: "pronounce", word: "pull someone's leg", hint: "tomar el pelo a alguien" },
        { type: "pronounce", word: "break the ice", hint: "romper el hielo" },
        { type: "pronounce", word: "under the weather", hint: "encontrarse mal / pachuco" },
        { type: "pronounce", word: "once in a blue moon", hint: "de vez en cuando / muy raramente" }
      ]
    },
    writing: {
      title: "Writing",
      items: [
        {
          type: "translate",
          prompt: t('translate_to_english'),
          sentence: "No pude cumplir el plazo de entrega.",
          answer: "I couldn't meet the deadline."
        },
        {
          type: "translate",
          prompt: t('translate_to_english'),
          sentence: "Estamos bajo mucha presión últimamente.",
          answer: "We've been under a lot of pressure lately."
        },
        {
          type: "translate",
          prompt: t('translate_to_english'),
          sentence: "La reunión ha sido cancelada debido al mal tiempo.",
          answer: "The meeting has been cancelled due to bad weather."
        }
      ]
    },
    mix: {
      title: tx('challenge_label', 'Desafio'),
      items: [
        {
          type: 'match',
          question: 'Match the pairs as fast as possible. Faster time means better score.',
          time_limit_seconds: 60,
          pairs: [
            { left: 'deadline', right: 'plazo de entrega' },
            { left: 'meeting', right: 'reunion' },
            { left: 'feedback', right: 'retroalimentacion' },
            { left: 'evidence', right: 'evidencia' },
            { left: 'improve', right: 'mejorar' },
            { left: 'schedule', right: 'programar' },
          ]
        },
        {
          type: 'memory',
          question: 'Memory Matrix: find all translation pairs with the fewest moves.',
          grid_columns: 4,
          preview_ms: 900,
          pairs: [
            { front: 'deadline', back: 'plazo de entrega' },
            { front: 'meeting', back: 'reunion' },
            { front: 'feedback', back: 'retroalimentacion' },
            { front: 'evidence', back: 'evidencia' },
            { front: 'improve', back: 'mejorar' },
            { front: 'schedule', back: 'programar' },
            { front: 'target', back: 'objetivo' },
            { front: 'review', back: 'repasar' },
          ]
        }
      ]
    }
  };

  let currentMode = null;
  let currentIndex = 0;
  let sessionStartedAt = null;
  let sessionAnsweredItems = 0;
  let sessionCorrectItems = 0;
  let sessionAnswerRecords = [];
  let currentModeData = null;
  let runtimeRequestToken = 0;

  function renderExerciseLoading(title) {
    const content = document.getElementById('exerciseContent');
    document.getElementById('exPanelTitle').textContent = title;
    document.getElementById('exProgress').textContent = tx('loading_short', 'Cargando...');
    document.getElementById('exProgressBar').style.width = '12%';
    document.getElementById('btnPrevEx').disabled = true;
    document.getElementById('btnNextEx').disabled = true;
    document.getElementById('btnNextEx').innerHTML = tx('loading_short', 'Cargando...');
    content.innerHTML =
      '<div class="ex-loading-state" role="status" aria-live="polite">' +
      '<span class="ex-loading-spinner" aria-hidden="true"></span>' +
      '<span class="ex-loading-text">' + tx('loading_exercise', 'Preparando ejercicios...') + '</span>' +
      '</div>';
  }

  function resetExerciseSessionMetrics() {
    sessionStartedAt = Date.now();
    sessionAnsweredItems = 0;
    sessionCorrectItems = 0;
    sessionAnswerRecords = [];
  }

  function getRuntimeSelectionContext() {
    const sourceType = getSelectedSourceType();
    const context = {
      source_type: sourceType,
      language: getActiveLang(),
    };

    if (sourceType === 'saved') {
      context.source_id = localStorage.getItem(EXERCISE_COLLECTION_KEY) || 'library';
      return context;
    }

    context.source_id = 'catalog';
    context.level = localStorage.getItem(EXERCISE_CATALOG_LEVEL_KEY) || null;
    context.topic = localStorage.getItem(EXERCISE_CATALOG_TOPIC_KEY) || null;

    return context;
  }

  async function fetchRuntimeModeData(mode) {
    const context = getRuntimeSelectionContext();

    const response = await exerciseApiFetch('/api/exercise-runtime/start', {
      method: 'POST',
      body: JSON.stringify({
        mode,
        ...context,
      }),
    });

    if (!response.ok) {
      throw new Error('runtime-start-failed');
    }

    const payload = await response.json();

    if (!payload || !payload.ok || !Array.isArray(payload.items) || !payload.items.length) {
      return null;
    }

    return {
      title: payload.title || EXERCISES[mode].title,
      items: payload.items,
    };
  }

  function markExerciseItemResult(container, isCorrect, details = {}) {
    if (!container || container.dataset.evaluated === '1') return;

    container.dataset.evaluated = '1';
    sessionAnsweredItems += 1;
    if (isCorrect) {
      sessionCorrectItems += 1;
    }

    sessionAnswerRecords.push({
      item_id: details.itemId || null,
      item_type: details.itemType || null,
      prompt: details.prompt || null,
      expected_answer: details.expectedAnswer || null,
      answer_text: details.answerText || null,
      answer_payload: details.answerPayload || null,
      is_correct: isCorrect,
      points_obtained: typeof details.pointsObtained === 'number' ? details.pointsObtained : (isCorrect ? 1 : 0),
      feedback: details.feedback || null,
    });
  }

  document.querySelectorAll('.exercise-card[data-mode]').forEach(card => {
    card.addEventListener('click', () => openMode(card.dataset.mode));
    card.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openMode(card.dataset.mode); }
    });
    card.style.cursor = 'pointer';
  });

  document.getElementById('btnBack').addEventListener('click', closePanel);
  document.getElementById('btnNextEx').addEventListener('click', nextExercise);
  document.getElementById('btnPrevEx').addEventListener('click', prevExercise);
  document.getElementById('btnRepeat').addEventListener('click', () => {
    document.getElementById('exCompleteModal').hidden = true;
    currentIndex = 0;
    document.getElementById('exercisePanel').hidden = false;
    renderExercise();
  });
  document.getElementById('btnBackFromComplete').addEventListener('click', () => {
    document.getElementById('exCompleteModal').hidden = true;
    document.getElementById('exerciseMenu').hidden = false;
  });

  async function openMode(mode) {
    currentMode = mode;
    currentIndex = 0;
    resetExerciseSessionMetrics();
    currentModeData = null;
    const fallbackModeData = getModeData(mode);

    document.getElementById('exerciseMenu').hidden = true;
    document.getElementById('exercisePanel').hidden = false;
    document.getElementById('exCompleteModal').hidden = true;
    renderExerciseLoading(fallbackModeData.title);
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const token = ++runtimeRequestToken;

    try {
      const runtimeData = await fetchRuntimeModeData(mode);

      if (token !== runtimeRequestToken || currentMode !== mode) {
        return;
      }

      if (runtimeData && Array.isArray(runtimeData.items) && runtimeData.items.length) {
        currentModeData = runtimeData;
      } else {
        currentModeData = fallbackModeData;
      }
    } catch {
      if (token !== runtimeRequestToken || currentMode !== mode) {
        return;
      }
      currentModeData = fallbackModeData;
    }

    if (token === runtimeRequestToken && currentMode === mode) {
      currentIndex = 0;
      document.getElementById('btnNextEx').disabled = false;
      renderExercise();
    }
  }

  function closePanel() {
    document.getElementById('exercisePanel').hidden = true;
    document.getElementById('exerciseMenu').hidden = false;
  }

  function renderExercise() {
    const modeData = currentModeData || getModeData(currentMode);
    const items = modeData.items;
    const total = items.length;
    const item = items[currentIndex];

    document.getElementById('exPanelTitle').textContent = modeData.title;

    document.getElementById('exProgress').textContent = currentIndex + 1 + ' / ' + total;
    document.getElementById('exProgressBar').style.width = ((currentIndex + 1) / total * 100) + '%';
    document.getElementById('btnPrevEx').disabled = currentIndex === 0;
    document.getElementById('btnNextEx').innerHTML = currentIndex === total - 1
      ? t('finish') + ' <i class="bi bi-check-lg"></i>'
      : t('next') + ' <i class="bi bi-arrow-right"></i>';

    const content = document.getElementById('exerciseContent');
    content.innerHTML = '';

    if (item.type === 'mcq') renderMCQ(content, item);
    else if (item.type === 'fillin') renderFillin(content, item);
    else if (item.type === 'pronounce') renderPronounce(content, item);
    else if (item.type === 'translate') renderTranslate(content, item);
    else if (item.type === 'flashcard') renderFlashcard(content, item);
    else if (item.type === 'match') renderMatching(content, item);
    else if (item.type === 'memory') renderMemory(content, item);
  }

  function nextExercise() {
    const modeData = currentModeData || getModeData(currentMode);
    const items = modeData.items;
    if (currentIndex < items.length - 1) {
      currentIndex++;
      renderExercise();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      showCompletion();
    }
  }

  function prevExercise() {
    if (currentIndex > 0) {
      currentIndex--;
      renderExercise();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  function renderMCQ(container, item) {
    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-book"></i> ' + t('reading_label') + '</p>' +
      '<div class="ex-passage">' + item.passage + '</div>' +
      '<p class="ex-question">' + item.question + '</p>' +
      '<div class="ex-options">' +
      item.options.map((opt, i) =>
        '<button class="ex-option" data-idx="' + i + '">' +
        '<span class="ex-option-letter">' + String.fromCharCode(65 + i) + '</span>' + opt +
        '</button>'
      ).join('') +
      '</div>' +
      '<div class="ex-feedback" hidden></div>';

    container.querySelectorAll('.ex-option').forEach(btn => {
      btn.addEventListener('click', function () {
        const idx = parseInt(this.dataset.idx);
        const feedback = container.querySelector('.ex-feedback');
        container.querySelectorAll('.ex-option').forEach(b => b.disabled = true);
        if (idx === item.correct) {
          this.classList.add('ex-option--correct');
          feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + t('correct');
          feedback.className = 'ex-feedback ex-feedback--ok';
          markExerciseItemResult(container, true, {
            itemId: item.itemId,
            itemType: item.type,
            prompt: item.question,
            expectedAnswer: item.options[item.correct],
            answerText: item.options[idx],
            answerPayload: { selected_index: idx, selected_option: item.options[idx] },
            feedback: t('correct'),
          });
        } else {
          this.classList.add('ex-option--wrong');
          container.querySelectorAll('.ex-option')[item.correct].classList.add('ex-option--correct');
          feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + t('incorrect_reference', { answer: '<strong>' + item.options[item.correct] + '</strong>' });
          feedback.className = 'ex-feedback ex-feedback--err';
          markExerciseItemResult(container, false, {
            itemId: item.itemId,
            itemType: item.type,
            prompt: item.question,
            expectedAnswer: item.options[item.correct],
            answerText: item.options[idx],
            answerPayload: { selected_index: idx, selected_option: item.options[idx] },
            feedback: t('incorrect'),
          });
        }
        feedback.hidden = false;
      });
    });
  }

  function renderFillin(container, item) {
    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-headphones"></i> ' + t('listening_label') + '</p>' +
      '<div class="ex-audio-mock">' +
      '<div class="ex-audio-wave"><span></span><span></span><span></span><span></span><span></span></div>' +
      '<span class="ex-audio-label">' + t('simulated_audio') + '</span>' +
      '</div>' +
      '<button class="ex-transcript-toggle">' + t('show_transcript') + ' <i class="bi ' + t('close_transcript_icon_down') + '"></i></button>' +
      '<div class="ex-transcript" hidden>' + item.transcript + '</div>' +
      '<p class="ex-question">' + item.question + '</p>' +
      '<div class="ex-fillin-wrap">' + item.sentence.replace('________', '<input class="ex-input" type="text" autocomplete="off" spellcheck="false" placeholder="...">') + '</div>' +
      '<button class="btn btn-primary ex-check-btn">' + t('check') + '</button>' +
      '<div class="ex-feedback" hidden></div>';

    container.querySelector('.ex-transcript-toggle').addEventListener('click', function () {
      const transcriptEl = container.querySelector('.ex-transcript');
      transcriptEl.hidden = !transcriptEl.hidden;
      this.innerHTML = transcriptEl.hidden
        ? t('show_transcript') + ' <i class="bi ' + t('close_transcript_icon_down') + '"></i>'
        : t('hide_transcript') + ' <i class="bi ' + t('close_transcript_icon_up') + '"></i>';
    });

    container.querySelector('.ex-check-btn').addEventListener('click', () => {
      const val = container.querySelector('.ex-input').value.trim().toLowerCase();
      const feedback = container.querySelector('.ex-feedback');
      if (val === item.answer.toLowerCase()) {
        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + t('correct');
        feedback.className = 'ex-feedback ex-feedback--ok';
        markExerciseItemResult(container, true, {
          itemId: item.itemId,
          itemType: item.type,
          prompt: item.question,
          expectedAnswer: item.answer,
          answerText: val,
          feedback: t('correct'),
        });
      } else {
        feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + t('answer_is', { answer: '<strong>"' + item.answer + '"</strong>' });
        feedback.className = 'ex-feedback ex-feedback--err';
        markExerciseItemResult(container, false, {
          itemId: item.itemId,
          itemType: item.type,
          prompt: item.question,
          expectedAnswer: item.answer,
          answerText: val,
          feedback: t('incorrect'),
        });
      }
      feedback.hidden = false;
    });

    container.querySelector('.ex-input').addEventListener('keydown', e => {
      if (e.key === 'Enter') container.querySelector('.ex-check-btn').click();
    });
  }

  function renderPronounce(container, item) {
    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-mic"></i> ' + t('speaking_label') + '</p>' +
      '<p class="ex-speaking-prompt">' + t('pronounce_out_loud') + '</p>' +
      '<div class="ex-word-big">' + item.word + '</div>' +
      '<p class="ex-hint-text">' + item.hint + '</p>' +
      '<div class="ex-mic-area">' +
      '<button class="ex-mic-btn" id="micBtn"><i class="bi bi-mic"></i><span>' + t('press_to_speak') + '</span></button>' +
      '</div>' +
      '<div class="ex-self-check" hidden>' +
      '<p>' + t('self_check_question') + '</p>' +
      '<div class="ex-self-check-btns">' +
      '<button class="btn btn-success ex-self-yes"><i class="bi bi-check-lg"></i> ' + t('self_check_yes') + '</button>' +
      '<button class="btn btn-outline-danger ex-self-no">' + t('self_check_retry') + '</button>' +
      '</div></div>';

    const micBtn = container.querySelector('#micBtn');
    const selfCheck = container.querySelector('.ex-self-check');

    micBtn.addEventListener('click', function () {
      this.classList.toggle('recording');
      const icon = this.querySelector('i');
      const label = this.querySelector('span');
      if (this.classList.contains('recording')) {
        icon.className = 'bi bi-stop-fill';
        label.textContent = t('recording');
      } else {
        icon.className = 'bi bi-mic';
        label.textContent = t('press_to_speak');
        selfCheck.hidden = false;
      }
    });

    container.querySelector('.ex-self-yes').addEventListener('click', () => {
      markExerciseItemResult(container, true, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.word,
        expectedAnswer: item.word,
        answerText: item.word,
        feedback: t('autovalidated_correct'),
      });
      selfCheck.innerHTML = '<p class="ex-feedback ex-feedback--ok" style="display:block"><i class="bi bi-check-circle-fill"></i> ' + t('self_check_success') + '</p>';
    });
    container.querySelector('.ex-self-no').addEventListener('click', () => {
      selfCheck.hidden = true;
      micBtn.classList.remove('recording');
      micBtn.querySelector('i').className = 'bi bi-mic';
      micBtn.querySelector('span').textContent = t('press_to_speak');
    });
  }

  function renderTranslate(container, item) {
    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-pencil"></i> ' + t('writing_label') + '</p>' +
      '<p class="ex-prompt">' + item.prompt + '</p>' +
      '<div class="ex-sentence-box">' + item.sentence + '</div>' +
      '<textarea class="ex-textarea" placeholder="Escribe tu traducción aquí..."></textarea>' +
      '<button class="btn btn-primary ex-check-btn">' + t('check') + '</button>' +
      '<div class="ex-feedback" hidden></div>';

    container.querySelector('.ex-check-btn').addEventListener('click', () => {
      const val = container.querySelector('.ex-textarea').value.trim().toLowerCase();
      const feedback = container.querySelector('.ex-feedback');
      const keywords = item.answer.toLowerCase().split(' ').filter(w => w.length > 3);
      const matches = keywords.filter(k => val.includes(k)).length;
      if (val.length > 0 && matches >= Math.ceil(keywords.length * 0.65)) {
        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + t('great_reference', { answer: '<em>"' + item.answer + '"</em>' });
        feedback.className = 'ex-feedback ex-feedback--ok';
        markExerciseItemResult(container, true, {
          itemId: item.itemId,
          itemType: item.type,
          prompt: item.sentence,
          expectedAnswer: item.answer,
          answerText: val,
          feedback: t('correct'),
        });
      } else if (val.length === 0) {
        feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + t('write_answer_first');
        feedback.className = 'ex-feedback ex-feedback--warn';
      } else {
        feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + t('reference_answer', { answer: '<em>"' + item.answer + '"</em>' });
        feedback.className = 'ex-feedback ex-feedback--err';
        markExerciseItemResult(container, false, {
          itemId: item.itemId,
          itemType: item.type,
          prompt: item.sentence,
          expectedAnswer: item.answer,
          answerText: val,
          feedback: t('incorrect'),
        });
      }
      feedback.hidden = false;
    });
  }

  function renderFlashcard(container, item) {
    const revealMs = Number(item.reveal_ms) > 0 ? Number(item.reveal_ms) : 1200;
    const hintText = item.hint ? '<span class="ex-flashcard-hint">' + item.hint + '</span>' : '';

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-layers"></i> ' + tx('flashcards_label', 'Tarjetas rapidas') + '</p>' +
      '<p class="ex-question">' + tx('flashcards_instruction', 'Mira la traduccion un instante, se tapa y marca si la recordaste.') + '</p>' +
      '<div class="ex-flashcard" data-visible="0">' +
        '<div class="ex-flashcard-face ex-flashcard-face--front">' + item.front + '</div>' +
        '<div class="ex-flashcard-face ex-flashcard-face--back" hidden>' + item.back + '</div>' +
      '</div>' +
      '<div class="ex-flashcard-meta">' +
        '<span class="ex-flashcard-timer">' + tx('flashcards_reveal_label', 'Vista') + ': ' + Math.round(revealMs / 1000) + 's</span>' +
        hintText +
      '</div>' +
      '<div class="ex-flashcard-actions">' +
        '<button class="btn btn-primary ex-flashcard-show">' + tx('flashcards_show', 'Mostrar 1 segundo') + '</button>' +
      '</div>' +
      '<div class="ex-self-check" hidden>' +
        '<p>' + tx('flashcards_remembered_question', '¿La recordaste sin mirar otra vez?') + '</p>' +
        '<div class="ex-self-check-btns">' +
          '<button class="btn btn-success ex-flashcard-yes"><i class="bi bi-check-lg"></i> ' + tx('flashcards_yes', 'Si, la sabia') + '</button>' +
          '<button class="btn btn-outline-danger ex-flashcard-no">' + tx('flashcards_no', 'No, me costo') + '</button>' +
        '</div>' +
      '</div>';

    const card = container.querySelector('.ex-flashcard');
    const front = container.querySelector('.ex-flashcard-face--front');
    const back = container.querySelector('.ex-flashcard-face--back');
    const showBtn = container.querySelector('.ex-flashcard-show');
    const selfCheck = container.querySelector('.ex-self-check');

    showBtn.addEventListener('click', () => {
      showBtn.disabled = true;
      card.dataset.visible = '1';
      back.hidden = false;
      front.hidden = true;

      window.setTimeout(() => {
        card.dataset.visible = '0';
        back.hidden = true;
        front.hidden = false;
        selfCheck.hidden = false;
      }, revealMs);
    });

    container.querySelector('.ex-flashcard-yes').addEventListener('click', () => {
      markExerciseItemResult(container, true, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.front,
        expectedAnswer: item.back,
        answerText: item.front,
        answerPayload: { remembered: true, reveal_ms: revealMs },
        feedback: tx('correct', 'Correcto!'),
      });
      selfCheck.innerHTML = '<p class="ex-feedback ex-feedback--ok" style="display:block"><i class="bi bi-check-circle-fill"></i> ' + tx('flashcards_success', 'Perfecto, seguimos.') + '</p>';
    });

    container.querySelector('.ex-flashcard-no').addEventListener('click', () => {
      markExerciseItemResult(container, false, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.front,
        expectedAnswer: item.back,
        answerText: null,
        answerPayload: { remembered: false, reveal_ms: revealMs },
        feedback: tx('incorrect', 'Incorrecto'),
      });
      selfCheck.innerHTML = '<p class="ex-feedback ex-feedback--warn" style="display:block"><i class="bi bi-arrow-repeat"></i> ' + tx('flashcards_retry_hint', 'Repite esta tarjeta al final para fijarla mejor.') + '</p>';
    });
  }

  function renderMatching(container, item) {
    const pairs = Array.isArray(item.pairs) ? item.pairs : [];
    const leftItems = shuffleArray(pairs.map(pair => pair.left));
    const rightItems = shuffleArray(pairs.map(pair => pair.right));
    const expected = new Map(pairs.map(pair => [pair.left, pair.right]));
    const totalPairs = pairs.length;
    const timeLimitSeconds = Math.max(25, Math.min(120, Number(item.time_limit_seconds) || 60));
    const startedAt = Date.now();
    const deadlineAt = startedAt + (timeLimitSeconds * 1000);
    const matchedLeft = new Set();
    const matchedRight = new Set();
    const connections = [];
    let selectedLeft = null;
    let selectedRight = null;
    let mistakes = 0;
    let gameFinished = false;
    let timerId = null;

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-bezier2"></i> ' + tx('matching_label', 'Conectar columnas') + '</p>' +
      '<p class="ex-question">' + (item.question || tx('matching_question', 'Conecta cada palabra con su traduccion correcta')) + '</p>' +
      '<div class="ex-game-meta">' +
        '<span class="ex-game-chip"><i class="bi bi-stopwatch"></i> <strong class="ex-match-timer">' + timeLimitSeconds + 's</strong></span>' +
        '<span class="ex-game-chip"><i class="bi bi-lightning-charge"></i> ' + tx('matching_pairs', 'Pairs') + ': <strong class="ex-match-count">0/' + totalPairs + '</strong></span>' +
      '</div>' +
      '<div class="ex-matching-board">' +
        '<div class="ex-matching-column" data-column="left"></div>' +
        '<div class="ex-matching-column" data-column="right"></div>' +
      '</div>' +
      '<div class="ex-matching-connections" aria-live="polite"></div>' +
      '<div class="ex-feedback" hidden></div>';

    const leftColumn = container.querySelector('[data-column="left"]');
    const rightColumn = container.querySelector('[data-column="right"]');
    const connectionList = container.querySelector('.ex-matching-connections');
    const feedback = container.querySelector('.ex-feedback');
    const timerEl = container.querySelector('.ex-match-timer');
    const countEl = container.querySelector('.ex-match-count');

    const stopTimer = () => {
      if (timerId !== null) {
        clearInterval(timerId);
        timerId = null;
      }
    };

    const disableBoard = () => {
      leftColumn.querySelectorAll('.ex-match-btn').forEach(button => {
        button.disabled = true;
      });
      rightColumn.querySelectorAll('.ex-match-btn').forEach(button => {
        button.disabled = true;
      });
    };

    const finishGame = (success, timedOut = false) => {
      if (gameFinished) return;
      gameFinished = true;
      stopTimer();

      const elapsedSeconds = Math.max(0, Math.round((Date.now() - startedAt) / 1000));
      const scoreBase = Math.max(0, totalPairs - mistakes);
      const score = totalPairs > 0 ? Math.round((scoreBase / totalPairs) * 100) : 0;

      disableBoard();

      if (timedOut) {
        feedback.innerHTML = '<i class="bi bi-hourglass-split"></i> ' + tx('matching_timeout', 'Time is over. Try again to beat the clock.');
        feedback.className = 'ex-feedback ex-feedback--warn';
      } else if (success) {
        feedback.innerHTML = '<i class="bi bi-trophy-fill"></i> ' + tx('matching_success', 'Great speed. Challenge completed.');
        feedback.className = 'ex-feedback ex-feedback--ok';
      }

      feedback.hidden = false;
      markExerciseItemResult(container, success, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.question,
        expectedAnswer: JSON.stringify(pairs),
        answerText: JSON.stringify(connections),
        answerPayload: { mistakes, score, elapsed_seconds: elapsedSeconds, timed_out: timedOut },
        pointsObtained: score / 100,
        feedback: success ? tx('correct', 'Correcto!') : tx('incorrect', 'Incorrecto'),
      });
    };

    const updateTimer = () => {
      const remainingMs = deadlineAt - Date.now();
      const remainingSeconds = Math.max(0, Math.ceil(remainingMs / 1000));
      timerEl.textContent = remainingSeconds + 's';

      if (remainingSeconds <= 8) {
        timerEl.classList.add('is-warning');
      } else {
        timerEl.classList.remove('is-warning');
      }

      if (remainingMs <= 0) {
        finishGame(false, true);
      }
    };

    const refreshConnectionList = () => {
      if (countEl) {
        countEl.textContent = connections.length + '/' + totalPairs;
      }

      if (!connections.length) {
        connectionList.innerHTML = '<span class="ex-matching-placeholder">' + tx('matching_pending', 'Conexiones pendientes...') + '</span>';
        return;
      }

      connectionList.innerHTML = connections
        .map(connection => '<span class="ex-match-pill"><strong>' + connection.left + '</strong> <i class="bi bi-arrow-right"></i> ' + connection.right + '</span>')
        .join('');
    };

    const selectLeft = (button, value) => {
      if (matchedLeft.has(value)) return;
      selectedLeft = value;
      leftColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      button.classList.add('is-selected');
      evaluateSelection();
    };

    const selectRight = (button, value) => {
      if (matchedRight.has(value)) return;
      selectedRight = value;
      rightColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      button.classList.add('is-selected');
      evaluateSelection();
    };

    const evaluateSelection = () => {
      if (gameFinished) return;
      if (!selectedLeft || !selectedRight) return;

      const isCorrectMatch = expected.get(selectedLeft) === selectedRight;
      if (isCorrectMatch) {
        matchedLeft.add(selectedLeft);
        matchedRight.add(selectedRight);
        connections.push({ left: selectedLeft, right: selectedRight });
        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + tx('correct', 'Correcto!');
        feedback.className = 'ex-feedback ex-feedback--ok';
      } else {
        mistakes += 1;
        feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + tx('matching_wrong_pair', 'Esa pareja no coincide. Intenta otra.');
        feedback.className = 'ex-feedback ex-feedback--err';
      }

      feedback.hidden = false;
      selectedLeft = null;
      selectedRight = null;
      leftColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      rightColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      paintMatchedButtons();
      refreshConnectionList();

      if (matchedLeft.size === pairs.length) {
        finishGame(true, false);
      }
    };

    const paintMatchedButtons = () => {
      leftColumn.querySelectorAll('.ex-match-btn').forEach(button => {
        const value = button.dataset.value;
        button.disabled = matchedLeft.has(value);
        button.classList.toggle('is-matched', matchedLeft.has(value));
      });

      rightColumn.querySelectorAll('.ex-match-btn').forEach(button => {
        const value = button.dataset.value;
        button.disabled = matchedRight.has(value);
        button.classList.toggle('is-matched', matchedRight.has(value));
      });
    };

    leftItems.forEach(value => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'ex-match-btn';
      button.dataset.value = value;
      button.textContent = value;
      button.addEventListener('click', () => selectLeft(button, value));
      leftColumn.appendChild(button);
    });

    rightItems.forEach(value => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'ex-match-btn';
      button.dataset.value = value;
      button.textContent = value;
      button.addEventListener('click', () => selectRight(button, value));
      rightColumn.appendChild(button);
    });

    refreshConnectionList();
    updateTimer();
    timerId = window.setInterval(updateTimer, 250);
  }

  function renderMemory(container, item) {
    const sourcePairs = Array.isArray(item.pairs) ? item.pairs : [];
    const previewMs = Math.max(500, Math.min(2200, Number(item.preview_ms) || 900));
    const columns = [4, 6].includes(Number(item.grid_columns)) ? Number(item.grid_columns) : (sourcePairs.length >= 12 ? 6 : 4);

    const pairs = sourcePairs
      .filter(pair => pair && pair.front && pair.back)
      .slice(0, columns === 6 ? 18 : 8);

    const cards = shuffleArray(pairs.flatMap((pair, index) => ([
      { id: 'f-' + index, pairId: index, text: pair.front },
      { id: 'b-' + index, pairId: index, text: pair.back },
    ])));

    let selectedCardId = null;
    let lockBoard = false;
    let moves = 0;
    const matchedPairs = new Set();
    const startedAt = Date.now();

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-grid-3x3-gap"></i> ' + tx('memory_label', 'Memory Matrix') + '</p>' +
      '<p class="ex-question">' + (item.question || tx('memory_question', 'Find all translation pairs with the fewest moves.')) + '</p>' +
      '<div class="ex-game-meta">' +
        '<span class="ex-game-chip"><i class="bi bi-arrows-move"></i> ' + tx('moves_label', 'Moves') + ': <strong class="ex-memory-moves">0</strong></span>' +
        '<span class="ex-game-chip"><i class="bi bi-patch-check"></i> ' + tx('pairs_label', 'Pairs') + ': <strong class="ex-memory-count">0/' + pairs.length + '</strong></span>' +
      '</div>' +
      '<div class="ex-memory-board" style="--memory-cols:' + columns + '"></div>' +
      '<div class="ex-feedback" hidden></div>';

    const board = container.querySelector('.ex-memory-board');
    const feedback = container.querySelector('.ex-feedback');
    const movesEl = container.querySelector('.ex-memory-moves');
    const countEl = container.querySelector('.ex-memory-count');

    const cardState = new Map(cards.map(card => [card.id, { ...card, revealed: false, matched: false }]));

    const renderCards = () => {
      board.innerHTML = cards.map(card => {
        const state = cardState.get(card.id);
        const classes = ['ex-memory-card'];
        if (state.matched) classes.push('is-matched');
        if (state.revealed) classes.push('is-revealed');
        return '<button type="button" class="' + classes.join(' ') + '" data-card-id="' + card.id + '"><span class="ex-memory-face ex-memory-face--front">?</span><span class="ex-memory-face ex-memory-face--back">' + state.text + '</span></button>';
      }).join('');

      board.querySelectorAll('[data-card-id]').forEach(button => {
        button.addEventListener('click', () => handleCardClick(button.dataset.cardId));
      });
    };

    const syncMeta = () => {
      movesEl.textContent = String(moves);
      countEl.textContent = matchedPairs.size + '/' + pairs.length;
    };

    const setCardRevealed = (cardId, revealed) => {
      const state = cardState.get(cardId);
      if (!state || state.matched) return;
      state.revealed = revealed;
      renderCards();
    };

    const finishMemory = () => {
      const elapsedSeconds = Math.max(1, Math.round((Date.now() - startedAt) / 1000));
      const score = Math.max(0, Math.round(100 - ((moves - pairs.length) * 6)));
      const success = matchedPairs.size === pairs.length;

      feedback.innerHTML = '<i class="bi bi-trophy-fill"></i> ' + tx('memory_success', 'All pairs completed. Great focus.');
      feedback.className = 'ex-feedback ex-feedback--ok';
      feedback.hidden = false;

      markExerciseItemResult(container, success, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.question,
        expectedAnswer: JSON.stringify(pairs),
        answerText: JSON.stringify(Array.from(matchedPairs)),
        answerPayload: { moves, elapsed_seconds: elapsedSeconds, score },
        pointsObtained: score / 100,
        feedback: tx('correct', 'Correcto!'),
      });
    };

    const handleCardClick = (cardId) => {
      if (lockBoard) return;

      const state = cardState.get(cardId);
      if (!state || state.matched || state.revealed) return;

      setCardRevealed(cardId, true);

      if (!selectedCardId) {
        selectedCardId = cardId;
        window.setTimeout(() => {
          const selectedState = selectedCardId ? cardState.get(selectedCardId) : null;
          if (selectedState && !selectedState.matched && selectedState.revealed) {
            selectedState.revealed = false;
            selectedCardId = null;
            renderCards();
          }
        }, previewMs);
        return;
      }

      if (selectedCardId === cardId) {
        return;
      }

      moves += 1;
      syncMeta();

      const firstId = selectedCardId;
      const first = cardState.get(firstId);
      const second = cardState.get(cardId);
      selectedCardId = null;

      if (!first || !second) return;

      if (first.pairId === second.pairId) {
        first.matched = true;
        second.matched = true;
        first.revealed = true;
        second.revealed = true;
        matchedPairs.add(first.pairId);
        renderCards();
        syncMeta();

        if (matchedPairs.size === pairs.length) {
          finishMemory();
        }
        return;
      }

      lockBoard = true;
      window.setTimeout(() => {
        first.revealed = false;
        second.revealed = false;
        lockBoard = false;
        renderCards();
      }, Math.max(450, previewMs - 200));
    };

    renderCards();
    syncMeta();
  }

  function showCompletion() {
    registerExerciseAttempt();

    document.getElementById('exercisePanel').hidden = true;
    const modal = document.getElementById('exCompleteModal');
    const modeData = currentModeData || getModeData(currentMode);
    document.getElementById('exCompleteMsg').textContent =
      t('completed_all', { title: modeData.title });
    modal.hidden = false;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
})();
</script>
@endsection
