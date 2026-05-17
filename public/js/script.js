document.addEventListener("DOMContentLoaded", () => {
  const JS_I18N = window.lexiTranslations || {};
  const getNestedValue = (source, key) => key.split(".").reduce((value, segment) => value && value[segment], source);
  const t = (key, replacements = {}) => {
    const template = getNestedValue(JS_I18N, key);
    if (typeof template !== "string") return key;

    return template.replace(/:([a-zA-Z_]+)/g, (_, token) => replacements[token] ?? `:${token}`);
  };
  const countWordLabel = (count, singularKey, pluralKey) => `${count} ${t(count === 1 ? singularKey : pluralKey)}`;
  const getWordLabel = (value) => value || t("js.word_fallback");
  const isServerRenderedProfile = document.body.hasAttribute("data-server-profile");
  const hasServerLibrary = window.location.pathname.endsWith("/biblioteca.html");
  const canUseServerProgress = ["/app.html", "/biblioteca.html", "/carrito.html", "/ejercicios.html", "/progreso.html", "/perfil.html"].includes(window.location.pathname);
  const canUseServerSession = canUseServerProgress;
  let serverLibrary = [];
  let serverCollections = [];
  let serverCatalog = [];
  let serverLibraryLoadPromise = null;
  let serverProgressState = null;
  let serverProgressLoadPromise = null;
  let serverProgressLanguage = null;
  let serverSessionState = null;
  let serverSessionLoadPromise = null;

  const CART_KEY = "lexiCart";
  const LIBRARY_KEY = "lexiLibrary";
  const LANG_KEY = "lexiLang";
  const SESSION_ADMIN_KEY = "lexiIsAdmin";

  const LANG_CONFIG = {
    // Europa Occidental (lenguas más estudiadas)
    en: { flag: "icons/flags/united_kingdom_flag.svg", label: t("languages.en") },
    es: { flag: "icons/flags/spain_flag.svg", label: t("languages.es") },
    fr: { flag: "icons/flags/france_flag.svg", label: t("languages.fr") },
    de: { flag: "icons/flags/germany_flag.svg", label: t("languages.de") },
    it: { flag: "icons/flags/italy_flag.svg", label: t("languages.it") },
    pt: { flag: "icons/flags/brazil_flag.svg", label: t("languages.pt") },
    // Germánico continental
    nl: { flag: "icons/flags/netherlands_flag.svg", label: t("languages.nl") },
    // Países Nórdicos
    no: { flag: "icons/flags/norway_flag.svg", label: t("languages.no") },
    sv: { flag: "icons/flags/sweden_flag.svg", label: t("languages.sv") },
    dk: { flag: "icons/flags/denmark_flag.svg", label: t("languages.dk") },
    fi: { flag: "icons/flags/finland_flag.svg", label: t("languages.fi") },
    // Europa del Este (eslavos)
    ru: { flag: "icons/flags/russia_flag.svg", label: t("languages.ru") },
    ua: { flag: "icons/flags/ukraine_flag.svg", label: t("languages.ua") },
    pl: { flag: "icons/flags/poland_flag.svg", label: t("languages.pl") },
    cs: { flag: "icons/flags/czech_republic_flag.svg", label: t("languages.cs") },
    sk: { flag: "icons/flags/slovakia_flag.svg", label: t("languages.sk") },
    // Europa Central y Balcánica
    hu: { flag: "icons/flags/hungary_flag.svg", label: t("languages.hu") },
    ro: { flag: "icons/flags/romania_flag.svg", label: t("languages.ro") },
    bg: { flag: "icons/flags/bulgaria_flag.svg", label: t("languages.bg") },
    gr: { flag: "icons/flags/greece_flag.svg", label: t("languages.gr") },
    // Oriente Medio
    tr: { flag: "icons/flags/turkey_flag.svg", label: t("languages.tr") },
    ar: { flag: "icons/flags/saudi_arabia_flag.svg", label: t("languages.ar") },
    he: { flag: "icons/flags/israel_flag.svg", label: t("languages.he") },
    // Asia Oriental
    zh: { flag: "icons/flags/china_flag.svg", label: t("languages.zh") },
    ja: { flag: "icons/flags/japan_flag.svg", label: t("languages.ja") },
    ko: { flag: "icons/flags/south_korea_flag.svg", label: t("languages.ko") },
    // Asia del Sur y Sudeste
    hi: { flag: "icons/flags/india_flag.svg", label: t("languages.hi") },
    th: { flag: "icons/flags/thailand_flag.svg", label: t("languages.th") },
    vi: { flag: "icons/flags/vietnam_flag.svg", label: t("languages.vi") },
    id: { flag: "icons/flags/indonesia_flag.svg", label: t("languages.id") },
  };

  const LANG_HISTORY_KEY = "lexiLangHistory";
  const getNativeLang = () => serverSessionState?.user?.mother_tongue_code || "es";
  const getLangHistory = () => {
    try { return JSON.parse(localStorage.getItem(LANG_HISTORY_KEY) || "[]"); } catch { return []; }
  };
  const recordLangActivity = (lang) => {
    if (!lang || lang === getNativeLang()) return;
    const hist = getLangHistory();
    if (!hist.find(h => h.lang === lang)) {
      hist.push({ lang, firstAt: Date.now() });
      localStorage.setItem(LANG_HISTORY_KEY, JSON.stringify(hist));
    }
  };

  const getActiveLang = () => localStorage.getItem(LANG_KEY) || "en";
  const setActiveLang = (lang) => localStorage.setItem(LANG_KEY, lang);
  const setStoredAdminState = (isAdmin) => localStorage.setItem(SESSION_ADMIN_KEY, isAdmin ? "1" : "0");
  const isCurrentUserAdmin = () => serverSessionState?.user?.is_admin ?? (localStorage.getItem(SESSION_ADMIN_KEY) === "1");

  const syncActiveLangFlag = () => {
    const flagImg = document.getElementById("activeLangFlag");
    const cfg = LANG_CONFIG[getActiveLang()] || LANG_CONFIG.en;
    if (flagImg && cfg) flagImg.src = cfg.flag;
  };

  const createPageLoader = () => {
    if (document.getElementById("pageLoader")) return document.getElementById("pageLoader");

    const loader = document.createElement("div");
    loader.id = "pageLoader";
    loader.className = "page-loader";
    loader.hidden = true;
    loader.innerHTML = `
      <div class="page-loader__panel" role="status" aria-live="polite">
        <span class="page-loader__spinner" aria-hidden="true"></span>
        <span class="page-loader__label">${t("js.loading")}</span>
      </div>
    `;
    document.body.appendChild(loader);
    return loader;
  };

  const setPageLoading = (isLoading, label = t("js.loading")) => {
    if (!canUseServerSession) return;
    const loader = createPageLoader();
    const labelEl = loader.querySelector(".page-loader__label");
    if (labelEl) labelEl.textContent = label;
    loader.hidden = !isLoading;
    document.body.classList.toggle("page-is-loading", isLoading);
  };

  const getXsrfToken = () => {
    const cookie = document.cookie
      .split("; ")
      .find((item) => item.startsWith("XSRF-TOKEN="));

    return cookie ? decodeURIComponent(cookie.split("=")[1]) : "";
  };

  const libraryApiFetch = async (url, options = {}) => {
    const method = (options.method || "GET").toUpperCase();
    const headers = {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
      ...(options.headers || {}),
    };

    if (method !== "GET") {
      headers["Content-Type"] = "application/json";
      headers["X-XSRF-TOKEN"] = getXsrfToken();
    }

    return fetch(url, {
      credentials: "same-origin",
      ...options,
      headers,
    });
  };

  const escapeHtml = (value) => String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");

  const renderLibraryCatalog = (catalog = []) => {
    const grid = document.querySelector("[data-library-grid]");
    if (!grid || !Array.isArray(catalog) || !catalog.length) return false;

    grid.innerHTML = catalog.map((item) => {
      const cefr = String(item.cefr || "").toUpperCase();
      const topic = String(item.topic || "");
      const badgeClass = cefr ? `cefr-${cefr.toLowerCase()}` : "";
      const meta = [
        cefr ? `<span class="cefr-badge ${badgeClass}">${escapeHtml(cefr)}</span>` : "",
        topic ? `<span class="topic-tag">${t('categories.' + topic)}</span>` : "",
      ].filter(Boolean).join("");

      return `
      <article class="result-card" data-word-card data-language="${escapeHtml(item.language)}" data-cefr="${escapeHtml(cefr)}" data-topic="${escapeHtml(topic)}" data-word-id="${escapeHtml(item.id)}" data-word-label="${escapeHtml(item.label)}" data-translation="${escapeHtml(item.translation || "")}">
        <div class="card-accent"></div>
        <div class="word-save-split"><button class="word-save-split__main" type="button" data-save-word aria-label="${t("js.save.save_to_main", { list: MAIN_LIBRARY_NAME })}"><i class="bi bi-bookmark"></i></button></div>
        <div class="card-body-inner">
          <div class="card-meta">${meta}</div>
          <h2 class="card-word">${escapeHtml(item.label)}</h2>
          <p class="card-translation">${escapeHtml(item.translation || "")}</p>
        </div>
      </article>`;
    }).join("");

    document.getElementById("noResults")?.setAttribute("hidden", "hidden");
    if (document.getElementById("libraryPagination")) {
      document.getElementById("libraryPagination").innerHTML = "";
    }

    return true;
  };

  const applyServerSessionState = (payload) => {
    serverSessionState = payload || null;

    const activeCode = payload?.active_language?.code;
    if (activeCode && LANG_CONFIG[activeCode]) {
      setActiveLang(activeCode);
      recordLangActivity(activeCode);
    }

    setStoredAdminState(Boolean(payload?.user?.is_admin));
    syncActiveLangFlag();

    return serverSessionState;
  };

  const loadServerSessionState = async () => {
    if (!canUseServerSession) {
      syncActiveLangFlag();
      return null;
    }

    if (serverSessionState) return serverSessionState;
    if (serverSessionLoadPromise) return serverSessionLoadPromise;

    serverSessionLoadPromise = libraryApiFetch("/api/session/state")
      .then((response) => response.ok ? response.json() : Promise.reject(response))
      .then((payload) => applyServerSessionState(payload))
      .catch(() => {
        serverSessionState = null;
        setStoredAdminState(false);
        syncActiveLangFlag();
        return null;
      })
      .finally(() => {
        serverSessionLoadPromise = null;
      });

    return serverSessionLoadPromise;
  };

  const persistActiveLanguage = async (lang) => {
    if (!canUseServerSession) {
      setActiveLang(lang);
      syncActiveLangFlag();
      return true;
    }

    const response = await libraryApiFetch("/api/session/active-language", {
      method: "PUT",
      body: JSON.stringify({ language_code: lang }),
    });

    if (!response.ok) {
      throw new Error("active-language-update-failed");
    }

    const payload = await response.json();
    applyServerSessionState(payload);

    return true;
  };

  const syncServerLibraryState = (payload = {}) => {
    serverLibrary = Array.isArray(payload.items) ? payload.items : [];
    serverCollections = Array.isArray(payload.collections) ? payload.collections : serverCollections;
    serverCatalog = Array.isArray(payload.catalog) ? payload.catalog : serverCatalog;
    renderLibraryCatalog(serverCatalog);
    updateAllBookmarkStates();
    window.dispatchEvent(new Event("lexi-library-updated"));
  };

  const loadServerLibrary = async () => {
    if (!hasServerLibrary) return [];
    if (serverLibraryLoadPromise) return serverLibraryLoadPromise;

    const language = encodeURIComponent(getActiveLang());

    serverLibraryLoadPromise = libraryApiFetch(`/api/library/state?language=${language}`)
      .then((response) => response.ok ? response.json() : Promise.reject(response))
      .then((payload) => {
        syncServerLibraryState(payload || {});
        return payload.items || [];
      })
      .catch(() => {
        syncServerLibraryState({ items: [], collections: [] });
        return [];
      })
      .finally(() => {
        serverLibraryLoadPromise = null;
      });

    return serverLibraryLoadPromise;
  };

  const buildLocalProgressState = () => {
    const library = getLibrary();
    let stats = {};
    try { stats = JSON.parse(localStorage.getItem("lexiStats") || "{}"); } catch {}

    const wordsByLanguageMap = {};
    library.forEach((item) => {
      const code = item.language || getActiveLang();
      wordsByLanguageMap[code] = (wordsByLanguageMap[code] || 0) + 1;
    });

    const wordCount = library.length;
    const level = getCefrLevel(wordCount);
    const progressPercent = level.next
      ? Math.min(100, Math.round(((wordCount - level.min) / (level.next - level.min)) * 100))
      : 100;

    return {
      user: {
        first_name: "",
        full_name: "",
      },
      active_language: {
        code: getActiveLang(),
        label: LANG_CONFIG[getActiveLang()]?.label || getActiveLang().toUpperCase(),
      },
      summary: {
        saved_words_total: wordCount,
        saved_words_active: library.filter((item) => (item.language || getActiveLang()) === getActiveLang()).length,
        collections_active: getCollections().filter((collection) => !collection.lang || collection.lang === getActiveLang()).length,
        recent_words: [...library].reverse().slice(0, 6),
        words_by_language: Object.entries(wordsByLanguageMap).map(([code, count]) => ({
          code,
          label: LANG_CONFIG[code]?.label || code.toUpperCase(),
          count,
        })),
      },
      exercises: {
        streak: stats.streak || 0,
        total_completed: (stats.reading || 0) + (stats.listening || 0) + (stats.speaking || 0) + (stats.writing || 0) + (stats.mix || 0),
        modes: {
          reading: stats.reading || 0,
          listening: stats.listening || 0,
          speaking: stats.speaking || 0,
          writing: stats.writing || 0,
          mix: stats.mix || 0,
        },
      },
      level: {
        key: level.key,
        label: level.label,
        description: level.desc,
        progress_percent: progressPercent,
        next_target: level.next,
        next_label: level.next ? (CEFR_LEVELS.find((item) => item.min === level.next)?.label || "") : null,
        current_words: wordCount,
      },
    };
  };

  const loadServerProgressState = async (forceReload = false) => {
    const language = getActiveLang();

    if (!forceReload && serverProgressState && serverProgressLanguage === language) {
      return serverProgressState;
    }

    if (!canUseServerProgress) {
      serverProgressState = buildLocalProgressState();
      serverProgressLanguage = language;
      return serverProgressState;
    }

    if (!forceReload && serverProgressLoadPromise && serverProgressLanguage === language) {
      return serverProgressLoadPromise;
    }

    serverProgressLanguage = language;
    serverProgressLoadPromise = libraryApiFetch(`/api/progress/state?language=${encodeURIComponent(language)}`)
      .then((response) => {
        const contentType = response.headers.get("content-type") || "";
        if (!response.ok || !contentType.includes("application/json")) {
          return Promise.reject(response);
        }
        return response.json();
      })
      .then((payload) => {
        serverProgressState = payload;
        return payload;
      })
      .catch(() => {
        serverProgressState = buildLocalProgressState();
        return serverProgressState;
      })
      .finally(() => {
        serverProgressLoadPromise = null;
      });

    return serverProgressLoadPromise;
  };

  const getCart = () => {
    try {
      const raw = localStorage.getItem(CART_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  };

  const saveCart = (cart) => {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  };

  const getLibrary = () => {
    if (hasServerLibrary) return serverLibrary;

    try {
      const raw = localStorage.getItem(LIBRARY_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  };

  const saveLibrary = (items) => {
    if (hasServerLibrary) {
      serverLibrary = Array.isArray(items) ? items : [];
      return;
    }

    localStorage.setItem(LIBRARY_KEY, JSON.stringify(items));
  };

  const COLLECTIONS_KEY = "lexiCollections";
  const getCollections = () => {
    if (hasServerLibrary) return serverCollections;

    try { return JSON.parse(localStorage.getItem(COLLECTIONS_KEY) || "[]"); } catch { return []; }
  };
  const saveCollections = (c) => {
    if (hasServerLibrary) {
      serverCollections = Array.isArray(c) ? c : [];
      return;
    }

    localStorage.setItem(COLLECTIONS_KEY, JSON.stringify(c));
  };

  const MAIN_LIBRARY_NAME = t("js.main_library_name");

  const isWordInMainLibrary = (wordId, library = getLibrary()) =>
    library.some(item => item.id === wordId);

  const isWordInAnyCollection = (wordId, collections = getCollections()) =>
    collections.some(collection => (collection.items || []).some(item => item.id === wordId));

  const ensureWordInMainLibrary = (wordId, wordLabel, language, meta = {}) => {
    const library = getLibrary();
    if (isWordInMainLibrary(wordId, library)) return false;
    recordLangActivity(language || getActiveLang());

    saveLibrary([...library, {
      id: wordId,
      label: wordLabel,
      language: language || getActiveLang(),
      translation: meta.translation || "",
      cefr: meta.cefr || "",
      topic: meta.topic || "",
    }]);

    if (hasServerLibrary) {
      libraryApiFetch("/api/library/words", {
        method: "POST",
        body: JSON.stringify({
          client_key: wordId,
          label: wordLabel,
          language: language || getActiveLang(),
          translation: meta.translation || "",
          cefr: meta.cefr || "",
          topic: meta.topic || "",
        }),
      })
        .then((response) => response.ok ? response.json() : Promise.reject(response))
        .then((payload) => syncServerLibraryState(payload || {}))
        .catch(() => loadServerLibrary());
    }

    return true;
  };

  const removeWordFromAllSavedLists = (wordId) => {
    const library = getLibrary();
    const collections = getCollections();
    saveLibrary(library.filter(item => item.id !== wordId));

    if (hasServerLibrary) {
      libraryApiFetch(`/api/library/words/${encodeURIComponent(wordId)}?language=${encodeURIComponent(getActiveLang())}`, {
        method: "DELETE",
      })
        .then((response) => response.ok ? response.json() : Promise.reject(response))
        .then((payload) => syncServerLibraryState(payload || {}))
        .catch(() => loadServerLibrary());
    }

    saveCollections(collections.map(collection => ({
      ...collection,
      items: (collection.items || []).filter(item => item.id !== wordId)
    })));
  };

  const saveWordIntoCollection = (collId, wordEntry) => {
    const collections = getCollections();
    const idx = collections.findIndex(collection => collection.id === collId);
    if (idx === -1) return false;

    if (hasServerLibrary) {
      const collection = collections[idx];
      libraryApiFetch(`/api/library/collections/${encodeURIComponent(collId)}/toggle-word`, {
        method: "POST",
        body: JSON.stringify({
          client_key: wordEntry.id,
          label: wordEntry.label,
          language: wordEntry.language || collection.lang || getActiveLang(),
          translation: wordEntry.translation || "",
          cefr: wordEntry.cefr || "",
          topic: wordEntry.topic || "",
        }),
      })
        .then((response) => response.ok ? response.json() : Promise.reject(response))
        .then((payload) => syncServerLibraryState({ items: serverLibrary, collections: payload.collections || [] }))
        .catch(() => loadServerLibrary());
    }

    const items = collections[idx].items || [];
    if (items.some(item => item.id === wordEntry.id)) return true;
    collections[idx].items = [...items, wordEntry];
    saveCollections(collections);
    return true;
  };

  const updateAllBookmarkStates = () => {
    const library = getLibrary();
    const collections = getCollections();
    document.querySelectorAll("[data-word-card]").forEach(card => {
      const mainBtn = card.querySelector("[data-save-word]");
      if (!mainBtn) return;
      const wordId = card.dataset.wordId;
      const inMain = isWordInMainLibrary(wordId, library);
      const inAny = inMain || isWordInAnyCollection(wordId, collections);
      mainBtn.classList.toggle("is-saved", inAny);
      mainBtn.setAttribute("aria-label", inAny ? t("js.save.view_options") : t("js.save.save_and_view_options", { list: MAIN_LIBRARY_NAME }));
      mainBtn.innerHTML = `<i class="bi bi-bookmark${inAny ? "-fill" : ""}"></i>`;
    });
  };

  const setupSaveDropdown = () => {
    const dropdown = document.getElementById("saveDropdown");
    const mainSection = document.getElementById("saveDropdownMain");
    const collectionsDivider = document.getElementById("saveDropdownCollectionsDivider");
    const ddList = document.getElementById("saveDropdownLists");
    const newBtn = document.getElementById("saveDropdownNewBtn");
    if (!dropdown || !mainSection || !collectionsDivider) return;

    let activeCard = null;

    const close = () => { dropdown.hidden = true; activeCard = null; window._activeDropdownCard = null; };
    window._closeSaveDropdown = close;

    const renderDropdown = () => {
      if (!activeCard) return;
      const wordId = activeCard.dataset.wordId;
      const wordLabel = activeCard.dataset.wordLabel ||
        getWordLabel(activeCard.querySelector("h2")?.textContent?.trim());
      const wordLanguage = activeCard.dataset.language || getActiveLang();
      const library = getLibrary();
      const collections = getCollections();
      const activeLang = getActiveLang();
      const inMain = isWordInMainLibrary(wordId, library);
      const visibleCollections = collections
        .map(collection => ({
          id: collection.id,
          name: collection.name,
          saved: (collection.items || []).some(item => item.id === wordId)
        }));

      mainSection.innerHTML = `
        <div class="save-dropdown-main-row">
          <span class="save-dropdown-main-label">${MAIN_LIBRARY_NAME}</span>
          <button class="save-dropdown-main-toggle${inMain ? " is-saved" : ""}" id="saveDropdownMainToggle" type="button" aria-label="${inMain ? t("js.save.remove_from_main", { list: MAIN_LIBRARY_NAME }) : t("js.save.save_to_main", { list: MAIN_LIBRARY_NAME })}">
            <i class="bi bi-bookmark${inMain ? "-fill" : ""}"></i>
          </button>
        </div>`;

      const mainToggle = document.getElementById("saveDropdownMainToggle");
      if (mainToggle) {
        mainToggle.addEventListener("click", (event) => {
          event.stopPropagation();
          if (isWordInMainLibrary(wordId)) {
            removeWordFromAllSavedLists(wordId);
            updateAllBookmarkStates();
            window.dispatchEvent(new Event("lexi-library-updated"));
            close();
            return;
          }
          ensureWordInMainLibrary(wordId, wordLabel, wordLanguage, {
            translation: activeCard.dataset.translation,
            cefr: activeCard.dataset.cefr,
            topic: activeCard.dataset.topic,
          });
          updateAllBookmarkStates();
          renderDropdown();
          window.dispatchEvent(new Event("lexi-library-updated"));
        });
      }

      collectionsDivider.hidden = visibleCollections.length === 0;
      ddList.hidden = visibleCollections.length === 0;
      ddList.innerHTML = visibleCollections.map(collection => `
        <li>
          <button class="save-dropdown-item${collection.saved ? " is-saved" : ""}" type="button" data-coll-id="${collection.id}">
            <span class="save-dropdown-item-name">${collection.name}</span>
            <span class="save-dropdown-item-status" aria-hidden="true"><i class="bi ${collection.saved ? "bi-check2" : "bi-plus-lg"}"></i></span>
          </button>
        </li>`).join("");

      ddList.querySelectorAll(".save-dropdown-item").forEach(item => {
        item.addEventListener("click", () => {
          const collId = item.dataset.collId;
          const colls = getCollections();
          const idx = colls.findIndex(collection => collection.id === collId);
          if (idx === -1) return;
          const items = colls[idx].items || [];
          const exists = items.some(itemEntry => itemEntry.id === wordId);
          if (exists) colls[idx].items = items.filter(itemEntry => itemEntry.id !== wordId);
          else colls[idx].items = [...items, {
            id: wordId,
            label: wordLabel,
            language: wordLanguage,
            translation: activeCard.dataset.translation || "",
            cefr: activeCard.dataset.cefr || "",
            topic: activeCard.dataset.topic || "",
          }];
          saveCollections(colls);
          if (hasServerLibrary) {
            saveWordIntoCollection(collId, {
              id: wordId,
              label: wordLabel,
              language: wordLanguage,
              translation: activeCard.dataset.translation,
              cefr: activeCard.dataset.cefr,
              topic: activeCard.dataset.topic,
            });
          }
          updateAllBookmarkStates();
          renderDropdown();
          window.dispatchEvent(new Event("lexi-library-updated"));
        });
      });
    };

    window._openSaveDropdown = (card, btn) => {
      activeCard = card;
      window._activeDropdownCard = card;
      renderDropdown();
      dropdown.hidden = false;
      // Position
      const rect = btn.getBoundingClientRect();
      const ddW = 240;
      let left = rect.right - ddW;
      if (left < 8) left = 8;
      let top = rect.bottom + 6;
      dropdown.style.left = left + "px";
      dropdown.style.top = top + "px";
      // Adjust if off bottom
      requestAnimationFrame(() => {
        const ddH = dropdown.offsetHeight;
        if (top + ddH > window.innerHeight - 8) {
          dropdown.style.top = (rect.top - ddH - 6) + "px";
        }
      });
    };

    newBtn.addEventListener("click", () => {
      const currentCard = activeCard;
      const wordId = currentCard?.dataset.wordId;
      const wordLabel = currentCard?.dataset.wordLabel ||
        getWordLabel(currentCard?.querySelector("h2")?.textContent?.trim());
      const wordLanguage = currentCard?.dataset.language || getActiveLang();
      close();
      openCreateCollectionModal((createdCollection) => {
        if (createdCollection && wordId) {
          ensureWordInMainLibrary(wordId, wordLabel, wordLanguage, {
            translation: currentCard?.dataset.translation,
            cefr: currentCard?.dataset.cefr,
            topic: currentCard?.dataset.topic,
          });
          saveWordIntoCollection(createdCollection.id, { id: wordId, label: wordLabel });
        }
        updateAllBookmarkStates();
        window.dispatchEvent(new Event("lexi-library-updated"));
      });
    });
    document.addEventListener("click", e => {
      if (dropdown.hidden) return;
      if (!dropdown.contains(e.target) && !e.target.closest("[data-save-word]")) close();
    });
    document.addEventListener("keydown", e => { if (e.key === "Escape" && !dropdown.hidden) close(); });
    window.addEventListener("scroll", () => {
      if (!dropdown.hidden) close();
    }, true);
    window.addEventListener("resize", () => {
      if (!dropdown.hidden) close();
    });
  };

  const cartTotalItems = (cart) => cart.reduce((acc, item) => acc + item.qty, 0);
  const cartTotalPrice = (cart) => cart.reduce((acc, item) => acc + item.price * item.qty, 0);
  const formatEur = (n) => `${Number(n).toFixed(2)} EUR`;

  const showAlert = (message, type = "success") => {
    let alertBox = document.getElementById("globalAlert");
    if (!alertBox) {
      alertBox = document.createElement("div");
      alertBox.id = "globalAlert";
      alertBox.className = "global-alert";
      document.body.appendChild(alertBox);
    }

    alertBox.innerHTML = `<div class="alert alert-${type} mb-0" role="status">${message}</div>`;

    clearTimeout(showAlert.timer);
    showAlert.timer = setTimeout(() => {
      alertBox.innerHTML = "";
    }, 2200);
  };

  const updateCartBadges = () => {
    const count = cartTotalItems(getCart());
    document.querySelectorAll("[data-cart-count]").forEach((el) => {
      el.textContent = count;
      el.setAttribute("data-cart-count", count);
    });
  };

  const addToCart = (product) => {
    const cart = getCart();
    const existing = cart.find((p) => p.id === product.id);

    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ ...product, qty: 1 });
    }

    saveCart(cart);
    updateCartBadges();
    renderCartDrawer();
  };

  const removeFromCart = (id) => {
    const cart = getCart().filter((item) => item.id !== id);
    saveCart(cart);
    updateCartBadges();
    renderCartDrawer();
    renderCartPage();
  };

  const changeQty = (id, delta) => {
    const cart = getCart();
    const item = cart.find((p) => p.id === id);
    if (!item) return;

    item.qty += delta;

    if (item.qty <= 0) {
      saveCart(cart.filter((p) => p.id !== id));
    } else {
      saveCart(cart);
    }

    updateCartBadges();
    renderCartDrawer();
    renderCartPage();
  };

  const clearCart = () => {
    saveCart([]);
    updateCartBadges();
    renderCartDrawer();
    renderCartPage();
  };

  const createCartDrawer = () => {
    if (document.getElementById("cartDrawer")) return;

    const backdrop = document.createElement("div");
    backdrop.className = "cart-drawer-backdrop";
    backdrop.id = "cartDrawerBackdrop";

    const drawer = document.createElement("aside");
    drawer.className = "cart-drawer";
    drawer.id = "cartDrawer";
    drawer.setAttribute("aria-label", t("js.cart.summary"));

    drawer.innerHTML = `
      <div class="cart-drawer-header">
        <strong>${t("js.cart.title")}</strong>
        <button class="btn btn-sm btn-outline-secondary" type="button" id="closeCartDrawer">${t("js.utility.close")}</button>
      </div>
      <div class="cart-drawer-body" id="cartDrawerBody"></div>
      <div class="cart-drawer-footer">
        <div class="cart-total-line">
          <span>${t("js.cart.total")}</span>
          <span id="cartDrawerTotal">0.00 EUR</span>
        </div>
        <a class="btn btn-primary w-100" href="carrito.html">${t("js.cart.go_to_cart")}</a>
      </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    backdrop.addEventListener("click", () => toggleCartDrawer(false));
    drawer.querySelector("#closeCartDrawer").addEventListener("click", () => toggleCartDrawer(false));
  };

  const toggleCartDrawer = (open) => {
    const drawer = document.getElementById("cartDrawer");
    const backdrop = document.getElementById("cartDrawerBackdrop");
    if (!drawer || !backdrop) return;

    drawer.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);
  };

  const renderCartDrawer = () => {
    const body = document.getElementById("cartDrawerBody");
    const total = document.getElementById("cartDrawerTotal");
    if (!body || !total) return;

    const cart = getCart();

    if (!cart.length) {
      body.innerHTML = '<p class="cart-empty">' + t("js.cart.empty") + '</p>';
      total.textContent = formatEur(0);
      return;
    }

    body.innerHTML = cart
      .map(
        (item) => `
          <div class="cart-drawer-item">
            <div>
              <strong>${item.name}</strong><br>
              <small>${t("js.cart.quantity", { count: item.qty })}</small>
            </div>
            <div>${formatEur(item.price * item.qty)}</div>
          </div>
        `
      )
      .join("");

    total.textContent = formatEur(cartTotalPrice(cart));
  };

  const setupCartTriggers = () => {
    createCartDrawer();
    renderCartDrawer();

    document.querySelectorAll("[data-cart-trigger]").forEach((btn) => {
      btn.addEventListener("click", () => {
        renderCartDrawer();
        toggleCartDrawer(true);
      });
    });
  };

  const createUtilityDrawer = () => {
    if (document.getElementById("utilityDrawer")) return;

    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "";
    const submitLogoutForm = () => {
      const form = document.createElement("form");
      form.method = "POST";
      form.action = "/logout";
      form.style.display = "none";

      const token = document.createElement("input");
      token.type = "hidden";
      token.name = "_token";
      token.value = getCsrfToken();
      form.appendChild(token);

      document.body.appendChild(form);
      form.submit();
    };

    const currentPage = window.location.pathname.split("/").pop() || "app.html";
    const activeClass = (href) => currentPage === href ? " utility-link--active" : "";
    const adminLink = isCurrentUserAdmin()
      ? `<a class="utility-link utility-link--admin${activeClass("admin.html")}" href="admin.html"><i class="bi bi-shield-lock"></i><span>${t("js.utility.admin")}</span></a>`
      : "";

    const backdrop = document.createElement("div");
    backdrop.className = "utility-drawer-backdrop";
    backdrop.id = "utilityDrawerBackdrop";

    const drawer = document.createElement("aside");
    drawer.className = "utility-drawer";
    drawer.id = "utilityDrawer";
    drawer.setAttribute("aria-label", t("js.utility.secondary_menu"));

    drawer.innerHTML = `
      <div class="utility-drawer-header">
        <strong>${t("js.utility.menu")}</strong>
        <button class="btn btn-sm btn-outline-secondary" type="button" id="closeUtilityDrawer">${t("js.utility.close")}</button>
      </div>
      <div class="utility-drawer-body">
        <div class="utility-drawer-section utility-drawer-section--mobile-nav">
          <a class="utility-link${activeClass("app.html")}" href="app.html"><i class="bi bi-house"></i><span>${t("js.utility.home")}</span></a>
          <a class="utility-link${activeClass("biblioteca.html")}" href="biblioteca.html"><i class="bi bi-journals"></i><span>${t("js.utility.library")}</span></a>
          <a class="utility-link${activeClass("ejercicios.html")}" href="ejercicios.html"><i class="bi bi-lightning-charge"></i><span>${t("js.utility.exercises")}</span></a>
        </div>
        <div class="utility-drawer-divider" aria-hidden="true"></div>
        <div class="utility-drawer-section">
          <a class="utility-link utility-link--premium${activeClass("producto.html")}" href="producto.html"><i class="bi bi-gem"></i><span>${t("js.utility.premium")}</span></a>
          <a class="utility-link utility-link--cart${activeClass("carrito.html")}" href="carrito.html"><i class="bi bi-bag"></i><span>${t("js.utility.cart")}</span><span class="cart-count-badge cart-count-badge--drawer" data-cart-count="0">0</span></a>
          <a class="utility-link${activeClass("contacto.html")}" href="contacto.html"><i class="bi bi-envelope"></i><span>${t("js.utility.contact")}</span></a>
          <a class="utility-link${activeClass("info.html")}" href="info.html"><i class="bi bi-info-circle"></i><span>${t("js.utility.info")}</span></a>
          ${adminLink}
          <button class="btn btn-outline-danger utility-logout-btn" type="button" id="logoutAction"><i class="bi bi-box-arrow-right"></i><span>${t("js.utility.logout")}</span></button>
        </div>
      </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    backdrop.addEventListener("click", () => toggleUtilityDrawer(false));
    drawer.querySelector("#closeUtilityDrawer").addEventListener("click", () => toggleUtilityDrawer(false));
    drawer.querySelector("#logoutAction").addEventListener("click", () => {
      toggleUtilityDrawer(false);
      submitLogoutForm();
    });
  };

  const toggleUtilityDrawer = (open) => {
    const drawer = document.getElementById("utilityDrawer");
    const backdrop = document.getElementById("utilityDrawerBackdrop");
    if (!drawer || !backdrop) return;

    drawer.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);

    document.querySelectorAll("[data-utility-trigger]").forEach((btn) => {
      btn.setAttribute("aria-expanded", String(open));
    });
  };

  const setupUtilityMenu = () => {
    createUtilityDrawer();

    document.querySelectorAll("[data-utility-trigger]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const isOpen = document.getElementById("utilityDrawer")?.classList.contains("open");
        toggleUtilityDrawer(!isOpen);
      });
    });
  };

  const setupAddToCartButtons = () => {
    document.querySelectorAll("[data-add-cart]").forEach((btn) => {
      btn.addEventListener("click", () => {
        addToCart({
          id: btn.dataset.id,
          name: btn.dataset.name,
          price: Number(btn.dataset.price)
        });
        showAlert(t("js.alerts.product_added"));
      });
    });
  };

  let pendingRemoveId = null;

  const renderCartPage = () => {
    const tbody = document.getElementById("cartItems");
    const total = document.getElementById("cartTotal");
    if (!tbody || !total) return;

    const cart = getCart();

    if (!cart.length) {
      tbody.innerHTML = '<tr><td colspan="5">' + t("js.cart.no_products") + '</td></tr>';
      total.textContent = formatEur(0);
      return;
    }

    tbody.innerHTML = cart
      .map(
        (item) => `
          <tr>
            <td>${item.name}</td>
            <td>
              <div class="cart-qty-controls">
                <button class="qty-btn" type="button" data-qty-minus="${item.id}" aria-label="${t("js.cart.decrease_quantity")}">-</button>
                <span>${item.qty}</span>
                <button class="qty-btn" type="button" data-qty-plus="${item.id}" aria-label="${t("js.cart.increase_quantity")}">+</button>
              </div>
            </td>
            <td>${formatEur(item.price)}</td>
            <td>${formatEur(item.price * item.qty)}</td>
            <td>
              <button class="btn btn-sm btn-outline-danger" type="button" data-remove-id="${item.id}">${t("js.cart.remove")}</button>
            </td>
          </tr>
        `
      )
      .join("");

    total.textContent = formatEur(cartTotalPrice(cart));
  };

  const setupCartPageEvents = () => {
    const cartPage = document.querySelector("[data-cart-page]");
    if (!cartPage) return;

    const tbody = document.getElementById("cartItems");
    const clearButton = document.getElementById("clearCart");
    const confirmBtn = document.getElementById("confirmRemoveBtn");
    const modalElement = document.getElementById("confirmRemoveModal");
    const bsModal = modalElement && window.bootstrap ? new bootstrap.Modal(modalElement) : null;

    if (tbody) {
      tbody.addEventListener("click", (e) => {
        const plusId = e.target.getAttribute("data-qty-plus");
        const minusId = e.target.getAttribute("data-qty-minus");
        const removeId = e.target.getAttribute("data-remove-id");

        if (plusId) return changeQty(plusId, 1);
        if (minusId) return changeQty(minusId, -1);

        if (removeId) {
          pendingRemoveId = removeId;
          if (bsModal) bsModal.show();
          else if (confirm(t("js.cart.confirm_body"))) removeFromCart(removeId);
        }
      });
    }

    if (confirmBtn) {
      confirmBtn.addEventListener("click", () => {
        if (!pendingRemoveId) return;
        removeFromCart(pendingRemoveId);
        pendingRemoveId = null;
        if (bsModal) bsModal.hide();
      });
    }

    if (clearButton) {
      clearButton.addEventListener("click", () => {
        clearCart();
        showAlert("Carrito vaciado", "warning");
      });
    }

    renderCartPage();
  };

  const setupNavToggle = () => {
    const toggle = document.querySelector(".nav-toggle");
    const nav = document.querySelector(".nav-left");
    if (!toggle || !nav) return;

    toggle.addEventListener("click", () => {
      const isOpen = nav.classList.toggle("open");
      toggle.setAttribute("aria-expanded", String(isOpen));
    });
  };

  const setupScrollButton = () => {
    const btnSubir = document.getElementById("btnSubir");
    if (!btnSubir) return;

    const onScroll = () => {
      btnSubir.style.display = window.scrollY > 200 ? "block" : "none";
    };

    onScroll();
    window.addEventListener("scroll", onScroll);

    btnSubir.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  };

  const setupKeyboardShortcut = () => {
    document.addEventListener("keydown", (e) => {
      const isTyping = ["INPUT", "TEXTAREA", "SELECT"].includes(document.activeElement?.tagName);
      if (isTyping) return;

      if (e.key.toLowerCase() === "t") {
        window.scrollTo({ top: 0, behavior: "smooth" });
        showAlert("Atajo: volver arriba");
      }
    });
  };

  const setupObserver = () => {
    const items = document.querySelectorAll(".scroll-animado");
    if (!items.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) entry.target.classList.add("visible");
      });
    }, { threshold: 0.12 });

    items.forEach((item) => observer.observe(item));
  };

  const setupActionButtons = () => {
    document.querySelectorAll(".accion").forEach((btn) => {
      btn.addEventListener("click", () => {
        showAlert(t("js.alerts.exercise_started"));
      });
    });
  };

  const setupLibraryList = () => {
    const listEl = document.getElementById("librarySavedList");
    const countEl = document.getElementById("libraryCount");
    const clearBtn = document.getElementById("clearLibrary");
    const filterInput = document.getElementById("savedWordFilter");
    const listsGrid = document.getElementById("listsGrid");
    const listsIndex = document.getElementById("listsIndex");
    const listDetail = document.getElementById("listDetail");
    const listBackBtn = document.getElementById("listBackBtn");
    const listDetailTitle = document.getElementById("listDetailTitle");

    // --- Vista índice: tarjeta por lista ---
    const renderListsIndex = () => {
      if (!listsGrid) return;
      const saved = getLibrary();
      const lang = getActiveLang();
      const filtered = saved.filter((item) => {
        if (!item.id) return true;
        if (item.id.startsWith("custom-")) return item.id.startsWith(`custom-${lang}-`);
        const card = document.querySelector(`[data-word-id="${item.id}"]`);
        if (card) return card.dataset.language === lang;
        return true;
      });

      const count = filtered.length;
      const collections = getCollections().filter(c => !c.lang || c.lang === lang);

      // Tarjeta principal de Guardado siempre primera
      let html = `
        <div class="list-card" id="mainListCard" role="button" tabindex="0" aria-label="${t("js.library.open_collection", { name: MAIN_LIBRARY_NAME })}">
          <div class="list-card-top">
            <div class="list-card-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
            <div>
              <p class="list-card-name">${MAIN_LIBRARY_NAME}</p>
              <span class="list-card-count">${countWordLabel(count, "js.library.word_one", "js.library.word_other")}</span>
            </div>
          </div>
        </div>`;

      // Tarjetas de colecciones extra
      collections.forEach(c => {
        const cCount = (c.items || []).length;
        html += `
        <div class="list-card" data-coll-card="${c.id}" role="button" tabindex="0" aria-label="${t("js.library.open_collection", { name: c.name })}">
          <div class="list-card-top">
            <div class="list-card-icon" style="background:#f0eeff;color:#7c3aed"><i class="bi bi-collection"></i></div>
            <div>
              <p class="list-card-name">${c.name}</p>
              <span class="list-card-count">${countWordLabel(cCount, "js.library.word_one", "js.library.word_other")}</span>
            </div>
          </div>
          <div class="coll-kebab-wrap">
            <button class="coll-kebab-btn" type="button" data-kebab-coll="${c.id}" aria-label="${t("js.library.options_for", { name: c.name })}"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="coll-kebab-menu" data-kebab-menu="${c.id}" hidden>
              <button type="button" data-rename-coll="${c.id}"><i class="bi bi-pencil"></i> ${t("js.library.rename")}</button>
              <div class="coll-kebab-divider"></div>
              <button type="button" data-clear-coll="${c.id}" class="coll-kebab-clear"><i class="bi bi-eraser"></i> ${t("js.library.clear_list")}</button>
              <button type="button" data-delete-coll="${c.id}" class="coll-kebab-delete"><i class="bi bi-trash"></i> ${t("js.library.delete")}</button>
            </div>
          </div>
        </div>`;
      });

      listsGrid.innerHTML = html;

      document.getElementById("mainListCard")?.addEventListener("click", openDetail);
      document.getElementById("mainListCard")?.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") openDetail();
      });

      listsGrid.querySelectorAll("[data-coll-card]").forEach(card => {
        card.addEventListener("click", (e) => {
          if (e.target.closest("[data-kebab-coll]") || e.target.closest(".coll-kebab-menu")) return;
          openCollDetail(card.dataset.collCard);
        });
        card.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") openCollDetail(card.dataset.collCard);
        });
      });

      // Kebab: abrir/cerrar menú
      listsGrid.querySelectorAll("[data-kebab-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.kebabColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          // Cerrar todos los demás
          listsGrid.querySelectorAll(".coll-kebab-menu").forEach(m => { if (m !== menu) m.hidden = true; });
          menu.hidden = !menu.hidden;
        });
      });

      // Kebab: renombrar
      listsGrid.querySelectorAll("[data-rename-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.renameColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          if (menu) menu.hidden = true;
          openRenameModal(id, renderListsIndex);
        });
      });

      // Kebab: vaciar lista
      listsGrid.querySelectorAll("[data-clear-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.clearColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          if (menu) menu.hidden = true;
          openClearModal(id, renderListsIndex);
        });
      });

      // Kebab: eliminar
      listsGrid.querySelectorAll("[data-delete-coll]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.dataset.deleteColl;
          const menu = listsGrid.querySelector(`[data-kebab-menu="${id}"]`);
          if (menu) menu.hidden = true;
          openDeleteModal(id, renderListsIndex);
        });
      });
    };

    // Cerrar kebab al hacer click fuera (registrado una sola vez)
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".coll-kebab-wrap")) {
        listsGrid?.querySelectorAll(".coll-kebab-menu").forEach(m => m.hidden = true);
      }
    });

    // Botón "Nueva colección" del índice
    const newCollTopBtn = document.getElementById("newCollectionTopBtn");
    if (newCollTopBtn) {
      newCollTopBtn.addEventListener("click", () => {
        openCreateCollectionModal(renderListsIndex);
      });
    }

    const addPanel = document.querySelector(".library-add-panel");
    let activeCollId = null;

    const showDetail = (title) => {
      if (listsIndex) { listsIndex.style.animation = "fadeOut 0.18s ease forwards"; setTimeout(() => { listsIndex.hidden = true; listsIndex.style.animation = ""; }, 180); }
      if (addPanel) { addPanel.style.animation = "fadeOut 0.18s ease forwards"; setTimeout(() => { addPanel.hidden = true; addPanel.style.animation = ""; }, 180); }
      setTimeout(() => {
        if (listDetail) { listDetail.hidden = false; listDetail.style.animation = "fadeIn 0.2s ease"; }
        if (listDetailTitle) listDetailTitle.textContent = title;
        renderList();
      }, 190);
    };

    const openDetail = () => {
      activeCollId = null;
      showDetail(MAIN_LIBRARY_NAME);
    };

    const openCollDetail = (collId) => {
      const coll = getCollections().find(c => c.id === collId);
      if (!coll) return;
      activeCollId = collId;
      showDetail(coll.name);
    };

    const closeDetail = () => {
      activeCollId = null;
      if (listDetail) { listDetail.style.animation = "fadeOut 0.18s ease forwards"; setTimeout(() => { listDetail.hidden = true; listDetail.style.animation = ""; }, 180); }
      setTimeout(() => {
        if (addPanel) { addPanel.hidden = false; addPanel.style.animation = "fadeIn 0.2s ease"; }
        if (listsIndex) { listsIndex.hidden = false; listsIndex.style.animation = "fadeIn 0.2s ease"; }
        renderListsIndex();
      }, 190);
    };

    if (listBackBtn) listBackBtn.addEventListener("click", closeDetail);

    if (!listEl || !countEl) return;

    const PAGE_SIZE = 10;
    let currentPage = 0;

    const paginationEl = document.getElementById("listPagination");
    const pagePrevBtn  = document.getElementById("listPagePrev");
    const pageNextBtn  = document.getElementById("listPageNext");
    const pageInfoEl   = document.getElementById("listPageInfo");

    const renderList = () => {
      const query = filterInput ? filterInput.value.trim().toLowerCase() : "";
      let source;
      if (activeCollId) {
        const coll = getCollections().find(c => c.id === activeCollId);
        source = coll ? (coll.items || []) : [];
      } else {
        const lang = getActiveLang();
        source = getLibrary().filter((item) => {
          if (!item.id) return true;
          if (item.id.startsWith("custom-")) return item.id.startsWith(`custom-${lang}-`);
          const card = document.querySelector(`[data-word-id="${item.id}"]`);
          if (card) return card.dataset.language === lang;
          return true;
        });
      }

      let filtered = source;
      if (query) {
        filtered = filtered.filter((item) =>
          item.label.toLowerCase().includes(query)
        );
      }

      if (!filtered.length) {
        const hasAny = source.length > 0;
        listEl.innerHTML = query
          ? `<li class="library-empty-state"><i class="bi bi-search"></i><span>${t("js.library.no_word_matches", { query: `"<strong>${escapeHtml(query)}</strong>"` })}</span></li>`
          : hasAny
          ? `<li class="library-empty-state"><i class="bi bi-globe"></i><span>${t("js.library.no_saved_words_for_language")}</span></li>`
          : `<li class="library-empty-state"><i class="bi bi-journal-plus"></i><span>${t("js.library.empty_list")}<br><small>${t("js.library.save_from_catalog")}</small></span></li>`;
        if (paginationEl) paginationEl.hidden = true;
      } else {
        const totalPages = Math.ceil(filtered.length / PAGE_SIZE);
        if (currentPage >= totalPages) currentPage = totalPages - 1;
        if (currentPage < 0) currentPage = 0;

        const pageStart = currentPage * PAGE_SIZE;
        const pageEnd   = pageStart + PAGE_SIZE;
        const toShow    = filtered.slice(pageStart, pageEnd);

        listEl.innerHTML = toShow
          .map((item) => {
            const card = document.querySelector(`[data-word-id="${item.id}"]`);
            const cefr        = card?.dataset.cefr        || "";
            const topic       = card?.dataset.topic       || "";
            const translation = card?.dataset.translation || item.translation || "";
            const cefrClass   = cefr ? `cefr-${cefr.toLowerCase()}` : "";
            return `<li class="saved-word-row">
              <div class="saved-word-info">
                <span class="saved-word-label">${item.label}</span>
                ${translation ? `<span class="saved-word-translation">${translation}</span>` : ""}
                <div class="saved-word-tags">
                  ${cefr  ? `<span class="cefr-badge ${cefrClass}">${cefr}</span>` : ""}
                  ${topic ? `<span class="topic-tag">${topic}</span>` : ""}
                </div>
              </div>
              <button class="remove-saved-btn" type="button" data-remove-saved="${item.id}" data-remove-label="${item.label}" data-remove-language="${item.language || getActiveLang()}" data-remove-translation="${translation}" data-remove-cefr="${cefr}" data-remove-topic="${topic}" aria-label="${t("js.library.remove_word", { word: item.label })}"><i class="bi bi-x"></i></button>
            </li>`;
          })
          .join("");

        if (paginationEl) {
          paginationEl.hidden = totalPages <= 1;
          if (pageInfoEl) pageInfoEl.textContent = `${currentPage + 1} / ${totalPages}`;
          if (pagePrevBtn) pagePrevBtn.disabled = currentPage === 0;
          if (pageNextBtn) pageNextBtn.disabled = currentPage >= totalPages - 1;
        }
      }

      countEl.textContent = countWordLabel(filtered.length, "js.library.word_one", "js.library.word_other");

      updateAllBookmarkStates();
    };

    listEl.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-remove-saved]");
      if (!btn) return;
      const id = btn.dataset.removeSaved;
      if (activeCollId) {
        const colls = getCollections();
        const idx = colls.findIndex(c => c.id === activeCollId);
        if (idx !== -1) { colls[idx].items = (colls[idx].items || []).filter(i => i.id !== id); saveCollections(colls); }
        if (hasServerLibrary) {
          saveWordIntoCollection(activeCollId, {
            id,
            label: getWordLabel(btn.dataset.removeLabel),
            language: btn.dataset.removeLanguage || getActiveLang(),
            translation: btn.dataset.removeTranslation || "",
            cefr: btn.dataset.removeCefr || "",
            topic: btn.dataset.removeTopic || "",
          });
        }
      } else {
        saveLibrary(getLibrary().filter((item) => item.id !== id));
      }
      showAlert(t("js.alerts.word_removed"), "warning");
      renderList();
    });

    if (pagePrevBtn) {
      pagePrevBtn.addEventListener("click", () => {
        if (currentPage > 0) { currentPage--; renderList(); listEl.scrollIntoView({ behavior: "smooth", block: "nearest" }); }
      });
    }
    if (pageNextBtn) {
      pageNextBtn.addEventListener("click", () => {
        currentPage++; renderList(); listEl.scrollIntoView({ behavior: "smooth", block: "nearest" });
      });
    }

    if (filterInput) {
      filterInput.addEventListener("input", () => {
        currentPage = 0;
        renderList();
      });
    }

    document.addEventListener("click", (e) => {
      const mainBtn = e.target.closest("[data-save-word]");
      if (!mainBtn) return;

      const card = mainBtn.closest("[data-word-card]");
      if (!card) return;

      e.stopPropagation();

      const id = card.dataset.wordId;
      const label = getWordLabel(card.dataset.wordLabel || card.querySelector("h2")?.textContent?.trim());

      ensureWordInMainLibrary(id, label, card.dataset.language || getActiveLang(), {
        translation: card.dataset.translation,
        cefr: card.dataset.cefr,
        topic: card.dataset.topic,
      });

      updateAllBookmarkStates();
      window.dispatchEvent(new Event("lexi-library-updated"));

      if (window._openSaveDropdown) {
        window._openSaveDropdown(card, mainBtn);
      }
    });

    updateAllBookmarkStates();

    if (clearBtn) {
      clearBtn.addEventListener("click", () => {
        if (activeCollId) {
          openClearModal(activeCollId, () => {
            currentPage = 0;
            if (filterInput) filterInput.value = "";
            renderList();
            renderListsIndex();
          });
          return;
        } else {
          openClearMainListModal(() => {
            saveLibrary([]);
            if (hasServerLibrary) {
              libraryApiFetch(`/api/library/words?language=${encodeURIComponent(getActiveLang())}`, {
                method: "DELETE",
              })
                .then((response) => response.ok ? response.json() : Promise.reject(response))
                .then((payload) => syncServerLibraryState(payload || {}))
                .catch(() => loadServerLibrary());
            }
            currentPage = 0;
            if (filterInput) filterInput.value = "";
            renderList();
            renderListsIndex();
            showAlert(t("js.alerts.list_cleared"), "warning");
          });
          return;
        }
      });
    }

    window.addEventListener("lexi-library-updated", () => {
      currentPage = 0;
      if (listDetail && !listDetail.hidden) renderList();
      else renderListsIndex();
    });
    window.addEventListener("lexi-lang-changed", () => {
      currentPage = 0;
      if (listDetail && !listDetail.hidden) renderList();
      else renderListsIndex();
    });

    // Arranque: siempre muestra el índice primero
    renderListsIndex();
  };

  // ---- Modal de renombrar colección ----
  const createCollection = (name) => {
    const trimmedName = String(name || "").trim();
    if (!trimmedName) return null;
    const createdCollection = { id: "coll-" + Date.now(), name: trimmedName, lang: getActiveLang(), items: [] };
    saveCollections([...getCollections(), createdCollection]);
    return createdCollection;
  };

  const openCreateCollectionModal = (onCreated) => {
    const modal = document.getElementById("createCollModal");
    const input = document.getElementById("createCollInput");
    const confirmBtn = document.getElementById("createCollConfirm");
    const cancelBtn = document.getElementById("createCollCancel");
    if (!modal || !input || !confirmBtn || !cancelBtn) return;

    input.value = "";
    modal.hidden = false;
    requestAnimationFrame(() => input.focus());

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      input.removeEventListener("keydown", onKey);
      modal.removeEventListener("click", onBackdrop);
    };
    const onConfirm = () => {
      const name = input.value.trim();
      if (!name) return;

      if (hasServerLibrary) {
        libraryApiFetch("/api/library/collections", {
          method: "POST",
          body: JSON.stringify({ name, language: getActiveLang() }),
        })
          .then((response) => response.ok ? response.json() : Promise.reject(response))
          .then((payload) => {
            syncServerLibraryState({ items: serverLibrary, collections: payload.collections || [] });
            close();
            if (onCreated) onCreated(payload.collection || null);
            showAlert(t("js.alerts.collection_created"));
          })
          .catch(() => loadServerLibrary());
        return;
      }

      const createdCollection = createCollection(name);
      if (!createdCollection) return;
      close();
      if (onCreated) onCreated(createdCollection);
      showAlert(t("js.alerts.collection_created"));
    };
    const onKey = (e) => {
      if (e.key === "Enter") onConfirm();
      if (e.key === "Escape") close();
    };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    input.addEventListener("keydown", onKey);
    modal.addEventListener("click", onBackdrop);
  };

  const openRenameModal = (collId, onSaved) => {
    const modal = document.getElementById("renameCollModal");
    const input = document.getElementById("renameCollInput");
    const confirmBtn = document.getElementById("renameCollConfirm");
    const cancelBtn = document.getElementById("renameCollCancel");
    if (!modal || !input) return;

    const colls = getCollections();
    const coll = colls.find(c => c.id === collId);
    if (!coll) return;

    input.value = coll.name;
    modal.hidden = false;
    requestAnimationFrame(() => input.select());

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      input.removeEventListener("keydown", onKey);
      modal.removeEventListener("click", onBackdrop);
    };
    const onConfirm = () => {
      const name = input.value.trim();
      if (!name) return;
      const updated = getCollections();
      const idx = updated.findIndex(c => c.id === collId);
      if (idx !== -1) { updated[idx].name = name; saveCollections(updated); }
      if (hasServerLibrary) {
        libraryApiFetch(`/api/library/collections/${encodeURIComponent(collId)}`, {
          method: "PATCH",
          body: JSON.stringify({ name }),
        })
          .then((response) => response.ok ? response.json() : Promise.reject(response))
          .then((payload) => syncServerLibraryState({ items: serverLibrary, collections: payload.collections || [] }))
          .catch(() => loadServerLibrary());
      }
      close();
      if (onSaved) onSaved();
      showAlert(t("js.alerts.list_renamed"));
    };
    const onKey = (e) => {
      if (e.key === "Enter") onConfirm();
      if (e.key === "Escape") close();
    };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    input.addEventListener("keydown", onKey);
    modal.addEventListener("click", onBackdrop);
  };

  const openDeleteModal = (collId, onConfirmed) => {
    const modal      = document.getElementById("deleteCollModal");
    const bodyEl     = document.getElementById("deleteModalBody");
    const confirmBtn = document.getElementById("deleteCollConfirm");
    const cancelBtn  = document.getElementById("deleteCollCancel");
    if (!modal) return;

    if (bodyEl) bodyEl.textContent = t("js.library.irreversible_action");

    modal.hidden = false;

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      modal.removeEventListener("click", onBackdrop);
      document.removeEventListener("keydown", onKey);
    };
    const onConfirm = () => {
      saveCollections(getCollections().filter(c => c.id !== collId));
      if (hasServerLibrary) {
        libraryApiFetch(`/api/library/collections/${encodeURIComponent(collId)}`, {
          method: "DELETE",
        })
          .then((response) => response.ok ? response.json() : Promise.reject(response))
          .then((payload) => syncServerLibraryState({ items: serverLibrary, collections: payload.collections || [] }))
          .catch(() => loadServerLibrary());
      }
      close();
      if (onConfirmed) onConfirmed();
      showAlert(t("js.alerts.collection_deleted"), "warning");
    };
    const onKey = (e) => { if (e.key === "Escape") close(); };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    modal.addEventListener("click", onBackdrop);
    document.addEventListener("keydown", onKey);
  };

  const openClearMainListModal = (onConfirmed) => {
    const modal      = document.getElementById("clearCollModal");
    const titleEl    = document.getElementById("clearModalTitle");
    const bodyEl     = document.getElementById("clearModalBody");
    const confirmBtn = document.getElementById("clearCollConfirm");
    const cancelBtn  = document.getElementById("clearCollCancel");
    if (!modal || !confirmBtn || !cancelBtn) return;

    if (titleEl) titleEl.textContent = t("js.library.clear_main_title", { list: MAIN_LIBRARY_NAME });
    if (bodyEl) bodyEl.textContent = t("js.library.clear_main_body");

    modal.hidden = false;

    const close = () => {
      modal.hidden = true;
      if (titleEl) titleEl.textContent = t("js.library.clear_list");
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      modal.removeEventListener("click", onBackdrop);
      document.removeEventListener("keydown", onKey);
    };
    const onConfirm = () => {
      close();
      if (onConfirmed) onConfirmed();
    };
    const onKey = (e) => { if (e.key === "Escape") close(); };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    modal.addEventListener("click", onBackdrop);
    document.addEventListener("keydown", onKey);
  };

  const openClearModal = (collId, onConfirmed) => {
    const modal      = document.getElementById("clearCollModal");
    const bodyEl     = document.getElementById("clearModalBody");
    const confirmBtn = document.getElementById("clearCollConfirm");
    const cancelBtn  = document.getElementById("clearCollCancel");
    if (!modal) return;

    const colls = getCollections();
    const idx   = colls.findIndex(c => c.id === collId);
    if (idx === -1) return;
    if (bodyEl) bodyEl.textContent = t("js.library.clear_collection_body");

    modal.hidden = false;

    const close = () => {
      modal.hidden = true;
      confirmBtn.removeEventListener("click", onConfirm);
      cancelBtn.removeEventListener("click", close);
      modal.removeEventListener("click", onBackdrop);
      document.removeEventListener("keydown", onKey);
    };
    const onConfirm = () => {
      const updated = getCollections();
      const i = updated.findIndex(c => c.id === collId);
      if (i !== -1) { updated[i].items = []; saveCollections(updated); }
      if (hasServerLibrary) {
        libraryApiFetch(`/api/library/collections/${encodeURIComponent(collId)}/words`, {
          method: "DELETE",
        })
          .then((response) => response.ok ? response.json() : Promise.reject(response))
          .then((payload) => syncServerLibraryState({ items: serverLibrary, collections: payload.collections || [] }))
          .catch(() => loadServerLibrary());
      }
      close();
      if (onConfirmed) onConfirmed();
      showAlert(t("js.alerts.list_cleared"), "warning");
    };
    const onKey = (e) => { if (e.key === "Escape") close(); };
    const onBackdrop = (e) => { if (e.target === modal) close(); };

    confirmBtn.addEventListener("click", onConfirm);
    cancelBtn.addEventListener("click", close);
    modal.addEventListener("click", onBackdrop);
    document.addEventListener("keydown", onKey);
  };

  const setupLibraryImport = () => {
    const fileInput = document.getElementById("wordFileInput");
    const dropZone = document.getElementById("dropZone");
    const chooseFileBtn = document.getElementById("chooseFileBtn");
    const fileNameDisplay = document.getElementById("fileNameDisplay");
    const importFileBtn = document.getElementById("importFileBtn");
    const pasteInput = document.getElementById("pasteWordsInput");
    const importPasteBtn = document.getElementById("importPasteBtn");

    if (!fileInput && !pasteInput) return;

    const normalize = (value) =>
      String(value || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    const parseWords = (text) =>
      String(text || "")
        .split(/[\n,;\t]+/)
        .map((item) => item.trim())
        .filter((item) => item.length > 0);

    const addWords = (words, language = "en") => {
      const saved = getLibrary();
      const seen = new Set(saved.map((item) => normalize(item.label)));
      let added = 0;
      const importedEntries = [];

      words.forEach((rawLabel) => {
        const label = rawLabel.trim();
        const key = normalize(label);
        if (!key || seen.has(key)) return;

        const suffix = key.replace(/[^a-z0-9]+/g, "-") || Array.from(label).map((char) => char.charCodeAt(0).toString(16)).join("").slice(0, 24);
        const clientKey = `custom-${language}-${suffix}`;

        saved.push({
          id: clientKey,
          label,
          language,
          translation: "",
        });
        importedEntries.push({ client_key: clientKey, label });
        seen.add(key);
        added += 1;
      });

      saveLibrary(saved);

      if (hasServerLibrary && importedEntries.length) {
        libraryApiFetch("/api/library/import", {
          method: "POST",
          body: JSON.stringify({ language, entries: importedEntries }),
        })
          .then((response) => response.ok ? response.json() : Promise.reject(response))
          .then((payload) => syncServerLibrary(payload.items || []))
          .catch(() => loadServerLibrary());
      }

      return added;
    };

    if (importFileBtn && fileInput) {
      if (chooseFileBtn) {
        chooseFileBtn.addEventListener("click", (e) => { e.stopPropagation(); fileInput.click(); });
      }
      if (fileInput && fileNameDisplay) {
        fileInput.addEventListener("change", () => {
          fileNameDisplay.textContent = fileInput.files[0]?.name ?? "";
        });
      }
      if (dropZone) {
        dropZone.addEventListener("dragover", (e) => { e.preventDefault(); dropZone.classList.add("drag-over"); });
        dropZone.addEventListener("dragleave", () => dropZone.classList.remove("drag-over"));
        dropZone.addEventListener("drop", (e) => {
          e.preventDefault();
          dropZone.classList.remove("drag-over");
          const file = e.dataTransfer.files?.[0];
          if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            fileNameDisplay.textContent = file.name;
          }
        });
      }
      importFileBtn.addEventListener("click", () => {
        const file = fileInput.files?.[0];
        if (!file) {
          showAlert(t("js.library.select_file_first"), "warning");
          return;
        }

        const reader = new FileReader();
        reader.onload = () => {
          const words = parseWords(reader.result);
          const added = addWords(words, getActiveLang());
          showAlert(t(added === 1 ? "js.library.imported_words_one" : "js.library.imported_words_other", { count: added }), added ? "success" : "warning");
          window.dispatchEvent(new Event("lexi-library-updated"));
        };
        reader.readAsText(file);
      });
    }

    if (importPasteBtn && pasteInput) {
      importPasteBtn.addEventListener("click", () => {
        const words = parseWords(pasteInput.value);
        const added = addWords(words, getActiveLang());
        showAlert(t(added === 1 ? "js.library.imported_words_one" : "js.library.imported_words_other", { count: added }), added ? "success" : "warning");
        if (added) pasteInput.value = "";
        window.dispatchEvent(new Event("lexi-library-updated"));
      });
    }
  };

  const setupLibrarySearch = () => {
    const searchInput = document.getElementById("searchInput");
    const cefrFilter = document.getElementById("cefrFilter");
    const topicFilter = document.getElementById("topicFilter");
    const noResults = document.getElementById("noResults");
    const paginationEl = document.getElementById("libraryPagination");

    if (!searchInput) return;

    const CARDS_PER_PAGE = 16;
    let currentPage = 1;
    let filteredCards = [];
    let isBound = false;

    const getCards = () => Array.from(document.querySelectorAll("[data-word-card]"));

    const normalize = (value) =>
      String(value || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

    const renderPagination = (totalPages) => {
      if (!paginationEl) return;
      if (totalPages <= 1) {
        paginationEl.innerHTML = "";
        return;
      }

      paginationEl.innerHTML = Array.from({ length: totalPages }, (_, i) => {
        const page = i + 1;
        const active = page === currentPage ? "page-btn--active" : "";
        return `<button class="page-btn ${active}" type="button" data-page="${page}">${page}</button>`;
      }).join("");

      paginationEl.querySelectorAll("[data-page]").forEach((btn) => {
        btn.addEventListener("click", () => {
          currentPage = Number(btn.dataset.page);
          renderPage();
          document.getElementById("searchInput")?.scrollIntoView({ behavior: "smooth", block: "nearest" });
        });
      });
    };

    const renderPage = () => {
      const cards = getCards();
      if (!cards.length) {
        filteredCards = [];
        renderPagination(0);
        if (noResults) noResults.hidden = false;
        return;
      }

      const totalPages = Math.max(1, Math.ceil(filteredCards.length / CARDS_PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      const start = (currentPage - 1) * CARDS_PER_PAGE;
      const end = start + CARDS_PER_PAGE;

      cards.forEach((card) => {
        const idx = filteredCards.indexOf(card);
        card.hidden = idx === -1 || idx < start || idx >= end;
      });

      renderPagination(totalPages);
    };

    const applyFilters = ({ preservePage = false } = {}) => {
      const cards = getCards();
      if (!cards.length) {
        filteredCards = [];
        renderPagination(0);
        if (noResults) noResults.hidden = false;
        return;
      }

      const query = normalize(searchInput.value.trim());
      const activeLang = getActiveLang();
      const cefrValue = cefrFilter ? cefrFilter.value : "all";
      const topicValue = topicFilter ? topicFilter.value : "all";

      filteredCards = cards.filter((card) => {
        const title = normalize(card.querySelector("h2")?.textContent);
        const text = normalize(card.textContent);
        const language = card.dataset.language || "all";
        const cefr = card.dataset.cefr || "all";
        const topic = card.dataset.topic || "";

        const matchesQuery = !query || title.includes(query) || text.includes(query);
        const matchesLanguage = language === activeLang;
        const matchesCefr = cefrValue === "all" || cefr === cefrValue;
        const matchesTopic = topicValue === "all" || topic === topicValue;

        return matchesQuery && matchesLanguage && matchesCefr && matchesTopic;
      });

      if (!preservePage) {
        currentPage = 1;
      }

      renderPage();

      if (noResults) noResults.hidden = filteredCards.length > 0;
    };

    if (!isBound) {
      searchInput.addEventListener("input", applyFilters);
      if (cefrFilter) cefrFilter.addEventListener("change", applyFilters);
      if (topicFilter) topicFilter.addEventListener("change", applyFilters);
      window.addEventListener("lexi-library-updated", () => applyFilters({ preservePage: true }));
      isBound = true;
    }

    applyFilters();
  };

  const setupLangDropdown = () => {
    const trigger = document.querySelector("[data-lang-trigger]");
    const dropdown = document.getElementById("langDropdown");

    const applyLang = async (lang, saveToStorage = true) => {
      const cfg = LANG_CONFIG[lang];
      if (!cfg) return;
      if (saveToStorage) {
        setPageLoading(true, t("js.change_language_loading"));
        try {
          await persistActiveLanguage(lang);
          location.href = location.pathname + location.search;
        } catch {
          setPageLoading(false);
          showAlert(t("js.change_language_error"), "danger");
        }
        return;
      }
      // Initial load: sync saved list with active lang
      syncActiveLangFlag();
      window.dispatchEvent(new CustomEvent("lexi-lang-changed", { detail: { lang } }));
    };

    if (trigger && dropdown) {
      // Build dropdown items
      dropdown.innerHTML = Object.entries(LANG_CONFIG)
        .map(([code, cfg]) => {
          const isNative = code === getNativeLang();
          return `<button
            class="lang-option${isNative ? " lang-option--native" : ""}"
            type="button"
            data-lang-code="${code}"
            ${isNative ? "disabled aria-disabled='true'" : ""}>
            <img src="${cfg.flag}" alt="${cfg.label}">
            <span>${cfg.label}</span>
            ${isNative ? `<span class='lang-native-badge'>${t("js.native_badge")}</span>` : ""}
          </button>`;
        })
        .join("");

      trigger.addEventListener("click", (e) => {
        e.stopPropagation();
        const isHidden = dropdown.hidden;
        dropdown.hidden = !isHidden;
        trigger.setAttribute("aria-expanded", String(isHidden));
      });

      dropdown.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-lang-code]");
        if (!btn || btn.disabled) return;
        applyLang(btn.dataset.langCode);
        dropdown.hidden = true;
        trigger.setAttribute("aria-expanded", "false");
      });

      document.addEventListener("click", (e) => {
        if (!e.target.closest("[data-lang-wrap]")) {
          dropdown.hidden = true;
          trigger.setAttribute("aria-expanded", "false");
        }
      });
    }

    // Apply stored lang on load — don't re-save, just reflect
    applyLang(getActiveLang(), false);
  };

  // ===== PANEL DE PROGRESO =====
  const CEFR_LEVELS = [
    { key: "c2", label: "C2", min: 70, next: null,  desc: t("js.progress.cefr_desc_c2") },
    { key: "c1", label: "C1", min: 55, next: 70,    desc: t("js.progress.cefr_desc_c1") },
    { key: "b2", label: "B2", min: 40, next: 55,    desc: t("js.progress.cefr_desc_b2") },
    { key: "b1", label: "B1", min: 25, next: 40,    desc: t("js.progress.cefr_desc_b1") },
    { key: "a2", label: "A2", min: 10, next: 25,    desc: t("js.progress.cefr_desc_a2") },
    { key: "a1", label: "A1", min: 0,  next: 10,    desc: t("js.progress.cefr_desc_a1") },
  ];

  const PALETTE = [
    "#7c3aed", "#a855f7", "#4f8ef7", "#2dc98b", "#f9b233",
    "#f76b4f", "#06b6d4", "#ec4899", "#84cc16", "#f59e0b",
  ];

  const getCefrLevel = (wordCount) =>
    CEFR_LEVELS.find((l) => wordCount >= l.min) || CEFR_LEVELS[CEFR_LEVELS.length - 1];

  const createProgressDrawer = () => {
    if (document.getElementById("progressDrawer")) return;

    const backdrop = document.createElement("div");
    backdrop.className = "progress-drawer-backdrop";
    backdrop.id = "progressDrawerBackdrop";

    const drawer = document.createElement("aside");
    drawer.className = "progress-drawer";
    drawer.id = "progressDrawer";
    drawer.setAttribute("aria-label", t("js.tooltips.progress"));

    drawer.innerHTML = `
      <div class="progress-drawer-header">
        <p class="progress-drawer-title">${t("js.tooltips.progress")}</p>
        <button class="progress-drawer-close" type="button" id="closeProgressDrawer" aria-label="${t("js.utility.close")}">&#x2715;</button>
      </div>
      <div class="progress-drawer-body" id="progressDrawerBody"></div>
      <div class="progress-drawer-footer">
        <a class="btn btn-warning w-100" href="progreso.html">${t("js.progress.view_full_progress")}</a>
      </div>
    `;

    document.body.appendChild(backdrop);
    document.body.appendChild(drawer);

    backdrop.addEventListener("click", () => toggleProgressDrawer(false));
    drawer.querySelector("#closeProgressDrawer").addEventListener("click", () => toggleProgressDrawer(false));
  };

  const toggleProgressDrawer = (open) => {
    const drawer = document.getElementById("progressDrawer");
    const backdrop = document.getElementById("progressDrawerBackdrop");
    if (!drawer || !backdrop) return;
    drawer.classList.toggle("open", open);
    backdrop.classList.toggle("open", open);
  };

  const renderProgressDrawer = () => {
    const body = document.getElementById("progressDrawerBody");
    if (!body) return;
    body.innerHTML = `<p style="margin:0;color:var(--muted)">${t("js.progress.drawer_loading")}</p>`;

    const modeData = [
      { key: "reading",   label: t("js.progress.reading"),   icon: "bi-book-half",   color: "#4f8ef7" },
      { key: "listening", label: t("js.progress.listening"), icon: "bi-headphones",  color: "#f76b4f" },
      { key: "speaking",  label: t("js.progress.speaking"),  icon: "bi-mic-fill",    color: "#2dc98b" },
      { key: "writing",   label: t("js.progress.writing"),   icon: "bi-pencil-fill", color: "#a855f7" },
      { key: "mix",       label: t("js.progress.mix"),       icon: "bi-shuffle",     color: "#f9b233" },
    ];

    loadServerProgressState().then((state) => {
      const streak = state?.exercises?.streak || 0;
      const wordCount = state?.summary?.saved_words_total || 0;
      const totalEx = state?.exercises?.total_completed || 0;
      const level = state?.level || {
        key: "a1",
        label: "A1",
        progress_percent: 0,
        next_target: 10,
        next_label: "A2",
        current_words: 0,
      };
      const toNext = level.next_target ? Math.max(level.next_target - level.current_words, 0) : 0;

      body.innerHTML = `
      <!-- Racha -->
      <div>
        <p class="pd-section-title">${t("js.progress.daily_streak")}</p>
        <div class="pd-streak">
          <span class="pd-streak-fire">${streak > 0 ? "🔥" : "💤"}</span>
          <div>
            <div class="pd-streak-num">${streak}</div>
            <div class="pd-streak-label">${t(streak === 1 ? "js.progress.streak_day_one" : "js.progress.streak_day_other")}</div>
          </div>
          <div style="margin-left:auto;text-align:right;">
            <div style="font-size:1.1rem;font-weight:700;color:var(--text)">${wordCount}</div>
            <div style="font-size:0.78rem;color:var(--muted)">${t(wordCount === 1 ? "js.progress.saved_word_one" : "js.progress.saved_word_other")}</div>
          </div>
        </div>
      </div>

      <!-- Nivel -->
      <div>
        <p class="pd-section-title">${t("js.progress.estimated_level")}</p>
        <div class="pd-level-row">
          <div class="pd-level-badge cefr-${level.key}">${level.label}</div>
          <div class="pd-level-info">
            <div class="pd-bar-wrap"><div class="pd-bar" style="width:${level.progress_percent || 0}%"></div></div>
            <p class="pd-bar-next">${level.next_target ? t("js.progress.words_to_next_level", { count: toNext, plural: toNext !== 1 ? "s" : "", level: level.next_label || "" }) : t("js.progress.max_level_reached_celebration")}</p>
          </div>
        </div>
      </div>

      <!-- Modos -->
      <div>
        <p class="pd-section-title">${t("js.progress.completed_exercises_total", { total: totalEx })}</p>
        <div class="pd-modes">
          ${modeData.map(m => `
            <div class="pd-mode">
              <div class="pd-mode-icon" style="--mc:${m.color}"><i class="bi ${m.icon}"></i></div>
              <div class="pd-mode-count">${state?.exercises?.modes?.[m.key] || 0}</div>
              <div class="pd-mode-label">${m.label}</div>
            </div>
          `).join("")}
        </div>
      </div>
    `;
    });
  };

  const setupDashboardPage = () => {
    const title = document.querySelector("[data-dashboard-title]");
    const subtitle = document.querySelector("[data-dashboard-subtitle]");
    if (!title || !subtitle) return;

    loadServerProgressState().then((state) => {
      const firstName = state?.user?.first_name || "";
      const activeLanguage = LANG_CONFIG[state?.active_language?.code]?.label || state?.active_language?.label || t("js.progress.your_language");
      const savedWords = state?.summary?.saved_words_active ?? 0;
      const collections = state?.summary?.collections_active ?? 0;
      const level = state?.level?.label || "A1";

      if (firstName) {
        title.textContent = `Hola, ${firstName}`;
      }

      subtitle.textContent = t("js.progress.active_summary", {
        language: activeLanguage,
        words: savedWords,
        words_plural: savedWords === 1 ? "" : "s",
        words_saved_plural: savedWords === 1 ? "" : "s",
        collections,
        collections_plural: collections === 1 ? "" : "es",
        level,
      });
    });
  };

  const setupProgressPage = () => {
    const streakNum = document.getElementById("streakNum");
    if (!streakNum) return;

    let donutChart = null;
    let barChart = null;

    const renderCharts = (state) => {
      if (typeof Chart === "undefined") return;

      const byLanguage = state?.summary?.words_by_language || [];
      const donutCanvas = document.getElementById("langDonutChart");
      const donutEmpty = document.getElementById("donutEmpty");
      const barCanvas = document.getElementById("modeBarChart");
      const barEmpty = document.getElementById("barEmpty");
      const modeCounts = ["reading", "listening", "speaking", "writing", "mix"].map((key) => state?.exercises?.modes?.[key] || 0);

      if (donutChart) donutChart.destroy();
      if (barChart) barChart.destroy();

      if (!byLanguage.length) {
        if (donutCanvas) donutCanvas.hidden = true;
        if (donutEmpty) donutEmpty.hidden = false;
      } else if (donutCanvas) {
        donutCanvas.hidden = false;
        if (donutEmpty) donutEmpty.hidden = true;
        donutChart = new Chart(donutCanvas, {
          type: "doughnut",
          data: {
            labels: byLanguage.map((item) => item.label),
            datasets: [{
              data: byLanguage.map((item) => item.count),
              backgroundColor: byLanguage.map((_, index) => PALETTE[index % PALETTE.length]),
              borderWidth: 2,
              borderColor: "#fff",
              hoverOffset: 8,
            }],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: "bottom", labels: { font: { family: "Inter", size: 12 }, padding: 12, color: "#374151" } },
              tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${countWordLabel(ctx.parsed, "js.library.word_one", "js.library.word_other")}` } },
            },
            cutout: "62%",
          },
        });
      }

      if (!modeCounts.reduce((total, count) => total + count, 0)) {
        if (barCanvas) barCanvas.hidden = true;
        if (barEmpty) barEmpty.hidden = false;
      } else if (barCanvas) {
        barCanvas.hidden = false;
        if (barEmpty) barEmpty.hidden = true;
        barChart = new Chart(barCanvas, {
          type: "bar",
          data: {
            labels: [t("js.progress.reading"), t("js.progress.listening"), t("js.progress.speaking"), t("js.progress.writing"), t("js.progress.mix")],
            datasets: [{
              data: modeCounts,
              backgroundColor: ["#4f8ef7", "#f76b4f", "#2dc98b", "#a855f7", "#f9b233"],
              borderRadius: 8,
              borderSkipped: false,
            }],
          },
          options: {
            responsive: true,
            indexAxis: "y",
            plugins: {
              legend: { display: false },
              tooltip: { callbacks: { label: (ctx) => ` ${ctx.parsed.x} ${t(ctx.parsed.x === 1 ? "js.progress.session_one" : "js.progress.session_other")}` } },
            },
            scales: {
              x: { grid: { color: "#f3f4f6" }, ticks: { font: { family: "Inter", size: 11 }, color: "#6b7280", stepSize: 1 } },
              y: { grid: { display: false }, ticks: { font: { family: "Inter", size: 12 }, color: "#374151" } },
            },
          },
        });
      }
    };

    const renderPage = () => {
      loadServerProgressState().then((state) => {
        const streak = state?.exercises?.streak || 0;
        const totalWords = state?.summary?.saved_words_total || 0;
        const level = state?.level || {};
        const recentWords = state?.summary?.recent_words || [];
        const activeLanguage = LANG_CONFIG[state?.active_language?.code]?.label || state?.active_language?.label || "?";
        const recentWordsList = document.getElementById("recentWordsList");

        document.getElementById("streakNum").textContent = streak;
        document.getElementById("streakFire").textContent = streak > 0 ? "🔥" : "🌱";
        document.getElementById("streakMsg").textContent = streak > 1
          ? t("js.progress.streak_message_many", { count: streak })
          : streak === 1
          ? t("js.progress.streak_message_one")
          : t("js.progress.streak_message_zero");

        document.getElementById("statWords").textContent = totalWords;
        document.getElementById("statExDone").textContent = state?.exercises?.total_completed || 0;
        document.getElementById("statLang").textContent = activeLanguage;
        document.getElementById("levelBadge").textContent = level.label || "A1";
        document.getElementById("levelBadge").className = `profile-level-badge cefr-${level.key || "a1"}`;
        document.getElementById("levelBar").style.width = `${level.progress_percent || 0}%`;
        document.getElementById("levelNext").textContent = level.next_target
          ? `${level.current_words || 0} / ${level.next_target} ${t("js.progress.saved_word_other")} ${level.next_label || "" ? `para ${level.next_label || ""}` : ""}`
          : t("js.progress.max_level_reached");

        document.getElementById("modeReading").textContent = state?.exercises?.modes?.reading || 0;
        document.getElementById("modeListening").textContent = state?.exercises?.modes?.listening || 0;
        document.getElementById("modeSpeaking").textContent = state?.exercises?.modes?.speaking || 0;
        document.getElementById("modeWriting").textContent = state?.exercises?.modes?.writing || 0;
        document.getElementById("modeMix").textContent = state?.exercises?.modes?.mix || 0;

        if (recentWordsList) {
          recentWordsList.innerHTML = "";
          if (!recentWords.length) {
            recentWordsList.innerHTML = `<li class="profile-words-empty">${t("js.progress.no_saved_words_long")} <a href="biblioteca.html">${t("js.progress.explore_library")}</a></li>`;
          } else {
            recentWords.forEach((item) => {
              const li = document.createElement("li");
              li.className = "profile-word-item";
              const translation = item.translation ? `<span class="profile-word-translation">${item.translation}</span>` : "";
              const meta = [item.language ? item.language.toUpperCase() : "", item.cefr || "", item.topic || ""]
                .filter(Boolean)
                .join(" · ");

              li.innerHTML = `
                <i class="bi bi-bookmark-fill profile-word-icon"></i>
                <div class="profile-word-copy">
                  <div class="profile-word-main">
                    <span class="profile-word-label">${item.label}</span>
                    ${translation}
                  </div>
                  ${meta ? `<div class="profile-word-meta">${meta}</div>` : ""}
                </div>
              `;
              recentWordsList.appendChild(li);
            });
          }
        }

        renderCharts(state);
      });
    };

    renderPage();
    window.addEventListener("lexi-lang-changed", () => {
      serverProgressState = null;
      renderPage();
    });
  };

  const setupProgressTrigger = () => {
    createProgressDrawer();

    document.querySelectorAll("[data-progress-trigger]").forEach((btn) => {
      btn.addEventListener("click", () => {
        renderProgressDrawer();
        toggleProgressDrawer(true);
      });
    });
  };

  const setupProfileLangChips = () => {
    const container = document.getElementById("langChips");
    if (!container || isServerRenderedProfile) return;
    const activeLang = getActiveLang();
    const hist = getLangHistory().sort((a, b) => a.firstAt - b.firstAt);
    if (!hist.length) {
      container.innerHTML = `<span style="font-size:0.85rem;color:var(--muted)">${t("js.profile.no_studied_languages")}</span>`;
      return;
    }
    container.innerHTML = hist.map(({ lang }) => {
      const cfg = LANG_CONFIG[lang];
      if (!cfg) return "";
      const isActive = lang === activeLang;
      return `<span class="profile-lang-chip${isActive ? " is-active" : ""}">
        <img src="${cfg.flag}" alt="${cfg.label}">
        ${cfg.label}${isActive ? ` <span style="font-size:0.7rem;opacity:0.65">· ${t("js.profile.active_badge")}</span>` : ""}
      </span>`;
    }).join("");
  };

  window.lexiSessionReady = loadServerSessionState();

  const applyHeaderTooltips = () => {
    const headerTooltips = [
      { selector: '[data-progress-trigger]', label: t("js.tooltips.progress") },
      { selector: '[data-lang-trigger]',     label: t("js.tooltips.change_language") },
      { selector: 'a.icon-btn[href="perfil.html"]', label: t("js.tooltips.profile") },
      { selector: '[data-cart-trigger]',     label: t("js.tooltips.review_list") },
      { selector: '[data-utility-trigger]',  label: t("js.tooltips.more_options") },
    ];
    headerTooltips.forEach(({ selector, label }) => {
      const el = document.querySelector(selector);
      if (!el) return;
      el.setAttribute('data-tooltip', label);
    });
  };

  const initializeApp = async () => {
    if (canUseServerSession) {
      setPageLoading(true);
      await window.lexiSessionReady;
      setPageLoading(false);
    } else {
      syncActiveLangFlag();
    }

    setupObserver();
    setupNavToggle();
    setupScrollButton();
    setupKeyboardShortcut();
    setupActionButtons();
    setupCartTriggers();
    setupUtilityMenu();
    setupProgressTrigger();
    setupLangDropdown();
    applyHeaderTooltips();
    setupAddToCartButtons();
    setupCartPageEvents();
    setupSaveDropdown();
    if (hasServerLibrary) {
      await loadServerLibrary();
      window.addEventListener("lexi-lang-changed", () => {
        serverLibrary = [];
        serverCatalog = [];
        loadServerLibrary();
      });
    }
    setupLibrarySearch();
    setupLibraryList();
    setupLibraryImport();
    setupDashboardPage();
    setupProgressPage();
    updateCartBadges();
    setupProfileLangChips();
    if (canUseServerSession) {
      setPageLoading(false);
    }
  };

  initializeApp();

});
