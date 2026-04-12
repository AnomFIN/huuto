#!/usr/bin/env node

// Simple HTML parsing test for cookie elements
const fs = require('fs');
const { execSync } = require('child_process');

try {
  // Hae HTML sivu
  const html = execSync('curl -s http://localhost:8080/', { encoding: 'utf8' });
  
  // Surasser HTML:nt yksinkertaisella regex haulla
  const cookieConsentMatches = html.match(/id="cookieConsent"/g) || [];
  const premiumCookieMatches = html.match(/class="[^"]*premium-cookie-consent[^"]*"/g) || [];
  // Fix: Match only exact cookie-consent class (excluding premium-cookie-consent)
  const allCookieConsentMatches = html.match(/class="[^"]*cookie-consent[^"]*"/g) || [];
  const cookieConsentClassMatches = allCookieConsentMatches.filter(match => !match.includes('premium-cookie-consent'));
  
  console.log('🍪 Cookie HTML Test Results:');
  console.log(`- cookieConsent ID elements: ${cookieConsentMatches.length}`);
  console.log(`- premium-cookie-consent classes: ${premiumCookieMatches.length}`);
  console.log(`- cookie-consent classes: ${cookieConsentClassMatches.length}`);
  
  if (cookieConsentMatches.length === 1 && premiumCookieMatches.length >= 1 && cookieConsentClassMatches.length === 0) {
    console.log('✅ Cookie HTML structure looks correct!');
  } else {
    console.log('❌ Cookie HTML structure has issues:');
    if (cookieConsentMatches.length !== 1) {
      console.log(`  - Expected 1 cookieConsent ID, found ${cookieConsentMatches.length}`);
    }
    if (premiumCookieMatches.length < 1) {
      console.log(`  - Expected at least 1 premium-cookie-consent class, found ${premiumCookieMatches.length}`);
    }
    if (cookieConsentClassMatches.length > 0) {
      console.log(`  - Found ${cookieConsentClassMatches.length} legacy cookie-consent classes (should be 0)`);
    }
  }
  
} catch (error) {
  console.error('❌ Test failed:', error.message);
  process.exit(1);
}