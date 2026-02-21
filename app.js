// Commit to intelligence. Push innovation. Pull results.
(() => {
  'use strict';

  const CAROUSEL_INTERVAL_MS = 7000;
  const INITIAL_COUNT = 20;
  const LOAD_MORE_COUNT = 10;
  const LOAD_DELAY_MS = 550;
  const IMAGE_FALLBACK = `data:image/svg+xml,${encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="320" height="240"><rect width="320" height="240" fill="#f0f0f0"/><text x="50%" y="50%" text-anchor="middle" dy=".35em" fill="#999" font-family="Arial" font-size="14">Ei kuvaa</text></svg>')}`;
  const CATEGORIES = ['Ajoneuvot', 'Työkoneet', 'Asunnot', 'Vapaa-aika', 'Piha', 'Työkalut', 'Rakennus', 'Sisustus', 'Elektroniikka', 'Keräily', 'Muut'];
  const FILTER_PILLS = ['', 'Ajoneuvot', 'Työkoneet', 'Elektroniikka'];
  const LOCATIONS = ['Helsinki', 'Lahti', 'Tampere', 'Oulu', 'Turku', 'Jyväskylä'];
  const SELLERS = ['Kone Keltto Oy', 'Lahden Varaosa Oy', 'Pohjolan Kodit', 'Yritysmyyjä', 'Yksityinen myyjä'];
  const FOOTER_LINKS = [
    { label: 'Tietoa palvelusta', page: 'tietoa-palvelusta' },
    { label: 'Tietoa huutajalle', page: 'tietoa-huutajalle' },
    { label: 'Palvelun käyttöehdot', page: 'kayttoehdot' },
    { label: 'Aloita myyminen', page: 'myyminen' },
    { label: 'Huutokaupat.com-myyntiehdot', page: 'myyntiehdot' },
    { label: 'Hinnasto', page: 'hinnasto' },
    { label: 'Maksutavat', page: 'maksutavat' },
    { label: 'Asiakaspalvelu', page: 'asiakaspalvelu' },
    { label: 'Ohjeet ja vinkit', page: 'ohjeet' },
    { label: 'Tilaa uutiskirje', page: 'uutiskirje' },
    { label: 'Blogi', page: 'blogi' },
    { label: 'Kampanjat', page: 'kampanjat' },
    { label: 'Tietoa meistä', page: 'tietoa-meista' },
    { label: 'Lahen huutokauppa', page: 'lahen-huutokauppa' },
    { label: 'Meille töihin', page: 'meille-toihin' },
    { label: 'Medialle', page: 'medialle' },
    { label: 'Tietosuojaseloste', page: 'tietosuojaseloste' },
    { label: 'Evästeasetukset', page: 'evasteet' },
    { label: 'Läpinäkyvyysraportointi', page: 'lapinakyvyys' },
    { label: 'Saavutettavuusseloste', page: 'saavutettavuus' },
  ];
  const SLOGANS = [
    'Huuda fiksusti, voita oikeat kohteet.',
    'Luottamusta herättävä markkinapaikka jokaiselle huutajalle.',
    'Kun sekunnit ratkaisevat, näkymäsi pysyy edellä.',
    'Premium-kokemus ilman backend-kompleksisuutta.',
  ];

  const storedFavorites = readJson('huuto247-favorites', []);
  const favoriteIterable =
    Array.isArray(storedFavorites) ||
    (storedFavorites && typeof storedFavorites[Symbol.iterator] === 'function')
      ? storedFavorites
      : [];

  const state = {
    user: { loggedIn: false, name: 'Oma tili' },
    favorites: new Set(favoriteIterable),
    items: [],
    popularItems: [],
    closingItems: [],
    popularShown: INITIAL_COUNT,
    endingShown: INITIAL_COUNT,
    popularFilter: null,
    endingFilter: null,
    searchQuery: '',
    searchCategory: null,
    carouselIndex: 0,
    carouselPaused: false,
    carouselTickStartMs: performance.now(),
    sloganIndex: 0,
    touchStartX: 0,
  };

  const refs = {
    header: byId('siteHeader'),
    langToggle: byId('langToggle'),
    langMenu: byId('langMenu'),
    searchForm: byId('searchForm'),
    searchInput: byId('searchInput'),
    searchCategory: byId('searchCategory'),
    clearSearch: byId('clearSearch'),
    loginLink: byId('loginLink'),
    registerLink: byId('registerLink'),
    rotatingSlogan: byId('rotatingSlogan'),
    carouselTrack: byId('carouselTrack'),
    carouselDots: byId('carouselDots'),
    carouselPrev: byId('carouselPrev'),
    carouselNext: byId('carouselNext'),
    carouselProgress: byId('carouselProgress'),
    categoryList: byId('categoryList'),
    popularPills: byId('popularPills'),
    endingPills: byId('endingPills'),
    popularGrid: byId('popularGrid'),
    endingGrid: byId('endingGrid'),
    loadMorePopular: byId('loadMorePopular'),
    loadMoreEnding: byId('loadMoreEnding'),
    popularTip: byId('popularTip'),
    endingTip: byId('endingTip'),
    footerLinks: byId('footerLinks'),
    loginModal: byId('loginModal'),
    benefitModal: byId('benefitModal'),
    simulateLogin: byId('simulateLogin'),
    itemModal: byId('itemModal'),
    itemModalContent: byId('itemModalContent'),
  };

  boot();

  function boot() {
    // Only use data from server-side (PHP -> JavaScript), never mock data
    if (window.__HOME_DATA__) {
      const popularItems = window.__HOME_DATA__.popular || [];
      const closingItems = window.__HOME_DATA__.closing || [];
      state.popularItems = normalizeServerItems(popularItems);
      state.closingItems = normalizeServerItems(closingItems);
      state.items = normalizeServerItems([...popularItems, ...closingItems]);
      state.user.loggedIn = window.__HOME_DATA__.isLoggedIn || false;
      if (Array.isArray(window.__HOME_DATA__.favoriteIds)) {
        window.__HOME_DATA__.favoriteIds.forEach((id) => {
          const parsed = Number(id);
          if (Number.isInteger(parsed) && parsed > 0) {
            state.favorites.add(parsed);
          }
        });
      }
    } else {
      // No fallback to mock data - empty state if no server data
      state.items = [];
      console.warn('No server data available (__HOME_DATA__ not found)');
    }
    
    renderStaticBlocks();
    renderAll();
    bindEvents();

    setInterval(updateVisibleCountdowns, 1000);
    setInterval(syncEndedAuctions, 15000);
    setInterval(() => {
      if (!state.carouselPaused) moveCarousel(1);
    }, CAROUSEL_INTERVAL_MS);
    setInterval(rotateSlogan, 5000);
    requestAnimationFrame(updateCarouselProgress);

    logInfo('boot_complete', { totalItems: state.items.length, favorites: state.favorites.size });
  }

  function normalizeServerItems(items) {
    if (!Array.isArray(items)) return [];

    const byId = new Map();
    items.forEach((item) => {
      if (!item || typeof item !== 'object') return;

      const id = Number(item.id);
      if (!Number.isInteger(id) || id <= 0) return;

      const parsedEnd = Date.parse(String(item.endTime || item.end_time || ''));
      const endTime = Number.isFinite(parsedEnd) ? parsedEnd : Date.now() + 86400000;
      const imageUrl = typeof item.imageUrl === 'string' && item.imageUrl.trim() !== '' ? item.imageUrl.trim() : IMAGE_FALLBACK;

      byId.set(id, {
        ...item,
        id,
        endTime,
        imageUrl,
        title: cleanDisplayText(item.title || 'Kohde', 160),
        category: cleanDisplayText(item.category || 'Muut', 60),
        location: cleanDisplayText(item.location || 'Ei sijaintia', 120),
        seller: String(item.seller || 'Tuntematon myyjä'),
        delivery: String(item.delivery || 'Nouto / Toimitus'),
        bidsCount: Number.isFinite(Number(item.bidsCount)) ? Number(item.bidsCount) : 0,
        minIncrement: Number.isFinite(Number(item.minIncrement)) ? Number(item.minIncrement) : 1,
        priceNow: Number.isFinite(Number(item.priceNow)) ? Number(item.priceNow) : 0,
        startingPrice: Number.isFinite(Number(item.startingPrice)) ? Number(item.startingPrice) : 0,
        buyNowPrice: item.buyNowPrice === null || item.buyNowPrice === undefined ? null : Number(item.buyNowPrice),
      });
    });

    return Array.from(byId.values());
  }

  function renderStaticBlocks() {
    refs.searchCategory.innerHTML += CATEGORIES.map((category) => `<option value="${escapeHtml(category)}">${escapeHtml(category)}</option>`).join('');
    refs.footerLinks.innerHTML = FOOTER_LINKS.map((item) => `<a href="/info.php?page=${encodeURIComponent(item.page)}">${escapeHtml(item.label)}</a>`).join('');
    refs.popularPills.innerHTML = renderPills('popular');
    refs.endingPills.innerHTML = renderPills('ending');
    refs.categoryList.innerHTML = CATEGORIES.map((category) => `<li><button data-category="${escapeHtml(category)}">${escapeHtml(category)} ›</button></li>`).join('');
    refs.rotatingSlogan.textContent = SLOGANS[state.sloganIndex];
  }

  function bindEvents() {
    window.addEventListener('scroll', () => refs.header.classList.toggle('scrolled', window.scrollY > 6));

    refs.langToggle.addEventListener('click', () => {
      const open = refs.langMenu.classList.toggle('open');
      refs.langToggle.setAttribute('aria-expanded', String(open));
    });

    refs.searchInput.addEventListener('input', () => {
      refs.clearSearch.style.visibility = refs.searchInput.value ? 'visible' : 'hidden';
    });

    refs.clearSearch.addEventListener('click', () => {
      refs.searchInput.value = '';
      refs.clearSearch.style.visibility = 'hidden';
      refs.searchInput.focus();
      state.searchQuery = '';
      renderPopular();
    });

    refs.searchForm.addEventListener('submit', (event) => {
      event.preventDefault();
      state.searchQuery = sanitizeQuery(refs.searchInput.value);
      state.searchCategory = sanitizeCategory(refs.searchCategory.value);
      state.popularShown = INITIAL_COUNT;
      renderPopular();
      byId('popularSection').scrollIntoView({ behavior: 'smooth' });
    });

    refs.carouselPrev.addEventListener('click', () => moveCarousel(-1));
    refs.carouselNext.addEventListener('click', () => moveCarousel(1));

    refs.carouselTrack.addEventListener('mouseenter', () => { state.carouselPaused = true; });
    refs.carouselTrack.addEventListener('mouseleave', () => { state.carouselPaused = false; state.carouselTickStartMs = performance.now(); });
    refs.carouselTrack.addEventListener('touchstart', (event) => { state.touchStartX = event.changedTouches[0]?.clientX ?? 0; });
    refs.carouselTrack.addEventListener('touchend', (event) => {
      const delta = (event.changedTouches[0]?.clientX ?? 0) - state.touchStartX;
      if (Math.abs(delta) > 40) moveCarousel(delta > 0 ? -1 : 1);
    });

    refs.loadMorePopular.addEventListener('click', () => loadMore('popular'));
    refs.loadMoreEnding.addEventListener('click', () => loadMore('ending'));

    if (refs.loginLink && refs.loginLink.tagName === 'BUTTON') {
      refs.loginLink.addEventListener('click', () => {
        if (state.user.loggedIn) return logout();
        refs.loginModal.showModal();
      });
    }

    if (refs.registerLink && refs.registerLink.tagName === 'BUTTON') {
      refs.registerLink.addEventListener('click', () => {
        if (state.user.loggedIn) return logout();
        refs.loginModal.showModal();
      });
    }

    if (refs.simulateLogin) {
      refs.simulateLogin.addEventListener('click', () => {
        state.user.loggedIn = true;
        if (refs.loginLink && refs.loginLink.tagName === 'BUTTON') {
          refs.loginLink.textContent = state.user.name;
        }
        if (refs.registerLink && refs.registerLink.tagName === 'BUTTON') {
          refs.registerLink.textContent = 'Kirjaudu ulos';
        }
        refs.loginModal.close();
      });
    }

    [refs.loginModal, refs.benefitModal, refs.itemModal].forEach(bindDialogOutsideClose);
    window.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') refs.langMenu.classList.remove('open');
    });

    document.addEventListener('click', (event) => {
      if (!event.target.closest('.lang-wrap')) {
        refs.langMenu.classList.remove('open');
        refs.langToggle.setAttribute('aria-expanded', 'false');
      }

      const action = event.target.dataset.action;
      if (action === 'scroll-popular') byId('popularSection').scrollIntoView({ behavior: 'smooth' });
      if (action === 'open-benefit') refs.benefitModal.showModal();

      const categoryButton = event.target.closest('[data-category]');
      if (categoryButton) {
        state.popularFilter = sanitizeCategory(categoryButton.dataset.category);
        highlightActiveCategory();
        renderPopular();
      }

      const dot = event.target.closest('[data-dot]');
      if (dot) {
        state.carouselIndex = Number(dot.dataset.dot);
        state.carouselTickStartMs = performance.now();
        renderCarousel();
      }

      const pill = event.target.closest('[data-pill]');
      if (pill) {
        const kind = pill.dataset.kind;
        const value = sanitizeCategory(pill.dataset.pill);
        if (kind === 'popular') state.popularFilter = value;
        if (kind === 'ending') state.endingFilter = value;
        renderAll();
      }

      const favoriteButton = event.target.closest('[data-favorite]');
      if (favoriteButton) toggleFavorite(Number(favoriteButton.dataset.favorite));

      const bidButton = event.target.closest('[data-bid]');
      if (bidButton) placeBid(Number(bidButton.dataset.bid));

      const buyNowButton = event.target.closest('[data-buy-now]');
      if (buyNowButton) buyNow(Number(buyNowButton.dataset.buyNow));

      const card = event.target.closest('[data-item-card]');
      if (card && !event.target.closest('button')) openItemModal(Number(card.dataset.itemCard));
    });
  }

  function bindDialogOutsideClose(dialog) {
    dialog.addEventListener('click', (event) => {
      const rect = dialog.getBoundingClientRect();
      const inside = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
      if (!inside) dialog.close();
    });
  }

  function logout() {
    state.user.loggedIn = false;
    refs.loginLink.textContent = 'Kirjaudu sisään';
    refs.registerLink.textContent = 'Rekisteröidy';
  }

  function renderAll() {
    refs.popularPills.innerHTML = renderPills('popular');
    refs.endingPills.innerHTML = renderPills('ending');
    highlightActiveCategory();
    renderCarousel();
    renderPopular();
    renderEnding();
  }

  function renderPills(kind) {
    const active = kind === 'popular' ? state.popularFilter : state.endingFilter;
    return FILTER_PILLS.map((entry) => {
      const label = entry || 'Kaikki';
      const isActive = (active || '') === entry;
      return `<button class="pill ${isActive ? 'active' : ''}" data-kind="${kind}" data-pill="${escapeHtml(entry)}">${escapeHtml(label)}</button>`;
    }).join('');
  }

  function highlightActiveCategory() {
    refs.categoryList.querySelectorAll('button').forEach((button) => {
      const isActive = button.dataset.category === state.popularFilter;
      button.classList.toggle('active', isActive);
    });
  }

  function renderCarousel() {
    const carouselItems = getEndingItems().slice(0, 5);
    refs.carouselTrack.innerHTML = carouselItems.map((item, index) => {
      const pos = classifyCarouselPosition(index, state.carouselIndex, carouselItems.length);
      return `
        <article class="carousel-item ${pos}">
          <div class="carousel-media">
            <img src="${escapeHtml(item.imageUrl || IMAGE_FALLBACK)}" alt="${escapeHtml(item.title)}" />
            <div class="carousel-overlay">
              <span class="countdown-badge" data-end-time="${item.endTime}">${formatCountdown(item.endTime)}</span>
              <h3>${escapeHtml(item.title)}</h3>
              <div class="price">Hinta nyt: ${formatPrice(item.priceNow)}</div>
              <div class="subline">Tarjouksia ${item.bidsCount}</div>
              <button class="bid-btn" data-bid="${item.id}">Huutaa nyt: ${formatPrice(item.priceNow)} (+${formatPrice(item.minIncrement)})</button>
            </div>
          </div>
        </article>
      `;
    }).join('');

    refs.carouselDots.innerHTML = carouselItems.map((_, index) => `<button class="dot ${index === state.carouselIndex ? 'active' : ''}" data-dot="${index}" aria-label="Kohde ${index + 1}"></button>`).join('');
  }

  function moveCarousel(step) {
    // Determine current carousel length to avoid hard-coded modulo
    const carouselLength = Math.min(5, getEndingItems().length);

    if (carouselLength <= 1) {
      // With 0 or 1 items, always reset index to 0 and just re-render
      state.carouselIndex = 0;
      state.carouselTickStartMs = performance.now();
      renderCarousel();
      return;
    }

    state.carouselIndex = (state.carouselIndex + step + carouselLength) % carouselLength;
    state.carouselTickStartMs = performance.now();
    renderCarousel();
  }

  function updateCarouselProgress() {
    const elapsed = Math.max(0, performance.now() - state.carouselTickStartMs);
    const ratio = state.carouselPaused ? Math.min(1, elapsed / CAROUSEL_INTERVAL_MS) : elapsed / CAROUSEL_INTERVAL_MS;
    refs.carouselProgress.style.transform = `scaleX(${Math.max(0, 1 - ratio)})`;
    requestAnimationFrame(updateCarouselProgress);
  }

  function rotateSlogan() {
    refs.rotatingSlogan.classList.add('fade');
    state.sloganIndex = (state.sloganIndex + 1) % SLOGANS.length;
    setTimeout(() => {
      refs.rotatingSlogan.textContent = SLOGANS[state.sloganIndex];
      refs.rotatingSlogan.classList.remove('fade');
    }, 220);
  }

  function renderPopular(animated = false) {
    const source = getPopularItems();
    const slice = source.slice(0, state.popularShown);
    refs.popularGrid.innerHTML = renderCards(withAdCard(slice), animated);
    updateLoadButton(refs.loadMorePopular, refs.popularTip, state.popularShown, source.length);
  }

  function renderEnding(animated = false) {
    const source = getEndingItems();
    const slice = source.slice(0, state.endingShown);
    refs.endingGrid.innerHTML = renderCards(withAdCard(slice), animated);
    updateLoadButton(refs.loadMoreEnding, refs.endingTip, state.endingShown, source.length);
  }

  function getPopularItems() {
    const byBids = [...state.popularItems]
      .filter((item) => item.endTime > Date.now())
      .sort((a, b) => b.bidsCount - a.bidsCount);
    return byBids.filter((item) => {
      if (state.popularFilter && item.category !== state.popularFilter) return false;
      if (state.searchCategory && item.category !== state.searchCategory) return false;
      if (state.searchQuery && !item.title.toLowerCase().includes(state.searchQuery.toLowerCase())) return false;
      return true;
    });
  }

  function getEndingItems() {
    const byEnding = [...state.closingItems]
      .filter((item) => item.endTime > Date.now())
      .sort((a, b) => a.endTime - b.endTime);
    return byEnding.filter((item) => (state.endingFilter ? item.category === state.endingFilter : true));
  }

  function withAdCard(items) {
    if (items.length < 8) return items;
    const list = [...items];
    const adIndex = Math.floor(items.length / 2);
    list.splice(adIndex, 0, { isAd: true, id: -1 });
    return list;
  }

  function renderCards(items, animated) {
    return items.map((item, index) => {
      if (item.isAd) {
        return `<article class="ad-card ${animated ? 'with-enter' : ''}" style="animation-delay:${index * 24}ms"><span class="ad-label">MAINOS</span><h3 class="item-title">Kasvata kohteesi näkyvyyttä premium-sijoittelulla</h3><p class="subline">Yksi kampanja tavoittaa oikeat ostajat huutopiikin hetkellä.</p><a href="#" class="ad-cta">Tutustu kampanjaan →</a></article>`;
      }

      const favoriteClass = state.favorites.has(item.id) ? 'active' : '';
      const todayBadge = item.endTime - Date.now() < 86400000 ? '<span>Sulkeutuu tänään</span>' : '';
      const newBadge = item.id % 9 === 0 ? '<span>Uusi</span>' : '';

      return `
        <article class="card ${animated ? 'with-enter' : ''}" style="animation-delay:${index * 24}ms" data-item-card="${item.id}">
          <div class="thumb">
            <img src="${escapeHtml(item.imageUrl || IMAGE_FALLBACK)}" alt="${escapeHtml(item.title)}" onerror="this.onerror=null;this.src='${IMAGE_FALLBACK}'" />
            <button class="watch-btn ${favoriteClass}" data-favorite="${item.id}" aria-label="Lisää suosikiksi">♥</button>
          </div>
          <h3 class="item-title"><a href="auction.php?id=${item.id}" class="auction-link">${escapeHtml(item.title)}</a></h3>
          <div class="meta-row"><small>${escapeHtml(item.location)}</small><span class="category-pill">${escapeHtml(item.category)}</span></div>
          <span class="countdown" data-end-time="${item.endTime}">${formatCountdown(item.endTime)}</span>
          <p class="price">Hinta nyt: ${formatPrice(item.priceNow)}</p>
          <p class="subline">Tarjouksia ${item.bidsCount} • Minikorotus ${formatPrice(item.minIncrement)}</p>
          <p class="trust-line">Myyjä: ${escapeHtml(item.seller)} • ${escapeHtml(item.delivery)}</p>
          <div class="badges">${todayBadge}${newBadge}</div>
        </article>
      `;
    }).join('');
  }

  function loadMore(kind) {
    const grid = kind === 'popular' ? refs.popularGrid : refs.endingGrid;
    const button = kind === 'popular' ? refs.loadMorePopular : refs.loadMoreEnding;

    button.classList.add('loading');
    const skeletonHtml = Array.from({ length: LOAD_MORE_COUNT }, () => '<article class="skeleton-card"></article>').join('');
    grid.insertAdjacentHTML('beforeend', skeletonHtml);

    setTimeout(() => {
      button.classList.remove('loading');
      if (kind === 'popular') {
        state.popularShown += LOAD_MORE_COUNT;
        renderPopular(true);
      } else {
        state.endingShown += LOAD_MORE_COUNT;
        renderEnding(true);
      }
    }, LOAD_DELAY_MS);
  }

  function updateLoadButton(button, tipNode, shown, total) {
    if (shown >= total) {
      button.disabled = true;
      button.textContent = 'Ei enempää kohteita';
      tipNode.hidden = false;
      return;
    }
    button.disabled = false;
    button.textContent = 'Lataa lisää kohteita';
    tipNode.hidden = true;
  }

  function toggleFavorite(itemId) {
    if (!Number.isInteger(itemId) || itemId <= 0) return;
    if (!state.user.loggedIn) {
      refs.loginModal.showModal();
      return;
    }

    const willFavorite = !state.favorites.has(itemId);
    if (willFavorite) state.favorites.add(itemId);
    else state.favorites.delete(itemId);
    renderPopular();
    renderEnding();

    fetch('/api/toggle_favourite.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ auction_id: itemId })
    })
      .then((response) => response.json())
      .then((payload) => {
        if (!payload || payload.ok !== true) {
          throw new Error((payload && payload.error) || 'Suosikin tallennus epäonnistui');
        }

        if (payload.favorited === true) state.favorites.add(itemId);
        if (payload.favorited === false) state.favorites.delete(itemId);
        writeJson('huuto247-favorites', [...state.favorites]);
        renderPopular();
        renderEnding();
      })
      .catch(() => {
        if (willFavorite) state.favorites.delete(itemId);
        else state.favorites.add(itemId);
        renderPopular();
        renderEnding();
      });
  }

  function placeBid(itemId) {
    if (!Number.isInteger(itemId) || itemId <= 0) return;

    const updateBid = (item) => {
      if (item.id !== itemId) return item;
      return { ...item, bidsCount: item.bidsCount + 1, priceNow: item.priceNow + item.minIncrement };
    };

    state.items = state.items.map(updateBid);
    state.popularItems = state.popularItems.map(updateBid);
    state.closingItems = state.closingItems.map(updateBid);

    renderAll();
  }

  async function openItemModal(itemId) {
    if (!Number.isInteger(itemId) || itemId <= 0) return;
    const item = state.items.find((entry) => entry.id === itemId);
    if (!item) return;

    refs.itemModalContent.innerHTML = `
      <h3>${escapeHtml(item.title)}</h3>
      <img src="${escapeHtml(item.imageUrl || IMAGE_FALLBACK)}" alt="${escapeHtml(item.title)}" />
      <p class="price">Hinta nyt: ${formatPrice(item.priceNow)}</p>
      <p class="subline">Tarjouksia ${item.bidsCount} • Minikorotus ${formatPrice(item.minIncrement)}</p>
      <p class="trust-line">Myyjä: ${escapeHtml(item.seller)} • ${escapeHtml(item.delivery)} • ${escapeHtml(item.location)}</p>
      <p class="subline">Ladataan kohteen lisätietoja…</p>
      <div class="modal-actions">
        <button value="cancel" class="btn-secondary">Sulje</button>
        <a class="btn-secondary" href="auction.php?id=${item.id}">Avaa kohde</a>
        <button type="button" class="btn-secondary" data-buy-now="${item.id}" ${item.buyNowPrice ? '' : 'disabled'}>
          ${item.buyNowPrice ? `Osta heti ${formatPrice(item.buyNowPrice)}` : 'Osta heti ei käytössä'}
        </button>
        <button value="confirm" class="btn-primary" data-bid="${item.id}">Huutaa nyt</button>
      </div>
    `;
    refs.itemModal.showModal();

    try {
      const response = await fetch(`api/get_auction_popup.php?id=${item.id}`);
      const data = await response.json();
      if (!response.ok || !data.success || !data.item) {
        return;
      }

      const details = data.item;
      const bids = Array.isArray(data.bids) ? data.bids : [];
      const bidHistory = bids.length
        ? `<ul class="modal-bids">${bids.map((bid) => `<li>${escapeHtml(bid.username)}: <strong>${formatPrice(bid.amount)}</strong> <small>${escapeHtml(formatBidTime(bid.bidTime))}</small></li>`).join('')}</ul>`
        : '<p class="subline">Ei huutohistoriaa vielä.</p>';

      refs.itemModalContent.innerHTML = `
        <h3>${escapeHtml(cleanDisplayText(details.title || item.title, 160))}</h3>
        <img src="${escapeHtml(details.imageUrl || item.imageUrl || IMAGE_FALLBACK)}" alt="${escapeHtml(cleanDisplayText(details.title || item.title, 160))}" />
        <p class="price">Hinta nyt: ${formatPrice(details.currentPrice ?? item.priceNow)}</p>
        <p class="subline">Tarjouksia ${Number(details.bidCount ?? item.bidsCount)} • Minikorotus ${formatPrice(details.bidIncrement ?? item.minIncrement)}</p>
        <p class="trust-line">Sijainti: ${escapeHtml(cleanDisplayText(details.location || item.location, 120))} • Myyjä: ${escapeHtml(cleanDisplayText(details.seller || item.seller, 80))}</p>
        <p class="subline">Päättyy: ${escapeHtml(formatBidTime(details.endTime || item.endTime))} • Kategoria: ${escapeHtml(cleanDisplayText(details.category || item.category, 60))}</p>
        <p>${escapeHtml(cleanDisplayText(details.description || '', 600))}</p>
        <h4 style="margin:.55rem 0 .35rem;">Huutohistoria</h4>
        ${bidHistory}
        <div class="modal-actions">
          <button value="cancel" class="btn-secondary">Sulje</button>
          <a class="btn-secondary" href="auction.php?id=${item.id}">Avaa kohde</a>
          <button type="button" class="btn-secondary" data-buy-now="${item.id}" ${details.buyNowPrice ? '' : 'disabled'}>
            ${details.buyNowPrice ? `Osta heti ${formatPrice(details.buyNowPrice)}` : 'Osta heti ei käytössä'}
          </button>
          <button value="confirm" class="btn-primary" data-bid="${item.id}">Huutaa nyt</button>
        </div>
      `;
    } catch (error) {
      logInfo('popup_details_load_failed', { itemId, message: error.message });
    }
  }

  function updateVisibleCountdowns() {
    const viewportH = window.innerHeight || 0;
    document.querySelectorAll('[data-end-time]').forEach((node) => {
      const rect = node.getBoundingClientRect();
      if (rect.bottom < 0 || rect.top > viewportH + 80) return;
      const endTime = Number(node.dataset.endTime);
      if (!Number.isFinite(endTime)) return;
      node.textContent = formatCountdown(endTime);
    });
  }

  function syncEndedAuctions() {
    const now = Date.now();
    const previousPopular = state.popularItems.length;
    const previousClosing = state.closingItems.length;

    state.popularItems = state.popularItems.filter((item) => item.endTime > now);
    state.closingItems = state.closingItems.filter((item) => item.endTime > now);
    state.items = state.items.filter((item) => item.endTime > now);

    const carouselLength = Math.min(5, getEndingItems().length);
    if (carouselLength <= 1) {
      state.carouselIndex = 0;
    } else if (state.carouselIndex >= carouselLength) {
      state.carouselIndex = 0;
    }

    if (previousPopular !== state.popularItems.length || previousClosing !== state.closingItems.length) {
      renderAll();
    }
  }

  function buyNow(itemId) {
    if (!Number.isInteger(itemId) || itemId <= 0) return;

    const applyBuyNow = (entry) => {
      if (entry.id !== itemId) return entry;
      const buyNowPrice = Number(entry.buyNowPrice);
      if (!Number.isFinite(buyNowPrice) || buyNowPrice <= 0) return entry;
      return {
        ...entry,
        priceNow: buyNowPrice,
        bidsCount: entry.bidsCount + 1,
      };
    };

    state.items = state.items.map(applyBuyNow);
    state.popularItems = state.popularItems.map(applyBuyNow);
    state.closingItems = state.closingItems.map(applyBuyNow);

    renderAll();
  }

  function createMockItems(totalCount) {
    if (!Number.isInteger(totalCount) || totalCount <= 0) throw new Error('Invalid mock item count');

    return Array.from({ length: totalCount }, (_, index) => {
      const id = index + 1;
      const category = CATEGORIES[index % CATEGORIES.length];
      const seller = SELLERS[index % SELLERS.length];
      return {
        id,
        title: `${category} kohde ${id}`,
        location: LOCATIONS[index % LOCATIONS.length],
        category,
        endTime: Date.now() + ((index * 3) % 260) * 3600000 + ((index * 9) % 60) * 60000 + 120000,
        priceNow: 45 + ((index * 17) % 1100),
        bidsCount: 1 + (index % 27),
        minIncrement: 5 + (index % 6) * 5,
        seller,
        delivery: index % 2 === 0 ? 'Nouto / Toimitus' : 'Nouto',
        imageUrl: null, // No mock images, only real images from database
      };
    });
  }

  function buildPhotoLikePlaceholder(category, id, seed) {
    const hue = 206 + (seed % 8);
    const label = escapeHtml(`${category} #${id}`);
    const svg = `<svg xmlns='http://www.w3.org/2000/svg' width='640' height='420'><defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0%' stop-color='hsl(${hue},14%,78%)'/><stop offset='100%' stop-color='hsl(${hue + 4},10%,63%)'/></linearGradient><filter id='noise'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/><feColorMatrix type='saturate' values='0'/><feComponentTransfer><feFuncA type='table' tableValues='0 .06 .1'/></feComponentTransfer></filter></defs><rect width='640' height='420' fill='url(#g)'/><rect width='640' height='420' fill='rgba(8,14,24,.08)'/><circle cx='160' cy='118' r='42' fill='rgba(255,255,255,.3)'/><rect x='96' y='238' width='448' height='84' rx='14' fill='rgba(255,255,255,.2)'/><text x='50%' y='90%' text-anchor='middle' fill='rgba(24,33,48,.78)' font-size='25' font-family='Arial'>${label}</text><rect width='640' height='420' filter='url(#noise)'/></svg>`;
    return encodeURIComponent(svg);
  }

  function classifyCarouselPosition(index, activeIndex, length) {
    const prev = (activeIndex - 1 + length) % length;
    const next = (activeIndex + 1) % length;
    if (index === activeIndex) return 'active';
    if (index === prev) return 'prev';
    if (index === next) return 'next';
    return '';
  }

  function formatCountdown(endTime) {
    const diffSec = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
    if (diffSec <= 0) return 'Sulkeutunut';
    if (diffSec < 60) return `Sulkeutuu nyt (${diffSec} s)`;

    if (diffSec >= 3600) {
      const days = Math.floor(diffSec / 86400);
      const hours = Math.floor((diffSec % 86400) / 3600);
      const minutes = Math.floor((diffSec % 3600) / 60);
      return `${days > 0 ? `${days} pv ` : ''}${hours} h ${minutes} min`;
    }
    const minutes = Math.floor((diffSec % 3600) / 60);
    const seconds = diffSec % 60;
    return `${minutes} min ${seconds} s`;
  }

  function formatPrice(amount) {
    const numeric = Number(amount);
    return `${Number.isFinite(numeric) ? numeric.toLocaleString('fi-FI') : 0} €`;
  }

  function sanitizeCategory(value) {
    if (typeof value !== 'string') return null;
    const clean = value.trim();
    if (!clean) return null;
    return CATEGORIES.includes(clean) ? clean : null;
  }

  function sanitizeQuery(value) {
    if (typeof value !== 'string') return '';
    return value.replace(/[<>]/g, '').trim().slice(0, 70);
  }

  function cleanDisplayText(value, maxLength = 120) {
    const raw = String(value ?? '');
    const withoutArtifacts = raw
      .replace(/&quot;/gi, '"')
      .replace(/&amp;quot;/gi, '"')
      .replace(/\s*\"\s*\/?\s*>/g, ' ')
      .replace(/\s*\/?\s*>\s*/g, ' ')
      .replace(/```(?:json)?/gi, ' ')
      .replace(/[\u0000-\u001F\u007F]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();

    return withoutArtifacts.slice(0, maxLength);
  }

  function formatBidTime(value) {
    if (typeof value === 'number' && Number.isFinite(value)) {
      return new Date(value).toLocaleString('fi-FI');
    }

    const parsed = Date.parse(String(value || ''));
    if (!Number.isFinite(parsed)) {
      return String(value || '');
    }

    return new Date(parsed).toLocaleString('fi-FI');
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function byId(id) {
    const node = document.getElementById(id);
    if (!node) throw new Error(`Missing required element: ${id}`);
    return node;
  }

  function readJson(key, fallback) {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (error) {
      logInfo('local_storage_read_error', { key, message: error.message });
      return fallback;
    }
  }

  function writeJson(key, value) {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      logInfo('local_storage_write_error', { key, message: error.message });
    }
  }

  function logInfo(event, payload) {
    console.info(JSON.stringify({ level: 'info', event, ...payload }));
  }
})();
