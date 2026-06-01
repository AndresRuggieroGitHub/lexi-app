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
        <div class="exercise-source-notice" id="exerciseSourceNotice" hidden aria-live="polite">
          <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
          <span id="exerciseSourceNoticeText"></span>
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
  const JS_I18N = (window.lexiTranslations && window.lexiTranslations.js) || {};
  const EXERCISES_GROUP_I18N = (window.lexiTranslations && window.lexiTranslations.exercises) || {};
  const CATEGORY_I18N = (window.lexiTranslations && window.lexiTranslations.categories) || {};
  const t = (key, replacements = {}) => {
    const template = EXERCISE_I18N[key];
    if (typeof template !== 'string') return key;

    return template.replace(/:([a-zA-Z_]+)/g, (_, token) => replacements[token] ?? `:${token}`);
  };
  const tx = (key, fallback) => {
    const value = t(key);
    return value === key ? fallback : value;
  };
  const te = (key, fallback) => {
    const value = EXERCISES_GROUP_I18N[key];
    return typeof value === 'string' && value.trim() !== '' ? value : fallback;
  };
  const sanitizeRuntimeText = (value) => {
    let text = String(value || '');
    text = text.replace(/```+/g, '');
    text = text.replace(/^\s{0,3}#{1,6}\s*/gm, '');
    text = text.replace(/\*\*(.*?)\*\*/g, '$1');
    text = text.replace(/__(.*?)__/g, '$1');
    text = text.replace(/`([^`]+)`/g, '$1');
    return text.replace(/\s+/g, ' ').trim();
  };
  const isLikelyEnglishRuntimeText = (value) => {
    const text = String(value || '').toLowerCase();
    if (!text) return false;
    return /(choose|option|sentence|scenario|correct|answer|gap|draft|proposal|aligns|strategic|priorities|before|should|team)/.test(text);
  };
  const getUiLocale = () => {
    const fromWindow = String(window.lexiUiLocale || '').trim().toLowerCase();
    if (fromWindow) return fromWindow.split('-')[0];

    const fromSession = String(window.lexiSessionState?.user?.mother_tongue_code || '').trim().toLowerCase();
    if (fromSession) return fromSession.split('-')[0];

    return String(document.documentElement.lang || 'en').toLowerCase().split('-')[0];
  };
  const isNonEnglishUiLocale = () => getUiLocale() !== 'en';
  const UI_RUNTIME_FALLBACKS = @json((function () {
    $fallbacks = [
        'en' => [
            'loading_exercise' => 'Preparing exercises...',
            'loading_options' => 'Loading options...',
            'loading_saved_lists' => 'Loading your lists...',
            'loading_catalog' => 'Loading catalog...',
            'all_levels' => 'All levels',
            'all_categories' => 'All categories',
            'all_collections' => 'All collections',
            'catalog_name' => 'Catalog',
            'saved_name' => 'Saved list',
            'saved_lists_name' => 'Your lists',
            'listening_question_default' => 'Listen and type the missing expression.',
            'translate_to_english' => 'Translate to English.',
            'write_placeholder' => 'Write your translation here...',
            'matching_question' => 'Match each word with the correct translation.',
            'memory_question' => 'Find all translation pairs with the fewest moves.',
            'matching_label' => 'Matching',
            'memory_label' => 'Memory',
            'check' => 'Check',
            'speaking_time' => 'Time',
            'speaking_listening' => 'Listening...',
            'speaking_transcript_label' => 'Transcript',
            'speaking_not_supported' => 'Voice transcription is not available in this browser.',
            'speaking_no_speech' => 'No speech was detected. Try again.',
            'pairs_label' => 'Pairs',
            'moves_label' => 'Moves',
            'matching_timeout' => 'Time is over. Try again to beat the clock.',
            'matching_success' => 'Great speed. Challenge completed.',
            'memory_success' => 'All pairs completed. Great focus.',
        ],
        'es' => [
            'loading_exercise' => 'Preparando ejercicios...',
            'loading_options' => 'Cargando opciones...',
            'loading_saved_lists' => 'Cargando tus listas...',
            'loading_catalog' => 'Cargando catalogo...',
            'all_levels' => 'Todos los niveles',
            'all_categories' => 'Todas las categorias',
            'all_collections' => 'Todas las listas',
            'catalog_name' => 'Catalogo',
            'saved_name' => 'Lista guardada',
            'saved_lists_name' => 'Tus listas',
            'listening_question_default' => 'Escucha y escribe la expresion que falta.',
            'translate_to_english' => 'Traduce al ingles.',
            'write_placeholder' => 'Escribe tu traduccion aqui...',
            'matching_question' => 'Conecta cada palabra con su traduccion correcta.',
            'memory_question' => 'Encuentra todas las parejas de traduccion con el menor numero de movimientos.',
            'matching_label' => 'Conectar columnas',
            'memory_label' => 'Memoria',
            'check' => 'Comprobar',
            'speaking_time' => 'Tiempo',
            'speaking_listening' => 'Escuchando...',
            'speaking_transcript_label' => 'Transcripcion',
            'speaking_not_supported' => 'La transcripcion de voz no esta disponible en este navegador.',
            'speaking_no_speech' => 'No se detecto voz. Intentalo de nuevo.',
            'pairs_label' => 'Parejas',
            'moves_label' => 'Movimientos',
            'matching_timeout' => 'Se acabo el tiempo. Intentalo de nuevo.',
            'matching_success' => 'Gran velocidad. Desafio completado.',
            'memory_success' => 'Todas las parejas completadas. Gran concentracion.',
        ],
        'fr' => [
            'loading_exercise' => 'Preparation des exercices...',
            'loading_options' => 'Chargement des options...',
            'loading_saved_lists' => 'Chargement de tes listes...',
            'loading_catalog' => 'Chargement du catalogue...',
            'all_levels' => 'Tous les niveaux',
            'all_categories' => 'Toutes les categories',
            'all_collections' => 'Toutes les listes',
            'catalog_name' => 'Catalogue',
            'saved_name' => 'Liste enregistree',
            'saved_lists_name' => 'Tes listes',
            'listening_question_default' => 'Ecoute et ecris lexpression manquante.',
            'translate_to_english' => 'Traduis en anglais.',
            'write_placeholder' => 'Ecris ta traduction ici...',
            'matching_question' => 'Associe chaque mot a la bonne traduction.',
            'memory_question' => 'Trouve toutes les paires de traduction avec le moins de mouvements.',
            'matching_label' => 'Correspondance',
            'memory_label' => 'Memoire',
            'check' => 'Verifier',
            'speaking_time' => 'Temps',
            'speaking_listening' => 'Ecoute en cours...',
            'speaking_transcript_label' => 'Transcription',
            'speaking_not_supported' => 'La transcription vocale nest pas disponible dans ce navigateur.',
            'speaking_no_speech' => 'Aucune voix detectee. Reessaie.',
            'pairs_label' => 'Paires',
            'moves_label' => 'Mouvements',
            'matching_timeout' => 'Le temps est ecoule. Reessaie.',
            'matching_success' => 'Excellente vitesse. Defi reussi.',
            'memory_success' => 'Toutes les paires sont completees. Excellent focus.',
        ],
        'de' => [
            'loading_exercise' => 'Ubungen werden vorbereitet...',
            'loading_options' => 'Optionen werden geladen...',
            'loading_saved_lists' => 'Deine Listen werden geladen...',
            'loading_catalog' => 'Katalog wird geladen...',
            'all_levels' => 'Alle Niveaus',
            'all_categories' => 'Alle Kategorien',
            'all_collections' => 'Alle Listen',
            'catalog_name' => 'Katalog',
            'saved_name' => 'Gespeicherte Liste',
            'saved_lists_name' => 'Deine Listen',
            'listening_question_default' => 'Hore zu und schreibe den fehlenden Ausdruck.',
            'translate_to_english' => 'Ubersetze ins Englische.',
            'write_placeholder' => 'Schreibe deine Ubersetzung hier...',
            'matching_question' => 'Ordne jedes Wort der richtigen Ubersetzung zu.',
            'memory_question' => 'Finde alle Ubersetzungspaare mit moglichst wenigen Zugen.',
            'matching_label' => 'Zuordnen',
            'memory_label' => 'Memory',
            'check' => 'Prufen',
            'speaking_time' => 'Zeit',
            'speaking_listening' => 'Aufnahme lauft...',
            'speaking_transcript_label' => 'Transkript',
            'speaking_not_supported' => 'Spracherkennung ist in diesem Browser nicht verfugbar.',
            'speaking_no_speech' => 'Keine Sprache erkannt. Bitte erneut versuchen.',
            'pairs_label' => 'Paare',
            'moves_label' => 'Zuge',
            'matching_timeout' => 'Die Zeit ist abgelaufen. Versuche es erneut.',
            'matching_success' => 'Starkes Tempo. Herausforderung geschafft.',
            'memory_success' => 'Alle Paare gefunden. Sehr gut.',
        ],
    ];

    $activeLocale = strtolower((string) app()->getLocale());

    return [
        'en' => $fallbacks['en'],
        $activeLocale => $fallbacks[$activeLocale] ?? $fallbacks['en'],
    ];
})());
  const uiFallback = (key, fallback = '') => {
    const locale = getUiLocale();
    const table = UI_RUNTIME_FALLBACKS[locale] || UI_RUNTIME_FALLBACKS.en;
    const candidate = table && typeof table[key] === 'string' ? table[key] : '';
    if (candidate.trim() !== '') return candidate;
    return fallback;
  };
  const normalizeLooseText = (value) => String(value || '').trim().toLowerCase();
  const isEnglishUiLeakForKey = (key, value) => {
    if (getUiLocale() === 'en') return false;

    const english = normalizeLooseText(UI_RUNTIME_FALLBACKS.en?.[key] || '');
    const candidate = normalizeLooseText(value);
    if (!english || !candidate) return false;

    return candidate === english;
  };
  const resolveUiChoiceLabel = (key, fallback = '') => {
    const candidate = sanitizeRuntimeText(tx(key, uiFallback(key, fallback)));
    if (candidate && !isEnglishUiLeakForKey(key, candidate)) {
      return candidate;
    }

    return sanitizeRuntimeText(uiFallback(key, fallback));
  };
  const resolveExerciseRuntimeText = (key, fallback = '') => {
    const keyValue = sanitizeRuntimeText(t(key));
    if (keyValue && keyValue !== key && (!isNonEnglishUiLocale() || !isLikelyEnglishRuntimeText(keyValue))) {
      return keyValue;
    }

    const fallbackValue = sanitizeRuntimeText(fallback);
    if (!isNonEnglishUiLocale() || !isLikelyEnglishRuntimeText(fallbackValue)) {
      return fallbackValue;
    }

    return '';
  };
  const resolveExerciseRuntimeTextRelaxed = (key, fallback = '') => {
    const strictValue = resolveExerciseRuntimeText(key, fallback);
    if (strictValue) return strictValue;

    const keyValue = sanitizeRuntimeText(tx(key, uiFallback(key, fallback)));
    if (keyValue) return keyValue;

    return sanitizeRuntimeText(uiFallback(key, fallback));
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
  let serverVocabularyState = { library: { id: 'library', name: resolveExerciseRuntimeTextRelaxed('saved_name', uiFallback('saved_name', 'Saved list')), items: [] }, collections: [], catalog: [] };
  let serverVocabularyLanguage = null;
  let exerciseSourceSwitchToken = 0;

  function setExerciseCollectionsLoading(isLoading, message = resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...'))) {
    const select = document.getElementById('exerciseCollectionSelect');
    const levelSelect = document.getElementById('exerciseCatalogLevelSelect');
    const topicSelect = document.getElementById('exerciseCatalogTopicSelect');
    const loading = document.getElementById('exerciseSourceLoading');
    const loadingText = document.getElementById('exerciseSourceLoadingText');

    if (loading) loading.hidden = !isLoading;
    if (loadingText) loadingText.textContent = message;

    if (isLoading) {
      if (select) {
        select.innerHTML = '<option selected disabled>' + t('loading_lists') + '</option>';
        select.disabled = true;
      }
      if (levelSelect) levelSelect.disabled = true;
      if (topicSelect) topicSelect.disabled = true;
      return;
    }

    if (select) select.disabled = false;
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
        const labels = resolveModeLabelsForLanguage(getUiLocale());
        return tx('challenge_label', labels[4] || 'Challenge');
      }

      const modeIndex = BADGE_MODES.indexOf(mode);
      if (modeIndex === -1) {
        return mode;
      }

      const labels = resolveModeLabelsForLanguage(getUiLocale());
      return labels[modeIndex] || mode;
    }

  function extractTopicEmoji(label) {
    const source = String(label || '').trim();
    const match = source.match(/^([^\p{L}\p{N}\s]+)/u);
    return match ? match[1] : '';
  }

  function stripTopicEmoji(label) {
    return String(label || '').replace(/^([^\p{L}\p{N}\s]+\s*)+/u, '').trim();
  }

  const TOPIC_OPTION_EMOJIS = SHARED_TOPIC_OPTIONS.reduce((labels, topic) => {
    const key = normalizeTopicKey(topic.value);
    labels[key] = extractTopicEmoji(topic.label);
    return labels;
  }, {});

  const EXERCISE_TOPIC_FALLBACKS = {
    en: {
      travel: 'Travel', food: 'Food', work: 'Work', business: 'Business', education: 'Education',
      health: 'Health', science: 'Science', technology: 'Technology', culture: 'Culture', social: 'Social',
      home: 'Home', nature: 'Nature', politics: 'Politics', sport: 'Sport', art: 'Art',
      media: 'Media', law: 'Law', finance: 'Finance'
    },
    es: {
      travel: 'Viajes', food: 'Gastronomia', work: 'Trabajo', business: 'Negocios', education: 'Educacion',
      health: 'Salud', science: 'Ciencia', technology: 'Tecnologia', culture: 'Cultura', social: 'Social',
      home: 'Hogar', nature: 'Naturaleza', politics: 'Politica', sport: 'Deporte', art: 'Arte',
      media: 'Medios', law: 'Derecho', finance: 'Finanzas'
    },
    fr: {
      travel: 'Voyages', food: 'Gastronomie', work: 'Travail', business: 'Affaires', education: 'Education',
      health: 'Sante', science: 'Science', technology: 'Technologie', culture: 'Culture', social: 'Social',
      home: 'Maison', nature: 'Nature', politics: 'Politique', sport: 'Sport', art: 'Art',
      media: 'Medias', law: 'Droit', finance: 'Finance'
    },
    de: {
      travel: 'Reisen', food: 'Essen', work: 'Arbeit', business: 'Wirtschaft', education: 'Bildung',
      health: 'Gesundheit', science: 'Wissenschaft', technology: 'Technologie', culture: 'Kultur', social: 'Soziales',
      home: 'Zuhause', nature: 'Natur', politics: 'Politik', sport: 'Sport', art: 'Kunst',
      media: 'Medien', law: 'Recht', finance: 'Finanzen'
    }
  };

  function getExerciseTopicFallbackLabel(topicKey) {
    const locale = getUiLocale();
    const table = EXERCISE_TOPIC_FALLBACKS[locale] || EXERCISE_TOPIC_FALLBACKS.en;
    const value = table && typeof table[topicKey] === 'string' ? table[topicKey] : '';
    return value.trim();
  }
  function isEnglishExerciseCategoryLeak(topicKey, value) {
    if (getUiLocale() === 'en') return false;

    const english = normalizeLooseText(EXERCISE_TOPIC_FALLBACKS.en?.[topicKey] || '');
    const candidate = normalizeLooseText(value);
    if (!english || !candidate) return false;

    return candidate === english;
  }

  const TOPIC_OPTION_LABELS = SHARED_TOPIC_OPTIONS.reduce((labels, topic) => {
    const key = normalizeTopicKey(topic.value);
    const translated = CATEGORY_I18N[key];
    const fallbackLabel = getExerciseTopicFallbackLabel(key);
    labels[key] = typeof translated === 'string' && translated.trim() !== ''
      && !isEnglishExerciseCategoryLeak(key, translated)
      ? translated
      : (fallbackLabel || stripTopicEmoji(String(topic.label || key)).trim());
    return labels;
  }, {});
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

  function getTopicLabel(topicValue) {
    const key = normalizeTopicKey(topicValue);
    const translatedLabel = TOPIC_OPTION_LABELS[key] || '';
    const fallbackLabel = stripTopicEmoji(String(topicValue || '').replace(/[_-]+/g, ' ')).trim();
    const label = (translatedLabel || fallbackLabel || key || '').trim();
    const emoji = TOPIC_OPTION_EMOJIS[key] || '';
    return emoji ? (emoji + ' ' + label).trim() : label;
  }

  function getTopicOptions() {
    return SHARED_TOPIC_OPTIONS;
  }

  async function loadVocabularySources(message = resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...')), showSpinner = false, forceReload = false) {
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
          name: resolveExerciseRuntimeTextRelaxed('saved_name', uiFallback('saved_name', 'Saved list')),
          items: Array.isArray(payload.items) ? payload.items : [],
        },
        collections: Array.isArray(payload.collections) ? payload.collections : [],
        catalog: Array.isArray(payload.catalog) ? payload.catalog : [],
      };
      serverVocabularyLanguage = activeLang;
    } catch {
      serverVocabularyState = {
        library: { id: 'library', name: resolveExerciseRuntimeTextRelaxed('saved_name', uiFallback('saved_name', 'Saved list')), items: [] },
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
        name: resolveExerciseRuntimeTextRelaxed('saved_name', uiFallback('saved_name', 'Saved list')),
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
      name: [selectedLevel, selectedTopic].filter(Boolean).join(' · ') || resolveUiChoiceLabel('catalog_name', 'Catalog'),
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
        name: resolveExerciseRuntimeTextRelaxed('all_collections', uiFallback('all_collections', 'All collections')),
        items: aggregateSavedItems(library, collections),
      };
    }
    return collections.find(collection => String(collection.id) === selectedId) || (selectedId === 'library' ? library : { id: 'saved', name: resolveExerciseRuntimeTextRelaxed('saved_lists_name', uiFallback('saved_lists_name', 'Your lists')), items: [] });
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

  function setExerciseSourceSwitching(isSwitching, sourceType = getSelectedSourceType()) {
    document.querySelectorAll('[data-source-tab]').forEach(button => {
      button.disabled = isSwitching;
      button.classList.toggle('is-loading', isSwitching && button.dataset.sourceTab === sourceType);
    });

    if (isSwitching) {
      const message = sourceType === 'saved'
        ? resolveExerciseRuntimeTextRelaxed('loading_saved_lists', uiFallback('loading_saved_lists', 'Loading your lists...'))
        : resolveExerciseRuntimeTextRelaxed('loading_catalog', uiFallback('loading_catalog', 'Loading catalog...'));
      setExerciseCollectionsLoading(true, message);
    } else {
      setExerciseCollectionsLoading(false);
    }
  }

  function setExerciseSourceNotice(message = '') {
    const notice = document.getElementById('exerciseSourceNotice');
    const noticeText = document.getElementById('exerciseSourceNoticeText');

    if (!notice || !noticeText) return;

    const hasMessage = String(message || '').trim() !== '';
    notice.hidden = !hasMessage;
    noticeText.textContent = hasMessage ? String(message) : '';
  }

  function setExerciseCardEnabled(mode, isEnabled) {
    const card = document.querySelector('.exercise-card[data-mode="' + mode + '"]');
    if (!card) return;

    card.classList.toggle('is-disabled', !isEnabled);
    card.setAttribute('aria-disabled', isEnabled ? 'false' : 'true');
    card.tabIndex = isEnabled ? 0 : -1;
  }

  function getSourceModeAvailability() {
    const sourceType = getSelectedSourceType();
    const source = getSelectedVocabularySource();
    const count = Array.isArray(source?.items) ? source.items.length : 0;
    const modeAvailability = {
      reading: count > 0,
      listening: count > 0,
      speaking: count > 0,
      writing: count > 0,
      mix: count > 0,
    };

    if (sourceType === 'saved' && count > 0 && count < 4) {
      modeAvailability.mix = false;
    }

    return { sourceType, count, modeAvailability };
  }

  function updateExerciseSourceEligibility(mode = null) {
    const { sourceType, count, modeAvailability } = getSourceModeAvailability();

    Object.entries(modeAvailability).forEach(([modeKey, isEnabled]) => {
      setExerciseCardEnabled(modeKey, isEnabled);
    });

    if (count <= 0) {
      if (sourceType === 'saved') {
        setExerciseSourceNotice(tx('source_saved_empty', 'This list has no words yet. Add vocabulary to practice.'));
      } else {
        setExerciseSourceNotice(tx('source_catalog_empty', 'There are no words for the current catalog filters.'));
      }
    } else if (sourceType === 'saved' && count < 4) {
      setExerciseSourceNotice(tx('mix_needs_four_words', 'Challenge mode is enabled when this list has at least 4 words.'));
    } else {
      setExerciseSourceNotice('');
    }

    if (mode) {
      return Boolean(modeAvailability[mode]);
    }

    return Object.values(modeAvailability).some(Boolean);
  }

  function setupExerciseSourceTabs() {
    const toggleRoot = document.querySelector('.exercise-source-toggle');
    if (!toggleRoot || toggleRoot.dataset.bound === '1') {
      syncSourcePanels();
      updateExerciseSourceEligibility();
      return;
    }

    const handleSourceTabClick = async (event) => {
      const trigger = event.target.closest('[data-source-tab]');
      if (!trigger || !toggleRoot.contains(trigger)) return;

      event.preventDefault();

      const nextSource = trigger.dataset.sourceTab;
      if (!nextSource) return;

      const currentSource = getSelectedSourceType();
      localStorage.setItem(EXERCISE_SOURCE_KEY, nextSource);
      if (nextSource === 'saved' && !localStorage.getItem(EXERCISE_COLLECTION_KEY)) {
        localStorage.setItem(EXERCISE_COLLECTION_KEY, 'all_saved');
      }

      syncSourcePanels();

      const requestToken = ++exerciseSourceSwitchToken;
      setExerciseSourceSwitching(true, nextSource);

      try {
        if (nextSource !== currentSource) {
          await loadVocabularySources(nextSource === 'saved' ? t('loading_saved_lists') : t('loading_catalog'), true);
        }

        if (requestToken !== exerciseSourceSwitchToken) {
          return;
        }

        setupExerciseCatalogSelects();
        setupExerciseCollectionSelect();
        syncSourcePanels();
        updateExerciseSourceEligibility();
      } catch {
        if (requestToken === exerciseSourceSwitchToken) {
          setupExerciseCatalogSelects();
          setupExerciseCollectionSelect();
          syncSourcePanels();
          updateExerciseSourceEligibility();
        }
      } finally {
        if (requestToken === exerciseSourceSwitchToken) {
          setExerciseSourceSwitching(false, nextSource);
        }
      }
    };

    toggleRoot.addEventListener('click', handleSourceTabClick);
    toggleRoot.dataset.bound = '1';

    syncSourcePanels();
    updateExerciseSourceEligibility();
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
    levelSelect.innerHTML = '<option value="">' + resolveUiChoiceLabel('all_levels', 'All levels') + '</option>' + catalogSource.levels.map(level => '<option value="' + level + '">' + levelOptionLabel(level) + '</option>').join('');
    topicSelect.innerHTML = '<option value="">' + resolveUiChoiceLabel('all_categories', 'All categories') + '</option>' + getTopicOptions().map(topic => '<option value="' + topic.value + '">' + getTopicLabel(topic.value) + '</option>').join('');

    levelSelect.value = catalogSource.selectedLevel;
    topicSelect.value = catalogSource.selectedTopic;

    levelSelect.onchange = () => {
      localStorage.setItem(EXERCISE_CATALOG_LEVEL_KEY, levelSelect.value);
      updateExerciseSourceEligibility();
    };
    topicSelect.onchange = () => {
      localStorage.setItem(EXERCISE_CATALOG_TOPIC_KEY, topicSelect.value);
      updateExerciseSourceEligibility();
    };
  }

  function setupExerciseCollectionSelect() {
    const select = document.getElementById('exerciseCollectionSelect');
    if (!select) return;

    const { library, collections } = getVocabularySources();
    const selectedId = localStorage.getItem(EXERCISE_COLLECTION_KEY) || 'all_saved';
    const options = [library, ...collections];
    const allCount = aggregateSavedItems(library, collections).length;

    select.innerHTML = '<option value="all_saved">' + resolveUiChoiceLabel('all_collections', 'All collections') + ' (' + allCount + ')</option>' + options.map(source => {
      const count = source.items.length;
      const label = source.name + ' (' + count + ')';
      return '<option value="' + String(source.id) + '">' + label + '</option>';
    }).join('');

    const hasSelected = selectedId === 'all_saved' || options.some(source => String(source.id) === selectedId);
    select.value = hasSelected ? selectedId : 'all_saved';
    localStorage.setItem(EXERCISE_COLLECTION_KEY, select.value);

    select.onchange = () => {
      localStorage.setItem(EXERCISE_COLLECTION_KEY, select.value);
      updateExerciseSourceEligibility();
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
    await loadVocabularySources(resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...')), true, forceReloadSources);
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
    syncSourcePanels();
    updateExerciseSourceEligibility();
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
    const prompt = resolveExerciseRuntimeTextRelaxed('translate_to_english', uiFallback('translate_to_english', 'Translate to English.'));

    return items
      .map(item => ({
        type: 'translate',
        prompt,
        sentence: sanitizeRuntimeText(item.translation || item.meaning || ''),
        answer: item.text || item.word || '',
      }))
      .filter(item => item.sentence && item.answer)
      .slice(0, 4);
  }

  function buildCustomListeningItems(items, difficultyLevel = 'B1') {
    const question = resolveExerciseRuntimeTextRelaxed('listening_question_default', uiFallback('listening_question_default', 'Listen and type the missing expression.'));

    return items
      .filter(item => item.text && item.translation)
      .slice(0, 3)
      .map(item => ({
        type: 'fillin',
        transcript: String(item.text || ''),
        question,
        sentence: '______',
        answer: item.text,
      }));
  }

  function buildCustomMixItems(items, difficultyLevel = 'B1', sourceType = 'catalog') {
    const matchingItems = buildCustomMatchingItems(items, difficultyLevel, sourceType).slice(0, 1);
    const memoryItems = buildCustomMemoryItems(items, difficultyLevel, sourceType).slice(0, 1);

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

  function resolveSourceItemText(item) {
    return String(item?.text || item?.word || item?.label || '').trim();
  }

  function resolveSourceItemTranslation(item) {
    return String(item?.translation || item?.meaning || '').trim();
  }

  function collectSourcePairs(items) {
    const seen = new Set();

    return (items || [])
      .map(item => ({ left: resolveSourceItemText(item), right: resolveSourceItemTranslation(item) }))
      .filter(pair => pair.left && pair.right)
      .filter(pair => {
        const key = (pair.left + '::' + pair.right).toLowerCase();
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
      });
  }

  function buildCustomMatchingItems(items, difficultyLevel = 'B1', sourceType = 'catalog') {
    const sourcePairs = collectSourcePairs(items);
    const maxPairs = sourceType === 'saved' ? 8 : 12;
    const pairs = sourcePairs.slice(0, maxPairs);

    if (pairs.length < 3) return [];

    const timePerPair = ['A1', 'A2'].includes(difficultyLevel) ? 8 : difficultyLevel === 'B1' ? 7 : difficultyLevel === 'B2' ? 6 : 5;

    return [{
      type: 'match',
      question: resolveExerciseRuntimeTextRelaxed('matching_question', uiFallback('matching_question', 'Match the pairs as fast as possible.')),
      pairs,
      time_limit_seconds: Math.max(35, Math.min(95, pairs.length * timePerPair)),
    }];
  }

  function buildCustomMemoryItems(items, difficultyLevel = 'B1', sourceType = 'catalog') {
    const sourcePairs = collectSourcePairs(items);
    const maxPairs = sourceType === 'saved' ? 10 : 16;
    const pairs = sourcePairs
      .slice(0, maxPairs)
      .map(pair => ({ front: pair.left, back: pair.right }));

    if (pairs.length < 4) return [];

    const previewMs = ['A1', 'A2'].includes(difficultyLevel) ? 1500 : difficultyLevel === 'B1' ? 1200 : difficultyLevel === 'B2' ? 900 : 700;

    return [{
      type: 'memory',
      question: resolveExerciseRuntimeTextRelaxed('memory_question', uiFallback('memory_question', 'Find all translation pairs with the fewest moves.')),
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
    const sourceType = getSelectedSourceType();
    const templateModeData = sourceType === 'saved' ? null : getTemplateModeData(mode);
    const difficultyLevel = resolveDifficultyLevel(source, source.items || []);

    if (!source.items.length) {
      if (templateModeData) {
        return templateModeData;
      }
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
      const customItems = buildCustomMixItems(source.items, difficultyLevel, sourceType);
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
      const customItems = buildCustomMatchingItems(source.items, difficultyLevel, sourceType);
      if (customItems.length) {
        return { title: base.title + ' · ' + source.name, items: customItems };
      }
    }

    if (templateModeData) {
      return templateModeData;
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
    const lang = getUiLocale();
    const labels = resolveModeLabelsForLanguage(lang);
    document.querySelectorAll('.exercise-card[data-mode]').forEach(card => {
      const idx = BADGE_MODES.indexOf(card.dataset.mode);
      if (idx === -1) return;
      const badge = card.querySelector('.exercise-card-badge');
      if (badge) badge.textContent = labels[idx];
    });
  }

  updateExerciseModeLabels();

  async function bootstrapExercises() {
    initSpeechVoices();
    primeUiAudio();
    const setGlobalLoading = typeof window.lexiSetPageLoading === 'function' ? window.lexiSetPageLoading : null;
    const loadingLabel = resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...'));

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

  const primeAudioOnInteraction = () => {
    primeUiAudio();
    window.removeEventListener('pointerdown', primeAudioOnInteraction);
    window.removeEventListener('keydown', primeAudioOnInteraction);
  };

  window.addEventListener('pointerdown', primeAudioOnInteraction, { passive: true });
  window.addEventListener('keydown', primeAudioOnInteraction);

  window.addEventListener('lexi-lang-changed', async () => {
    updateExerciseModeLabels();
    await loadVocabularySources(resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...')), false, true);
    setupExerciseCatalogSelects();
    setupExerciseCollectionSelect();
    syncSourcePanels();
  });

  window.addEventListener('pageshow', async (event) => {
    if (!event.persisted) return;
    const setGlobalLoading = typeof window.lexiSetPageLoading === 'function' ? window.lexiSetPageLoading : null;
    const loadingLabel = resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...'));

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
          transcript: "second",
          question: resolveExerciseRuntimeTextRelaxed('listening_question_default', uiFallback('listening_question_default', 'Listen and type the missing expression.')),
          sentence: "______",
          answer: "second"
        },
        {
          type: "fillin",
          transcript: "eighty",
          question: resolveExerciseRuntimeTextRelaxed('listening_question_default', uiFallback('listening_question_default', 'Listen and type the missing expression.')),
          sentence: "______",
          answer: "eighty"
        },
        {
          type: "fillin",
          transcript: "fog",
          question: resolveExerciseRuntimeTextRelaxed('listening_question_default', uiFallback('listening_question_default', 'Listen and type the missing expression.')),
          sentence: "______",
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
      title: tx('challenge_label', 'Challenge'),
      items: [
        {
          type: 'match',
          question: resolveExerciseRuntimeTextRelaxed('matching_question', uiFallback('matching_question', 'Match the pairs as fast as possible.')),
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
          question: resolveExerciseRuntimeTextRelaxed('memory_question', uiFallback('memory_question', 'Find all translation pairs with the fewest moves.')),
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
    document.getElementById('exProgress').textContent = '';
    document.getElementById('exProgressBar').style.width = '12%';
    document.getElementById('btnNextEx').disabled = true;
    document.getElementById('btnNextEx').innerHTML = te('next', tx('skip', 'Skip')) + ' <i class="bi bi-arrow-right"></i>';
    if (nav) nav.hidden = true;
    content.innerHTML =
      '<div class="ex-loading-state" role="status" aria-live="polite">' +
      '<span class="ex-loading-spinner" aria-hidden="true"></span>' +
        '<span class="ex-loading-text">' + resolveExerciseRuntimeTextRelaxed('loading_exercise', te('loading_options', uiFallback('loading_exercise', ''))) + '</span>' +
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

    btnNext.innerHTML = te('next', tx('skip', 'Skip')) + ' <i class="bi bi-arrow-right"></i>';
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
      title: sanitizeRuntimeText(payload.title || EXERCISES[mode].title),
      items: payload.items,
    };
  }

  function renderRuntimeUnavailable(title, reason) {
    const content = document.getElementById('exerciseContent');
    const nav = document.getElementById('exNavBtns');
    document.getElementById('exPanelTitle').textContent = title;
    document.getElementById('exProgress').textContent = resolveExerciseRuntimeText('not_available_short');
    document.getElementById('exProgressBar').style.width = '0%';
    document.getElementById('btnNextEx').disabled = true;
    document.getElementById('btnNextEx').innerHTML = te('next', tx('skip', 'Skip')) + ' <i class="bi bi-arrow-right"></i>';
    if (nav) nav.hidden = true;

    content.innerHTML =
      '<div class="ex-feedback ex-feedback--warn" style="display:block">' +
      '<i class="bi bi-exclamation-triangle-fill"></i> ' +
      String(reason || tx('ai_generation_failed', 'A high-quality AI exercise could not be generated for this selection.')) +
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
    card.addEventListener('click', () => {
      if (card.classList.contains('is-disabled')) return;
      openMode(card.dataset.mode);
    });
    card.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        if (card.classList.contains('is-disabled')) return;
        openMode(card.dataset.mode);
      }
    });
    card.style.cursor = 'pointer';
  });

  document.getElementById('btnBack').addEventListener('click', closePanel);
  document.getElementById('btnNextEx').addEventListener('click', nextExercise);

  async function openMode(mode) {
    if (!updateExerciseSourceEligibility(mode)) {
      return;
    }

    const aiFirstModes = ['reading', 'listening', 'speaking', 'writing'];
    const needsAiRuntime = aiFirstModes.includes(mode);

    if (needsAiRuntime) {
      await loadVocabularySources(resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...')), true);
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
      loadVocabularySources(resolveExerciseRuntimeTextRelaxed('loading_options', uiFallback('loading_options', 'Loading options...')), false).catch(() => {});
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

  function resolveSpeechRecognitionLocale(langCode) {
    const base = normalizeLangCodeForSpeech(langCode);
    const map = {
      en: 'en-US',
      es: 'es-ES',
      fr: 'fr-FR',
      de: 'de-DE',
      it: 'it-IT',
      pt: 'pt-PT',
      nl: 'nl-NL',
      sv: 'sv-SE',
      da: 'da-DK',
      nb: 'nb-NO',
      fi: 'fi-FI',
      pl: 'pl-PL',
      cs: 'cs-CZ',
      sk: 'sk-SK',
      hu: 'hu-HU',
      ro: 'ro-RO',
      el: 'el-GR',
      tr: 'tr-TR',
      uk: 'uk-UA',
      ru: 'ru-RU',
      ar: 'ar-SA',
      he: 'he-IL',
      hi: 'hi-IN',
      id: 'id-ID',
      vi: 'vi-VN',
      th: 'th-TH',
      ja: 'ja-JP',
      ko: 'ko-KR',
      zh: 'zh-CN',
    };

    return map[base] || `${base}-${base.toUpperCase()}`;
  }

  function normalizeSpeechComparisonText(value) {
    return String(value || '')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^\p{L}\p{N}\s]/gu, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function computeSpeechMatchScore(spokenText, expectedText) {
    const spoken = normalizeSpeechComparisonText(spokenText);
    const expected = normalizeSpeechComparisonText(expectedText);

    if (!spoken || !expected) return 0;
    if (spoken === expected) return 1;
    if (spoken.includes(expected)) return 0.95;
    if (expected.includes(spoken)) return 0.85;

    const spokenTokens = spoken.split(' ').filter(Boolean);
    const expectedTokens = expected.split(' ').filter(Boolean);
    if (!spokenTokens.length || !expectedTokens.length) return 0;

    const expectedSet = new Set(expectedTokens);
    const overlap = spokenTokens.filter(token => expectedSet.has(token)).length;

    return overlap / expectedTokens.length;
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

  bootstrapExercises();

  function parseReadingPassage(passage) {
    const raw = sanitizeRuntimeText(passage);
    if (!raw) {
      return { scenario: '', sentence: '' };
    }

    const parts = raw.match(/^(.*?)\.\s*Sentence:\s*(.+)$/i);
    if (!parts) {
      return { scenario: '', sentence: raw };
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

    return resolveExerciseRuntimeText('listening_question_default');
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
      return resolveExerciseRuntimeText('listening_question_default');
    }

    // If the question already includes a blank placeholder, show a short prompt
    // and let the sentence-with-gap below carry the actual text.
    if (gapRegex.test(normalizedQuestion)) {
      return resolveExerciseRuntimeText('listening_question_default');
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
      return resolveExerciseRuntimeText('listening_question_default');
    }

    if (normalizedAnswer === '') {
      return normalizedQuestion;
    }

    const escapedAnswer = normalizedAnswer.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const answerRegex = new RegExp(escapedAnswer, 'i');

    if (answerRegex.test(normalizedQuestion)) {
      return resolveExerciseRuntimeText('listening_question_default');
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
    const targetLanguage = String(getActiveLang() || 'en').toLowerCase().split('-')[0];
    const readingView = parseReadingPassage(sanitizeRuntimeText(item.passage));
    const safeReadingLabel = sanitizeRuntimeText(t('reading_label'));
    const safeScenarioTitle = sanitizeRuntimeText(tx('context_label', 'Context'));
    const fallbackQuestion = sanitizeRuntimeText(tx('select_correct_answer', 'Select the correct answer.'));
    const rawQuestion = sanitizeRuntimeText(item.question || '');
    const normalizedQuestion = (targetLanguage !== 'en' && isLikelyEnglishRuntimeText(rawQuestion))
      ? fallbackQuestion
      : (rawQuestion || fallbackQuestion);
    const scenarioHtml = readingView.scenario ? '<div class="ex-passage-meta"><span class="ex-passage-chip"><i class="bi bi-journal-text"></i> ' + safeScenarioTitle + '</span><span class="ex-passage-context"> · ' + sanitizeRuntimeText(readingView.scenario) + '</span></div>' : '';
    const sentenceHtml = readingView.sentence ? '<div class="ex-passage ex-passage--sentence">' + sanitizeRuntimeText(readingView.sentence) + '</div>' : '<div class="ex-passage">' + sanitizeRuntimeText(item.passage) + '</div>';
    const safeOptions = Array.isArray(item.options) ? item.options.map(opt => sanitizeRuntimeText(opt)) : [];

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-book"></i> ' + safeReadingLabel + '</p>' +
      scenarioHtml +
      sentenceHtml +
      '<p class="ex-question">' + normalizedQuestion + '</p>' +
      '<div class="ex-options">' +
      safeOptions.map((opt, i) =>
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
            prompt: normalizedQuestion,
            expectedAnswer: safeOptions[item.correct],
            answerText: safeOptions[idx],
            answerPayload: { selected_index: idx, selected_option: safeOptions[idx] },
            feedback: t('correct'),
          });
        } else {
          this.classList.add('ex-option--wrong');
          container.querySelectorAll('.ex-option')[item.correct].classList.add('ex-option--correct');
          playIncorrectFeedback(item.type);
          feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + t('incorrect_reference', { answer: '<strong>' + safeOptions[item.correct] + '</strong>' });
          feedback.className = 'ex-feedback ex-feedback--err';
          markExerciseItemResult(container, false, {
            itemId: item.itemId,
            itemType: item.type,
            prompt: normalizedQuestion,
            expectedAnswer: safeOptions[item.correct],
            answerText: safeOptions[idx],
            answerPayload: { selected_index: idx, selected_option: safeOptions[idx] },
            feedback: t('incorrect'),
          });
        }
        feedback.hidden = false;
      });
    });
  }

  function renderFillin(container, item) {
    const uiLanguage = getUiLocale();
    const listeningBaseTextRaw = resolveListeningBaseText(item);
    const listeningBaseText = (uiLanguage !== 'en' && isLikelyEnglishRuntimeText(listeningBaseTextRaw))
      ? sanitizeRuntimeText(item.answer || '')
      : listeningBaseTextRaw;
    const listeningAnswerText = String(item.answer || '').trim();
    const listeningQuestionRaw = sanitizeListeningQuestion(item.question, listeningAnswerText, listeningBaseText);
    const listeningQuestion = (uiLanguage !== 'en' && isLikelyEnglishRuntimeText(listeningQuestionRaw))
      ? resolveExerciseRuntimeTextRelaxed('listening_question_default', uiFallback('listening_question_default', 'Listen and type the missing expression.'))
      : listeningQuestionRaw;
    const listeningSentenceTextRaw = resolveListeningSentenceForGap(listeningBaseText, item.question);
    const listeningSentenceText = (uiLanguage !== 'en' && isLikelyEnglishRuntimeText(listeningSentenceTextRaw))
      ? '______'
      : listeningSentenceTextRaw;
    const expectedAnswerLength = Math.max(4, String(item.answer || '').trim().length || 4);
    const inputWidthCh = Math.max(6, Math.min(34, expectedAnswerLength + 2));
    const inputHtml = '<input class="ex-input" type="text" autocomplete="off" spellcheck="false" style="width:' + inputWidthCh + 'ch">';
    const sentenceWithInput = buildListeningSentenceWithGap(listeningSentenceText, listeningAnswerText, inputHtml);
    const checkLabel = resolveExerciseRuntimeTextRelaxed('check', uiFallback('check', 'Check'));

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-headphones"></i> ' + t('listening_label') + '</p>' +
      '<div class="ex-audio-player">' +
      '<div class="ex-audio-track" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>' +
      '<div class="ex-audio-controls">' +
      '<button class="btn btn-outline-secondary btn-sm ex-audio-toggle" type="button" aria-label="' + tx('play_audio', 'Play audio') + '" title="' + tx('play_audio', 'Play audio') + '"><i class="bi bi-play-fill"></i></button>' +
      '</div>' +
      '</div>' +
      '<p class="ex-question">' + listeningQuestion + '</p>' +
      '<div class="ex-fillin-wrap">' + sentenceWithInput + '</div>' +
      '<button class="btn btn-outline-secondary ex-check-btn">' + checkLabel + '</button>' +
      '<div class="ex-feedback" hidden></div>';

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
    const transcriptLabel = resolveExerciseRuntimeTextRelaxed('speaking_transcript_label', uiFallback('speaking_transcript_label', 'Transcript'));
    const listeningLabel = resolveExerciseRuntimeTextRelaxed('speaking_listening', uiFallback('speaking_listening', 'Listening...'));
    const timerLabel = resolveExerciseRuntimeTextRelaxed('speaking_time', uiFallback('speaking_time', 'Time'));
    const unsupportedText = resolveExerciseRuntimeTextRelaxed('speaking_not_supported', uiFallback('speaking_not_supported', 'Voice transcription is not available in this browser.'));
    const noSpeechText = resolveExerciseRuntimeTextRelaxed('speaking_no_speech', uiFallback('speaking_no_speech', 'No speech was detected. Try again.'));

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-mic"></i> ' + t('speaking_label') + '</p>' +
      '<p class="ex-speaking-prompt">' + t('pronounce_out_loud') + '</p>' +
      '<div class="ex-word-big">' + item.word + '</div>' +
      '<p class="ex-hint-text">' + item.hint + '</p>' +
      '<div class="ex-mic-area">' +
      '<button class="ex-mic-btn" id="micBtn" aria-label="' + t('speaking_label') + '"><i class="bi bi-mic"></i></button>' +
      '</div>' +
      '<p class="ex-speaking-status" id="speakingStatus" hidden></p>' +
      '<p class="ex-speaking-timer" id="speakingTimer">' + timerLabel + ': 00:00</p>' +
      '<p class="ex-speaking-transcript" id="speakingTranscript" hidden><strong>' + transcriptLabel + ':</strong> <span class="ex-speaking-transcript-text"></span></p>' +
      '<div class="ex-feedback" hidden></div>';

    const micBtn = container.querySelector('#micBtn');
    const micIcon = micBtn ? micBtn.querySelector('i') : null;
    const feedback = container.querySelector('.ex-feedback');
    const status = container.querySelector('#speakingStatus');
    const timer = container.querySelector('#speakingTimer');
    const transcript = container.querySelector('#speakingTranscript');
    const transcriptText = container.querySelector('.ex-speaking-transcript-text');
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    let recognition = null;
    let isRecording = false;
    let timerId = null;
    let elapsedSeconds = 0;
    let shouldEvaluateOnStop = true;
    let finalTranscript = '';
    let interimTranscript = '';
    let recognitionSessionId = 0;
    let manualStopRequested = false;
    let captureState = 'idle';
    let hardStopTimerId = null;
    let speechDetected = false;
    let microphoneStream = null;
    const MAX_RECORDING_MS = 4000;
    const INITIAL_SPEECH_TIMEOUT_MS = 4500;

    const formatDuration = (seconds) => {
      const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
      const secs = String(seconds % 60).padStart(2, '0');
      return `${mins}:${secs}`;
    };

    const resetTimer = () => {
      elapsedSeconds = 0;
      if (timer) timer.textContent = `${timerLabel}: ${formatDuration(0)}`;
    };

    const stopTimer = () => {
      if (timerId !== null) {
        clearInterval(timerId);
        timerId = null;
      }
    };

    const startTimer = () => {
      stopTimer();
      timerId = window.setInterval(() => {
        elapsedSeconds += 1;
        if (timer) timer.textContent = `${timerLabel}: ${formatDuration(elapsedSeconds)}`;
      }, 1000);
    };

    const clearSpeakingAutoStopTimers = () => {
      if (hardStopTimerId !== null) {
        clearTimeout(hardStopTimerId);
        hardStopTimerId = null;
      }
    };

    const releaseMicrophoneStream = () => {
      if (!microphoneStream) return;
      try {
        microphoneStream.getTracks().forEach(track => track.stop());
      } catch {
      }
      microphoneStream = null;
    };

    const ensureMicrophoneStream = async () => {
      if (microphoneStream && microphoneStream.active) {
        return microphoneStream;
      }

      if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
        throw new Error('microphone-unavailable');
      }

      microphoneStream = await navigator.mediaDevices.getUserMedia({
        audio: {
          channelCount: 1,
          echoCancellation: true,
          noiseSuppression: true,
          autoGainControl: true,
        },
      });

      return microphoneStream;
    };

    const prepareMicrophonePermission = async () => {
      await ensureMicrophoneStream();
      releaseMicrophoneStream();
    };

    const setCaptureState = (nextState) => {
      captureState = nextState;
      const active = nextState === 'starting' || nextState === 'recording' || nextState === 'stopping';
      isRecording = nextState === 'recording';

      if (micBtn) {
        micBtn.classList.toggle('recording', active);
        micBtn.disabled = nextState === 'stopping';
      }

      if (micIcon) {
        micIcon.className = active ? 'bi bi-stop-fill' : 'bi bi-mic';
      }

      if (status) {
        status.hidden = !active;
        status.textContent = active ? listeningLabel : '';
      }
    };

    const resetSpeakingOutput = () => {
      clearSpeakingAutoStopTimers();
      finalTranscript = '';
      interimTranscript = '';
      speechDetected = false;
      resetTimer();
      if (feedback) {
        feedback.hidden = true;
        feedback.className = 'ex-feedback';
        feedback.innerHTML = '';
      }
      if (transcriptText) transcriptText.textContent = '';
      if (transcript) transcript.hidden = true;
    };

    const applyLiveTranscript = () => {
      if (!transcriptText || !transcript) return;
      const combined = [finalTranscript, interimTranscript].filter(Boolean).join(' ').trim();
      transcriptText.textContent = combined;
      transcript.hidden = combined === '';
    };

    const evaluateSpeakingAttempt = () => {
      const spoken = [finalTranscript, interimTranscript].filter(Boolean).join(' ').trim();
      if (!spoken) {
        playUiTone('neutral');
        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--warn';
          feedback.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> ' + noSpeechText;
          feedback.hidden = false;
        }
        return;
      }

      if (transcriptText) transcriptText.textContent = spoken;
      if (transcript) transcript.hidden = false;

      const score = computeSpeechMatchScore(spoken, item.word || '');
      const isCorrect = score >= 0.75;

      if (isCorrect) {
        playUiTone('success');
        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--ok';
          feedback.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + t('correct');
          feedback.hidden = false;
        }
      } else {
        playIncorrectFeedback(item.type);
        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--err';
          feedback.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + t('answer_is', { answer: '<strong>"' + item.word + '"</strong>' });
          feedback.hidden = false;
        }
      }

      markExerciseItemResult(container, isCorrect, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.word,
        expectedAnswer: item.word,
        answerText: spoken,
        answerPayload: {
          transcript: spoken,
          recognition_lang: resolveSpeechRecognitionLocale(getActiveLang()),
          duration_seconds: elapsedSeconds,
          score,
        },
        feedback: isCorrect ? t('correct') : t('incorrect'),
      });
    };

    const stopSpeakingCapture = (evaluateAfterStop = false) => {
      stopTimer();
      clearSpeakingAutoStopTimers();
      shouldEvaluateOnStop = evaluateAfterStop;

      if (recognition && captureState !== 'idle') {
        manualStopRequested = true;
        const shouldAbort = captureState === 'starting';
        setCaptureState('stopping');
        try {
          if (shouldAbort) {
            recognition.abort();
          } else {
            recognition.stop();
          }
        } catch {
          recognition = null;
          recognitionSessionId = 0;
          manualStopRequested = false;
          setCaptureState('idle');
          releaseMicrophoneStream();
        }
        return;
      }

      setCaptureState('idle');
      releaseMicrophoneStream();
      if (evaluateAfterStop) {
        evaluateSpeakingAttempt();
      }
    };

    const startSpeakingCapture = async () => {
      if (recognition || captureState !== 'idle') {
        return;
      }

      resetSpeakingOutput();

      if (!SpeechRecognition) {
        playUiTone('neutral');
        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--warn';
          feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + unsupportedText;
          feedback.hidden = false;
        }
        return;
      }

      setCaptureState('starting');

      try {
        await prepareMicrophonePermission();
      } catch {
        setCaptureState('idle');
        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--warn';
          feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + sanitizeRuntimeText(tx('microphone_permission_required', 'Microphone permission is required to record.'));
          feedback.hidden = false;
        }
        return;
      }

      if (captureState !== 'starting') {
        return;
      }

      recognition = new SpeechRecognition();
      const sessionId = recognitionSessionId + 1;
      recognitionSessionId = sessionId;
      manualStopRequested = false;
      speechDetected = false;
      recognition.lang = resolveSpeechRecognitionLocale(getActiveLang());
      recognition.continuous = false;
      recognition.interimResults = true;
      recognition.maxAlternatives = 1;

      recognition.onstart = () => {
        if (sessionId !== recognitionSessionId) return;
        shouldEvaluateOnStop = true;
        setCaptureState('recording');
        startTimer();
        clearSpeakingAutoStopTimers();
        hardStopTimerId = window.setTimeout(() => {
          hardStopTimerId = null;
          stopSpeakingCapture(true);
        }, MAX_RECORDING_MS);
        window.setTimeout(() => {
          if (sessionId !== recognitionSessionId || speechDetected || !recognition || captureState === 'stopping') return;
          stopSpeakingCapture(true);
        }, INITIAL_SPEECH_TIMEOUT_MS);
      };

      recognition.onresult = (event) => {
        if (sessionId !== recognitionSessionId) return;
        interimTranscript = '';

        for (let index = event.resultIndex; index < event.results.length; index += 1) {
          const segment = String(event.results[index][0]?.transcript || '').trim();
          if (!segment) continue;

          speechDetected = true;

          if (event.results[index].isFinal) {
            finalTranscript = (finalTranscript + ' ' + segment).trim();
          } else {
            interimTranscript = (interimTranscript + ' ' + segment).trim();
          }
        }

        applyLiveTranscript();
      };

      recognition.onerror = (event) => {
        if (sessionId !== recognitionSessionId) return;

        stopTimer();
        clearSpeakingAutoStopTimers();

        const code = String(event?.error || '').toLowerCase();
        if (code === 'aborted' && manualStopRequested) {
          recognition = null;
          recognitionSessionId = 0;
          manualStopRequested = false;
          setCaptureState('idle');
          releaseMicrophoneStream();
          return;
        }

        const permissionDenied = code === 'not-allowed' || code === 'service-not-allowed';
        const noMatch = code === 'no-speech' || code === 'nomatch';
        const networkError = code === 'network';
        const audioCapture = code === 'audio-capture';
        const aborted = code === 'aborted';
        const langUnsupported = code === 'language-not-supported';

        if ((networkError || noMatch) && (finalTranscript || interimTranscript || speechDetected)) {
          shouldEvaluateOnStop = true;
          setCaptureState('idle');
          releaseMicrophoneStream();
          return;
        }

        shouldEvaluateOnStop = false;
        setCaptureState('idle');
        recognition = null;
        recognitionSessionId = 0;
        manualStopRequested = false;
        releaseMicrophoneStream();

        const message = permissionDenied
          ? tx('microphone_permission_required', 'Microphone permission is required to record.')
          : (langUnsupported
            ? unsupportedText
            : ((noMatch || networkError || audioCapture || aborted)
            ? noSpeechText
            : tx('recording_failed', 'Recording failed. Try again.')));

        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--warn';
          feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + sanitizeRuntimeText(message);
          feedback.hidden = false;
        }
      };

      recognition.onend = () => {
        if (sessionId !== recognitionSessionId) return;

        stopTimer();
        clearSpeakingAutoStopTimers();
        setCaptureState('idle');
        recognition = null;
        recognitionSessionId = 0;
        const shouldEvaluate = shouldEvaluateOnStop;
        manualStopRequested = false;
        releaseMicrophoneStream();

        if (shouldEvaluate) {
          evaluateSpeakingAttempt();
        }
      };

      try {
        recognition.start();
      } catch {
        recognition = null;
        recognitionSessionId = 0;
        setCaptureState('idle');
        releaseMicrophoneStream();
        if (feedback) {
          feedback.className = 'ex-feedback ex-feedback--warn';
          feedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> ' + unsupportedText;
          feedback.hidden = false;
        }
      }
    };

    micBtn.addEventListener('click', function () {
      if (captureState !== 'idle') {
        playUiTone('neutral');
        stopSpeakingCapture(true);
        return;
      }

      playUiTone('neutral');
      void startSpeakingCapture();
    });

    activeListeningStopHandler = () => {
      stopSpeakingCapture(false);
      releaseMicrophoneStream();
    };
  }

  function renderTranslate(container, item) {
    const uiLanguage = getUiLocale();
    const rawPrompt = sanitizeRuntimeText(item.prompt || '');
    const rawSentence = sanitizeRuntimeText(item.sentence || '');
    const extractedContext = (() => {
      const quotedMatch = rawSentence.match(/context source:\s*"([^"]+)"/i);
      if (quotedMatch && quotedMatch[1]) return sanitizeRuntimeText(quotedMatch[1]);
      const plainMatch = rawSentence.match(/context source:\s*(.+)$/i);
      if (plainMatch && plainMatch[1]) return sanitizeRuntimeText(plainMatch[1]);
      return rawSentence;
    })();
    const writingPrompt = (() => {
      if (uiLanguage !== 'en' && isLikelyEnglishRuntimeText(rawPrompt)) {
        return resolveExerciseRuntimeTextRelaxed('translate_to_english', uiFallback('translate_to_english', rawPrompt || 'Translate to English.'));
      }
      if (rawPrompt) return rawPrompt;
      return resolveExerciseRuntimeTextRelaxed('translate_to_english', uiFallback('translate_to_english', 'Translate to English.'));
    })();
    const writingSentence = (() => {
      if (uiLanguage !== 'en' && isLikelyEnglishRuntimeText(rawSentence)) {
        return extractedContext || rawSentence;
      }
      return rawSentence;
    })();
    const writingPlaceholder = resolveExerciseRuntimeTextRelaxed('write_placeholder', uiFallback('write_placeholder', ''));
    const checkLabel = resolveExerciseRuntimeTextRelaxed('check', uiFallback('check', 'Check'));

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-pencil"></i> ' + t('writing_label') + '</p>' +
      '<p class="ex-prompt">' + writingPrompt + '</p>' +
      '<div class="ex-sentence-box">' + writingSentence + '</div>' +
      '<textarea class="ex-textarea" placeholder="' + writingPlaceholder + '"></textarea>' +
      '<button class="btn btn-outline-secondary ex-check-btn">' + checkLabel + '</button>' +
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
          prompt: writingSentence,
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
          prompt: writingSentence,
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
      '<p class="ex-type-label"><i class="bi bi-layers"></i> ' + tx('flashcards_label', 'Flashcards') + '</p>' +
      '<p class="ex-question">' + tx('flashcards_instruction', 'View the translation briefly, then decide if you remembered it.') + '</p>' +
      '<div class="ex-flashcard" data-visible="0">' +
        '<div class="ex-flashcard-face ex-flashcard-face--front">' + item.front + '</div>' +
        '<div class="ex-flashcard-face ex-flashcard-face--back" hidden>' + item.back + '</div>' +
      '</div>' +
      '<div class="ex-flashcard-meta">' +
        '<span class="ex-flashcard-timer">' + tx('flashcards_reveal_label', 'Reveal') + ': ' + Math.round(revealMs / 1000) + 's</span>' +
        hintText +
      '</div>' +
      '<div class="ex-flashcard-actions">' +
        '<button class="btn btn-primary ex-flashcard-show">' + tx('flashcards_show', 'Show for 1 second') + '</button>' +
      '</div>' +
      '<div class="ex-self-check" hidden>' +
        '<p>' + tx('flashcards_remembered_question', 'Did you remember it without looking again?') + '</p>' +
        '<div class="ex-self-check-btns">' +
          '<button class="btn btn-success ex-flashcard-yes"><i class="bi bi-check-lg"></i> ' + tx('flashcards_yes', 'Yes, I knew it') + '</button>' +
          '<button class="btn btn-outline-danger ex-flashcard-no">' + tx('flashcards_no', 'No, it was hard') + '</button>' +
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
        feedback: tx('correct', 'Correct!'),
      });
      selfCheck.innerHTML = '<p class="ex-feedback ex-feedback--ok" style="display:block"><i class="bi bi-check-circle-fill"></i> ' + tx('flashcards_success', 'Great, keep going.') + '</p>';
    });

    container.querySelector('.ex-flashcard-no').addEventListener('click', () => {
      markExerciseItemResult(container, false, {
        itemId: item.itemId,
        itemType: item.type,
        prompt: item.front,
        expectedAnswer: item.back,
        answerText: null,
        answerPayload: { remembered: false, reveal_ms: revealMs },
        feedback: tx('incorrect', 'Incorrect'),
      });
      selfCheck.innerHTML = '<p class="ex-feedback ex-feedback--warn" style="display:block"><i class="bi bi-arrow-repeat"></i> ' + tx('flashcards_retry_hint', 'Repeat this card later to reinforce memory.') + '</p>';
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

    const matchingQuestion = resolveExerciseRuntimeTextRelaxed('matching_question', sanitizeRuntimeText(item.question || uiFallback('matching_question', '')));
    const pairsLabel = resolveExerciseRuntimeTextRelaxed('matching_pairs', resolveExerciseRuntimeTextRelaxed('pairs_label', uiFallback('pairs_label', 'Pairs')));
    const matchingLabel = resolveExerciseRuntimeTextRelaxed('matching_label', resolveExerciseRuntimeTextRelaxed('challenge_mode', resolveExerciseRuntimeTextRelaxed('challenge_label', uiFallback('matching_label', 'Challenge'))));

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-bezier2"></i> ' + matchingLabel + '</p>' +
      '<p class="ex-question">' + matchingQuestion + '</p>' +
      '<div class="ex-game-meta">' +
        '<span class="ex-game-chip"><i class="bi bi-stopwatch"></i> <strong class="ex-match-timer">' + timeLimitSeconds + 's</strong></span>' +
        '<span class="ex-game-chip"><i class="bi bi-lightning-charge"></i> ' + (pairsLabel ? (pairsLabel + ': ') : '') + '<strong class="ex-match-count">0/' + totalPairs + '</strong></span>' +
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
        feedback.innerHTML = '<i class="bi bi-hourglass-split"></i> ' + resolveExerciseRuntimeTextRelaxed('matching_timeout', uiFallback('matching_timeout', 'Time is over. Try again to beat the clock.'));
        feedback.className = 'ex-feedback ex-feedback--warn';
      } else if (success) {
        playUiTone('success');
        feedback.innerHTML = '<i class="bi bi-trophy-fill"></i> ' + resolveExerciseRuntimeTextRelaxed('matching_success', uiFallback('matching_success', 'Great speed. Challenge completed.'));
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
        feedback: success ? tx('correct', 'Correct!') : tx('incorrect', 'Incorrect'),
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

    const memoryQuestion = resolveExerciseRuntimeTextRelaxed('memory_question', sanitizeRuntimeText(item.question || uiFallback('memory_question', '')));
    const movesLabel = resolveExerciseRuntimeTextRelaxed('moves_label', uiFallback('moves_label', 'Moves'));
    const pairsLabel = resolveExerciseRuntimeTextRelaxed('pairs_label', resolveExerciseRuntimeTextRelaxed('matching_pairs', uiFallback('pairs_label', 'Pairs')));
    const memoryLabel = resolveExerciseRuntimeTextRelaxed('memory_label', resolveExerciseRuntimeTextRelaxed('memory_mode', resolveExerciseRuntimeTextRelaxed('practice_label', uiFallback('memory_label', 'Memory'))));

    container.innerHTML =
      '<p class="ex-type-label"><i class="bi bi-grid-3x3-gap"></i> ' + memoryLabel + '</p>' +
      '<p class="ex-question">' + memoryQuestion + '</p>' +
      '<div class="ex-game-meta">' +
        '<span class="ex-game-chip"><i class="bi bi-arrows-move"></i> ' + (movesLabel ? (movesLabel + ': ') : '') + '<strong class="ex-memory-moves">0</strong></span>' +
        '<span class="ex-game-chip"><i class="bi bi-patch-check"></i> ' + (pairsLabel ? (pairsLabel + ': ') : '') + '<strong class="ex-memory-count">0/' + pairs.length + '</strong></span>' +
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
      feedback.innerHTML = '<i class="bi bi-trophy-fill"></i> ' + resolveExerciseRuntimeTextRelaxed('memory_success', uiFallback('memory_success', 'All pairs completed. Great focus.'));
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
        feedback: tx('correct', 'Correct!'),
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
          '<h2>' + te('completed', tx('completed', 'Section completed!')) + '</h2>' +
          '<div class="ex-complete-actions" style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">' +
            '<button class="btn btn-outline-secondary" id="btnRepeatInline">' + te('repeat', tx('repeat', 'Repeat')) + '</button>' +
            '<button class="btn btn-primary" id="btnBackInline">' + te('choose_other_mode', tx('choose_other_mode', 'Choose another mode')) + '</button>' +
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
