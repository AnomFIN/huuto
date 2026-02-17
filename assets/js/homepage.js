// Commit to intelligence. Push innovation. Pull results.
(() => {
  const payload = window.__HOME_DATA__;
  if (!payload || !Array.isArray(payload.popular) || !Array.isArray(payload.closing)) {
    console.error(JSON.stringify({ event: 'home_boot_failed', reason: 'invalid_payload' }));
    return;
  }

  const config = {
    initialVisible: 20,
    loadBatch: 10,
    carouselMs: 7000,
    loadDelayMs: 540,
    sloganMs: 5000,
  };

  const slogans = [
    'Suomen laadukkain huutokauppakokemus.',
    'Ammattilaistason näkymä jokaiseen huutoon.',
    'Nopeat löydöt, selkeä ostopolku.',
    'Luottamus ja laatu jokaisessa kohteessa.',
  ];

  const nodes = {
    header: document.getElementById('site-header'),
    authAction: document.getElementById('auth-action'),
    authModal: document.getElementById('auth-modal'),
    confirmLogin: document.getElementById('confirm-login'),
    cancelLogin: document.getElementById('cancel-login'),
    itemModal: document.getElementById('item-modal'),
    itemTitle: document.getElementById('item-modal-title'),
    itemMeta: document.getElementById('item-modal-meta'),
    itemPrice: document.getElementById('item-modal-price'),
    itemDetail: document.getElementById('item-modal-detail'),
    itemImage: document.getElementById('item-modal-image'),
    itemBidBtn: document.getElementById('item-bid-btn'),
    itemClose: document.getElementById('item-modal-close'),
    searchInput: document.getElementById('search-input'),
    searchCategory: document.getElementById('search-category'),
    searchSubmit: document.getElementById('search-submit'),
    searchClear: document.getElementById('search-clear'),
    languageToggle: document.getElementById('language-toggle'),
    dropdown: document.querySelector('.dropdown-menu'),
    categoryButtons: Array.from(document.querySelectorAll('#category-list [data-category]')),
    sectionFilterButtons: Array.from(document.querySelectorAll('[data-section-filter]')),
    slogan: document.getElementById('rotating-slogan'),
    popularGrid: document.getElementById('popular-grid'),
    closingGrid: document.getElementById('closing-grid'),
    popularLoad: document.getElementById('popular-load'),
    closingLoad: document.getElementById('closing-load'),
    popularEnd: document.getElementById('popular-end'),
    closingEnd: document.getElementById('closing-end'),
    carouselTrack: document.getElementById('carousel-track'),
    carouselDots: document.getElementById('carousel-dots'),
    carouselPrev: document.getElementById('carousel-prev'),
    carouselNext: document.getElementById('carousel-next'),
    carouselProgress: document.getElementById('carousel-progress'),
  };

  const state = {
    category: 'ALL',
    sectionCategory: 'ALL',
    query: '',
    isLoggedIn: localStorage.getItem('isLoggedIn') === 'true' || Boolean(payload.isLoggedIn),
    favorites: new Set(JSON.parse(localStorage.getItem('favorites') || '[]')),
    visiblePopular: config.initialVisible,
    visibleClosing: config.initialVisible,
    carouselStart: 0,
    sloganIndex: 0,
    carouselPaused: false,
    activeItemId: null,
  };

  const visibleCountdownNodes = new Set();
  let observer = null;
  let carouselTimerId = null;

  const sanitizeText = (value, maxLen = 80) => String(value ?? '').trim().slice(0, maxLen);
  const decodeHtmlEntities = (text) => {
    const doc = new DOMParser().parseFromString(text, 'text/html');
    return doc.documentElement.textContent;
  };
  const parseDateMs = (value) => {
    const parsed = Date.parse(String(value || ''));
    return Number.isFinite(parsed) ? parsed : Date.now();
  };
  const formatCurrency = (value) => `${Number(value).toLocaleString('fi-FI', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €`;

  const formatCountdown = (endTime) => {
    const diff = parseDateMs(endTime) - Date.now();
    if (diff <= 0) return 'Päättynyt';
    const days = Math.floor(diff / 86400000);
    const hours = Math.floor((diff % 86400000) / 3600000);
    const mins = Math.floor((diff % 3600000) / 60000);
    const secs = Math.floor((diff % 60000) / 1000);
    if (days > 0) return `${days} pv ${hours} h`;
    if (hours > 0) return `${hours} h ${mins} min`;
    return `${mins} min ${secs} s`;
  };

  const saveLocalState = () => {
    localStorage.setItem('isLoggedIn', String(state.isLoggedIn));
    localStorage.setItem('favorites', JSON.stringify(Array.from(state.favorites)));
  };

  const applyFilters = (items) => {
    const categoryFiltered = state.category === 'ALL' ? items : items.filter((item) => item.category === state.category);
    const sectionFiltered = state.sectionCategory === 'ALL' ? categoryFiltered : categoryFiltered.filter((item) => item.category === state.sectionCategory);

    if (!state.query) return sectionFiltered;
    const q = state.query.toLowerCase();
    return sectionFiltered.filter((item) => `${item.title} ${item.location} ${item.category}`.toLowerCase().includes(q));
  };

  const injectAd = (items, visibleCount) => {
    const batch = items.slice(0, visibleCount);
    if (batch.length < 20) return batch;
    const adIndex = Math.min(batch.length - 1, 1 + Math.floor(Math.random() * 20));
    const clone = [...batch];
    clone.splice(adIndex, 0, { id: `ad-${visibleCount}-${adIndex}`, isAd: true });
    return clone;
  };

  const observeCountdownNodes = (rootNode) => {
    const countdowns = Array.from(rootNode.querySelectorAll('[data-countdown]'));
    if (!observer) {
      countdowns.forEach((node) => visibleCountdownNodes.add(node));
      return;
    }
    countdowns.forEach((node) => observer.observe(node));
  };

  const getItemMarkup = (item, index) => {
    if (item.isAd) {
      return `<article class="item-card ad-card stagger-enter" style="animation-delay:${Math.min(index * 20, 260)}ms"><span class="ad-badge">MAINOS</span><strong>Kasvata näkyvyyttäsi premium-paikalla</strong><p>Nosta kohteesi eturiviin huutajien silmiin.</p><a href="#" class="ad-cta">Tutustu kampanjaan</a></article>`;
    }

    const isFav = state.favorites.has(item.id);
    return `<article class="item-card stagger-enter" data-item-id="${item.id}" style="animation-delay:${Math.min(index * 20, 260)}ms"><div class="item-image"><span>${sanitizeText(item.imageLabel || item.title, 28)}</span></div><div class="item-body"><h3 class="item-title">${sanitizeText(item.title, 82)}</h3><div class="row-top"><span>${sanitizeText(item.location, 26)}</span><span class="cat-pill">${sanitizeText(item.category, 18)}</span></div><div class="row-top"><span class="count-badge" data-countdown="${sanitizeText(item.endTime, 40)}">${formatCountdown(item.endTime)}</span><button class="favorite ${isFav ? 'active' : ''}" data-favorite="${item.id}" aria-label="Lisää suosikkeihin">♥</button></div><div class="item-price">${formatCurrency(item.priceNow)}</div><div class="row-meta"><span>${item.bidsCount} tarjousta</span><span>+${formatCurrency(item.minIncrement)}</span></div></div></article>`;
  };

  const renderSkeletons = (gridNode, count) => {
    gridNode.insertAdjacentHTML('beforeend', Array.from({ length: count }, () => '<article class="skeleton-card"></article>').join(''));
  };

  const renderGrid = ({ source, gridNode, loadNode, endNode, visible }) => {
    const filtered = applyFilters(source);
    const list = injectAd(filtered, visible);

    gridNode.innerHTML = list.map((item, index) => getItemMarkup(item, index)).join('');
    observeCountdownNodes(gridNode);

    const hasMore = filtered.length > visible;
    loadNode.disabled = !hasMore;
    loadNode.classList.remove('loading');
    loadNode.querySelector('span').textContent = hasMore ? 'Lataa lisää kohteita' : 'Ei enempää kohteita';
    endNode.hidden = hasMore;
  };

  const updateVisibleCountdowns = () => {
    visibleCountdownNodes.forEach((node) => {
      if (!node.isConnected) {
        visibleCountdownNodes.delete(node);
        return;
      }
      const endTime = node.getAttribute('data-countdown');
      node.textContent = formatCountdown(endTime);
    });
  };

  const getLookupItems = () => [...payload.popular, ...payload.closing].filter((item) => !item.isAd);

  const openItemModal = (item) => {
    if (!item) return;
    state.activeItemId = item.id;
    nodes.itemImage.textContent = decodeHtmlEntities(sanitizeText(item.imageLabel || item.title, 28));
    nodes.itemTitle.textContent = decodeHtmlEntities(sanitizeText(item.title, 100));
    nodes.itemMeta.textContent = `${decodeHtmlEntities(sanitizeText(item.location, 32))} • ${decodeHtmlEntities(sanitizeText(item.category, 24))} • ${formatCountdown(item.endTime)}`;
    nodes.itemPrice.textContent = `Hinta nyt ${formatCurrency(item.priceNow)}`;
    nodes.itemDetail.textContent = `Myyjä: ${decodeHtmlEntities(sanitizeText(item.seller || 'Premium Seller', 30))} • Toimitus: Nouto tai toimitus • Tarjouksia ${item.bidsCount}`;
    nodes.itemBidBtn.textContent = `Huutaa nyt ${formatCurrency(item.priceNow + item.minIncrement)} (+${formatCurrency(item.minIncrement)})`;
    nodes.itemModal.classList.add('open');
    nodes.itemModal.setAttribute('aria-hidden', 'false');
  };

  const closeItemModal = () => {
    nodes.itemModal.classList.remove('open');
    nodes.itemModal.setAttribute('aria-hidden', 'true');
    state.activeItemId = null;
  };

  const openAuthModal = () => {
    nodes.authModal.classList.add('open');
    nodes.authModal.setAttribute('aria-hidden', 'false');
  };

  const closeAuthModal = () => {
    nodes.authModal.classList.remove('open');
    nodes.authModal.setAttribute('aria-hidden', 'true');
  };

  const renderCarousel = () => {
    const source = applyFilters(payload.closing).slice(0, 12);
    if (!source.length) {
      nodes.carouselTrack.innerHTML = '';
      nodes.carouselDots.innerHTML = '';
      return;
    }

    const cards = [];
    for (let i = 0; i < 5; i += 1) {
      const item = source[(state.carouselStart + i) % source.length];
      const active = i === 2;
      const sideClass = i >= 3 ? 'last' : '';
      cards.push(`<article class="carousel-card ${active ? 'active' : sideClass}"><div class="carousel-image">${sanitizeText(item.imageLabel || item.title, 24)}${active ? `<span class="overlay-chip">Sulkeutuu ${formatCountdown(item.endTime)}</span><span class="overlay-price">Hinta nyt ${formatCurrency(item.priceNow)}</span>` : ''}</div><div class="carousel-meta"><strong>${sanitizeText(item.title, 56)}</strong><span data-countdown="${sanitizeText(item.endTime, 40)}">${formatCountdown(item.endTime)}</span><span>Tarjouksia ${item.bidsCount}</span>${active ? `<button class="primary-btn" data-bid="${item.id}" type="button">Huutaa nyt ${formatCurrency(item.priceNow + item.minIncrement)} (+${formatCurrency(item.minIncrement)})</button>` : ''}</div></article>`);
    }

    nodes.carouselTrack.innerHTML = cards.join('');
    observeCountdownNodes(nodes.carouselTrack);

    nodes.carouselDots.innerHTML = source.map((_, idx) => `<button type="button" data-dot="${idx}" class="${idx === state.carouselStart ? 'active' : ''}" aria-label="Kohde ${idx + 1}"></button>`).join('');
  };

  const resetCarouselProgress = () => {
    nodes.carouselProgress.style.transition = 'none';
    nodes.carouselProgress.style.width = '0%';
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        nodes.carouselProgress.style.transition = `width ${config.carouselMs}ms linear`;
        nodes.carouselProgress.style.width = '100%';
      });
    });
  };

  const advanceCarousel = () => {
    state.carouselStart = (state.carouselStart + 1) % 5;
    renderCarousel();
    resetCarouselProgress();
  };

  const restartCarouselTimer = () => {
    if (carouselTimerId) clearInterval(carouselTimerId);
    carouselTimerId = setInterval(() => {
      if (state.carouselPaused) return;
      advanceCarousel();
    }, config.carouselMs);
  };

  const redraw = () => {
    renderGrid({ source: payload.popular, gridNode: nodes.popularGrid, loadNode: nodes.popularLoad, endNode: nodes.popularEnd, visible: state.visiblePopular });
    renderGrid({ source: payload.closing, gridNode: nodes.closingGrid, loadNode: nodes.closingLoad, endNode: nodes.closingEnd, visible: state.visibleClosing });
    renderCarousel();
    updateVisibleCountdowns();
    nodes.authAction.textContent = state.isLoggedIn ? 'Kirjaudu ulos' : 'Kirjaudu sisään';
  };

  const applySearch = () => {
    state.query = sanitizeText(nodes.searchInput.value, 60);
    state.category = sanitizeText(nodes.searchCategory.value, 32) || 'ALL';
    nodes.categoryButtons.forEach((button) => button.classList.toggle('active', button.dataset.category === state.category));
    state.visiblePopular = config.initialVisible;
    state.visibleClosing = config.initialVisible;
    redraw();
  };

  const loadMore = (button, gridNode, key) => {
    button.disabled = true;
    button.classList.add('loading');
    renderSkeletons(gridNode, config.loadBatch);
    setTimeout(() => {
      state[key] += config.loadBatch;
      redraw();
    }, config.loadDelayMs);
  };

  const updateSectionFilterUi = (value) => {
    nodes.sectionFilterButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.sectionFilter === value));
  };

  document.addEventListener('click', (event) => {
    const favoriteBtn = event.target.closest('[data-favorite]');
    if (favoriteBtn) {
      const id = Number(favoriteBtn.getAttribute('data-favorite'));
      if (!Number.isFinite(id)) return;
      if (!state.isLoggedIn) {
        openAuthModal();
        return;
      }
      if (state.favorites.has(id)) state.favorites.delete(id); else state.favorites.add(id);
      saveLocalState();
      redraw();
      return;
    }

    const bidBtn = event.target.closest('[data-bid]');
    if (bidBtn) {
      const id = Number(bidBtn.getAttribute('data-bid'));
      getLookupItems().forEach((item) => {
        if (item.id === id) {
          item.priceNow += item.minIncrement;
          item.bidsCount += 1;
        }
      });
      redraw();
      return;
    }

    const itemCard = event.target.closest('[data-item-id]');
    if (itemCard && !event.target.closest('[data-favorite]')) {
      const id = Number(itemCard.getAttribute('data-item-id'));
      const found = getLookupItems().find((item) => item.id === id);
      openItemModal(found);
      return;
    }

    if (event.target === nodes.authModal) closeAuthModal();
    if (event.target === nodes.itemModal) closeItemModal();

    if (!event.target.closest('[data-dropdown]')) {
      nodes.dropdown.classList.remove('open');
      nodes.languageToggle.setAttribute('aria-expanded', 'false');
    }
  });

  nodes.searchSubmit.addEventListener('click', applySearch);
  nodes.searchInput.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') applySearch();
  });
  nodes.searchClear.addEventListener('click', () => {
    nodes.searchInput.value = '';
    applySearch();
  });

  nodes.categoryButtons.forEach((button) => {
    button.addEventListener('click', () => {
      state.category = sanitizeText(button.dataset.category, 32) || 'ALL';
      nodes.searchCategory.value = state.category;
      nodes.categoryButtons.forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
      redraw();
    });
  });

  nodes.sectionFilterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      state.sectionCategory = sanitizeText(button.dataset.sectionFilter, 32) || 'ALL';
      updateSectionFilterUi(state.sectionCategory);
      state.visiblePopular = config.initialVisible;
      state.visibleClosing = config.initialVisible;
      redraw();
    });
  });

  nodes.popularLoad.addEventListener('click', () => loadMore(nodes.popularLoad, nodes.popularGrid, 'visiblePopular'));
  nodes.closingLoad.addEventListener('click', () => loadMore(nodes.closingLoad, nodes.closingGrid, 'visibleClosing'));

  nodes.authAction.addEventListener('click', () => {
    // Delegate authentication to the server-side system.
    if (!state.isLoggedIn) {
      window.location.href = '/auth/login.php';
      return;
    }
    window.location.href = '/auth/logout.php';
  });

  nodes.confirmLogin.addEventListener('click', () => {
    // Confirming login should also go through the real authentication flow.
    window.location.href = '/auth/login.php';
  });
  nodes.cancelLogin.addEventListener('click', closeAuthModal);

  nodes.itemBidBtn.addEventListener('click', () => {
    if (!Number.isFinite(state.activeItemId)) return;
    getLookupItems().forEach((item) => {
      if (item.id === state.activeItemId) {
        item.priceNow += item.minIncrement;
        item.bidsCount += 1;
      }
    });
    redraw();
    closeItemModal();
  });
  nodes.itemClose.addEventListener('click', closeItemModal);

  nodes.languageToggle.addEventListener('click', () => {
    const open = nodes.dropdown.classList.toggle('open');
    nodes.languageToggle.setAttribute('aria-expanded', String(open));
  });

  nodes.carouselPrev.addEventListener('click', () => {
    state.carouselStart = (state.carouselStart - 1 + 5) % 5;
    renderCarousel();
    resetCarouselProgress();
  });
  nodes.carouselNext.addEventListener('click', advanceCarousel);
  nodes.carouselDots.addEventListener('click', (event) => {
    const dot = event.target.closest('[data-dot]');
    if (!dot) return;
    state.carouselStart = Number(dot.getAttribute('data-dot')) || 0;
    renderCarousel();
    resetCarouselProgress();
  });

  nodes.carouselTrack.addEventListener('mouseenter', () => { state.carouselPaused = true; });
  nodes.carouselTrack.addEventListener('mouseleave', () => { state.carouselPaused = false; resetCarouselProgress(); });

  let touchStartX = null;
  nodes.carouselTrack.addEventListener('touchstart', (event) => { touchStartX = event.changedTouches[0]?.clientX ?? null; }, { passive: true });
  nodes.carouselTrack.addEventListener('touchend', (event) => {
    if (touchStartX === null) return;
    const delta = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
    touchStartX = null;
    if (Math.abs(delta) < 35) return;
    if (delta < 0) advanceCarousel();
    else {
      state.carouselStart = (state.carouselStart - 1 + 5) % 5;
      renderCarousel();
      resetCarouselProgress();
    }
  }, { passive: true });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeAuthModal();
      closeItemModal();
      nodes.dropdown.classList.remove('open');
      nodes.languageToggle.setAttribute('aria-expanded', 'false');
    }
  });

  document.addEventListener('scroll', () => {
    nodes.header.classList.toggle('scrolled', window.scrollY > 10);
  }, { passive: true });

  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) visibleCountdownNodes.add(entry.target);
        else visibleCountdownNodes.delete(entry.target);
      });
    }, { rootMargin: '70px' });
  }

  setInterval(updateVisibleCountdowns, 1000);
  setInterval(() => {
    state.sloganIndex = (state.sloganIndex + 1) % slogans.length;
    nodes.slogan.textContent = slogans[state.sloganIndex];
  }, config.sloganMs);

  updateSectionFilterUi('ALL');
  saveLocalState();
  redraw();
  resetCarouselProgress();
  restartCarouselTimer();
})();
