/**
 * AseKauppa — assets/js/main.js
 * Main ES6 module: partials injection, theme, cart, search, page init.
 */

/* ── Storage helpers ── */
const LS_THEME   = 'asekauppa:theme';
const LS_CART    = 'asekauppa:cart';
const LS_USER    = 'asekauppa:user';
const LS_ORDERS  = 'asekauppa:orders';
const LS_FORMS   = 'asekauppa:sellForms';

function lsGet(key) {
  try { return JSON.parse(localStorage.getItem(key)); } catch { return null; }
}

function lsSet(key, val) {
  try { localStorage.setItem(key, JSON.stringify(val)); } catch { /* quota */ }
}

/* ── Theme ── */
function applyTheme(theme) {
  document.body.classList.toggle('theme-light', theme === 'light');
  document.body.classList.toggle('theme-dark',  theme === 'dark');
}

function initTheme() {
  const saved = lsGet(LS_THEME) || 'light';
  applyTheme(saved);
}

function toggleTheme() {
  const isLight = document.body.classList.contains('theme-light');
  const next = isLight ? 'dark' : 'light';
  applyTheme(next);
  lsSet(LS_THEME, next);
}

/* ── Cart ── */
function getCart() {
  return lsGet(LS_CART) || [];
}

function saveCart(cart) {
  lsSet(LS_CART, cart);
}

function addToCart(product, qty = 1) {
  const cart = getCart();
  const existing = cart.find(i => i.id === product.id);
  if (existing) {
    existing.qty += qty;
  } else {
    cart.push({
      id:    product.id,
      name:  product.name,
      price: product.price,
      qty:   qty,
      img:   (product.images && product.images[0]) || '',
    });
  }
  saveCart(cart);
  renderCartDrawer();
  showToast(`Lisätty: ${product.name}`);
}

function removeFromCart(id) {
  saveCart(getCart().filter(i => i.id !== id));
  renderCartDrawer();
}

function updateCartQty(id, qty) {
  const cart = getCart();
  const item = cart.find(i => i.id === id);
  if (item) {
    item.qty = Math.max(1, qty);
    saveCart(cart);
    renderCartDrawer();
  }
}

function clearCart() {
  saveCart([]);
  renderCartDrawer();
}

function cartTotal(cart) {
  return cart.reduce((s, i) => s + i.price * i.qty, 0);
}

