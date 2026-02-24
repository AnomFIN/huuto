// AnomFIN — the neural network of innovation.
const STORAGE_KEYS = {
  campaignDismissedAt: 'asekauppa:campaignDismissedAt',
  cookieConsent: 'asekauppa:cookieConsent'
};

const CAMPAIGN_DEADLINE_ISO = '2026-03-31T20:59:59Z';
const CAMPAIGN_COOLDOWN_DAYS = 2;

function parseIsoDate(value) {
  if (typeof value !== 'string') {
    return null;
  }
  const parsed = new Date(value);
  return Number.isNaN(parsed.getTime()) ? null : parsed;
}

export function getPopupSettings() {
  return {
    storageKeys: STORAGE_KEYS,
    campaignDeadlineIso: CAMPAIGN_DEADLINE_ISO,
    campaignCooldownDays: CAMPAIGN_COOLDOWN_DAYS
  };
}

export function readSafeStorage(storage, key) {
  if (!storage || typeof storage.getItem !== 'function' || typeof key !== 'string') {
    return null;
  }
  try {
    return storage.getItem(key);
  } catch {
    return null;
  }
}

export function writeSafeStorage(storage, key, value) {
  if (!storage || typeof storage.setItem !== 'function' || typeof key !== 'string') {
    return false;
  }
  try {
    storage.setItem(key, value);
    return true;
  } catch {
    return false;
  }
}

export function shouldShowCampaignPopup({ now, dismissedAtIso, deadlineIso, cooldownDays }) {
  if (!(now instanceof Date) || Number.isNaN(now.getTime())) {
    return false;
  }

  const deadline = parseIsoDate(deadlineIso);
  if (!deadline || now > deadline) {
    return false;
  }

  const dismissedAt = parseIsoDate(dismissedAtIso);
  if (!dismissedAt) {
    return true;
  }

  const cooldownMs = cooldownDays * 24 * 60 * 60 * 1000;
  return now.getTime() - dismissedAt.getTime() >= cooldownMs;
}

export function isCookieConsentAccepted(value) {
  return value === 'accepted';
}
