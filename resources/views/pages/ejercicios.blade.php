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
    <div class="ex-nav-btns" id="exNavBtns">
      <button class="btn btn-primary" id="btnNextEx">
        {{ __('lexi.exercises.next') }} <i class="bi bi-arrow-right"></i>
      </button>
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
    es: ['Lectura', 'Escucha', 'Habla', 'Escritura', 'Desafio'],
    fr: ['Lecture', 'Écoute', 'Expression', 'Écriture', 'Défi'],
    de: ['Lesen', 'Hören', 'Sprechen', 'Schreiben', 'Challenge'],
    it: ['Lettura', 'Ascolto', 'Parlato', 'Scrittura', 'Sfida'],
    pt: ['Leitura', 'Escuta', 'Fala', 'Escrita', 'Desafio'],
    ro: ['Citire', 'Ascultare', 'Vorbire', 'Scriere', 'Provocare'],
    bg: ['Четене', 'Слушане', 'Говорене', 'Писане', 'Предизвикателство'],
    ru: ['Чтение', 'Аудирование', 'Говорение', 'Письмо', 'Челлендж'],
    uk: ['Читання', 'Слухання', 'Говоріння', 'Письмо', 'Виклик'],
    zh: ['阅读', '听力', '口语', '写作', '挑战'],
    ja: ['読解', 'リスニング', 'スピーキング', 'ライティング', 'チャレンジ'],
    ko: ['읽기', '듣기', '말하기', '쓰기', '챌린지'],
    hi: ['पठन', 'श्रवण', 'बोलना', 'लेखन', 'चुनौती'],
    ar: ['قراءة', 'استماع', 'تحدث', 'كتابة', 'تحدي'],
    he: ['קריאה', 'האזנה', 'דיבור', 'כתיבה', 'אתגר'],
    tr: ['Okuma', 'Dinleme', 'Konuşma', 'Yazma', 'Meydan Okuma'],
    id: ['Membaca', 'Mendengarkan', 'Berbicara', 'Menulis', 'Tantangan'],
    vi: ['Đọc', 'Nghe', 'Nói', 'Viết', 'Thử thách'],
    th: ['การอ่าน', 'การฟัง', 'การพูด', 'การเขียน', 'ความท้าทาย'],
    el: ['Ανάγνωση', 'Ακρόαση', 'Ομιλία', 'Γραφή', 'Πρόκληση'],
    cs: ['Čtení', 'Poslech', 'Mluvení', 'Psaní', 'Výzva'],
    sk: ['Čítanie', 'Počúvanie', 'Hovorenie', 'Písanie', 'Výzva'],
    hu: ['Olvasás', 'Hallás utáni értés', 'Beszéd', 'Írás', 'Kihívás'],
    sv: ['Läsning', 'Hörförståelse', 'Tal', 'Skrivning', 'Utmaning'],
    da: ['Læsning', 'Lytning', 'Tale', 'Skrivning', 'Udfordring'],
    no: ['Lesing', 'Lytting', 'Snakking', 'Skriving', 'Utfordring'],
    fi: ['Lukeminen', 'Kuuntelu', 'Puhuminen', 'Kirjoittaminen', 'Haaste'],
  };
  SKILL_LABELS.gr = SKILL_LABELS.el;
  SKILL_LABELS.dk = SKILL_LABELS.da;
  SKILL_LABELS.ua = SKILL_LABELS.uk;
  SKILL_LABELS.nb = SKILL_LABELS.no;
  SKILL_LABELS.nn = SKILL_LABELS.no;
  const BADGE_MODES = ['reading', 'listening', 'speaking', 'writing', 'mix'];
  const EXERCISE_SOURCE_KEY = 'lexiExerciseSource';
  const EXERCISE_COLLECTION_KEY = 'lexiExerciseCollection';
  const EXERCISE_CATALOG_LEVEL_KEY = 'lexiExerciseCatalogLevel';
  const EXERCISE_CATALOG_TOPIC_KEY = 'lexiExerciseCatalogTopic';
  const CEFR_LEVELS = Array.isArray(SHARED_CEFR_LEVELS) && SHARED_CEFR_LEVELS.length ? SHARED_CEFR_LEVELS : ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
  let serverVocabularyState = { library: { id: 'library', name: t('saved_name'), items: [] }, collections: [], catalog: [] };
  let serverVocabularyLanguage = null;
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
    function resolveModeLabelsForLanguage(langCode) {
      const raw = String(langCode || '').trim().toLowerCase();
      const normalized = raw.split('-')[0];

      return SKILL_LABELS[raw] || SKILL_LABELS[normalized] || SKILL_LABELS.en;
    }

    function getLocalizedModeTitle(mode) {
      if (mode === 'mix') {
        const labels = resolveModeLabelsForLanguage(getActiveLang());
        return tx('challenge_label', labels[4] || 'Challenge');
      }

      const modeIndex = BADGE_MODES.indexOf(mode);
      if (modeIndex === -1) {
        return mode;
      }

      const labels = resolveModeLabelsForLanguage(getActiveLang());
      return labels[modeIndex] || mode;
    }

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

  async function loadVocabularySources(message = t('loading_options'), showSpinner = false, forceReload = false) {
    const activeLang = getActiveLang();
    const hasCachedState = Array.isArray(serverVocabularyState.catalog)
      && Array.isArray(serverVocabularyState.collections)
      && serverVocabularyLanguage === activeLang;

    if (!forceReload && hasCachedState) {
      return;
    }

    if (showSpinner) {
      setExerciseCollectionsLoading(true, message);
    }

    try {
      const response = await exerciseApiFetch('/api/library/state?language=' + encodeURIComponent(activeLang));
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
      serverVocabularyLanguage = activeLang;
    } catch {
      serverVocabularyState = {
        library: { id: 'library', name: t('saved_name'), items: [] },
        collections: [],
        catalog: [],
      };
      serverVocabularyLanguage = activeLang;
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
        const nextSource = button.dataset.sourceTab;
        const currentSource = getSelectedSourceType();
        localStorage.setItem(EXERCISE_SOURCE_KEY, nextSource);
        if (nextSource === 'saved' && !localStorage.getItem(EXERCISE_COLLECTION_KEY)) {
          localStorage.setItem(EXERCISE_COLLECTION_KEY, 'all_saved');
        }
        try {
          if (nextSource !== currentSource) {
            await loadVocabularySources(nextSource === 'saved' ? t('loading_saved_lists') : t('loading_catalog'), true);
          }
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

  async function initializeExercisePageEntryState(forceReloadSources = true) {
    resetExerciseSelectionState();
    syncSourcePanels();
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
    await loadVocabularySources(t('loading_options'), true, forceReloadSources);
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
    syncSourcePanels();
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
    const optionsLimit = ['A1', 'A2'].includes(difficultyLevel) ? 3 : 4;

    return items
      .filter(item => item.text && item.translation)
      .slice(0, 5)
      .map(item => {
        const correctAnswer = item.text;
        const distractors = selectReadingDistractors(items, item, Math.max(2, optionsLimit - 1));
        const options = shuffleArray([correctAnswer, ...distractors]).slice(0, optionsLimit);
        const topic = formatTopicLabel(item.topic);
        const cefr = normalizeCefrLevel(item.cefr) || difficultyLevel;
        const question = ['A1', 'A2'].includes(cefr)
          ? 'Choose the best word for the gap.'
          : ['B1', 'B2'].includes(cefr)
            ? 'Choose the most natural option for the gap.'
            : 'Choose the most precise option for the gap.';
        const sentence = buildReadingGapSentence(topic, correctAnswer, cefr);

        return {
          type: 'mcq',
          passage: `${topic} (${cefr}). ${sentence}`,
          question,
          options,
          correct: options.indexOf(correctAnswer),
        };
      });
  }

  function buildCustomWritingItems(items, difficultyLevel = 'B1') {
    const prompt = ['A1', 'A2'].includes(difficultyLevel)
      ? 'Write one natural sentence in English for this situation.'
      : ['B1', 'B2'].includes(difficultyLevel)
        ? 'Write one polished sentence in English. Keep the original meaning and tone.'
        : 'Write one precise C-level sentence in English. Keep meaning, register, and lexical accuracy.';

    return items
      .map(item => ({
        type: 'translate',
        prompt,
        sentence: `${buildWritingScenario(formatTopicLabel(item.topic), difficultyLevel)} Context source: "${item.translation || item.meaning || ''}"`,
        answer: item.text || item.word || '',
      }))
      .filter(item => item.sentence && item.answer)
      .slice(0, 4);
  }

  function buildCustomListeningItems(items, difficultyLevel = 'B1') {
    const question = ['A1', 'A2'].includes(difficultyLevel)
      ? 'Listen and type the missing expression.'
      : 'Type the exact expression you hear.';

    return items
      .filter(item => item.text && item.translation)
      .slice(0, 3)
      .map(item => ({
        type: 'fillin',
        transcript: ['C1', 'C2'].includes(difficultyLevel)
          ? `Professional briefing (${String(formatTopicLabel(item.topic)).toLowerCase()}): "Before the panel review, each candidate is expected to ${item.text} to ensure consistency, precision, and register control."`
          : ['A1', 'A2'].includes(difficultyLevel)
            ? `Audio note (${String(formatTopicLabel(item.topic)).toLowerCase()}): "Before we begin, please ${item.text} and then sit near the front."`
            : `Team voice message (${String(formatTopicLabel(item.topic)).toLowerCase()}): "Before the review starts, everyone should ${item.text} so the discussion stays focused."`,
        question,
        sentence: `In the ${String(formatTopicLabel(item.topic)).toLowerCase()} recording, the speaker says we should ________ before the next step.`,
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

  function selectReadingDistractors(items, currentItem, limit) {
    const correct = String(currentItem?.text || currentItem?.word || '').trim().toLowerCase();
    if (!correct) return [];

    const currentTopic = normalizeTopicKey(currentItem?.topic || '');
    const currentCefr = String(currentItem?.cefr || '').toUpperCase();
    const correctTokenCount = String(currentItem?.text || currentItem?.word || '').trim().split(/\s+/).filter(Boolean).length;
    const correctBucket = lexicalBucket(currentItem?.text || currentItem?.word || '');

    const scored = Array.from(new Set(
      (items || [])
        .map(item => ({
          word: String(item?.text || item?.word || '').trim(),
          topic: normalizeTopicKey(item?.topic || ''),
          cefr: String(item?.cefr || '').toUpperCase(),
          bucket: lexicalBucket(item?.text || item?.word || ''),
        }))
        .filter(row => row.word)
        .filter(row => row.word.toLowerCase() !== correct)
    ))
      .map(row => {
        const lower = row.word.toLowerCase();
        const sameInitial = lower[0] === correct[0] ? 3 : 0;
        const lengthDistance = Math.abs(lower.length - correct.length);
        const lengthScore = Math.max(0, 3 - lengthDistance);
        const tokenCount = row.word.split(/\s+/).filter(Boolean).length;
        const tokenScore = Math.max(0, 3 - Math.abs(tokenCount - correctTokenCount));
        const editScore = Math.max(0, 8 - levenshteinDistance(correct, lower));
        const topicScore = currentTopic && row.topic === currentTopic ? 4 : 0;
        const cefrScore = currentCefr && row.cefr === currentCefr ? 3 : 0;

        return { word: row.word, bucket: row.bucket, score: sameInitial + lengthScore + tokenScore + editScore + topicScore + cefrScore };
      })
      .sort((left, right) => right.score - left.score);

    let rows = scored
      .filter(row => row.bucket === correctBucket)
      .slice(0, limit)
      .map(item => item.word);

    if (rows.length < limit) {
      const curated = curatedDistractorsForBucket(correctBucket, correct);
      rows = Array.from(new Set([...rows, ...curated])).slice(0, limit);
    }

    if (rows.length < limit) {
      const fallback = scored.map(row => row.word);
      rows = Array.from(new Set([...rows, ...fallback])).slice(0, limit);
    }

    return rows;
  }

  function lexicalBucket(word) {
    const value = String(word || '').trim().toLowerCase();
    if (!value) return 'other';
    if (['please', 'hello', 'thanks', 'thank you', 'sorry'].includes(value)) return 'social';
    if (value.includes(' ')) return 'phrase';
    if (value.startsWith('to ') || /(ing|ed)$/i.test(value)) return 'verb';
    if (/(ly)$/i.test(value)) return 'adverb';
    if (/(ous|ive|al|ful|less|able|ible)$/i.test(value)) return 'adjective';
    if (/(tion|sion|ment|ness|ity|ship|ance|ence)$/i.test(value)) return 'noun';
    return 'word';
  }

  function curatedDistractorsForBucket(bucket, correctLower) {
    const map = {
      social: ['please', 'sorry', 'thanks', 'hello', 'excuse me'],
      phrase: ['make a decision', 'take a break', 'set a goal', 'keep in mind'],
      verb: ['review', 'prepare', 'organize', 'confirm', 'update'],
      noun: ['plan', 'report', 'policy', 'strategy', 'schedule'],
      adjective: ['clear', 'formal', 'effective', 'flexible', 'reliable'],
      adverb: ['carefully', 'clearly', 'quickly', 'properly', 'regularly'],
      word: ['option', 'result', 'project', 'issue', 'process'],
      other: ['option', 'result', 'project', 'issue', 'process'],
    };

    return (map[bucket] || map.other)
      .filter(word => word.toLowerCase() !== String(correctLower || '').toLowerCase());
  }

  function normalizeCefrLevel(value) {
    const level = String(value || '').trim().toUpperCase();
    return ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'].includes(level) ? level : null;
  }

  function buildReadingGapSentence(topic, correctWord, difficultyLevel = 'B1') {
    const key = String(topic || '').toLowerCase();
    const value = String(correctWord || '').trim();
    const lower = value.toLowerCase();
    const isVerbLike = lower.startsWith('to ') || /(ing|ed)$/i.test(lower);
    const isNounLike = /(tion|sion|ity|ment|ness|ship|ance|ence)$/i.test(lower);
    const bucket = lexicalBucket(value);

    if (bucket === 'social') {
      return ['A1', 'A2'].includes(difficultyLevel)
        ? 'Sentence: In a polite message to your classmate, write: "____, can you send me the file today?"'
        : 'Sentence: In a professional email opener, complete the line: "____, could you share the updated version before 4 PM?"';
    }

    if (key.includes('education')) {
      return isVerbLike
        ? 'Sentence: Before the seminar starts, students should ____ each key point from the reading so they can contribute with confidence.'
        : 'Sentence: The lecturer said that a strong ____ helps students connect ideas across the whole unit.';
    }

    if (key.includes('travel')) {
      return isVerbLike
        ? 'Sentence: Before boarding, travelers are advised to ____ all required details so there are no delays at the gate.'
        : 'Sentence: The agency confirmed that a clear ____ makes the whole trip smoother and less stressful.';
    }

    if (key.includes('business') || key.includes('work')) {
      return isVerbLike
        ? 'Sentence: During the weekly review, the manager asked the team to ____ the proposal before sharing it with the client.'
        : 'Sentence: In today\'s planning meeting, the team agreed that a clear ____ is essential before launch.';
    }

    if (key.includes('health')) {
      return isVerbLike
        ? 'Sentence: Doctors recommend that patients ____ small daily habits to build better long-term wellbeing.'
        : 'Sentence: The coach explained that a consistent ____ can improve wellbeing over time.';
    }

    if (key.includes('culture')) {
      return isVerbLike
        ? 'Sentence: The museum team worked together to ____ local history in a way that younger visitors could relate to.'
        : 'Sentence: The city council funded a new ____ to support local artists and community events.';
    }

    if (['A1', 'A2'].includes(difficultyLevel)) {
      return isVerbLike
        ? 'Sentence: We need to ____ this task before the lesson ends so everyone is ready for tomorrow.'
        : 'Sentence: We need a clear ____ today so the class can continue without confusion.';
    }

    if (['C1', 'C2'].includes(difficultyLevel)) {
      return isVerbLike
        ? 'Sentence: In the final draft, the proposal should ____ the strategic priorities while preserving precision and formal register.'
        : 'Sentence: In the final draft, the proposal should present a coherent ____ that aligns with the strategic priorities.';
    }

    if (isNounLike) {
      return 'Sentence: In this scenario, the team needs a stronger ____ to explain the decision clearly to stakeholders.';
    }

    return 'Sentence: In this scenario, the team should ____ the key idea clearly so everyone can act on it.';
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
    const word = item.text || item.word || '';

    if (['A1', 'A2'].includes(difficultyLevel)) {
      return `Say the expression naturally, then use it in one short real-life sentence about ${String(topic).toLowerCase()}.${meaning ? ' Hint: ' + meaning : ''}`;
    }

    if (['C1', 'C2'].includes(difficultyLevel)) {
      return `Give a 20-second formal response on ${String(topic).toLowerCase()} and integrate "${word}" with precise register.${meaning ? ' Hint: ' + meaning : ''}`;
    }

    return `Give a 15-second response: use "${word}" once in a fluent sentence about ${String(topic).toLowerCase()}.${meaning ? ' Hint: ' + meaning : ''}`;
  }

  function buildWritingScenario(topic, difficultyLevel = 'B1') {
    if (['A1', 'A2'].includes(difficultyLevel)) {
      return `Scenario (${topic}): You are writing a short message to a classmate.`;
    }

    if (['C1', 'C2'].includes(difficultyLevel)) {
      return `Scenario (${topic}): You are drafting a formal sentence for a professional report.`;
    }

    return `Scenario (${topic}): You are writing one sentence for an email update.`;
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
      title: selectedTemplate.title || getLocalizedModeTitle(mode),
      items,
    };
  }

  function getModeData(mode) {
    const base = EXERCISES[mode];
    if (base) {
      base.title = getLocalizedModeTitle(mode);
    }
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

  function updateExerciseModeLabels() {
    const lang = getActiveLang();
    const labels = resolveModeLabelsForLanguage(lang);
    document.querySelectorAll('.exercise-card[data-mode]').forEach(card => {
      const idx = BADGE_MODES.indexOf(card.dataset.mode);
      if (idx === -1) return;
      const badge = card.querySelector('.exercise-card-badge');
      const label = card.querySelector('.exercise-card-label');
      if (badge) badge.textContent = labels[idx];
      if (label) label.textContent = labels[idx];
    });
  }

  updateExerciseModeLabels();

  async function bootstrapExercises() {
    initSpeechVoices();
    primeUiAudio();
    const setGlobalLoading = typeof window.lexiSetPageLoading === 'function' ? window.lexiSetPageLoading : null;
    const loadingLabel = tx('loading_options', 'Cargando opciones...');

    if (setGlobalLoading) {
      setGlobalLoading(true, loadingLabel);
    }

    try {
      // Render source controls immediately, then enforce page-entry defaults.
      setupExerciseSourceTabs();
      await initializeExercisePageEntryState(true);

      const sessionReadyPromise = window.lexiSessionReady && typeof window.lexiSessionReady.then === 'function'
        ? window.lexiSessionReady
        : Promise.resolve();

      await Promise.allSettled([sessionReadyPromise]);
    } finally {
      if (setGlobalLoading) {
        setGlobalLoading(false);
      }
    }
  }

  bootstrapExercises();

  const primeAudioOnInteraction = () => {
    primeUiAudio();
    window.removeEventListener('pointerdown', primeAudioOnInteraction);
    window.removeEventListener('keydown', primeAudioOnInteraction);
  };

  window.addEventListener('pointerdown', primeAudioOnInteraction, { passive: true });
  window.addEventListener('keydown', primeAudioOnInteraction);

  window.addEventListener('lexi-lang-changed', async () => {
    updateExerciseModeLabels();
    await loadVocabularySources(t('loading_options'), false, true);
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
    syncSourcePanels();
  });

  window.addEventListener('pageshow', async (event) => {
    if (!event.persisted) return;
    const setGlobalLoading = typeof window.lexiSetPageLoading === 'function' ? window.lexiSetPageLoading : null;
    const loadingLabel = tx('loading_options', 'Cargando opciones...');

    if (setGlobalLoading) {
      setGlobalLoading(true, loadingLabel);
    }

    try {
      await initializeExercisePageEntryState(true);
    } finally {
      if (setGlobalLoading) {
        setGlobalLoading(false);
      }
    }
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
  let answeredExerciseIndexes = new Set();
  let autoPlayedListeningIndexes = new Set();
  let currentModeData = null;
  let runtimeRequestToken = 0;
  let activeListeningStopHandler = null;

  function renderExerciseLoading(title) {
    const content = document.getElementById('exerciseContent');
    const nav = document.getElementById('exNavBtns');
    document.getElementById('exPanelTitle').textContent = title;
    document.getElementById('exProgress').textContent = tx('loading_short', 'Cargando...');
    document.getElementById('exProgressBar').style.width = '12%';
    document.getElementById('btnNextEx').disabled = true;
    document.getElementById('btnNextEx').innerHTML = tx('skip', 'Saltar') + ' <i class="bi bi-arrow-right"></i>';
    if (nav) nav.hidden = true;
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
    answeredExerciseIndexes = new Set();
    autoPlayedListeningIndexes = new Set();
  }

  function updateNextButtonLabel(totalItems = null) {
    const btnNext = document.getElementById('btnNextEx');
    if (!btnNext) return;

    const modeData = currentModeData || (currentMode ? getModeData(currentMode) : null);
    const total = Number.isInteger(totalItems) ? totalItems : (modeData && Array.isArray(modeData.items) ? modeData.items.length : 0);

    if (!total || currentIndex >= total - 1) {
      btnNext.innerHTML = t('finish') + ' <i class="bi bi-check-lg"></i>';
      return;
    }

    if (answeredExerciseIndexes.has(currentIndex)) {
      btnNext.innerHTML = t('next') + ' <i class="bi bi-arrow-right"></i>';
      return;
    }

    btnNext.innerHTML = tx('skip', 'Saltar') + ' <i class="bi bi-arrow-right"></i>';
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
    const aiFirstModes = ['reading', 'listening', 'speaking', 'writing'];

    const response = await exerciseApiFetch('/api/exercise-runtime/start', {
      method: 'POST',
      body: JSON.stringify({
        mode,
        require_ai: aiFirstModes.includes(mode),
        quality_profile: aiFirstModes.includes(mode) ? 'exam_strict' : null,
        ...context,
      }),
    });

    if (!response.ok) {
      let message = 'runtime-start-failed';
      try {
        const errorPayload = await response.json();
        if (errorPayload && typeof errorPayload.message === 'string' && errorPayload.message.trim() !== '') {
          message = errorPayload.message.trim();
        }
      } catch {
      }
      throw new Error(message);
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

  function renderRuntimeUnavailable(title, reason) {
    const content = document.getElementById('exerciseContent');
    const nav = document.getElementById('exNavBtns');
    document.getElementById('exPanelTitle').textContent = title;
    document.getElementById('exProgress').textContent = tx('not_available_short', 'No disponible');
    document.getElementById('exProgressBar').style.width = '0%';
    document.getElementById('btnNextEx').disabled = true;
    document.getElementById('btnNextEx').innerHTML = tx('skip', 'Saltar') + ' <i class="bi bi-arrow-right"></i>';
    if (nav) nav.hidden = true;

    content.innerHTML =
      '<div class="ex-feedback ex-feedback--warn" style="display:block">' +
      '<i class="bi bi-exclamation-triangle-fill"></i> ' +
      String(reason || tx('ai_generation_failed', 'No se pudo generar un ejercicio de calidad con IA para esta selección.')) +
      '</div>';
  }

  function markExerciseItemResult(container, isCorrect, details = {}) {
    if (!container || container.dataset.evaluated === '1') return;
    if (answeredExerciseIndexes.has(currentIndex)) return;

    container.dataset.evaluated = '1';
    answeredExerciseIndexes.add(currentIndex);
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

    updateNextButtonLabel();
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

  async function openMode(mode) {
    if (exerciseSourceSwitchInFlight) {
      return;
    }

    const aiFirstModes = ['reading', 'listening', 'speaking', 'writing'];
    const needsAiRuntime = aiFirstModes.includes(mode);

    if (needsAiRuntime) {
      await loadVocabularySources(t('loading_options'), true);
    }

    stopSpeechAudio();
    currentMode = mode;
    currentIndex = 0;
    resetExerciseSessionMetrics();
    currentModeData = null;
    const fallbackModeData = getModeData(mode);

    document.getElementById('exerciseMenu').hidden = true;
    document.getElementById('exercisePanel').hidden = false;
    const nav = document.getElementById('exNavBtns');
    if (nav) nav.hidden = false;
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const token = ++runtimeRequestToken;

    if (!needsAiRuntime) {
      renderExerciseLoading(fallbackModeData.title);
      await new Promise(resolve => window.setTimeout(resolve, 1000));
      if (token !== runtimeRequestToken || currentMode !== mode) {
        return;
      }
      currentModeData = fallbackModeData;
      currentIndex = 0;
      document.getElementById('btnNextEx').disabled = false;
      renderExercise();
      loadVocabularySources(t('loading_options'), false).catch(() => {});
      return;
    }

    renderExerciseLoading(fallbackModeData.title);

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
    } catch (error) {
      if (token !== runtimeRequestToken || currentMode !== mode) {
        return;
      }

      if (['reading', 'listening', 'speaking', 'writing'].includes(mode)) {
        currentModeData = null;
        renderRuntimeUnavailable(fallbackModeData.title, error instanceof Error ? error.message : null);
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
    stopSpeechAudio();
    document.getElementById('exercisePanel').hidden = true;
    document.getElementById('exerciseMenu').hidden = false;
    const nav = document.getElementById('exNavBtns');
    if (nav) nav.hidden = false;
  }

  const uiAudio = {
    ctx: null,
    enabled: true,
    fxEnabled: false,
    speechRate: 0.92,
    speechPitch: 1,
    preferredVoice: null,
    voicesInitialized: false,
    sfxEnabled: true,
    sfxFiles: {
      success: '/audio/sfx/answer-correct.mp3',
      error: '/audio/sfx/answer-incorrect.mp3',
      complete: '/audio/sfx/exercise-complete.mp3',
      pair: '/audio/sfx/pair-correct.mp3',
    },
    sfxCache: {},
    primed: false,
  };

  function primeUiAudio() {
    if (!uiAudio.sfxEnabled || uiAudio.primed) return;

    Object.entries(uiAudio.sfxFiles).forEach(([type, src]) => {
      if (!src || uiAudio.sfxCache[type]) return;
      try {
        const audio = new Audio(src);
        audio.preload = 'auto';
        audio.load();
        uiAudio.sfxCache[type] = audio;
      } catch {
      }
    });

    const ctx = getAudioContext();
    if (ctx && ctx.state === 'suspended') {
      ctx.resume().catch(() => {});
    }

    uiAudio.primed = true;
  }

  function playUiSfx(type) {
    if (!uiAudio.sfxEnabled) return false;

    const src = uiAudio.sfxFiles[type];
    if (!src) return false;

    try {
      let audio = uiAudio.sfxCache[type];
      if (!audio) {
        audio = new Audio(src);
        audio.preload = 'auto';
        audio.load();
        uiAudio.sfxCache[type] = audio;
      }

      const target = audio.paused ? audio : audio.cloneNode(true);
      target.currentTime = 0;
      const promise = target.play();
      if (promise && typeof promise.catch === 'function') {
        promise.catch(() => {});
      }
      return true;
    } catch {
      return false;
    }
  }

  function getAudioContext() {
    if (!uiAudio.enabled) return null;
    try {
      if (!uiAudio.ctx) {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return null;
        uiAudio.ctx = new Ctx();
      }
      return uiAudio.ctx;
    } catch {
      uiAudio.enabled = false;
      return null;
    }
  }

  function playUiTone(type) {
    const playedSfx = playUiSfx(type);
    if (playedSfx) return;
    if (!uiAudio.fxEnabled) return;
    const ctx = getAudioContext();
    if (!ctx) return;

    const now = ctx.currentTime;
    const oscillator = ctx.createOscillator();
    const gain = ctx.createGain();
    oscillator.type = type === 'success' ? 'triangle' : type === 'error' ? 'sawtooth' : 'sine';

    if (type === 'success') {
      oscillator.frequency.setValueAtTime(620, now);
      oscillator.frequency.linearRampToValueAtTime(880, now + 0.12);
    } else if (type === 'error') {
      oscillator.frequency.setValueAtTime(300, now);
      oscillator.frequency.linearRampToValueAtTime(180, now + 0.16);
    } else {
      oscillator.frequency.setValueAtTime(470, now);
      oscillator.frequency.linearRampToValueAtTime(520, now + 0.08);
    }

    gain.gain.setValueAtTime(0.0001, now);
    gain.gain.linearRampToValueAtTime(0.08, now + 0.01);
    gain.gain.exponentialRampToValueAtTime(0.0001, now + (type === 'error' ? 0.2 : 0.16));

    oscillator.connect(gain);
    gain.connect(ctx.destination);
    oscillator.start(now);
    oscillator.stop(now + (type === 'error' ? 0.22 : 0.18));
  }

  function playIncorrectFeedback(itemType) {
    if (['mcq', 'fillin', 'pronounce', 'translate'].includes(String(itemType || ''))) {
      playUiTone('error');
    }
  }

  function normalizeLangCodeForSpeech(langCode) {
    const raw = String(langCode || '').trim().toLowerCase();
    const base = raw.split('-')[0];
    const aliases = {
      gr: 'el',
      dk: 'da',
      ua: 'uk',
      no: 'nb',
    };

    return aliases[base] || base || 'en';
  }

  function resolveSpeechVoice(langCode) {
    if (!('speechSynthesis' in window)) return null;

    const voices = window.speechSynthesis.getVoices() || [];
    if (!voices.length) return null;

    const normalizedLang = normalizeLangCodeForSpeech(langCode);
    const wantedPrefix = normalizedLang === 'en' ? 'en' : normalizedLang;

    const scoreVoice = (voice) => {
      const name = String(voice.name || '').toLowerCase();
      const lang = String(voice.lang || '').toLowerCase();
      let score = 0;

      if (lang.startsWith(wantedPrefix)) score += 8;
      if (voice.default) score += 3;
      if (/neural|natural|premium/.test(name)) score += 6;
      if (/microsoft|google|samantha|alex/.test(name)) score += 4;
      if (/compact|espeak|festival/.test(name)) score -= 4;

      return score;
    };

    return [...voices].sort((a, b) => scoreVoice(b) - scoreVoice(a))[0] || null;
  }

  function resolveSpeechVoicesForLang(langCode) {
    if (!('speechSynthesis' in window)) return [];

    const voices = window.speechSynthesis.getVoices() || [];
    if (!voices.length) return [];

    const normalizedLang = normalizeLangCodeForSpeech(langCode);
    const wantedPrefix = normalizedLang === 'en' ? 'en' : normalizedLang;
    const matching = voices.filter(voice => String(voice.lang || '').toLowerCase().startsWith(wantedPrefix));

    return matching.length ? matching : voices;
  }

  function resolveListeningVoiceUri(item) {
    if (!item || typeof item !== 'object') return null;
    if (!('speechSynthesis' in window)) return null;

    initSpeechVoices();

    if (typeof item.__voice_uri === 'string' && item.__voice_uri.trim() !== '') {
      return item.__voice_uri;
    }

    const candidates = resolveSpeechVoicesForLang(getActiveLang());
    if (!candidates.length) return null;

    const selectedVoice = candidates[currentIndex % candidates.length] || candidates[0];
    const selectedUri = String(selectedVoice.voiceURI || selectedVoice.name || '').trim();

    item.__voice_uri = selectedUri || null;

    return item.__voice_uri;
  }

  function initSpeechVoices() {
    if (uiAudio.voicesInitialized || !('speechSynthesis' in window)) return;
    uiAudio.voicesInitialized = true;

    const assignVoice = () => {
      uiAudio.preferredVoice = resolveSpeechVoice(getActiveLang());
    };

    assignVoice();
    window.speechSynthesis.onvoiceschanged = assignVoice;
  }

  function speakTranscript(text, preferredVoiceUri = null, events = {}) {
    if (!('speechSynthesis' in window)) return null;

    try {
      initSpeechVoices();
      window.speechSynthesis.cancel();
      window.speechSynthesis.resume();
      const utterance = new SpeechSynthesisUtterance(String(text || ''));
      const activeLang = String(getActiveLang() || 'en').toLowerCase();
      utterance.lang = activeLang === 'en' ? 'en-US' : `${activeLang}-${activeLang.toUpperCase()}`;
      utterance.rate = uiAudio.speechRate;
      utterance.pitch = uiAudio.speechPitch;

      const availableVoices = window.speechSynthesis.getVoices() || [];
      const exactVoice = preferredVoiceUri
        ? availableVoices.find(voice => (voice.voiceURI || voice.name) === preferredVoiceUri)
        : null;

      utterance.voice = exactVoice || uiAudio.preferredVoice || resolveSpeechVoice(activeLang);
      if (typeof events.onEnd === 'function') utterance.onend = events.onEnd;
      if (typeof events.onError === 'function') utterance.onerror = events.onError;
      window.speechSynthesis.speak(utterance);
      return utterance;
    } catch {
      return null;
    }
  }

  function stopSpeechAudio() {
    if (typeof activeListeningStopHandler === 'function') {
      try {
        activeListeningStopHandler();
      } catch {
      }
    }

    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
    }
  }

  function parseReadingPassage(passage) {
    const raw = String(passage || '').trim();
    if (!raw) {
      return { scenario: '', sentence: '' };
    }

    const parts = raw.match(/^(.*?)\.\s*Sentence:\s*(.+)$/i);
    if (!parts) {
      return { scenario: raw, sentence: '' };
    }

    return {
      scenario: parts[1].trim(),
      sentence: parts[2].trim(),
    };
  }

  function resolveListeningBaseText(item) {
    const transcriptText = normalizeTextCandidate(item.transcript);
    const sentenceText = normalizeTextCandidate(item.sentence);

    if (transcriptText === '' && sentenceText === '') {
      return '';
    }

    if (transcriptText.length >= sentenceText.length) {
      return transcriptText;
    }

    return sentenceText;
  }

  function resolveListeningSentenceForGap(baseText, questionText) {
    const normalizedBase = normalizeTextCandidate(baseText);
    const normalizedQuestion = normalizeTextCandidate(questionText);
    const gapRegex = /_{2,}/;

    if (gapRegex.test(normalizedBase)) {
      return normalizedBase;
    }

    if (gapRegex.test(normalizedQuestion)) {
      const colonIndex = normalizedQuestion.indexOf(':');
      if (colonIndex > 0) {
        const afterColon = normalizedQuestion.slice(colonIndex + 1).trim();
        if (gapRegex.test(afterColon)) {
          return afterColon;
        }
      }

      return normalizedQuestion;
    }

    return normalizedBase;
  }

  function normalizeTextCandidate(value) {
    if (typeof value === 'string') {
      return value.trim();
    }

    if (Array.isArray(value)) {
      const joined = value
        .map(item => (typeof item === 'string' ? item.trim() : (item == null ? '' : String(item).trim())))
        .filter(item => item !== '')
        .join(' ');
      return joined.trim();
    }

    if (value && typeof value === 'object') {
      if (typeof value.text === 'string' && value.text.trim() !== '') {
        return value.text.trim();
      }

      const joined = Object.values(value)
        .map(item => (typeof item === 'string' ? item.trim() : (item == null ? '' : String(item).trim())))
        .filter(item => item !== '')
        .join(' ');
      return joined.trim();
    }

    if (value == null) {
      return '';
    }

    return String(value).trim();
  }

  function buildListeningSentenceWithGap(baseText, answerText, inputHtml) {
    const normalizedBase = String(baseText || '').trim();
    const normalizedAnswer = String(answerText || '').trim();

    if (normalizedBase === '') {
      return inputHtml;
    }

    // Support generic placeholders like ___, _____, ________ across languages.
    const gapPlaceholderRegex = /_{2,}/;
    if (gapPlaceholderRegex.test(normalizedBase)) {
      return normalizedBase.replace(gapPlaceholderRegex, inputHtml);
    }

    if (normalizedAnswer !== '') {
      const escapedAnswer = normalizedAnswer.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      const answerRegex = new RegExp(escapedAnswer, 'i');
      if (answerRegex.test(normalizedBase)) {
        return normalizedBase.replace(answerRegex, inputHtml);
      }
    }

    return normalizedBase + ' ' + inputHtml;
  }

  function normalizeListeningSpeechText(baseText, answerText, questionText) {
    const normalizedBase = normalizeTextCandidate(baseText);
    const normalizedQuestion = normalizeTextCandidate(questionText);

    const cleanForSpeech = (text) => {
      if (text === '') return '';

      const withoutPrefix = text
        .replace(/^(audio\s*note|team\s*voice\s*message|professional\s*briefing)\s*\([^)]*\)\s*:\s*/i, '')
        .replace(/^\s*["“]|["”]\s*$/g, '');

      return withoutPrefix
        .replace(/_{2,}/g, ' blank ')
        .replace(/\bunderscore\b/gi, ' ')
        .replace(/\bunder\s*score\b/gi, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    };

    const fromBase = cleanForSpeech(normalizedBase);
    if (fromBase.length >= 3) {
      return fromBase;
    }

    const fromQuestion = cleanForSpeech(normalizedQuestion);
    if (fromQuestion.length >= 3) {
      return fromQuestion;
    }

    return 'Listen and complete the sentence.';
  }

  function sanitizeListeningQuestion(questionText, answerText, baseText = '') {
    const normalizedQuestion = normalizeTextCandidate(questionText);
    const normalizedAnswer = normalizeTextCandidate(answerText);
    const normalizedBase = normalizeTextCandidate(baseText);
    const gapRegex = /_{2,}/;

    const simplifyComparableText = (value, answerValue = '') => {
      let text = String(value || '').trim();

      // If text looks like "instruction: sentence", keep the sentence part.
      const colonIndex = text.indexOf(':');
      if (colonIndex > 0) {
        const afterColon = text.slice(colonIndex + 1).trim();
        if (afterColon.length >= 8) {
          text = afterColon;
        }
      }

      if (String(answerValue || '').trim() !== '') {
        const escapedAnswer = String(answerValue).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        text = text.replace(new RegExp(escapedAnswer, 'gi'), ' ');
      }

      return text
        .toLowerCase()
        .replace(/_{2,}/g, ' ')
        .replace(/\bblank\b/gi, ' ')
        .replace(/\s*\(.*?\)\s*/g, ' ')
        .replace(/[^\p{L}\p{N}\s]/gu, ' ')
        .replace(/\s+/g, ' ')
        .trim();
    };

    if (normalizedQuestion === '') {
      return tx('listening_question_default', 'Listen and type the missing expression.');
    }

    // If the question already includes a blank placeholder, show a short prompt
    // and let the sentence-with-gap below carry the actual text.
    if (gapRegex.test(normalizedQuestion)) {
      return tx('listening_question_default', 'Listen and type the missing expression.');
    }

    const comparableQuestion = simplifyComparableText(normalizedQuestion, normalizedAnswer);
    const comparableBase = simplifyComparableText(normalizedBase, normalizedAnswer);
    if (
      comparableQuestion !== '' &&
      comparableBase !== '' &&
      (
        comparableQuestion === comparableBase ||
        comparableQuestion.includes(comparableBase) ||
        comparableBase.includes(comparableQuestion)
      )
    ) {
      return tx('listening_question_default', 'Listen and type the missing expression.');
    }

    if (normalizedAnswer === '') {
      return normalizedQuestion;
    }

    const escapedAnswer = normalizedAnswer.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const answerRegex = new RegExp(escapedAnswer, 'i');

    if (answerRegex.test(normalizedQuestion)) {
      return tx('listening_question_default', 'Listen and type the missing expression.');
    }

    return normalizedQuestion;
  }

  function renderExercise() {
    const modeData = currentModeData || getModeData(currentMode);
    const items = modeData.items;
    const total = items.length;
    const item = items[currentIndex];
    const nav = document.getElementById('exNavBtns');

    document.getElementById('exPanelTitle').textContent = modeData.title;

    document.getElementById('exProgress').textContent = currentIndex + 1 + ' / ' + total;
    document.getElementById('exProgressBar').style.width = ((currentIndex + 1) / total * 100) + '%';
    updateNextButtonLabel(total);
    if (nav) nav.hidden = false;

    const content = document.getElementById('exerciseContent');
    content.innerHTML = '';
    content.classList.remove('ex-content--enter');
    requestAnimationFrame(() => content.classList.add('ex-content--enter'));

    if (item.type === 'mcq') renderMCQ(content, item);
    else if (item.type === 'fillin') renderFillin(content, item);
    else if (item.type === 'pronounce') renderPronounce(content, item);
    else if (item.type === 'translate') renderTranslate(content, item);
    else if (item.type === 'flashcard') renderFlashcard(content, item);
    else if (item.type === 'match') renderMatching(content, item);
    else if (item.type === 'memory') renderMemory(content, item);
  }

  function nextExercise() {
    stopSpeechAudio();
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

  function renderMCQ(container, item) {
    const readingView = parseReadingPassage(item.passage);
    const scenarioHtml = readingView.scenario ? '<div class="ex-passage-meta"><span class="ex-passage-chip"><i class="bi bi-journal-text"></i> Scenario</span><span class="ex-passage-context"> · ' + readingView.scenario + '</span></div>' : '';
    const sentenceHtml = readingView.sentence ? '<div class="ex-passage ex-passage--sentence">' + readingView.sentence + '</div>' : '<div class="ex-passage">' + item.passage + '</div>';

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-book"></i> ' + t('reading_label') + '</p>' +
      scenarioHtml +
      sentenceHtml +
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
          playUiTone('success');
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
          playIncorrectFeedback(item.type);
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
    const listeningBaseText = resolveListeningBaseText(item);
    const listeningAnswerText = String(item.answer || '').trim();
    const listeningQuestion = sanitizeListeningQuestion(item.question, listeningAnswerText, listeningBaseText);
    const listeningSentenceText = resolveListeningSentenceForGap(listeningBaseText, item.question);
    const expectedAnswerLength = Math.max(4, String(item.answer || '').trim().length || 4);
    const inputWidthCh = Math.max(6, Math.min(34, expectedAnswerLength + 2));
    const inputHtml = '<input class="ex-input" type="text" autocomplete="off" spellcheck="false" style="width:' + inputWidthCh + 'ch">';
    const sentenceWithInput = buildListeningSentenceWithGap(listeningSentenceText, listeningAnswerText, inputHtml);

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-headphones"></i> ' + t('listening_label') + '</p>' +
      '<div class="ex-audio-player">' +
      '<div class="ex-audio-track" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>' +
      '<div class="ex-audio-controls">' +
      '<button class="btn btn-outline-secondary btn-sm ex-audio-toggle" type="button" aria-label="' + tx('play_audio', 'Play audio') + '" title="' + tx('play_audio', 'Play audio') + '"><i class="bi bi-play-fill"></i></button>' +
      '</div>' +
      '</div>' +
      '<button class="ex-transcript-toggle">' + t('show_transcript') + ' <i class="bi ' + t('close_transcript_icon_down') + '"></i></button>' +
      '<div class="ex-transcript" hidden>' + listeningBaseText + '</div>' +
      '<p class="ex-question">' + listeningQuestion + '</p>' +
      '<div class="ex-fillin-wrap">' + sentenceWithInput + '</div>' +
      '<button class="btn btn-outline-secondary ex-check-btn">' + t('check') + '</button>' +
      '<div class="ex-feedback" hidden></div>';

    container.querySelector('.ex-transcript-toggle').addEventListener('click', function () {
      const transcriptEl = container.querySelector('.ex-transcript');
      transcriptEl.hidden = !transcriptEl.hidden;
      this.innerHTML = transcriptEl.hidden
        ? t('show_transcript') + ' <i class="bi ' + t('close_transcript_icon_down') + '"></i>'
        : t('hide_transcript') + ' <i class="bi ' + t('close_transcript_icon_up') + '"></i>';
    });

    const toggleBtn = container.querySelector('.ex-audio-toggle');
    const toggleIcon = toggleBtn ? toggleBtn.querySelector('i') : null;
    const audioSource = normalizeListeningSpeechText(listeningBaseText, listeningAnswerText, item.question);
    const voiceUri = resolveListeningVoiceUri(item);
    const renderIndex = currentIndex;
    const autoPlayDelayMs = 200;
    let isAudioPlaying = false;
    let playbackSessionId = 0;
    let retryTimerId = null;

    const clearRetryTimer = () => {
      if (retryTimerId !== null) {
        clearTimeout(retryTimerId);
        retryTimerId = null;
      }
    };

    const setAudioPlayingState = (playing) => {
      isAudioPlaying = playing;
      if (!toggleBtn || !toggleIcon) return;

      toggleBtn.classList.toggle('is-playing', playing);
      toggleBtn.setAttribute('aria-label', playing ? tx('stop_audio', 'Stop audio') : tx('play_audio', 'Play audio'));
      toggleBtn.setAttribute('title', playing ? tx('stop_audio', 'Stop audio') : tx('play_audio', 'Play audio'));
      toggleIcon.className = playing ? 'bi bi-stop-fill' : 'bi bi-play-fill';
    };

    const stopListeningAudio = () => {
      playbackSessionId += 1;
      clearRetryTimer();
      if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
      }
      setAudioPlayingState(false);
    };

    activeListeningStopHandler = stopListeningAudio;

    const playListeningAudio = (allowVoiceFallback = true, sessionId = null) => {
      const activeSessionId = sessionId === null ? (playbackSessionId + 1) : sessionId;
      playbackSessionId = activeSessionId;
      clearRetryTimer();
      setAudioPlayingState(true);
      const selectedVoiceUri = allowVoiceFallback ? voiceUri : null;
      const utterance = speakTranscript(audioSource, selectedVoiceUri, {
        onEnd: () => {
          if (activeSessionId !== playbackSessionId) return;
          setAudioPlayingState(false);
        },
        onError: () => {
          if (activeSessionId !== playbackSessionId) return;
          if (allowVoiceFallback) {
            clearRetryTimer();
            retryTimerId = window.setTimeout(() => {
              if (activeSessionId !== playbackSessionId) return;
              playListeningAudio(false, activeSessionId);
            }, 140);
            return;
          }
          setAudioPlayingState(false);
        },
      });

      if (!utterance) {
        if (activeSessionId !== playbackSessionId) return;
        if (allowVoiceFallback) {
          playListeningAudio(false, activeSessionId);
          return;
        }
        setAudioPlayingState(false);
      }
    };

    const startAutoPlay = () => {
      if (autoPlayedListeningIndexes.has(renderIndex)) {
        return;
      }

      autoPlayedListeningIndexes.add(renderIndex);

      window.setTimeout(() => {
        if (currentMode !== 'listening' || currentIndex !== renderIndex) return;
        playListeningAudio();
      }, autoPlayDelayMs);
    };

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        if (isAudioPlaying) {
          stopListeningAudio();
          return;
        }

        playListeningAudio();
      });
    }

    const inputEl = container.querySelector('.ex-input');
    const checkBtn = container.querySelector('.ex-check-btn');

    if (!inputEl || !checkBtn) {
      return;
    }

    checkBtn.addEventListener('click', () => {
      stopListeningAudio();
      const val = inputEl.value.trim().toLowerCase();
      const feedback = container.querySelector('.ex-feedback');
      if (val === item.answer.toLowerCase()) {
        playUiTone('success');
        feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + t('correct');
        feedback.className = 'ex-feedback ex-feedback--ok';
        markExerciseItemResult(container, true, {
          itemId: item.itemId,
          itemType: item.type,
          prompt: listeningQuestion,
          expectedAnswer: item.answer,
          answerText: val,
          feedback: t('correct'),
        });
      } else {
        playIncorrectFeedback(item.type);
        feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + t('answer_is', { answer: '<strong>"' + item.answer + '"</strong>' });
        feedback.className = 'ex-feedback ex-feedback--err';
        markExerciseItemResult(container, false, {
          itemId: item.itemId,
          itemType: item.type,
          prompt: listeningQuestion,
          expectedAnswer: item.answer,
          answerText: val,
          feedback: t('incorrect'),
        });
      }
      feedback.hidden = false;
    });

    inputEl.addEventListener('keydown', e => {
      if (e.key === 'Enter') checkBtn.click();
    });

    startAutoPlay();
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
        playUiTone('neutral');
        icon.className = 'bi bi-stop-fill';
        label.textContent = t('recording');
      } else {
        playUiTone('neutral');
        icon.className = 'bi bi-mic';
        label.textContent = t('press_to_speak');
        selfCheck.hidden = false;
      }
    });

    container.querySelector('.ex-self-yes').addEventListener('click', () => {
      playUiTone('success');
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
      playUiTone('neutral');
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
      '<button class="btn btn-outline-secondary ex-check-btn">' + t('check') + '</button>' +
      '<div class="ex-feedback" hidden></div>';

    container.querySelector('.ex-check-btn').addEventListener('click', () => {
      const val = container.querySelector('.ex-textarea').value.trim().toLowerCase();
      const feedback = container.querySelector('.ex-feedback');
      const keywords = item.answer.toLowerCase().split(' ').filter(w => w.length > 3);
      const matches = keywords.filter(k => val.includes(k)).length;
      if (val.length > 0 && matches >= Math.ceil(keywords.length * 0.65)) {
        playUiTone('success');
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
        playUiTone('neutral');
        feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + t('write_answer_first');
        feedback.className = 'ex-feedback ex-feedback--warn';
      } else {
        playUiTone('error');
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
      playUiTone('neutral');
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
      playUiTone('success');
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
    const solvedPairs = [];
    let selectedLeft = null;
    let selectedRight = null;
    let mistakes = 0;
    let gameFinished = false;
    let timerId = null;
    let selectedLeftButton = null;
    let selectedRightButton = null;

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
      '<div class="ex-feedback" hidden></div>';

    const leftColumn = container.querySelector('[data-column="left"]');
    const rightColumn = container.querySelector('[data-column="right"]');
    const feedback = container.querySelector('.ex-feedback');
    const timerEl = container.querySelector('.ex-match-timer');
    const countEl = container.querySelector('.ex-match-count');

    const flashMismatch = (leftBtn, rightBtn) => {
      [leftBtn, rightBtn].forEach(node => {
        if (!node) return;
        node.classList.remove('is-mismatch');
        void node.offsetWidth;
        node.classList.add('is-mismatch');
        window.setTimeout(() => node.classList.remove('is-mismatch'), 240);
      });
    };

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
        playUiTone('success');
        feedback.innerHTML = '<i class="bi bi-trophy-fill"></i> ' + tx('matching_success', 'Great speed. Challenge completed.');
        feedback.className = 'ex-feedback ex-feedback--trophy';
      }

      feedback.hidden = false;
      markExerciseItemResult(container, success, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.question,
        expectedAnswer: JSON.stringify(pairs),
        answerText: JSON.stringify(solvedPairs),
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

    const syncPairCount = () => {
      if (countEl) {
        countEl.textContent = matchedLeft.size + '/' + totalPairs;
      }
    };

    const selectLeft = (button, value) => {
      if (matchedLeft.has(value)) return;
      selectedLeft = value;
      selectedLeftButton = button;
      leftColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      button.classList.add('is-selected');
      evaluateSelection();
    };

    const selectRight = (button, value) => {
      if (matchedRight.has(value)) return;
      selectedRight = value;
      selectedRightButton = button;
      rightColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      button.classList.add('is-selected');
      evaluateSelection();
    };

    const evaluateSelection = () => {
      if (gameFinished) return;
      if (!selectedLeft || !selectedRight) return;

      const isCorrectMatch = expected.get(selectedLeft) === selectedRight;
      if (isCorrectMatch) {
        playUiTone('pair');
        matchedLeft.add(selectedLeft);
        matchedRight.add(selectedRight);
        solvedPairs.push({ left: selectedLeft, right: selectedRight });
      } else {
        flashMismatch(selectedLeftButton, selectedRightButton);
        mistakes += 1;
      }

      feedback.hidden = true;
      selectedLeft = null;
      selectedRight = null;
      selectedLeftButton = null;
      selectedRightButton = null;
      leftColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      rightColumn.querySelectorAll('.ex-match-btn').forEach(node => node.classList.remove('is-selected'));
      paintMatchedButtons();
      syncPairCount();

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

    syncPairCount();
    updateTimer();
    timerId = window.setInterval(updateTimer, 250);
  }

  function renderMemory(container, item) {
    const sourcePairs = Array.isArray(item.pairs) ? item.pairs : [];
    const previewMs = Math.max(500, Math.min(2200, Number(item.preview_ms) || 900));
    const requestedColumns = Number(item.grid_columns) || 0;
    const columns = Math.max(3, Math.min(5, requestedColumns || (sourcePairs.length >= 10 ? 5 : 4)));
    const maxPairs = columns === 5 ? 10 : 8;

    const pairs = sourcePairs
      .filter(pair => pair && pair.front && pair.back)
      .slice(0, maxPairs);

    const cards = shuffleArray(pairs.flatMap((pair, index) => ([
      { id: 'f-' + index, pairId: index, text: pair.front },
      { id: 'b-' + index, pairId: index, text: pair.back },
    ])));

    const singleRevealMs = Math.max(1000, Math.min(2200, previewMs));
    const mismatchRevealMs = Math.max(1400, Math.min(2400, previewMs + 700));
    let activePair = [];
    let resolvingMismatch = false;
    let mismatchTimeout = null;
    let singleRevealTimeout = null;
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

    const longestCardTextLength = cards.reduce((maxLen, card) => Math.max(maxLen, String(card.text || '').trim().length), 0);
    const baseCardHeight = columns === 5 ? 120 : 112;
    const extraHeight = Math.max(0, Math.ceil((longestCardTextLength - 18) / 8) * 10);
    const adaptiveCardHeight = Math.min(190, baseCardHeight + extraHeight);
    const adaptiveFontSize = longestCardTextLength > 42 ? 0.78 : (columns === 5 ? 0.8 : 0.86);

    if (board) {
      board.style.setProperty('--memory-card-min-height', adaptiveCardHeight + 'px');
      board.style.setProperty('--memory-card-font-size', adaptiveFontSize + 'rem');
    }

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

    const hideActivePair = () => {
      activePair.forEach((id) => {
        const state = cardState.get(id);
        if (state && !state.matched) {
          state.revealed = false;
        }
      });

      activePair = [];
      resolvingMismatch = false;
      if (mismatchTimeout !== null) {
        clearTimeout(mismatchTimeout);
        mismatchTimeout = null;
      }
      if (singleRevealTimeout !== null) {
        clearTimeout(singleRevealTimeout);
        singleRevealTimeout = null;
      }
      renderCards();
    };

    const finishMemory = () => {
      const elapsedSeconds = Math.max(1, Math.round((Date.now() - startedAt) / 1000));
      const score = Math.max(0, Math.round(100 - ((moves - pairs.length) * 6)));
      const success = matchedPairs.size === pairs.length;

      if (success) {
        playUiTone('success');
      }
      feedback.innerHTML = '<i class="bi bi-trophy-fill"></i> ' + tx('memory_success', 'All pairs completed. Great focus.');
      feedback.className = 'ex-feedback ex-feedback--trophy';
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
      if (resolvingMismatch && activePair.length === 2) {
        hideActivePair();
        return;
      }

      const state = cardState.get(cardId);
      if (!state || state.matched || state.revealed) return;

      setCardRevealed(cardId, true);

      if (!activePair.length) {
        activePair = [cardId];

        if (singleRevealTimeout !== null) {
          clearTimeout(singleRevealTimeout);
          singleRevealTimeout = null;
        }

        singleRevealTimeout = window.setTimeout(() => {
          // Auto-hide a lone revealed card when its time expires.
          if (activePair.length === 1 && activePair[0] === cardId && !resolvingMismatch) {
            hideActivePair();
          }
        }, singleRevealMs);
        return;
      }

      if (activePair[0] === cardId) {
        return;
      }

      if (activePair.length >= 2) {
        hideActivePair();
        return;
      }

      activePair.push(cardId);

      if (singleRevealTimeout !== null) {
        clearTimeout(singleRevealTimeout);
        singleRevealTimeout = null;
      }

      moves += 1;
      syncMeta();

      const firstId = activePair[0];
      const first = cardState.get(firstId);
      const second = cardState.get(activePair[1]);

      if (!first || !second) return;

      if (first.pairId === second.pairId) {
        playUiTone('success');
        first.matched = true;
        second.matched = true;
        first.revealed = true;
        second.revealed = true;
        matchedPairs.add(first.pairId);
        activePair = [];
        resolvingMismatch = false;
        renderCards();
        syncMeta();

        if (matchedPairs.size === pairs.length) {
          finishMemory();
        }
        return;
      }

      resolvingMismatch = true;
      mismatchTimeout = window.setTimeout(() => {
        hideActivePair();
      }, mismatchRevealMs);
    };

    renderCards();
    syncMeta();
  }

  function showCompletion() {
    stopSpeechAudio();
    playUiTone('complete');
    registerExerciseAttempt();

    const content = document.getElementById('exerciseContent');
    const nav = document.getElementById('exNavBtns');
    if (nav) nav.hidden = true;

    content.innerHTML =
      '<section class="ex-complete-inline is-visible" aria-live="polite">' +
        '<div class="ex-complete-bg ex-complete-bg--main" aria-hidden="true">' +
          '<img src="/images/ui/completion-celebration.gif" alt="" loading="lazy" decoding="async">' +
        '</div>' +
        '<div class="ex-complete-bg ex-complete-bg--cannon" aria-hidden="true">' +
          '<img src="/images/ui/Confetti_Cannon.gif" alt="" loading="lazy" decoding="async">' +
        '</div>' +
        '<div class="ex-complete-content">' +
          '<h2>' + tx('completed', '¡Seccion completada!') + '</h2>' +
          '<div class="ex-complete-actions" style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">' +
            '<button class="btn btn-outline-secondary" id="btnRepeatInline">' + tx('repeat', 'Repetir') + '</button>' +
            '<button class="btn btn-primary" id="btnBackInline">' + tx('choose_other_mode', 'Elegir otro modo') + '</button>' +
          '</div>' +
        '</div>' +
      '</section>';

    const completeSection = content.querySelector('.ex-complete-inline');
    if (completeSection) {
      // Show title/buttons only after GIF phase ends.
      window.setTimeout(() => {
        completeSection.classList.add('is-content-visible');

        // GIF does not support CSS loop control; hide layers after one visible cycle.
        completeSection.querySelectorAll('.ex-complete-bg').forEach(layer => {
          layer.classList.add('is-stopped');
        });
      }, 3200);
    }

    const repeatBtn = document.getElementById('btnRepeatInline');
    if (repeatBtn) {
      repeatBtn.addEventListener('click', () => {
        stopSpeechAudio();
        currentIndex = 0;
        if (nav) nav.hidden = false;
        renderExercise();
      });
    }

    const backBtn = document.getElementById('btnBackInline');
    if (backBtn) {
      backBtn.addEventListener('click', () => {
        stopSpeechAudio();
        if (nav) nav.hidden = false;
        document.getElementById('exercisePanel').hidden = true;
        document.getElementById('exerciseMenu').hidden = false;
      });
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
})();
</script>
@endsection
