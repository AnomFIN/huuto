import test from 'node:test';
import assert from 'node:assert/strict';
import {
  shouldShowCampaignPopup,
  isCookieConsentAccepted
} from '../popup-state.js';

test('campaign popup shown before deadline when not dismissed', () => {
  const result = shouldShowCampaignPopup({
    now: new Date('2026-03-20T10:00:00+02:00'),
    dismissedAtIso: null,
    deadlineIso: '2026-03-31T23:59:59+02:00',
    cooldownDays: 2
  });

  assert.equal(result, true);
});

test('campaign popup hidden during cooldown window', () => {
  const result = shouldShowCampaignPopup({
    now: new Date('2026-03-21T10:00:00+02:00'),
    dismissedAtIso: '2026-03-20T12:00:00+02:00',
    deadlineIso: '2026-03-31T23:59:59+02:00',
    cooldownDays: 2
  });

  assert.equal(result, false);
});

test('campaign popup hidden after deadline', () => {
  const result = shouldShowCampaignPopup({
    now: new Date('2026-04-01T00:00:00+02:00'),
    dismissedAtIso: null,
    deadlineIso: '2026-03-31T23:59:59+02:00',
    cooldownDays: 2
  });

  assert.equal(result, false);
});

test('cookie consent accepted only for strict accepted value', () => {
  assert.equal(isCookieConsentAccepted('accepted'), true);
  assert.equal(isCookieConsentAccepted('yes'), false);
  assert.equal(isCookieConsentAccepted(null), false);
});