function fmtPrice(n) {
  return n.toLocaleString('fi-FI', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

function renderCartDrawer() {
  const cart    = getCart();
  const badge   = document.getElementById('cartBadge');
  const body    = document.getElementById('cartBody');
  const total   = document.getElementById('cartTotal');
  const count   = cart.reduce((s, i) => s + i.qty, 0);

  if (badge) badge.textContent = count;

  if (!body) return;

  if (cart.length === 0) {
    body.innerHTML = '<p class="small" style="padding:14px 0;color:var(--muted)">Ostoskori on tyhjä.</p>';
  } else {
    body.innerHTML = cart.map(item => `
      <div class="cart-item" data-id="${item.id}">
        <img src="${item.img}" alt="${escHtml(item.name)}"
             onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 56 56%22><rect fill=%22%231e2d40%22 width=%2256%22 height=%2256%22/><text x=%2228%22 y=%2234%22 text-anchor=%22middle%22 fill=%22%238baac0%22 font-size=%2218%22>🔫</text></svg>'">
        <div class="ci-info">
          <div class="ci-name">${escHtml(item.name)}</div>
          <div class="ci-price">${fmtPrice(item.price)} × ${item.qty} = ${fmtPrice(item.price * item.qty)}</div>
          <div class="ci-actions">
            <button class="btn small" type="button" data-action="dec" data-id="${item.id}">−</button>
            <span style="min-width:24px;text-align:center;font-weight:700">${item.qty}</span>
            <button class="btn small" type="button" data-action="inc" data-id="${item.id}">+</button>
            <button class="btn small danger" type="button" data-action="rm" data-id="${item.id}">Poista</button>
          </div>
        </div>
      </div>
    `).join('');
  }

  if (total) total.textContent = fmtPrice(cartTotal(cart));
}

/* ── Toast ── */
function showToast(msg) {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();

  const el = document.createElement('div');
  el.className = 'toast';
  el.textContent = msg;
  document.body.appendChild(el);

  setTimeout(() => {
    el.classList.add('fade-out');
    setTimeout(() => el.remove(), 320);
  }, 2000);
}

/* ── HTML escape ── */
function escHtml(str) {
  return String(str ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

/* ── Data fetching ── */
let _products = null;
let _siteData = null;

async function getProducts() {
  if (_products) return _products;
  try {
    const res = await fetch('data/products.json');
    if (!res.ok) throw new Error('products fetch failed');
    _products = await res.json();
  } catch {
    _products = [];
  }
  return _products;
}

async function getSiteData() {
  if (_siteData) return _siteData;
  try {
    const res = await fetch('data/site.json');
    if (!res.ok) throw new Error('site fetch failed');
    _siteData = await res.json();
  } catch {
    _siteData = { categories: [], hero: {} };
  }
  return _siteData;
}

/* ── Product card HTML ── */
function productCardHTML(p) {
  const badge = p.badge
    ? `<span class="pill pc-badge"><strong>${escHtml(p.badge)}</strong></span>`
    : '';
  const oldPrice = p.old_price
    ? `<span class="pc-old">${fmtPrice(p.old_price)}</span>`
    : '';
  const img = (p.images && p.images[0]) || '';
  const PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 150'%3E%3Crect fill='%231e2d40' width='200' height='150'/%3E%3Ctext x='100' y='82' text-anchor='middle' fill='%238baac0' font-size='40'%3E%F0%9F%94%AB%3C/text%3E%3C/svg%3E";

  return `
    <div class="product-card" data-product-id="${p.id}">
      <img class="pc-img" src="${escHtml(img)}" alt="${escHtml(p.name)}" loading="lazy"
           onerror="this.src='${PLACEHOLDER}'">
      <div class="pc-body">
        ${badge}
        <div class="pc-name">${escHtml(p.name)}</div>
        <div class="pc-price-row">
          <span class="pc-price">${fmtPrice(p.price)}</span>
          ${oldPrice}
        </div>
      </div>
      <div class="pc-foot">
        <button class="btn primary small" type="button"
                data-action="addcart" data-id="${p.id}">Lisää koriin</button>
      </div>
    </div>
  `;
}

/* ── Render grid of products ── */
function renderProductGrid(el, products) {
  if (!el) return;
  if (!products || products.length === 0) {
    el.innerHTML = '<p class="small" style="padding:14px 0">Ei tuotteita.</p>';
    return;
  }
  el.innerHTML = products.map(productCardHTML).join('');
}

/* ── Delegate add-to-cart button clicks on a grid ── */
function wireGridCart(gridEl, products) {
  if (!gridEl) return;
  gridEl.addEventListener('click', e => {
    const btn = e.target.closest('[data-action="addcart"]');
    if (!btn) return;
    const id = parseInt(btn.dataset.id, 10);
    const product = products.find(p => p.id === id);
    if (product) addToCart(product, 1);
  });
}

/* ── Sort products ── */
function sortProducts(products, sortVal) {
  const arr = [...products];
  switch (sortVal) {
    case 'price_asc':  return arr.sort((a, b) => a.price - b.price);
    case 'price_desc': return arr.sort((a, b) => b.price - a.price);
    case 'rating':     return arr.sort((a, b) => (b.rating || 0) - (a.rating || 0));
    case 'new':        return arr.sort((a, b) => (b.created || '').localeCompare(a.created || ''));
    default:           return arr.sort((a, b) => (b.reviews || 0) - (a.reviews || 0));
  }
}

/* ── Partial injection ── */
async function loadPartials() {
  const headerEl = document.getElementById('siteHeader');
  const footerEl = document.getElementById('siteFooter');

  try {
    if (headerEl) {
      const res = await fetch('partials/header.html');
      if (res.ok) headerEl.innerHTML = await res.text();
    }
    if (footerEl) {
      const res = await fetch('partials/footer.html');
      if (res.ok) footerEl.innerHTML = await res.text();
    }
  } catch { /* offline/missing */ }
}

/* ── Wire header interactions ── */
function wireHeader(products) {
  const themeBtn    = document.getElementById('themeBtn');
  const mobileToggle = document.getElementById('mobileToggle');
  const mobileMenu  = document.getElementById('mobileMenu');
  const cartBtn     = document.getElementById('cartBtn');
  const cartClose   = document.getElementById('cartClose');
  const cartDrawer  = document.getElementById('cartDrawer');
  const searchForm  = document.getElementById('siteSearchForm');
  const searchInput = document.getElementById('siteSearchInput');
  const suggest     = document.getElementById('siteSuggest');

  themeBtn?.addEventListener('click', toggleTheme);

  mobileToggle?.addEventListener('click', () => {
    if (!mobileMenu) return;
    mobileMenu.style.display = mobileMenu.style.display === 'none' ? '' : 'none';
  });

  cartBtn?.addEventListener('click', () => cartDrawer?.classList.add('open'));
  cartClose?.addEventListener('click', () => cartDrawer?.classList.remove('open'));

  cartDrawer?.addEventListener('click', e => {
    if (e.target === cartDrawer) cartDrawer.classList.remove('open');

    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const id = parseInt(btn.dataset.id, 10);
    if (btn.dataset.action === 'rm')  removeFromCart(id);
    if (btn.dataset.action === 'inc') {
      const cart = getCart();
      const item = cart.find(i => i.id === id);
      if (item) updateCartQty(id, item.qty + 1);
    }
    if (btn.dataset.action === 'dec') {
      const cart = getCart();
      const item = cart.find(i => i.id === id);
      if (item) updateCartQty(id, item.qty - 1);
    }
  });

  searchForm?.addEventListener('submit', e => {
    e.preventDefault();
    const q = searchInput?.value.trim();
    if (q) window.location.href = `search.html?q=${encodeURIComponent(q)}`;
  });

  searchInput?.addEventListener('input', () => {
    if (!suggest) return;
    const q = searchInput.value.trim().toLowerCase();
    if (!q || !products) {
      suggest.classList.remove('open');
      suggest.innerHTML = '';
      return;
    }
    const matches = products
      .filter(p =>
        p.name.toLowerCase().includes(q) ||
        (p.brand || '').toLowerCase().includes(q) ||
        (p.category || '').toLowerCase().includes(q)
      )
      .slice(0, 5);

    if (matches.length === 0) {
      suggest.classList.remove('open');
      suggest.innerHTML = '';
      return;
    }

    suggest.innerHTML = matches.map(p => {
      const img = (p.images && p.images[0]) || '';
      return `
        <div class="suggest-item" data-id="${p.id}" role="option" tabindex="0">
          <img src="${escHtml(img)}" alt="" loading="lazy"
               onerror="this.style.display='none'">
          <span>${escHtml(p.name)} — ${fmtPrice(p.price)}</span>
        </div>
      `;
    }).join('');

    suggest.classList.add('open');

    suggest.querySelectorAll('.suggest-item').forEach(item => {
      const activate = () => {
        window.location.href = `product.html?id=${item.dataset.id}`;
      };
      item.addEventListener('click', activate);
      item.addEventListener('keydown', e => { if (e.key === 'Enter') activate(); });
    });
  });

  document.addEventListener('click', e => {
    if (!searchForm?.contains(e.target)) {
      suggest?.classList.remove('open');
    }
  });

  // Update account label
  const user = lsGet(LS_USER);
  const accLabel = document.getElementById('accountLabel');
  if (accLabel && user?.name) accLabel.textContent = user.name;
}

/* ════════════════════════════════════════════
   PAGE INITIALISERS
   ════════════════════════════════════════════ */

/* ── home ── */
async function initHome() {
  const [products, site] = await Promise.all([getProducts(), getSiteData()]);

  const heroBg  = document.getElementById('heroBg');
  const heroTex = document.getElementById('heroTex');
  if (heroBg  && site.hero?.bg)  heroBg.style.backgroundImage  = `url(${site.hero.bg})`;
  if (heroTex && site.hero?.tex) heroTex.style.backgroundImage = `url(${site.hero.tex})`;

  const catGrid = document.getElementById('catGrid');
  if (catGrid && site.categories) {
    catGrid.innerHTML = site.categories.map(cat => `
      <a class="cat-card" href="category.html?c=${encodeURIComponent(cat.name)}">
        <img class="cat-img" src="${escHtml(cat.img)}" alt="${escHtml(cat.name)}" loading="lazy"
             onerror="this.style.display='none'">
        <div class="cat-body">
          <div class="cat-name">${escHtml(cat.name)}</div>
          <div class="cat-desc">${escHtml(cat.desc)}</div>
        </div>
      </a>
    `).join('');
  }

  const featuredGrid = document.getElementById('featuredGrid');
  const featured = products.slice(0, 8);
  renderProductGrid(featuredGrid, featured);
  wireGridCart(featuredGrid, products);
}

/* ── search ── */
async function initSearch() {
  const params = new URLSearchParams(window.location.search);
  const q = params.get('q') || '';
  const qLabel = document.getElementById('qLabel');
  if (qLabel) qLabel.textContent = q;

  const products = await getProducts();
  let results = q
    ? products.filter(p =>
        p.name.toLowerCase().includes(q.toLowerCase()) ||
        (p.brand || '').toLowerCase().includes(q.toLowerCase()) ||
        (p.category || '').toLowerCase().includes(q.toLowerCase()) ||
        (p.subcategory || '').toLowerCase().includes(q.toLowerCase())
      )
    : [...products];

  const sSort = document.getElementById('sSort');
  const searchGrid = document.getElementById('searchGrid');
  const catChips = document.getElementById('catChips');

  let activeCategory = '';

  function getFiltered() {
    let arr = activeCategory
      ? results.filter(p => p.category === activeCategory)
      : results;
    return sortProducts(arr, sSort?.value || 'popular');
  }

  function render() {
    renderProductGrid(searchGrid, getFiltered());
    wireGridCart(searchGrid, products);
  }

  if (catChips) {
    const cats = [...new Set(results.map(p => p.category).filter(Boolean))];
    catChips.innerHTML = [
      `<button class="chip active" data-cat="">Kaikki (${results.length})</button>`,
      ...cats.map(c => `<button class="chip" data-cat="${escHtml(c)}">${escHtml(c)}</button>`)
    ].join('');

    catChips.addEventListener('click', e => {
      const chip = e.target.closest('[data-cat]');
      if (!chip) return;
      activeCategory = chip.dataset.cat;
      catChips.querySelectorAll('.chip').forEach(ch => {
        ch.classList.toggle('active', ch.dataset.cat === activeCategory);
      });
      render();
    });
  }

  sSort?.addEventListener('change', render);
  render();
}

/* ── campaigns ── */
async function initCampaigns() {
  const products = await getProducts();

  const saleGrid = document.getElementById('saleGrid');
  const newGrid  = document.getElementById('newGrid');
  const usedGrid = document.getElementById('usedGrid');

  const sale = products.filter(p => p.badge === 'Tarjous' || p.old_price);
  const newp = products.filter(p => p.badge === 'Uutuus');
  const used = products.filter(p => p.badge === 'Käytetty');

  renderProductGrid(saleGrid, sale);
  renderProductGrid(newGrid,  newp);
  renderProductGrid(usedGrid, used);

  [saleGrid, newGrid, usedGrid].forEach(g => wireGridCart(g, products));
}

/* ── product ── */
async function initProduct() {
  const params = new URLSearchParams(window.location.search);
  const id = parseInt(params.get('id'), 10);
  const products = await getProducts();
  const p = products.find(pr => pr.id === id);

  if (!p) {
    const main = document.getElementById('main');
    if (main) main.innerHTML = '<div class="notice" style="margin-top:20px"><strong>Tuotetta ei löydy.</strong> <a href="index.html">Palaa etusivulle</a></div>';
    return;
  }

  // Breadcrumb
  const bc = document.getElementById('pBreadcrumb');
  if (bc) {
    bc.innerHTML = [
      `<a href="index.html">Etusivu</a>`,
      `<span class="breadcrumb-sep">›</span>`,
      `<a href="category.html?c=${encodeURIComponent(p.category)}">${escHtml(p.category)}</a>`,
      `<span class="breadcrumb-sep">›</span>`,
      `<span>${escHtml(p.name)}</span>`,
    ].join('');
  }

  // Meta fields
  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  setEl('pName',  p.name);
  setEl('pSub',   `${p.brand} • ${p.subcategory || p.category}`);
  setEl('pBadge', p.badge || p.category);
  setEl('pMeta',  `${p.brand} • ${p.category}`);
  setEl('pPrice', fmtPrice(p.price));
  setEl('pOld',   p.old_price ? fmtPrice(p.old_price) : '');
  setEl('pStock', p.stock > 0 ? `${p.stock} kpl` : 'Loppunut');
  setEl('pSku',   p.sku || '—');

  document.title = `AseKauppa — ${p.name}`;

  // Gallery
  const images = (p.images && p.images.length) ? p.images : [''];
  const mainImg  = document.getElementById('mainImg');
  const thumbsEl = document.getElementById('thumbs');

  const PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect fill='%231e2d40' width='400' height='300'/%3E%3Ctext x='200' y='160' text-anchor='middle' fill='%238baac0' font-size='60'%3E%F0%9F%94%AB%3C/text%3E%3C/svg%3E";

  if (mainImg) {
    mainImg.src = images[0];
    mainImg.alt = p.name;
    mainImg.onerror = () => { mainImg.src = PLACEHOLDER; };
  }

  if (thumbsEl && images.length > 0) {
    thumbsEl.innerHTML = images.map((src, i) => `
      <img src="${escHtml(src)}" alt="${escHtml(p.name)} kuva ${i + 1}" loading="lazy"
           class="${i === 0 ? 'active' : ''}"
           onerror="this.style.display='none'">
    `).join('');

    thumbsEl.querySelectorAll('img').forEach(thumb => {
      thumb.addEventListener('click', () => {
        if (mainImg) {
          mainImg.src = thumb.src;
          mainImg.onerror = () => { mainImg.src = PLACEHOLDER; };
        }
        thumbsEl.querySelectorAll('img').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });
  }

  // Tabs
  const tabDesc     = document.getElementById('tab-desc');
  const tabSpecs    = document.getElementById('tab-specs');
  const tabDelivery = document.getElementById('tab-delivery');
  const tabReviews  = document.getElementById('tab-reviews');

  if (tabDesc) tabDesc.innerHTML = `<p>${escHtml(p.description || 'Ei kuvausta.')}</p>`;

  if (tabSpecs) {
    const specs = p.specs || {};
    const rows = Object.entries(specs).map(([k, v]) =>
      `<tr><td>${escHtml(k)}</td><td>${escHtml(String(v))}</td></tr>`
    ).join('');
    tabSpecs.innerHTML = rows
      ? `<table class="specs-table">${rows}</table>`
      : '<p class="small">Ei teknisiä tietoja.</p>';
  }

  if (tabDelivery) {
    tabDelivery.innerHTML = `
      <div class="notice good"><strong>Toimitus 1–3 arkipäivää</strong>
        <div class="small" style="margin-top:6px">Luvanvaraisissa tuotteissa toimitus ja luovutus aina lain ja lupien mukaan.</div>
      </div>
      <div class="hr"></div>
      <ul style="padding-left:18px;font-size:14px;color:var(--text)">
        <li>Nouto myymälästä — ilmainen</li>
        <li>Posti / Kotijakelu — 9,90 €</li>
        <li>Matkahuolto — 7,90 €</li>
        <li>Express 24h — 14,90 €</li>
      </ul>`;
  }

  if (tabReviews) {
    const rating = p.rating || 4.0;
    const reviews = p.reviews || 0;
    tabReviews.innerHTML = `
      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <span style="font-size:28px;font-weight:900;color:var(--heading)">${rating.toFixed(1)}</span>
        <div>
          <div style="font-size:18px">★★★★★</div>
          <div class="small">${reviews} arvio${reviews !== 1 ? 'ta' : ''}</div>
        </div>
      </div>
      <div class="hr"></div>
      <p class="small">Asiakasarvioita ei tässä esittelyversiossa näytetä.</p>`;
  }

  const tabbar = document.getElementById('tabbar');
  if (tabbar) {
    const allContent = [tabDesc, tabSpecs, tabDelivery, tabReviews].filter(Boolean);
    tabbar.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', () => {
        tabbar.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        allContent.forEach(tc => tc.classList.remove('active'));
        const target = document.getElementById(`tab-${btn.dataset.tab}`);
        target?.classList.add('active');
      });
    });
  }

  // Qty controls
  let qty = 1;
  const qtyVal   = document.getElementById('qtyVal');
  const qtyMinus = document.getElementById('qtyMinus');
  const qtyPlus  = document.getElementById('qtyPlus');

  qtyMinus?.addEventListener('click', () => {
    qty = Math.max(1, qty - 1);
    if (qtyVal) qtyVal.textContent = qty;
  });

  qtyPlus?.addEventListener('click', () => {
    qty = Math.min(p.stock || 99, qty + 1);
    if (qtyVal) qtyVal.textContent = qty;
  });

  const addBtn = document.getElementById('addBtn');
  addBtn?.addEventListener('click', () => addToCart(p, qty));

  // Related products
  const relatedGrid = document.getElementById('relatedGrid');
  if (relatedGrid) {
    const related = products
      .filter(pr => pr.category === p.category && pr.id !== p.id)
      .slice(0, 4);
    renderProductGrid(relatedGrid, related);
    wireGridCart(relatedGrid, products);
  }
}

/* ── cart ── */
async function initCart() {
  function renderCartPage() {
    const cart      = getCart();
    const cartList  = document.getElementById('cartList');
    const sumTotal  = document.getElementById('sumTotal');

    if (!cartList) return;

    if (cart.length === 0) {
      cartList.innerHTML = `
        <div class="notice" style="margin-top:10px">
          <strong>Ostoskori on tyhjä.</strong>
          <div class="small" style="margin-top:6px">
            <a href="index.html">Palaa etusivulle</a> ja lisää tuotteita koriin.
          </div>
        </div>`;
      if (sumTotal) sumTotal.textContent = fmtPrice(0);
      return;
    }

    const PLACEHOLDER = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 72 72'%3E%3Crect fill='%231e2d40' width='72' height='72'/%3E%3Ctext x='36' y='44' text-anchor='middle' fill='%238baac0' font-size='28'%3E%F0%9F%94%AB%3C/text%3E%3C/svg%3E";

    cartList.innerHTML = `<div class="cart-page-list">${cart.map(item => `
      <div class="cart-page-item" data-id="${item.id}">
        <img src="${escHtml(item.img)}" alt="${escHtml(item.name)}"
             onerror="this.src='${PLACEHOLDER}'">
        <div class="cpi-info">
          <div class="cpi-name">${escHtml(item.name)}</div>
          <div class="cpi-sub">${fmtPrice(item.price)} / kpl</div>
        </div>
        <div class="cpi-right">
          <div class="cpi-price">${fmtPrice(item.price * item.qty)}</div>
          <div class="qty">
            <button type="button" data-action="dec" data-id="${item.id}">−</button>
            <span>${item.qty}</span>
            <button type="button" data-action="inc" data-id="${item.id}">+</button>
          </div>
          <button class="btn small danger" type="button"
                  data-action="rm" data-id="${item.id}">Poista</button>
        </div>
      </div>
    `).join('')}</div>`;

    if (sumTotal) sumTotal.textContent = fmtPrice(cartTotal(cart));
  }

  renderCartPage();

  document.getElementById('cartList')?.addEventListener('click', async e => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    const id = parseInt(btn.dataset.id, 10);
    if (btn.dataset.action === 'rm') {
      removeFromCart(id);
    } else {
      const cart = getCart();
      const item = cart.find(i => i.id === id);
      if (!item) return;
      const delta = btn.dataset.action === 'inc' ? 1 : -1;
      updateCartQty(id, item.qty + delta);
    }
    renderCartPage();
  });

  document.getElementById('clearCartBtn')?.addEventListener('click', () => {
    clearCart();
    renderCartPage();
  });
}

/* ── checkout ── */
async function initCheckout() {
  const cart = getCart();
  const summaryEl = document.getElementById('checkoutSummary');

  if (summaryEl && cart.length > 0) {
    summaryEl.innerHTML = cart.map(i =>
      `<div style="display:flex;justify-content:space-between;padding-block:6px;border-bottom:1px solid var(--border);font-size:14px">
        <span>${escHtml(i.name)} × ${i.qty}</span>
        <strong>${fmtPrice(i.price * i.qty)}</strong>
      </div>`
    ).join('') +
    `<div style="display:flex;justify-content:space-between;padding-top:10px;font-weight:800;font-size:16px">
      <span>Yhteensä</span><span>${fmtPrice(cartTotal(cart))}</span>
    </div>`;
  }

  let step = 1;
  const totalSteps = 4;

  const stepPanels = [1, 2, 3, 4].map(n => document.getElementById(`step${n}`));
  const stepLabel  = document.getElementById('stepLabel');
  const stepTitle  = document.getElementById('stepTitle');
  const prevBtn    = document.getElementById('prevBtn');
  const nextBtn    = document.getElementById('nextBtn');
  const orderNo    = document.getElementById('orderNo');

  const STEP_TITLES = ['Osoitetiedot', 'Toimitus', 'Maksu', 'Vahvistus'];

  function showStep(n) {
    step = n;
    stepPanels.forEach((p, i) => {
      if (p) p.classList.toggle('hidden', i + 1 !== n);
    });
    if (stepLabel) stepLabel.textContent = n;
    if (stepTitle) stepTitle.textContent = STEP_TITLES[n - 1] || '';
    if (prevBtn) prevBtn.style.visibility = n === 1 ? 'hidden' : 'visible';
    if (nextBtn) nextBtn.textContent = n === totalSteps - 1 ? 'Vahvista tilaus' : n === totalSteps ? '' : 'Jatka →';
    if (n === totalSteps) nextBtn?.classList.add('hidden');
    else nextBtn?.classList.remove('hidden');
  }

  showStep(1);

  prevBtn?.addEventListener('click', () => { if (step > 1) showStep(step - 1); });

  nextBtn?.addEventListener('click', () => {
    if (step < totalSteps - 1) {
      showStep(step + 1);
    } else if (step === totalSteps - 1) {
      // Final step: create order
      const num = 'AK-' + Date.now().toString(36).toUpperCase();
      const addr = document.getElementById('cName')?.value || 'Asiakas';
      const ship = document.querySelector('[name="ship"]:checked')?.value || 'Nouto';
      const pay  = document.querySelector('[name="pay"]:checked')?.value || 'Kortti';

      const order = {
        id:      num,
        date:    new Date().toLocaleDateString('fi-FI'),
        items:   getCart(),
        total:   cartTotal(getCart()),
        address: addr,
        shipping: ship,
        payment:  pay,
      };

      const orders = lsGet(LS_ORDERS) || [];
      orders.unshift(order);
      lsSet(LS_ORDERS, orders);
      clearCart();

      if (orderNo) orderNo.textContent = num;
      showStep(totalSteps);
    }
  });
}

/* ── account ── */
async function initAccount() {
  const user    = lsGet(LS_USER);
  const orders  = lsGet(LS_ORDERS) || [];
  const forms   = lsGet(LS_FORMS)  || [];

  const accHello  = document.getElementById('accHello');
  const ordersBox = document.getElementById('ordersBox');
  const formsBox  = document.getElementById('formsBox');

  if (accHello) {
    accHello.textContent = user?.name
      ? `Tervetuloa, ${user.name}!`
      : 'Et ole kirjautunut sisään.';
  }

  if (ordersBox) {
    if (orders.length === 0) {
      ordersBox.innerHTML = '<p class="small">Ei tilauksia.</p>';
    } else {
      ordersBox.innerHTML = `<div class="orders-list">${orders.map(o => `
        <div class="order-card">
          <div>
            <div class="oc-num">${escHtml(o.id)}</div>
            <div class="oc-info">${escHtml(o.date)} • ${fmtPrice(o.total || 0)}</div>
          </div>
          <span class="pill"><strong>Käsittelyssä</strong></span>
        </div>`).join('')}</div>`;
    }
  }

  if (formsBox) {
    if (forms.length === 0) {
      formsBox.innerHTML = '<p class="small">Ei ilmoituksia.</p>';
    } else {
      formsBox.innerHTML = forms.map(f => `
        <div class="notice" style="margin-bottom:8px">
          <strong>${escHtml(f.type || 'Ilmoitus')}</strong>: ${escHtml(f.brand || '')} ${escHtml(f.model || '')}
          <div class="small" style="margin-top:4px">${escHtml(f.date || '')}</div>
        </div>`).join('');
    }
  }

  document.getElementById('logoutBtn')?.addEventListener('click', () => {
    lsSet(LS_USER, null);
    window.location.href = 'login.html';
  });
}

/* ── category ── */
async function initCategory() {
  const params = new URLSearchParams(window.location.search);
  const catName = params.get('c') || '';

  const [products, site] = await Promise.all([getProducts(), getSiteData()]);

  const catInfo   = site.categories?.find(c => c.name === catName) || { name: catName, desc: '' };
  const catHeader = document.getElementById('catHeader');
  const catTitle  = document.getElementById('catTitle');
  const catDesc   = document.getElementById('catDesc');

  if (catTitle) catTitle.textContent = catInfo.name || 'Osasto';
  if (catDesc)  catDesc.textContent  = catInfo.desc || '';

  if (catHeader && catInfo.img) {
    catHeader.innerHTML = `
      <div class="hero-card" style="min-height:120px;margin-bottom:14px">
        <div class="hero-bg" style="background-image:url(${escHtml(catInfo.img)})"></div>
        <div class="hero-content" style="padding:22px 28px">
          <h1 class="h1">${escHtml(catInfo.name)}</h1>
          <p class="lead">${escHtml(catInfo.desc)}</p>
        </div>
      </div>`;
  }

  const baseProducts = catName
    ? products.filter(p => p.category === catName)
    : [...products];

  // Subcategory chips
  const subChips  = document.getElementById('subChips');
  const subcats   = [...new Set(baseProducts.map(p => p.subcategory).filter(Boolean))];
  let activeSub   = '';

  if (subChips) {
    subChips.innerHTML = subcats.map(s =>
      `<button class="chip" data-sub="${escHtml(s)}">${escHtml(s)}</button>`
    ).join('');

    subChips.addEventListener('click', e => {
      const chip = e.target.closest('[data-sub]');
      if (!chip) return;
      activeSub = chip.dataset.sub === activeSub ? '' : chip.dataset.sub;
      subChips.querySelectorAll('.chip').forEach(c => {
        c.classList.toggle('active', c.dataset.sub === activeSub);
      });
      applyFilters();
    });
  }

  // Brand checkboxes
  const brandChecks = document.getElementById('brandChecks');
  const brands = [...new Set(baseProducts.map(p => p.brand).filter(Boolean))].sort();

  if (brandChecks) {
    brandChecks.innerHTML = brands.map(b => `
      <label class="chk">
        <input type="checkbox" value="${escHtml(b)}"> ${escHtml(b)}
      </label>`).join('');
  }

  const productGrid  = document.getElementById('productGrid');
  const resultCount  = document.getElementById('resultCount');
  const fQ           = document.getElementById('fQ');
  const fMin         = document.getElementById('fMin');
  const fMax         = document.getElementById('fMax');
  const fSort        = document.getElementById('fSort');
  const badgeChecks  = document.getElementById('badgeChecks');

  function applyFilters() {
    const q      = (fQ?.value || '').toLowerCase();
    const min    = parseFloat(fMin?.value) || 0;
    const max    = parseFloat(fMax?.value) || Infinity;
    const sort   = fSort?.value || 'popular';
    const selBrands = brandChecks
      ? [...brandChecks.querySelectorAll('input:checked')].map(i => i.value)
      : [];
    const selBadges = badgeChecks
      ? [...badgeChecks.querySelectorAll('input:checked')].map(i => i.value)
      : [];

    let filtered = baseProducts.filter(p => {
      if (activeSub && p.subcategory !== activeSub) return false;
      if (q && !(p.name.toLowerCase().includes(q) || (p.brand || '').toLowerCase().includes(q))) return false;
      if (p.price < min || p.price > max) return false;
      if (selBrands.length && !selBrands.includes(p.brand)) return false;
      if (selBadges.length && !selBadges.includes(p.badge)) return false;
      return true;
    });

    filtered = sortProducts(filtered, sort);

    renderProductGrid(productGrid, filtered);
    wireGridCart(productGrid, products);
    if (resultCount) resultCount.textContent = filtered.length;
  }

  document.getElementById('applyFilters')?.addEventListener('click', applyFilters);
  document.getElementById('clearFilters')?.addEventListener('click', () => {
    if (fQ)    fQ.value = '';
    if (fMin)  fMin.value = '';
    if (fMax)  fMax.value = '';
    if (fSort) fSort.value = 'popular';
    activeSub = '';
    if (brandChecks) brandChecks.querySelectorAll('input').forEach(i => { i.checked = false; });
    if (badgeChecks) badgeChecks.querySelectorAll('input').forEach(i => { i.checked = false; });
    if (subChips)    subChips.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    applyFilters();
  });

  applyFilters();
}

/* ── sell ── */
async function initSell() {
  document.getElementById('sellSend')?.addEventListener('click', () => {
    const type  = document.getElementById('sType')?.value  || '';
    const brand = document.getElementById('sBrand')?.value || '';
    const model = document.getElementById('sModel')?.value || '';
    const cal   = document.getElementById('sCal')?.value   || '';
    const cond  = document.getElementById('sCond')?.value  || '';
    const ask   = document.getElementById('sAsk')?.value   || '';
    const notes = document.getElementById('sNotes')?.value || '';

    if (!brand.trim() || !model.trim()) {
      showToast('Täytä vähintään merkki ja malli.');
      return;
    }

    const form = { type: 'Arviopyyntö', brand, model, cal, cond, ask, notes,
                   date: new Date().toLocaleDateString('fi-FI') };
    const forms = lsGet(LS_FORMS) || [];
    forms.unshift(form);
    lsSet(LS_FORMS, forms);

    const result = document.getElementById('sellResult');
    if (result) {
      result.innerHTML = `<div class="notice good"><strong>Arviopyyntö vastaanotettu!</strong>
        <div class="small" style="margin-top:6px">Pyyntösi (${escHtml(brand)} ${escHtml(model)}) on tallennettu selaimeen. Voit seurata sitä <a href="account.html">omalta tililtä</a>.</div>
      </div>`;
    }
  });
}

/* ── list ── */
async function initList() {
  document.getElementById('listSend')?.addEventListener('click', () => {
    const cat    = document.getElementById('lCat')?.value   || '';
    const brand  = document.getElementById('lBrand')?.value || '';
    const model  = document.getElementById('lModel')?.value || '';
    const price  = document.getElementById('lPrice')?.value || '';
    const desc   = document.getElementById('lDesc')?.value  || '';
    const name   = document.getElementById('lName')?.value  || '';
    const email  = document.getElementById('lEmail')?.value || '';

    if (!brand.trim() || !model.trim()) {
      showToast('Täytä vähintään merkki ja malli.');
      return;
    }

    const form = { type: 'Myynti-ilmoitus', brand, model, cat, price, desc, name, email,
                   date: new Date().toLocaleDateString('fi-FI') };
    const forms = lsGet(LS_FORMS) || [];
    forms.unshift(form);
    lsSet(LS_FORMS, forms);

    const result = document.getElementById('listResult');
    if (result) {
      result.innerHTML = `<div class="notice good"><strong>Ilmoitus vastaanotettu!</strong>
        <div class="small" style="margin-top:6px">Ilmoituksesi (${escHtml(brand)} ${escHtml(model)}) on tallennettu selaimeen. Näet sen <a href="account.html">omalla tilillä</a>.</div>
      </div>`;
    }
  });
}

/* ── login ── */
async function initLogin() {
  document.getElementById('loginBtn')?.addEventListener('click', () => {
    const email = document.getElementById('lEmail')?.value?.trim() || '';
    const pass  = document.getElementById('lPass')?.value?.trim() || '';

    if (!email) {
      showToast('Syötä sähköposti.');
      return;
    }
    if (!pass) {
      showToast('Syötä salasana.');
      return;
    }

    const name = email.split('@')[0].replace(/[^a-zA-Z0-9äöåÄÖÅ]/g, ' ').trim() || email;
    lsSet(LS_USER, { name, email });
    window.location.href = 'account.html';
  });
}

/* ════════════════════════════════════════════
   BOOTSTRAP
   ════════════════════════════════════════════ */

async function main() {
  initTheme();

  // Load partials, then wire header (products for suggest loaded in parallel)
  const productsPromise = getProducts();
  await loadPartials();
  const products = await productsPromise;

  wireHeader(products);
  renderCartDrawer();

  const page = document.body.dataset.page || '';

  switch (page) {
    case 'home':      await initHome();     break;
    case 'search':    await initSearch();   break;
    case 'campaigns': await initCampaigns();break;
    case 'product':   await initProduct();  break;
    case 'cart':      await initCart();     break;
    case 'checkout':  await initCheckout(); break;
    case 'account':   await initAccount();  break;
    case 'category':  await initCategory(); break;
    case 'sell':      await initSell();     break;
    case 'list':      await initList();     break;
    case 'login':     await initLogin();    break;
    default: break;
  }
}

main().catch(console.error);
