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
        <img src="images/mix_small.webp" srcset="images/mix_small.webp 300w, images/mix_medium.webp 600w, images/mix_large.webp 900w" sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 20vw" loading="lazy" alt="{{ __('lexi.exercises.mix_image_alt') }}">
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
  const sharedConfigNode = document.getElementById('lexiExerciseSharedConfig');
  const sharedConfig = sharedConfigNode ? JSON.parse(sharedConfigNode.textContent || '{}') : {};
  const templateCatalogNode = document.getElementById('lexiExerciseTemplateCatalog');
  const TEMPLATE_CATALOG = templateCatalogNode ? JSON.parse(templateCatalogNode.textContent || '{}') : {};
  const SHARED_CEFR_LEVELS = Array.isArray(sharedConfig.cefr_levels) ? sharedConfig.cefr_levels : [];
  const SHARED_TOPIC_OPTIONS = Array.isArray(sharedConfig.topic_options) ? sharedConfig.topic_options : [];
  const SKILL_LABELS = {
    en: ['Reading',     'Listening', 'Speaking',   'Writing',    'Mix'],
    fr: ['Lecture',     'Écoute',    'Expression',  'Écriture',   'Mix'],
    de: ['Lesen',       'Hören',     'Sprechen',    'Schreiben',  'Mix'],
    it: ['Lettura',     'Ascolto',   'Parlare',     'Scrittura',  'Mix'],
    no: ['Lesing',      'Lytting',   'Snakking',    'Skriving',   'Mix'],
    dk: ['Læsning',     'Lytning',   'Tale',        'Skrivning',  'Mix'],
    fi: ['Lukeminen',   'Kuuntelu',  'Puhuminen',   'Kirjoitus',  'Mix'],
    ko: ['읽기', '듣기', '말하기', '쓰기', 'Mix'],
    zh: ['阅读', '听力', '口语', '写作', 'Mix'],
    ru: ['Чтение', 'Слушание', 'Говорение', 'Письмо', 'Mix'],
    ua: ['Читання', 'Слухання', 'Говоріння', 'Письмо', 'Mix'],
    gr: ['Ανάγνωση', 'Ακρόαση', 'Ομιλία', 'Γραφή', 'Mix'],
    es: ['Lectura',     'Escucha',   'Habla',       'Escritura',  'Mix'],
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

  async function loadVocabularySources(message = t('loading_options')) {
    setExerciseCollectionsLoading(true, message);

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
      setExerciseCollectionsLoading(false);
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
      items: selectedLevel && selectedTopic ? filteredItems : [],
      levels,
      topics,
      selectedLevel,
      selectedTopic,
    };
  }

  function getSelectedVocabularySource() {
    if (getSelectedSourceType() === 'catalog') {
      return getSelectedCatalogSource();
    }

    const { library, collections } = getVocabularySources();
    const selectedId = localStorage.getItem(EXERCISE_COLLECTION_KEY) || '';
    if (!selectedId) {
      return { id: 'saved', name: t('saved_lists_name'), items: [] };
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
        localStorage.setItem(EXERCISE_COLLECTION_KEY, '');
        try {
          await loadVocabularySources(button.dataset.sourceTab === 'saved' ? t('loading_saved_lists') : t('loading_catalog'));
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
    levelSelect.innerHTML = '<option value="">' + t('select_level') + '</option>' + catalogSource.levels.map(level => '<option value="' + level + '">' + levelOptionLabel(level) + '</option>').join('');
    topicSelect.innerHTML = '<option value="">' + t('select_category') + '</option>' + getTopicOptions().map(topic => '<option value="' + topic.value + '">' + topic.label + '</option>').join('');

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
    const selectedId = localStorage.getItem(EXERCISE_COLLECTION_KEY) || '';
    const options = [library, ...collections];

    select.innerHTML = '<option value="">' + t('select_collection') + '</option>' + options.map(source => {
      const count = source.items.length;
      const label = source.name + ' (' + count + ')';
      return '<option value="' + String(source.id) + '">' + label + '</option>';
    }).join('');

    const hasSelected = options.some(source => String(source.id) === selectedId);
    select.value = hasSelected ? selectedId : '';
    localStorage.setItem(EXERCISE_COLLECTION_KEY, select.value);

    select.onchange = () => {
      localStorage.setItem(EXERCISE_COLLECTION_KEY, select.value);
    };
  }

  function resetExerciseSelectionState() {
    localStorage.setItem(EXERCISE_SOURCE_KEY, 'catalog');
    localStorage.setItem(EXERCISE_CATALOG_LEVEL_KEY, '');
    localStorage.setItem(EXERCISE_CATALOG_TOPIC_KEY, '');
    localStorage.setItem(EXERCISE_COLLECTION_KEY, '');
  }

  function buildCustomSpeakingItems(items) {
    return items
      .map(item => ({
        type: 'pronounce',
        word: item.text || item.word || '',
        hint: item.translation || item.meaning || '',
      }))
      .filter(item => item.word)
      .slice(0, 5);
  }

  function buildCustomReadingItems(items) {
    return items
      .filter(item => item.text && item.translation)
      .slice(0, 5)
      .map(item => {
        const correctAnswer = item.translation;
        const options = shuffleArray([
          correctAnswer,
          `not ${correctAnswer}`,
          item.topic ? `related to ${item.topic}` : 'an unrelated idea',
          item.cefr ? `level ${item.cefr}` : 'a grammar rule',
        ]);

        return {
          type: 'mcq',
          passage: `${item.text} means ${correctAnswer} in Spanish.${item.topic ? ' It belongs to the topic of ' + item.topic + '.' : ''}`,
          question: `What is the best translation of "${item.text}"?`,
          options,
          correct: options.indexOf(correctAnswer),
        };
      });
  }

  function buildCustomWritingItems(items) {
    return items
      .map(item => ({
        type: 'translate',
        prompt: t('js.exercise_runtime.translate_to_spanish'),
        sentence: item.text || item.word || '',
        answer: item.translation || item.meaning || '',
      }))
      .filter(item => item.sentence && item.answer)
      .slice(0, 3);
  }

  function buildCustomListeningItems(items) {
    return items
      .filter(item => item.text && item.translation)
      .slice(0, 3)
      .map(item => ({
        type: 'fillin',
        transcript: `${item.text} means ${item.translation}. Listen carefully and identify the missing word.`,
        question: t('complete_sentence'),
        sentence: `${item.translation} in English is ________.`,
        answer: item.text,
      }));
  }

  function buildCustomMixItems(items) {
    const readingItems = buildCustomReadingItems(items).slice(0, 1);
    const listeningItems = buildCustomListeningItems(items).slice(0, 1);
    const speakingItems = buildCustomSpeakingItems(items).slice(0, 1);
    const writingItems = buildCustomWritingItems(items).slice(0, 1);

    return [...readingItems, ...listeningItems, ...speakingItems, ...writingItems].filter(item => item && item.type);
  }

  function shuffleArray(items) {
    const clone = [...items];
    for (let index = clone.length - 1; index > 0; index -= 1) {
      const randomIndex = Math.floor(Math.random() * (index + 1));
      [clone[index], clone[randomIndex]] = [clone[randomIndex], clone[index]];
    }
    return clone;
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

    if (templateModeData) {
      return templateModeData;
    }

    if (!source.items.length) {
      return { title: base.title, items: base.items };
    }

    if (mode === 'reading') {
      const customItems = buildCustomReadingItems(source.items);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'speaking') {
      const customItems = buildCustomSpeakingItems(source.items);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'listening') {
      const customItems = buildCustomListeningItems(source.items);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'writing') {
      const customItems = buildCustomWritingItems(source.items);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (mode === 'mix') {
      const customItems = buildCustomMixItems(source.items);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    return { title: base.title, items: base.items };
  }

  async function registerExerciseAttempt() {
    try {
      const source = getSelectedVocabularySource();
      const items = getModeData(currentMode).items || [];
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
      title: "Combinado",
      items: [
        {
          type: "mcq",
          passage: "Remote work has become increasingly common since 2020. Many employees report higher productivity when working from home, while others miss the social aspects of the office. Companies are now exploring hybrid models that combine both approaches.",
          question: "What are companies exploring as a solution?",
          options: ["Full remote work", "Full office work", "Hybrid models", "Four-day work weeks"],
          correct: 2
        },
        {
          type: "fillin",
          transcript: "To apply for the position, please send your CV and a cover letter to the address shown on screen. The application deadline is the thirty-first of May. Late applications will not be considered.",
          question: t('complete_sentence'),
          sentence: "Please send your CV and a ________ letter to the address shown.",
          answer: "cover"
        },
        { type: "pronounce", word: "get the ball rolling", hint: "poner las cosas en marcha" },
        {
          type: "translate",
          prompt: t('translate_to_english'),
          sentence: "Lleva dos horas esperando una respuesta.",
          answer: "He has been waiting for an answer for two hours."
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

  function resetExerciseSessionMetrics() {
    sessionStartedAt = Date.now();
    sessionAnsweredItems = 0;
    sessionCorrectItems = 0;
    sessionAnswerRecords = [];
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

  function openMode(mode) {
    currentMode = mode;
    currentIndex = 0;
    resetExerciseSessionMetrics();
    const modeData = getModeData(mode);
    document.getElementById('exerciseMenu').hidden = true;
    document.getElementById('exercisePanel').hidden = false;
    document.getElementById('exCompleteModal').hidden = true;
    document.getElementById('exPanelTitle').textContent = modeData.title;
    renderExercise();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function closePanel() {
    document.getElementById('exercisePanel').hidden = true;
    document.getElementById('exerciseMenu').hidden = false;
  }

  function renderExercise() {
    const modeData = getModeData(currentMode);
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
  }

  function nextExercise() {
    const items = getModeData(currentMode).items;
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
      const t = container.querySelector('.ex-transcript');
      t.hidden = !t.hidden;
      this.innerHTML = t.hidden
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

  function showCompletion() {
    registerExerciseAttempt();

    document.getElementById('exercisePanel').hidden = true;
    const modal = document.getElementById('exCompleteModal');
    const modeData = getModeData(currentMode);
    document.getElementById('exCompleteMsg').textContent =
      t('completed_all', { title: modeData.title });
    modal.hidden = false;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
})();
</script>
@endsection