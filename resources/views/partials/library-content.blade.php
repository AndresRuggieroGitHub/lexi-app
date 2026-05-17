@php
  $cefrLevels = config('lexi.cefr_levels', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']);
  $catalogTopics = config('lexi.catalog_topics', []);

  $levelOptionLabel = static function (string $level): string {
      return match ($level) {
          'A1' => '▮▯▯▯▯ A1',
          'A2' => '▮▮▯▯▯ A2',
          'B1' => '▮▮▮▯▯ B1',
          'B2', 'C1' => '▮▮▮▮▯ ' . $level,
          default => '▮▮▮▮▮ ' . $level,
      };
  };
@endphp

<main id="mainContent" class="page-main container section-space">
  <h1 class="library-page-title">{{ __('lexi.library.title') }}</h1>

  <div id="listsIndex" class="mb-4">
    <div class="lists-index-header mb-3">
      <span class="lists-index-label">{{ __('lexi.library.saved_lists') }}</span>
      <button class="btn btn-outline-secondary btn-sm" id="newCollectionTopBtn" type="button">
        <i class="bi bi-plus"></i> {{ __('lexi.library.new_collection') }}
      </button>
    </div>
    <div class="lists-grid" id="listsGrid">
    </div>
  </div>

  <div id="listDetail" class="library-list-panel mb-4" hidden>
    <div class="list-detail-header">
      <button class="ex-back-btn" id="listBackBtn" type="button">
        <i class="bi bi-arrow-left"></i> {{ __('lexi.library.back') }}
      </button>
      <div class="ex-panel-meta">
        <h2 class="ex-panel-title" id="listDetailTitle">{{ __('lexi.library.saved_default') }}</h2>
        <span class="library-count" id="libraryCount">{{ __('lexi.library.initial_count') }}</span>
      </div>
    </div>
    <div class="library-list-filter mt-3 mb-2">
      <i class="bi bi-search" aria-hidden="true"></i>
      <input type="search" id="savedWordFilter" class="form-control form-control-sm" placeholder="{{ __('lexi.library.filter_placeholder') }}">
    </div>
    <ul class="library-saved-list" id="librarySavedList">
      <li class="text-muted">{{ __('lexi.library.empty_saved_words') }}</li>
    </ul>
    <div class="d-flex gap-2 mt-2 align-items-center flex-wrap">
      <div id="listPagination" class="list-pagination" hidden>
        <button class="list-page-btn" id="listPagePrev" type="button"><i class="bi bi-chevron-left"></i> {{ __('lexi.library.previous') }}</button>
        <span id="listPageInfo" class="list-page-info">1 / 1</span>
        <button class="list-page-btn" id="listPageNext" type="button">{{ __('lexi.library.next') }} <i class="bi bi-chevron-right"></i></button>
      </div>
      <button class="btn btn-outline-danger btn-sm ms-auto" id="clearLibrary" type="button">{{ __('lexi.library.clear_list') }}</button>
    </div>
  </div>

  <section class="library-add-panel mb-4">
    <h2 class="h5 mb-0">{{ __('lexi.library.add_vocabulary') }}</h2>
    <p class="text-muted small mt-1 mb-3">{{ __('lexi.library.add_vocabulary_text') }}</p>

    <p class="add-panel-sublabel">{{ __('lexi.library.import_words') }}</p>
    <div class="import-grid mb-3">
      <article class="import-card import-card--file">
        <div class="import-card-icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
        <div class="import-card-content">
          <h3 class="import-card-title"><span class="method-num">1</span> {{ __('lexi.library.upload_file') }}</h3>
          <p class="import-card-desc">{{ __('lexi.library.upload_file_text') }}</p>
          <input id="wordFileInput" type="file" accept=".txt,.csv" style="display:none">
          <div id="dropZone" class="file-drop-zone mb-2">
            <i class="bi bi-cloud-arrow-up file-drop-icon"></i>
            <span class="file-drop-text">{{ __('lexi.library.drag_file') }}</span>
            <button id="chooseFileBtn" class="file-drop-btn" type="button">{{ __('lexi.library.browse_file') }}</button>
            <span id="fileNameDisplay" class="file-drop-name"></span>
          </div>
          <button id="importFileBtn" class="btn btn-primary btn-sm w-100" type="button">{{ __('lexi.library.import_file') }}</button>
        </div>
      </article>

      <article class="import-card import-card--paste">
        <div class="import-card-icon"><i class="bi bi-clipboard-plus"></i></div>
        <div class="import-card-content">
          <h3 class="import-card-title"><span class="method-num">2</span> {{ __('lexi.library.paste_text') }}</h3>
          <p class="import-card-desc">{{ __('lexi.library.paste_text_desc') }}</p>
          <div class="import-card-grow">
            <textarea id="pasteWordsInput" class="form-control form-control-sm h-100" rows="3" placeholder="heritage&#10;sustainable growth,&#10;check in.&#10;breakthrough;"></textarea>
          </div>
          <button id="importPasteBtn" class="btn btn-primary btn-sm w-100 mt-2" type="button">{{ __('lexi.library.import_text') }}</button>
        </div>
      </article>
    </div>

    <div class="add-panel-divider mt-1"><span>{{ __('lexi.library.or_explore_catalog') }}</span></div>

    <article class="import-card import-card--catalog">
      <div class="import-card-icon"><i class="bi bi-journals"></i></div>
      <div class="import-card-content catalog-content">
        <h3 class="import-card-title"><span class="method-num">3</span> {{ __('lexi.library.explore_catalog') }}</h3>
        <p class="import-card-desc">{{ __('lexi.library.explore_catalog_text') }}</p>
        <div class="catalog-controls">
          <div class="catalog-search-wrap">
            <i class="bi bi-search catalog-search-icon"></i>
            <input id="searchInput" class="form-control catalog-search" type="search" placeholder="{{ __('lexi.library.search_placeholder') }}" aria-label="{{ __('lexi.library.search_aria') }}">
          </div>
          <div class="catalog-filters">
            <select id="cefrFilter" class="form-select form-select-sm">
              <option value="all">{{ __('lexi.library.all_levels') }}</option>
              @foreach ($cefrLevels as $level)
                <option value="{{ $level }}">{{ $levelOptionLabel($level) }}</option>
              @endforeach
            </select>
            <select id="topicFilter" class="form-select form-select-sm">
              <option value="all">{{ __('lexi.library.all_topics') }}</option>
              @foreach ($catalogTopics as $topic)
                <option value="{{ $topic['value'] }}">{{ $topic['label'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </article>

    <section class="result-grid mt-3" data-library-grid>
      <article class="result-card" data-word-card data-language="en" data-cefr="B2" data-topic="cultura" data-word-id="w-heritage" data-word-label="to preserve heritage" data-translation="preservar el patrimonio">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">{{ __('lexi.categories.culture') }}</span></div>
          <h2 class="card-word">to preserve heritage</h2>
          <p class="card-translation">preservar el patrimonio</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B1" data-topic="business" data-word-id="w-growth" data-word-label="sustainable growth" data-translation="crecimiento sostenible">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">{{ __('lexi.categories.business') }}</span></div>
          <h2 class="card-word">sustainable growth</h2>
          <p class="card-translation">crecimiento sostenible</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="A2" data-topic="travel" data-word-id="w-checkin" data-word-label="check in at the hotel" data-translation="hacer el check-in en el hotel">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">{{ __('lexi.categories.travel') }}</span></div>
          <h2 class="card-word">check in at the hotel</h2>
          <p class="card-translation">hacer el check-in en el hotel</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B1" data-topic="science" data-word-id="w-energy" data-word-label="renewable energy sources" data-translation="fuentes de energía renovable">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">{{ __('lexi.categories.science') }}</span></div>
          <h2 class="card-word">renewable energy sources</h2>
          <p class="card-translation">fuentes de energía renovable</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="fr" data-cefr="A1" data-topic="home" data-word-id="w-maison" data-word-label="la maison" data-translation="la casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">{{ __('lexi.categories.home') }}</span></div>
          <h2 class="card-word">la maison</h2>
          <p class="card-translation">la casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="A1" data-topic="food" data-word-id="w-breakfast" data-word-label="have breakfast" data-translation="desayunar">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">{{ __('lexi.categories.food') }}</span></div>
          <h2 class="card-word">have breakfast</h2>
          <p class="card-translation">desayunar</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="A2" data-topic="health" data-word-id="w-appointment" data-word-label="book an appointment" data-translation="pedir una cita">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">{{ __('lexi.categories.health') }}</span></div>
          <h2 class="card-word">book an appointment</h2>
          <p class="card-translation">pedir una cita</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B2" data-topic="work" data-word-id="w-deadline" data-word-label="meet a deadline" data-translation="cumplir un plazo de entrega">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">{{ __('lexi.categories.work') }}</span></div>
          <h2 class="card-word">meet a deadline</h2>
          <p class="card-translation">cumplir un plazo de entrega</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="fr" data-cefr="A2" data-topic="travel" data-word-id="w-gare" data-word-label="la gare" data-translation="la estación de tren">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">{{ __('lexi.categories.travel') }}</span></div>
          <h2 class="card-word">la gare</h2>
          <p class="card-translation">la estación de tren</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B1" data-topic="education" data-word-id="w-critical" data-word-label="critical thinking" data-translation="pensamiento crítico">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">{{ __('lexi.categories.education') }}</span></div>
          <h2 class="card-word">critical thinking</h2>
          <p class="card-translation">pensamiento crítico</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B2" data-topic="culture" data-word-id="w-art" data-word-label="art exhibition" data-translation="exposición de arte">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">{{ __('lexi.categories.culture') }}</span></div>
          <h2 class="card-word">art exhibition</h2>
          <p class="card-translation">exposición de arte</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="C1" data-topic="business" data-word-id="w-negotiate" data-word-label="to negotiate terms" data-translation="negociar las condiciones">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-c1">C1</span><span class="topic-tag">{{ __('lexi.categories.business') }}</span></div>
          <h2 class="card-word">to negotiate terms</h2>
          <p class="card-translation">negociar las condiciones</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="C1" data-topic="science" data-word-id="w-biodiversity" data-word-label="protect biodiversity" data-translation="proteger la biodiversidad">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-c1">C1</span><span class="topic-tag">{{ __('lexi.categories.science') }}</span></div>
          <h2 class="card-word">protect biodiversity</h2>
          <p class="card-translation">proteger la biodiversidad</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B1" data-topic="work" data-word-id="w-commute" data-word-label="daily commute" data-translation="desplazamiento diario">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">{{ __('lexi.categories.work') }}</span></div>
          <h2 class="card-word">daily commute</h2>
          <p class="card-translation">desplazamiento diario</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B2" data-topic="education" data-word-id="w-fluent" data-word-label="become fluent" data-translation="llegar a tener fluidez">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">{{ __('lexi.categories.education') }}</span></div>
          <h2 class="card-word">become fluent</h2>
          <p class="card-translation">llegar a tener fluidez</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="A2" data-topic="food" data-word-id="w-recipe" data-word-label="follow a recipe" data-translation="seguir una receta">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">{{ __('lexi.categories.food') }}</span></div>
          <h2 class="card-word">follow a recipe</h2>
          <p class="card-translation">seguir una receta</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="A1" data-topic="education" data-word-id="w-library" data-word-label="public library" data-translation="biblioteca pública">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">{{ __('lexi.categories.education') }}</span></div>
          <h2 class="card-word">public library</h2>
          <p class="card-translation">biblioteca pública</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="C1" data-topic="politics" data-word-id="w-treaty" data-word-label="sign a treaty" data-translation="firmar un tratado">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-c1">C1</span><span class="topic-tag">{{ __('lexi.categories.politics') }}</span></div>
          <h2 class="card-word">sign a treaty</h2>
          <p class="card-translation">firmar un tratado</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B2" data-topic="work" data-word-id="w-freelance" data-word-label="freelance designer" data-translation="diseñador freelance">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">{{ __('lexi.categories.work') }}</span></div>
          <h2 class="card-word">freelance designer</h2>
          <p class="card-translation">diseñador freelance</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="fr" data-cefr="A1" data-topic="food" data-word-id="w-boulangerie" data-word-label="la boulangerie" data-translation="la panadería">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">{{ __('lexi.categories.food') }}</span></div>
          <h2 class="card-word">la boulangerie</h2>
          <p class="card-translation">la panadería</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="C2" data-topic="culture" data-word-id="w-zeitgeist" data-word-label="capture the zeitgeist" data-translation="capturar el espíritu de la época">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-c2">C2</span><span class="topic-tag">{{ __('lexi.categories.culture') }}</span></div>
          <h2 class="card-word">capture the zeitgeist</h2>
          <p class="card-translation">capturar el espíritu de la época</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="en" data-cefr="B2" data-topic="health" data-word-id="w-empathy" data-word-label="show empathy" data-translation="mostrar empatía">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">{{ __('lexi.categories.health') }}</span></div>
          <h2 class="card-word">show empathy</h2>
          <p class="card-translation">mostrar empatía</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="de" data-cefr="A1" data-topic="home" data-word-id="w-haus" data-word-label="das Haus" data-translation="la casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">{{ __('lexi.categories.home') }}</span></div>
          <h2 class="card-word">das Haus</h2>
          <p class="card-translation">la casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="de" data-cefr="A2" data-topic="work" data-word-id="w-arbeit" data-word-label="die Arbeit" data-translation="el trabajo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">{{ __('lexi.categories.work') }}</span></div>
          <h2 class="card-word">die Arbeit</h2>
          <p class="card-translation">el trabajo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="de" data-cefr="B1" data-topic="travel" data-word-id="w-reise" data-word-label="die Reise" data-translation="el viaje">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">{{ __('lexi.categories.travel') }}</span></div>
          <h2 class="card-word">die Reise</h2>
          <p class="card-translation">el viaje</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="no" data-cefr="A1" data-topic="nature" data-word-id="w-vaer" data-word-label="været" data-translation="el tiempo meteorológico">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">nature</span></div>
          <h2 class="card-word">været</h2>
          <p class="card-translation">el tiempo meteorológico</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="no" data-cefr="A1" data-topic="education" data-word-id="w-bok" data-word-label="en bok" data-translation="un libro">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">education</span></div>
          <h2 class="card-word">en bok</h2>
          <p class="card-translation">un libro</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="no" data-cefr="B1" data-topic="nature" data-word-id="w-fjord" data-word-label="fjorden" data-translation="el fiordo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">nature</span></div>
          <h2 class="card-word">fjorden</h2>
          <p class="card-translation">el fiordo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="fi" data-cefr="A1" data-topic="home" data-word-id="w-talo" data-word-label="talo" data-translation="la casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">talo</h2>
          <p class="card-translation">la casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="fi" data-cefr="A1" data-topic="culture" data-word-id="w-sauna" data-word-label="sauna" data-translation="la sauna">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">sauna</h2>
          <p class="card-translation">la sauna</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="fi" data-cefr="A2" data-topic="education" data-word-id="w-koulu" data-word-label="koulu" data-translation="la escuela">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">education</span></div>
          <h2 class="card-word">koulu</h2>
          <p class="card-translation">la escuela</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ko" data-cefr="A1" data-topic="social" data-word-id="w-annyeong" data-word-label="안녕하세요" data-translation="hola / buenos días">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">안녕하세요</h2>
          <p class="card-translation">hola / buenos días</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ko" data-cefr="A1" data-topic="food" data-word-id="w-bap" data-word-label="밥" data-translation="arroz / comida">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">밥</h2>
          <p class="card-translation">arroz / comida</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ko" data-cefr="B1" data-topic="health" data-word-id="w-haengbok" data-word-label="행복" data-translation="felicidad">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">health</span></div>
          <h2 class="card-word">행복</h2>
          <p class="card-translation">felicidad</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ru" data-cefr="A1" data-topic="social" data-word-id="w-privet" data-word-label="привет" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">привет</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ru" data-cefr="A1" data-topic="education" data-word-id="w-kniga" data-word-label="книга" data-translation="libro">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">education</span></div>
          <h2 class="card-word">книга</h2>
          <p class="card-translation">libro</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ru" data-cefr="A2" data-topic="work" data-word-id="w-rabota" data-word-label="работа" data-translation="trabajo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">work</span></div>
          <h2 class="card-word">работа</h2>
          <p class="card-translation">trabajo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ua" data-cefr="A1" data-topic="social" data-word-id="w-dobroho" data-word-label="Доброго ранку" data-translation="buenos días">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">Доброго ранку</h2>
          <p class="card-translation">buenos días</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ua" data-cefr="A1" data-topic="home" data-word-id="w-dim" data-word-label="дім" data-translation="casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">дім</h2>
          <p class="card-translation">casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ua" data-cefr="B1" data-topic="social" data-word-id="w-liubyty" data-word-label="любити" data-translation="amar">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">любити</h2>
          <p class="card-translation">amar</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="it" data-cefr="A1" data-topic="food" data-word-id="w-mangiare" data-word-label="mangiare" data-translation="comer">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">mangiare</h2>
          <p class="card-translation">comer</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="it" data-cefr="A2" data-topic="travel" data-word-id="w-treno" data-word-label="il treno" data-translation="el tren">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">travel</span></div>
          <h2 class="card-word">il treno</h2>
          <p class="card-translation">el tren</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="it" data-cefr="B1" data-topic="culture" data-word-id="w-bellezza" data-word-label="la bellezza" data-translation="la belleza">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">la bellezza</h2>
          <p class="card-translation">la belleza</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="dk" data-cefr="A1" data-topic="social" data-word-id="w-hej" data-word-label="hej" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">hej</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="dk" data-cefr="A1" data-topic="home" data-word-id="w-hus" data-word-label="huset" data-translation="la casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">huset</h2>
          <p class="card-translation">la casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="dk" data-cefr="B1" data-topic="nature" data-word-id="w-skov" data-word-label="skoven" data-translation="el bosque">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">nature</span></div>
          <h2 class="card-word">skoven</h2>
          <p class="card-translation">el bosque</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="gr" data-cefr="A1" data-topic="social" data-word-id="w-yeia" data-word-label="Γεια σου" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">Γεια σου</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="gr" data-cefr="A1" data-topic="food" data-word-id="w-nero" data-word-label="νερό" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">νερό</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="gr" data-cefr="B1" data-topic="culture" data-word-id="w-thalassa" data-word-label="Θάλασσα" data-translation="el mar">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">Θάλασσα</h2>
          <p class="card-translation">el mar</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="zh" data-cefr="A1" data-topic="social" data-word-id="w-nihao" data-word-label="你好" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">你好</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="zh" data-cefr="A1" data-topic="food" data-word-id="w-chi" data-word-label="吃" data-translation="comer">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">吃</h2>
          <p class="card-translation">comer</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="zh" data-cefr="A2" data-topic="work" data-word-id="w-gongzuo" data-word-label="工作" data-translation="trabajo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">work</span></div>
          <h2 class="card-word">工作</h2>
          <p class="card-translation">trabajo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="pt" data-cefr="A1" data-topic="travel" data-word-id="w-praia" data-word-label="a praia" data-translation="la playa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">travel</span></div>
          <h2 class="card-word">a praia</h2>
          <p class="card-translation">la playa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="pt" data-cefr="B1" data-topic="culture" data-word-id="w-saudade" data-word-label="saudade" data-translation="nostalgia">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">saudade</h2>
          <p class="card-translation">nostalgia</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="pt" data-cefr="A2" data-topic="social" data-word-id="w-coracao" data-word-label="o coração" data-translation="el corazón">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">o coração</h2>
          <p class="card-translation">el corazón</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="sv" data-cefr="A1" data-topic="social" data-word-id="w-hej-sv" data-word-label="hej" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">hej</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="sv" data-cefr="A1" data-topic="food" data-word-id="w-fika" data-word-label="fika" data-translation="pausa para el café">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">fika</h2>
          <p class="card-translation">pausa para el café</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="sv" data-cefr="B1" data-topic="culture" data-word-id="w-lagom" data-word-label="lagom" data-translation="el término medio">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">lagom</h2>
          <p class="card-translation">el término medio</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ja" data-cefr="A1" data-topic="social" data-word-id="w-arigato" data-word-label="ありがとう" data-translation="gracias">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">ありがとう</h2>
          <p class="card-translation">gracias</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ja" data-cefr="A1" data-topic="food" data-word-id="w-taberu" data-word-label="食べる" data-translation="comer">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">食べる</h2>
          <p class="card-translation">comer</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ja" data-cefr="A2" data-topic="nature" data-word-id="w-sakura" data-word-label="桜" data-translation="flor de cerezo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">nature</span></div>
          <h2 class="card-word">桜</h2>
          <p class="card-translation">flor de cerezo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="bg" data-cefr="A1" data-topic="social" data-word-id="w-zdravey" data-word-label="Здравей" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">Здравей</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="bg" data-cefr="A1" data-topic="food" data-word-id="w-voda-bg" data-word-label="вода" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">вода</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="bg" data-cefr="B1" data-topic="culture" data-word-id="w-krasota" data-word-label="красота" data-translation="belleza">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">красота</h2>
          <p class="card-translation">belleza</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ro" data-cefr="A1" data-topic="social" data-word-id="w-buna-ro" data-word-label="buna ziua" data-translation="buenos días">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">buna ziua</h2>
          <p class="card-translation">buenos días</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ro" data-cefr="A1" data-topic="food" data-word-id="w-apa-ro" data-word-label="apa" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">apa</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ro" data-cefr="A2" data-topic="social" data-word-id="w-dragoste" data-word-label="dragoste" data-translation="amor">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">dragoste</h2>
          <p class="card-translation">amor</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="cs" data-cefr="A1" data-topic="social" data-word-id="w-dobry-cs" data-word-label="dobrý den" data-translation="buenos días">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">dobrý den</h2>
          <p class="card-translation">buenos días</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="cs" data-cefr="A1" data-topic="food" data-word-id="w-voda-cs" data-word-label="voda" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">voda</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="cs" data-cefr="A1" data-topic="culture" data-word-id="w-praha" data-word-label="Praha" data-translation="Praga">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">Praha</h2>
          <p class="card-translation">Praga</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="sk" data-cefr="A1" data-topic="social" data-word-id="w-dobry-sk" data-word-label="dobrý deň" data-translation="buenos días">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">dobrý deň</h2>
          <p class="card-translation">buenos días</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="sk" data-cefr="A1" data-topic="food" data-word-id="w-voda-sk" data-word-label="voda" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">voda</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="sk" data-cefr="A2" data-topic="social" data-word-id="w-rodina" data-word-label="rodina" data-translation="familia">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">rodina</h2>
          <p class="card-translation">familia</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hu" data-cefr="A1" data-topic="social" data-word-id="w-jonapot" data-word-label="jó napot" data-translation="buenos días">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">jó napot</h2>
          <p class="card-translation">buenos días</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hu" data-cefr="A1" data-topic="food" data-word-id="w-viz" data-word-label="víz" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">víz</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hu" data-cefr="B1" data-topic="social" data-word-id="w-szerelem" data-word-label="szerelem" data-translation="amor">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">szerelem</h2>
          <p class="card-translation">amor</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ar" data-cefr="A1" data-topic="social" data-word-id="w-marhaba" data-word-label="مرحبا" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">مرحبا</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ar" data-cefr="A1" data-topic="social" data-word-id="w-shukran" data-word-label="شكرًا" data-translation="gracias">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">شكرًا</h2>
          <p class="card-translation">gracias</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="ar" data-cefr="A1" data-topic="food" data-word-id="w-maa" data-word-label="ماء" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">ماء</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="vi" data-cefr="A1" data-topic="social" data-word-id="w-xinchao" data-word-label="xin chào" data-translation="hola">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">xin chào</h2>
          <p class="card-translation">hola</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="vi" data-cefr="A1" data-topic="social" data-word-id="w-camon" data-word-label="cảm ơn" data-translation="gracias">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">social</span></div>
          <h2 class="card-word">cảm ơn</h2>
          <p class="card-translation">gracias</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="vi" data-cefr="A1" data-topic="food" data-word-id="w-nuoc" data-word-label="nước" data-translation="agua">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">nước</h2>
          <p class="card-translation">agua</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="tr" data-cefr="A1" data-topic="food" data-word-id="w-tr-ekmek" data-word-label="ekmek" data-translation="pan">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">ekmek</h2>
          <p class="card-translation">pan</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="tr" data-cefr="A2" data-topic="travel" data-word-id="w-tr-havaalani" data-word-label="havaalani" data-translation="aeropuerto">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">travel</span></div>
          <h2 class="card-word">havaalani</h2>
          <p class="card-translation">aeropuerto</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="tr" data-cefr="B1" data-topic="work" data-word-id="w-tr-toplanti" data-word-label="toplantı" data-translation="reunión">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">work</span></div>
          <h2 class="card-word">toplantı</h2>
          <p class="card-translation">reunión</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="tr" data-cefr="B2" data-topic="culture" data-word-id="w-tr-tarih" data-word-label="tarih" data-translation="historia / fecha">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">tarih</h2>
          <p class="card-translation">historia / fecha</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="id" data-cefr="A1" data-topic="home" data-word-id="w-id-rumah" data-word-label="rumah" data-translation="casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">rumah</h2>
          <p class="card-translation">casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="id" data-cefr="A2" data-topic="food" data-word-id="w-id-nasi" data-word-label="nasi goreng" data-translation="arroz frito">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">nasi goreng</h2>
          <p class="card-translation">arroz frito</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="id" data-cefr="B1" data-topic="travel" data-word-id="w-id-perjalanan" data-word-label="perjalanan" data-translation="viaje">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">travel</span></div>
          <h2 class="card-word">perjalanan</h2>
          <p class="card-translation">viaje</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="id" data-cefr="B2" data-topic="education" data-word-id="w-id-pengetahuan" data-word-label="pengetahuan" data-translation="conocimiento">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">education</span></div>
          <h2 class="card-word">pengetahuan</h2>
          <p class="card-translation">conocimiento</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="he" data-cefr="A1" data-topic="home" data-word-id="w-he-bayit" data-word-label="בַּיִת" data-translation="casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">בַּיִת</h2>
          <p class="card-translation">casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="he" data-cefr="A2" data-topic="food" data-word-id="w-he-lechem" data-word-label="לֶחֶם" data-translation="pan">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">לֶחֶם</h2>
          <p class="card-translation">pan</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="he" data-cefr="B1" data-topic="work" data-word-id="w-he-avoda" data-word-label="עֲבוֹדָה" data-translation="trabajo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">work</span></div>
          <h2 class="card-word">עֲבוֹדָה</h2>
          <p class="card-translation">trabajo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="he" data-cefr="B2" data-topic="culture" data-word-id="w-he-tarbut" data-word-label="תַּרְבּוּת" data-translation="cultura">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">תַּרְבּוּת</h2>
          <p class="card-translation">cultura</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hi" data-cefr="A1" data-topic="home" data-word-id="w-hi-ghar" data-word-label="घर" data-translation="casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">घर</h2>
          <p class="card-translation">casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hi" data-cefr="A2" data-topic="food" data-word-id="w-hi-khana" data-word-label="खाना" data-translation="comida">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">खाना</h2>
          <p class="card-translation">comida</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hi" data-cefr="B1" data-topic="education" data-word-id="w-hi-vidyalaya" data-word-label="विद्यालय" data-translation="escuela">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">education</span></div>
          <h2 class="card-word">विद्यालय</h2>
          <p class="card-translation">escuela</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="hi" data-cefr="B2" data-topic="work" data-word-id="w-hi-vyapar" data-word-label="व्यापार" data-translation="negocio / comercio">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">work</span></div>
          <h2 class="card-word">व्यापार</h2>
          <p class="card-translation">negocio / comercio</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="th" data-cefr="A1" data-topic="food" data-word-id="w-th-aharn" data-word-label="อาหาร" data-translation="comida">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">อาหาร</h2>
          <p class="card-translation">comida</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="th" data-cefr="A2" data-topic="home" data-word-id="w-th-ban" data-word-label="บ้าน" data-translation="casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">บ้าน</h2>
          <p class="card-translation">casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="th" data-cefr="B1" data-topic="travel" data-word-id="w-th-thongthiao" data-word-label="ท่องเที่ยว" data-translation="viajar / turismo">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">travel</span></div>
          <h2 class="card-word">ท่องเที่ยว</h2>
          <p class="card-translation">viajar / turismo</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="th" data-cefr="B2" data-topic="culture" data-word-id="w-th-watthanatham" data-word-label="วัฒนธรรม" data-translation="cultura">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">วัฒนธรรม</h2>
          <p class="card-translation">cultura</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="nl" data-cefr="A1" data-topic="home" data-word-id="w-nl-huis" data-word-label="het huis" data-translation="la casa">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a1">A1</span><span class="topic-tag">home</span></div>
          <h2 class="card-word">het huis</h2>
          <p class="card-translation">la casa</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="nl" data-cefr="A2" data-topic="food" data-word-id="w-nl-brood" data-word-label="het brood" data-translation="el pan">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-a2">A2</span><span class="topic-tag">food</span></div>
          <h2 class="card-word">het brood</h2>
          <p class="card-translation">el pan</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="nl" data-cefr="B1" data-topic="work" data-word-id="w-nl-vergadering" data-word-label="de vergadering" data-translation="la reunión">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b1">B1</span><span class="topic-tag">work</span></div>
          <h2 class="card-word">de vergadering</h2>
          <p class="card-translation">la reunión</p>
        </div>
      </article>

      <article class="result-card" data-word-card data-language="nl" data-cefr="B2" data-topic="culture" data-word-id="w-nl-schilderij" data-word-label="het schilderij" data-translation="el cuadro / la pintura">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="Guardar en Guardado"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta"><span class="cefr-badge cefr-b2">B2</span><span class="topic-tag">culture</span></div>
          <h2 class="card-word">het schilderij</h2>
          <p class="card-translation">el cuadro / la pintura</p>
        </div>
      </article>
    </section>

    <nav class="library-pagination mt-2" id="libraryPagination" aria-label="Paginación del catálogo"></nav>

    <div class="no-results" id="noResults" hidden>
      <p class="text-muted small mb-0">Sin resultados. Prueba con otra palabra o cambia el nivel.</p>
    </div>
  </section>
</main>

<div id="saveDropdown" class="save-dropdown" hidden role="dialog" aria-label="Opciones de guardado">
  <div class="save-dropdown-main" id="saveDropdownMain"></div>
  <div class="save-dropdown-divider" id="saveDropdownCollectionsDivider" hidden></div>
  <ul class="save-dropdown-lists" id="saveDropdownLists" hidden></ul>
  <div class="save-dropdown-divider"></div>
  <div class="save-dropdown-new-wrap" id="saveDropdownNewWrap">
    <button class="save-dropdown-new-btn" id="saveDropdownNewBtn" type="button">
      <i class="bi bi-plus-circle"></i> Nueva colección
    </button>
  </div>
</div>

<div id="renameCollModal" class="lexi-modal-backdrop" hidden>
  <div class="lexi-modal" role="dialog" aria-modal="true" aria-labelledby="renameModalTitle">
    <div class="lexi-modal-header">
      <div class="lexi-modal-icon lexi-modal-icon--brand"><i class="bi bi-pencil-fill"></i></div>
      <p class="lexi-modal-title" id="renameModalTitle">Renombrar lista</p>
    </div>
    <input type="text" id="renameCollInput" class="lexi-modal-input" placeholder="Nombre de la lista" maxlength="40">
    <div class="lexi-modal-actions">
      <button type="button" class="lexi-modal-btn lexi-modal-btn--ghost" id="renameCollCancel">Cancelar</button>
      <button type="button" class="lexi-modal-btn lexi-modal-btn--primary" id="renameCollConfirm"><i class="bi bi-check2"></i> Guardar</button>
    </div>
  </div>
</div>

<div id="createCollModal" class="lexi-modal-backdrop" hidden>
  <div class="lexi-modal" role="dialog" aria-modal="true" aria-labelledby="createModalTitle">
    <div class="lexi-modal-header">
      <div class="lexi-modal-icon lexi-modal-icon--brand"><i class="bi bi-folder-plus"></i></div>
      <p class="lexi-modal-title" id="createModalTitle">Nueva colección</p>
      <p class="lexi-modal-body">Crea una lista personalizada para guardar vocabulario relacionado.</p>
    </div>
    <input type="text" id="createCollInput" class="lexi-modal-input" placeholder="Nombre de la colección" maxlength="40">
    <div class="lexi-modal-actions">
      <button type="button" class="lexi-modal-btn lexi-modal-btn--ghost" id="createCollCancel">Cancelar</button>
      <button type="button" class="lexi-modal-btn lexi-modal-btn--primary" id="createCollConfirm"><i class="bi bi-plus-lg"></i> Crear</button>
    </div>
  </div>
</div>

<div id="deleteCollModal" class="lexi-modal-backdrop" hidden>
  <div class="lexi-modal lexi-modal--confirm" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="lexi-modal-header">
      <div class="lexi-modal-icon lexi-modal-icon--danger"><i class="bi bi-trash-fill"></i></div>
      <p class="lexi-modal-title" id="deleteModalTitle">Eliminar colección</p>
      <p class="lexi-modal-body" id="deleteModalBody">Esta acción no se puede deshacer.</p>
    </div>
    <div class="lexi-modal-actions">
      <button type="button" class="lexi-modal-btn lexi-modal-btn--ghost" id="deleteCollCancel">Cancelar</button>
      <button type="button" class="lexi-modal-btn lexi-modal-btn--danger" id="deleteCollConfirm"><i class="bi bi-trash"></i> Eliminar</button>
    </div>
  </div>
</div>

<div id="clearCollModal" class="lexi-modal-backdrop" hidden>
  <div class="lexi-modal lexi-modal--confirm" role="dialog" aria-modal="true" aria-labelledby="clearModalTitle">
    <div class="lexi-modal-header">
      <div class="lexi-modal-icon lexi-modal-icon--warning"><i class="bi bi-eraser-fill"></i></div>
      <p class="lexi-modal-title" id="clearModalTitle">Vaciar lista</p>
      <p class="lexi-modal-body" id="clearModalBody">Se eliminarán todas las palabras de esta colección.</p>
    </div>
    <div class="lexi-modal-actions">
      <button type="button" class="lexi-modal-btn lexi-modal-btn--ghost" id="clearCollCancel">Cancelar</button>
      <button type="button" class="lexi-modal-btn lexi-modal-btn--warning" id="clearCollConfirm"><i class="bi bi-eraser"></i> Vaciar</button>
    </div>
  </div>
</div>