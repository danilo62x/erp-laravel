import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import i18n, { LANGUAGES } from './i18n.js';
import './pwa.js';

window.Alpine = Alpine;
Alpine.plugin(collapse);

// ─── localStorage helpers (mirror zustand persist) ──────────────────────────
function load(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    if (!raw) return fallback;
    const parsed = JSON.parse(raw);
    return parsed && parsed.state ? parsed.state : parsed;
  } catch (_) {
    return fallback;
  }
}
function save(key, state) {
  try {
    localStorage.setItem(key, JSON.stringify({ state, version: 0 }));
  } catch (_) {}
}

const ER = window.ER || {};
const CATEGORY_BY_ID = Object.fromEntries((ER.categories || []).map((c) => [c.id, c]));

// ─── Formatting — ported from erp/react src/lib/format.ts ────────────────────
// BUSINESS finance ERP: all figures are BRL regardless of UI language (only the
// locale separators change).
const LOCALE_MAP = { 'pt-BR': 'pt-BR', en: 'en-US', es: 'es-ES' };

function formatCurrency(value, lang = 'pt-BR') {
  const locale = LOCALE_MAP[lang] || 'pt-BR';
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
}
function formatCompactCurrency(value, lang = 'pt-BR') {
  const locale = LOCALE_MAP[lang] || 'pt-BR';
  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: 'BRL',
    notation: 'compact',
    maximumFractionDigits: 1,
  }).format(value);
}
function formatSigned(amount, type, lang = 'pt-BR') {
  const sign = type === 'receita' ? '+ ' : '− ';
  return sign + formatCurrency(Math.abs(amount), lang);
}
function formatNet(value, lang = 'pt-BR') {
  const sign = value > 0 ? '+ ' : value < 0 ? '− ' : '';
  return sign + formatCurrency(Math.abs(value), lang);
}
function formatPercent(value, lang = 'pt-BR') {
  const locale = LOCALE_MAP[lang] || 'pt-BR';
  const sign = value > 0 ? '+' : value < 0 ? '−' : '';
  const n = new Intl.NumberFormat(locale, { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(Math.abs(value));
  return `${sign}${n}%`;
}
function amountColor(type) {
  return type === 'receita'
    ? 'text-success-600 dark:text-success-500'
    : 'text-error-600 dark:text-error-500';
}
function deltaColor(value) {
  if (value > 0) return 'text-success-600 dark:text-success-500';
  if (value < 0) return 'text-error-600 dark:text-error-500';
  return 'text-muted';
}
function formatDate(iso, lang = 'pt-BR') {
  const locale = LOCALE_MAP[lang] || 'pt-BR';
  return new Intl.DateTimeFormat(locale, { dateStyle: 'medium' }).format(new Date(iso));
}
function formatMonth(iso, lang = 'pt-BR') {
  const locale = LOCALE_MAP[lang] || 'pt-BR';
  return new Intl.DateTimeFormat(locale, { month: 'short', year: '2-digit' }).format(new Date(iso));
}
function formatMonthShort(iso, lang = 'pt-BR') {
  const locale = LOCALE_MAP[lang] || 'pt-BR';
  return new Intl.DateTimeFormat(locale, { month: 'short' }).format(new Date(iso));
}
function daysUntil(iso) {
  const ms = new Date(iso).getTime() - Date.now();
  return Math.round(ms / 86400000);
}

// ─── i18n magics ─────────────────────────────────────────────────────────────
const touchLang = () => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
};
Alpine.magic('t', () => (key, params) => {
  touchLang();
  return i18n.t(key, params);
});
Alpine.magic('tArray', () => (key) => {
  touchLang();
  return i18n.tArray(key);
});
Alpine.magic('lang', () => {
  touchLang();
  return i18n.lang;
});
Alpine.magic('currency', () => (value) => {
  touchLang();
  return formatCurrency(value, i18n.lang);
});
Alpine.magic('compactCurrency', () => (value) => {
  touchLang();
  return formatCompactCurrency(value, i18n.lang);
});
Alpine.magic('signed', () => (value, type) => {
  touchLang();
  return formatSigned(value, type, i18n.lang);
});
Alpine.magic('net', () => (value) => {
  touchLang();
  return formatNet(value, i18n.lang);
});
Alpine.magic('percent', () => (value) => {
  touchLang();
  return formatPercent(value, i18n.lang);
});
Alpine.magic('date', () => (iso) => {
  touchLang();
  return formatDate(iso, i18n.lang);
});
Alpine.magic('month', () => (iso) => {
  touchLang();
  return formatMonth(iso, i18n.lang);
});

// ─── UI store — key: er-ui (theme, lang, sidebar, toasts, cookies) ───────────
let toastId = 0;
const savedUi = load('er-ui', {});
Alpine.store('ui', {
  darkMode: savedUi.darkMode ?? false,
  sidebarCollapsed: savedUi.sidebarCollapsed ?? false,
  sidebarMobileOpen: false,
  toasts: [],
  cookiesAccepted: savedUi.cookiesAccepted ?? false,
  showCookieBanner: false,
  lang: i18n.lang,

  init() {
    this.applyTheme();
    Alpine.effect(() => {
      this.darkMode;
      this.applyTheme();
      save('er-ui', {
        darkMode: this.darkMode,
        sidebarCollapsed: this.sidebarCollapsed,
        cookiesAccepted: this.cookiesAccepted,
      });
    });
    document.addEventListener('i18n:changed', (e) => {
      this.lang = e.detail.lang;
    });
    if (!this.cookiesAccepted) {
      setTimeout(() => this.openCookieBanner(), 800);
    }
  },
  applyTheme() {
    const root = document.documentElement;
    if (this.darkMode) root.classList.add('dark');
    else root.classList.remove('dark');
  },
  toggleDarkMode() {
    this.darkMode = !this.darkMode;
  },
  setDarkMode(v) {
    this.darkMode = v;
  },
  toggleSidebar() {
    this.sidebarCollapsed = !this.sidebarCollapsed;
  },
  toggleSidebarMobile(v) {
    this.sidebarMobileOpen = v !== undefined ? v : !this.sidebarMobileOpen;
  },
  setLang(lang) {
    i18n.setLang(lang);
    this.lang = lang;
  },
  get languages() {
    return LANGUAGES;
  },
  get currentLanguage() {
    return LANGUAGES.find((l) => l.code === this.lang) || LANGUAGES[0];
  },

  notify(message, type = 'info', duration = 4000) {
    const id = ++toastId;
    this.toasts.push({ id, message, type, duration });
    setTimeout(() => this.dismissToast(id), duration);
  },
  notifySuccess(m) {
    this.notify(m, 'success');
  },
  notifyError(m) {
    this.notify(m, 'error');
  },
  notifyWarning(m) {
    this.notify(m, 'warning');
  },
  dismissToast(id) {
    const i = this.toasts.findIndex((t) => t.id === id);
    if (i !== -1) this.toasts.splice(i, 1);
  },
  acceptCookies() {
    this.cookiesAccepted = true;
    this.showCookieBanner = false;
  },
  declineCookies() {
    this.cookiesAccepted = false;
    this.showCookieBanner = false;
  },
  openCookieBanner() {
    this.showCookieBanner = true;
  },
  closeCookieBanner() {
    this.showCookieBanner = false;
  },
});

// ─── Auth store — key: er-auth (demo financeiro@empresa.com / demo123) ───────
Alpine.store('auth', {
  user: load('er-auth', { user: null }).user ?? null,

  init() {
    if (ER.user && !this.user) {
      this.user = ER.user;
    }
    Alpine.effect(() => {
      this.user;
      save('er-auth', { user: this.user });
    });
  },
  get isAuthenticated() {
    return this.user !== null;
  },
  setUser(user) {
    this.user = user;
  },
  logout() {
    this.user = null;
  },
  updateProfile(patch) {
    if (!this.user) return;
    this.user = { ...this.user, ...patch };
  },
});

// ─── Query helpers — ported from erp/react src/data/query.ts ─────────────────
const EMPTY_ENTRY_FILTERS = {
  query: '',
  period: 'all',
  type: '',
  categoryId: '',
  accountId: '',
  costCenter: '',
  status: '',
};

function withinPeriod(iso, period) {
  if (period === 'all') return true;
  const days = period === '30d' ? 30 : period === '90d' ? 90 : 180;
  const cutoff = Date.now() - days * 86400000;
  return new Date(iso).getTime() >= cutoff;
}

/** Recompute status against today (so persisted data ages correctly). */
function liveStatus(e) {
  if (e.status === 'pago' || e.paidDate) return 'pago';
  return new Date(e.dueDate).getTime() < Date.now() ? 'vencido' : 'pendente';
}

function filterEntries(list, f) {
  const q = (f.query || '').trim().toLowerCase();
  return list.filter((e) => {
    if (q) {
      const cat = (CATEGORY_BY_ID[e.categoryId]?.name || '').toLowerCase();
      if (!(e.description.toLowerCase().includes(q) || cat.includes(q) || (e.invoiceNumber || '').includes(q))) return false;
    }
    if (!withinPeriod(e.dueDate, f.period)) return false;
    if (f.type && e.type !== f.type) return false;
    if (f.categoryId && e.categoryId !== f.categoryId) return false;
    if (f.accountId && e.accountId !== f.accountId) return false;
    if (f.costCenter && e.costCenter !== f.costCenter) return false;
    if (f.status && liveStatus(e) !== f.status) return false;
    return true;
  });
}

function sortByDueDesc(list) {
  return [...list].sort((a, b) => +new Date(b.dueDate) - +new Date(a.dueDate));
}

function paginate(list, page, pageSize) {
  const total = list.length;
  const pageCount = Math.max(1, Math.ceil(total / pageSize));
  const safePage = Math.min(Math.max(1, page), pageCount);
  const start = (safePage - 1) * pageSize;
  return { rows: list.slice(start, start + pageSize), total, page: safePage, pageCount };
}

function payable(list) {
  return list.filter((e) => e.type === 'despesa');
}
function receivable(list) {
  return list.filter((e) => e.type === 'receita');
}
function openTotal(list) {
  return list.filter((e) => liveStatus(e) !== 'pago').reduce((s, e) => s + e.amount, 0);
}
function overdueTotal(list) {
  return list.filter((e) => liveStatus(e) === 'vencido').reduce((s, e) => s + e.amount, 0);
}

function isSameMonth(iso) {
  const d = new Date(iso);
  const now = new Date();
  return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth();
}
function totalBalance(accounts) {
  return accounts.reduce((s, a) => s + a.balance, 0);
}

function computeMetrics(entries, accounts) {
  const ap = payable(entries);
  const ar = receivable(entries);
  let monthRevenue = 0;
  let monthExpense = 0;
  for (const e of entries) {
    if (!isSameMonth(e.dueDate)) continue;
    if (e.type === 'receita') monthRevenue += e.amount;
    else monthExpense += e.amount;
  }
  return {
    totalBalance: totalBalance(accounts),
    payableOpen: openTotal(ap),
    receivableOpen: openTotal(ar),
    overdue: overdueTotal(entries),
    monthRevenue,
    monthExpense,
    monthResult: monthRevenue - monthExpense,
  };
}

function monthKey(d) {
  return new Date(Date.UTC(d.getFullYear(), d.getMonth(), 1)).toISOString();
}

function cashFlowSeries(entries, accounts, back = 4, forward = 2) {
  const now = new Date();
  const buckets = [];
  const index = new Map();
  for (let offset = -back; offset <= forward; offset++) {
    const d = new Date(now.getFullYear(), now.getMonth() + offset, 1);
    const key = monthKey(d);
    index.set(key, buckets.length);
    buckets.push({ key, inflow: 0, outflow: 0 });
  }
  for (const e of entries) {
    const key = monthKey(new Date(e.dueDate));
    const idx = index.get(key);
    if (idx === undefined) continue;
    if (e.type === 'receita') buckets[idx].inflow += e.amount;
    else buckets[idx].outflow += e.amount;
  }
  const seed = totalBalance(accounts);
  let netFuture = 0;
  buckets.forEach((b) => {
    if (new Date(b.key) > now) netFuture += b.inflow - b.outflow;
  });
  let running = seed - netFuture;
  return buckets.map((b) => {
    running += b.inflow - b.outflow;
    return { month: b.key, inflow: b.inflow, outflow: b.outflow, balance: running };
  });
}

function revenueVsExpense(entries, back = 5) {
  const now = new Date();
  const buckets = [];
  const index = new Map();
  for (let offset = -back; offset <= 0; offset++) {
    const d = new Date(now.getFullYear(), now.getMonth() + offset, 1);
    const key = monthKey(d);
    index.set(key, buckets.length);
    buckets.push({ key, receita: 0, despesa: 0 });
  }
  for (const e of entries) {
    const idx = index.get(monthKey(new Date(e.dueDate)));
    if (idx === undefined) continue;
    if (e.type === 'receita') buckets[idx].receita += e.amount;
    else buckets[idx].despesa += e.amount;
  }
  return buckets.map((b) => ({ month: b.key, receita: b.receita, despesa: b.despesa }));
}

function expensesByCategory(entries) {
  const map = new Map();
  for (const e of entries) {
    if (e.type !== 'despesa') continue;
    if (!isSameMonth(e.dueDate)) continue;
    map.set(e.categoryId, (map.get(e.categoryId) ?? 0) + e.amount);
  }
  return [...map.entries()]
    .map(([categoryId, amount]) => ({ categoryId, name: CATEGORY_BY_ID[categoryId]?.name ?? categoryId, amount }))
    .sort((a, b) => b.amount - a.amount);
}

function dueSoon(entries, days = 30) {
  const now = Date.now();
  const limit = now + days * 86400000;
  return entries
    .filter((e) => {
      if (liveStatus(e) === 'pago') return false;
      return new Date(e.dueDate).getTime() <= limit;
    })
    .sort((a, b) => +new Date(a.dueDate) - +new Date(b.dueDate));
}

function dreSummary(entries, monthsBack = 0) {
  const now = new Date();
  const cutoff = new Date(now.getFullYear(), now.getMonth() - monthsBack, 1).getTime();
  const byGroup = new Map();
  const lines = [];
  for (const e of entries) {
    const d = new Date(e.dueDate);
    if (d.getTime() < cutoff) continue;
    if (d > new Date(now.getFullYear(), now.getMonth() + 1, 0)) continue;
    const cat = CATEGORY_BY_ID[e.categoryId];
    if (!cat) continue;
    byGroup.set(cat.group, (byGroup.get(cat.group) ?? 0) + e.amount);
  }
  const g = (k) => byGroup.get(k) ?? 0;
  for (const [group, amount] of byGroup.entries()) {
    const type = group.startsWith('receita') ? 'receita' : 'despesa';
    lines.push({ group, type, amount });
  }
  lines.sort((a, b) => b.amount - a.amount);
  const receitaBruta = g('receita_operacional');
  const deducoes = g('imposto');
  const receitaLiquida = receitaBruta - deducoes;
  const custos = g('custo');
  const lucroBruto = receitaLiquida - custos;
  const despesasOperacionais = g('despesa_operacional') + g('despesa_pessoal');
  const resultadoOperacional = lucroBruto - despesasOperacionais;
  const despesasFinanceiras = g('despesa_financeira');
  const receitasFinanceiras = g('receita_financeira');
  const resultadoLiquido = resultadoOperacional - despesasFinanceiras + receitasFinanceiras;
  return {
    receitaBruta,
    deducoes,
    receitaLiquida,
    custos,
    lucroBruto,
    despesasOperacionais,
    resultadoOperacional,
    despesasFinanceiras,
    receitasFinanceiras,
    resultadoLiquido,
    margemLiquida: receitaBruta === 0 ? 0 : (resultadoLiquido / receitaBruta) * 100,
    lines,
  };
}

function resultByCostCenter(entries) {
  const map = new Map();
  for (const e of entries) {
    const cur = map.get(e.costCenter) ?? { receita: 0, despesa: 0 };
    if (e.type === 'receita') cur.receita += e.amount;
    else cur.despesa += e.amount;
    map.set(e.costCenter, cur);
  }
  return [...map.entries()]
    .map(([costCenter, v]) => ({ costCenter, ...v, net: v.receita - v.despesa }))
    .sort((a, b) => Math.abs(b.despesa) - Math.abs(a.despesa));
}

function filterInvoices(list, f) {
  const q = (f.query || '').trim().toLowerCase();
  return list.filter((inv) => {
    if (q && !inv.number.toLowerCase().includes(q)) return false;
    if (f.type && inv.type !== f.type) return false;
    if (f.status && inv.status !== f.status) return false;
    return true;
  });
}

// Status pill config (mirrors components/fin/StatusPill.tsx).
const STATUS_PILL = {
  pago: { color: 'success', icon: 'check' },
  pendente: { color: 'warning', icon: 'clock' },
  vencido: { color: 'error', icon: 'alert-triangle' },
};

// ─── Fin store — key: er-fin ─────────────────────────────────────────────────
// Mirrors erp/react stores/fin.ts: entries/accounts/parties/invoices/categories
// + addEntry/updateEntry/removeEntry/markPaid/toggleReconciled/setFilters.
let seq = 0;
const nextId = () => `ent-new-${Date.now()}-${++seq}`;

const savedFin = load('er-fin', {});
Alpine.store('fin', {
  entries: savedFin.entries ?? (ER.entries || []),
  accounts: savedFin.accounts ?? (ER.accounts || []),
  parties: savedFin.parties ?? (ER.parties || []),
  invoices: savedFin.invoices ?? (ER.invoices || []),
  categories: ER.categories || [],
  filters: savedFin.filters ?? { ...EMPTY_ENTRY_FILTERS },

  init() {
    Alpine.effect(() => {
      this.entries.length;
      this.accounts.reduce((s, a) => s + a.balance, 0);
      JSON.stringify(this.filters);
      save('er-fin', {
        entries: this.entries,
        accounts: this.accounts,
        parties: this.parties,
        invoices: this.invoices,
        filters: this.filters,
      });
    });
  },

  getCategory(id) {
    return this.categories.find((c) => c.id === id);
  },
  getAccount(id) {
    return this.accounts.find((a) => a.id === id);
  },
  getParty(id) {
    return this.parties.find((p) => p.id === id);
  },
  partyName(id) {
    return this.getParty(id)?.name ?? '—';
  },

  addEntry(draft) {
    const due = new Date(draft.dueDate);
    const status = due.getTime() < Date.now() ? 'vencido' : 'pendente';
    const entry = {
      id: nextId(),
      description: draft.description,
      type: draft.type,
      categoryId: draft.categoryId,
      amount: Math.abs(Number(draft.amount)),
      dueDate: due.toISOString(),
      paidDate: null,
      status,
      accountId: draft.accountId,
      costCenter: draft.costCenter,
      partyId: draft.partyId,
      invoiceNumber: draft.invoiceNumber || null,
      reconciled: false,
    };
    this.entries.unshift(entry);
    return entry;
  },

  updateEntry(id, patch) {
    const i = this.entries.findIndex((e) => e.id === id);
    if (i === -1) return;
    const next = { ...this.entries[i], ...patch };
    if (patch.amount != null) next.amount = Math.abs(Number(patch.amount));
    this.entries.splice(i, 1, next);
  },

  removeEntry(id) {
    this.entries = this.entries.filter((e) => e.id !== id);
  },

  markPaid(id) {
    const entry = this.entries.find((e) => e.id === id);
    if (!entry || liveStatus(entry) === 'pago') return;
    const delta = entry.type === 'receita' ? entry.amount : -entry.amount;
    this.updateEntry(id, { status: 'pago', paidDate: new Date().toISOString() });
    const acc = this.getAccount(entry.accountId);
    if (acc) acc.balance += delta;
  },

  toggleReconciled(id) {
    const entry = this.entries.find((e) => e.id === id);
    if (entry) this.updateEntry(id, { reconciled: !entry.reconciled });
  },

  setFilters(patch) {
    this.filters = { ...this.filters, ...patch };
  },
  resetFilters() {
    this.filters = { ...EMPTY_ENTRY_FILTERS };
  },
});

// ─── Shared helpers exposed to x-data components ─────────────────────────────
window.erHelpers = {
  EMPTY_ENTRY_FILTERS,
  CATEGORY_BY_ID,
  liveStatus,
  filterEntries,
  sortByDueDesc,
  paginate,
  payable,
  receivable,
  openTotal,
  overdueTotal,
  computeMetrics,
  cashFlowSeries,
  revenueVsExpense,
  expensesByCategory,
  dueSoon,
  dreSummary,
  resultByCostCenter,
  filterInvoices,
  totalBalance,
  daysUntil,
  statusPill: (s) => STATUS_PILL[s] || STATUS_PILL.pendente,
  amountColor,
  deltaColor,
  formatCurrency: (v) => formatCurrency(v, i18n.lang),
  formatCompactCurrency: (v) => formatCompactCurrency(v, i18n.lang),
  formatSigned: (v, type) => formatSigned(v, type, i18n.lang),
  formatNet: (v) => formatNet(v, i18n.lang),
  formatPercent: (v) => formatPercent(v, i18n.lang),
  formatDate: (iso) => formatDate(iso, i18n.lang),
  formatMonth: (iso) => formatMonth(iso, i18n.lang),
  formatMonthShort: (iso) => formatMonthShort(iso, i18n.lang),
  categoryName: (id) => CATEGORY_BY_ID[id]?.name ?? id,
};

// ─── ApexCharts theme-aware factory (mirrors ui/ApexChart.tsx) ───────────────
// ApexCharts is loaded via CDN per chart page (@push('head')).
window.erChart = {
  base() {
    const dark = !!(Alpine.store('ui') && Alpine.store('ui').darkMode);
    const fg = dark ? '#98a2b3' : '#667085';
    const grid = dark ? '#1f2937' : '#e4e7ec';
    return {
      chart: { fontFamily: 'Outfit, sans-serif', foreColor: fg, toolbar: { show: false }, zoom: { enabled: false } },
      colors: ['#465fff', '#12b76a', '#f79009', '#f04438', '#0ba5ec'],
      grid: { borderColor: grid, strokeDashArray: 4 },
      tooltip: { theme: dark ? 'dark' : 'light' },
      legend: { labels: { colors: fg } },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
    };
  },
};

document.addEventListener('alpine:initialized', () => i18n.applyDom(document));

Alpine.start();
