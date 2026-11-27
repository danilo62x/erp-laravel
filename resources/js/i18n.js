// ─── i18n — mirrors erp/react src/i18n + lib/format ─────────────────────────
// The same pt-BR/en/es bundles, with i18next-style interpolation ({{x}}) and
// _other pluralization, exposed both as a vanilla helper (data-i18n attributes)
// and as an Alpine magic `$t()` so Blade templates can localize reactively.
import ptBR from './locales/pt-BR.json';
import en from './locales/en.json';
import es from './locales/es.json';

export const LANGUAGES = [
  { code: 'pt-BR', label: 'Português', flag: '🇧🇷' },
  { code: 'en', label: 'English', flag: '🇺🇸' },
  { code: 'es', label: 'Español', flag: '🇪🇸' },
];

const RESOURCES = { 'pt-BR': ptBR, en, es };
const FALLBACK = 'pt-BR';

function detect() {
  try {
    const stored = localStorage.getItem('lang');
    if (stored && RESOURCES[stored]) return stored;
    const nav = (navigator.language || '').toLowerCase();
    if (nav.startsWith('pt')) return 'pt-BR';
    if (nav.startsWith('es')) return 'es';
    if (nav.startsWith('en')) return 'en';
  } catch (_) {}
  return FALLBACK;
}

function lookup(dict, key) {
  return key.split('.').reduce((o, k) => (o && o[k] != null ? o[k] : undefined), dict);
}

function interpolate(str, params) {
  return str.replace(/\{\{\s*(\w+)\s*\}\}/g, (_, name) =>
    params[name] != null ? String(params[name]) : ''
  );
}

class I18n {
  constructor() {
    this.lang = detect();
    this.dict = RESOURCES[this.lang] || RESOURCES[FALLBACK];
    this.fallbackDict = RESOURCES[FALLBACK];
  }

  t(key, params = {}) {
    let realKey = key;
    if (params && typeof params.count === 'number' && params.count !== 1) {
      if (lookup(this.dict, `${key}_other`) != null) realKey = `${key}_other`;
    }
    let val = lookup(this.dict, realKey);
    if (val == null) val = lookup(this.fallbackDict, realKey);
    if (typeof val !== 'string') return key;
    return interpolate(val, params);
  }

  tArray(key) {
    let val = lookup(this.dict, key);
    if (val == null) val = lookup(this.fallbackDict, key);
    return Array.isArray(val) ? val : [];
  }

  setLang(lang) {
    if (!RESOURCES[lang]) return;
    this.lang = lang;
    this.dict = RESOURCES[lang];
    try {
      localStorage.setItem('lang', lang);
    } catch (_) {}
    document.documentElement.lang = lang;
    this.applyDom(document);
    document.dispatchEvent(new CustomEvent('i18n:changed', { detail: { lang } }));
  }

  applyDom(root) {
    (root || document).querySelectorAll('[data-i18n]').forEach((el) => {
      const v = this.t(el.getAttribute('data-i18n'));
      if (typeof v === 'string') el.textContent = v;
    });
    (root || document).querySelectorAll('[data-i18n-ph]').forEach((el) => {
      const v = this.t(el.getAttribute('data-i18n-ph'));
      if (typeof v === 'string') el.setAttribute('placeholder', v);
    });
  }
}

const i18n = new I18n();
window.I18n = i18n;
document.documentElement.lang = i18n.lang;

export default i18n;
