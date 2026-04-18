// Premium Auth Modal System - Modern 2025 UX
(() => {
  'use strict';

  // Configuration
  const CONFIG = {
    animationDuration: 300,
    debounceDelay: 300,
    minPasswordLength: 8,
    maxPasswordLength: 128
  };

  // State
  const state = {
    isOpen: false,
    currentMode: 'login', // 'login' or 'register'
    isLoading: false,
    validationErrors: new Map()
  };

  // DOM References
  const refs = {
    overlay: null,
    modal: null,
    closeBtn: null,
    title: null,
    subtitle: null,
    loginTab: null,
    registerTab: null,
    tabIndicator: null,
    loginForm: null,
    registerForm: null,
    loginTrigger: null,
    registerTrigger: null
  };

  // Validation patterns
  const patterns = {
    email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    name: /^[a-zA-ZäöåÄÖÅ\s]{2,50}$/,
    password: /^.{8,}$/
  };

  // Initialize the auth modal system
  function init() {
    setupDOMReferences();
    if (!validateDOMReferences()) return;
    
    setupEventListeners();
    setupValidation();
    console.log('🔐 Premium Auth Modal initialized');
  }

  function setupDOMReferences() {
    refs.overlay = document.getElementById('authModalOverlay');
    refs.modal = document.getElementById('authModal');
    refs.closeBtn = document.getElementById('authModalClose');
    refs.title = document.getElementById('authModalTitle');
    refs.subtitle = document.querySelector('.auth-modal-subtitle');
    refs.loginTab = document.getElementById('loginTab');
    refs.registerTab = document.getElementById('registerTab');
    refs.tabIndicator = document.getElementById('authTabIndicator');
    refs.loginForm = document.getElementById('loginForm');
    refs.registerForm = document.getElementById('registerForm');
    refs.loginTrigger = document.getElementById('loginTrigger');
    refs.registerTrigger = document.getElementById('registerTrigger');
  }

  function validateDOMReferences() {
    const requiredRefs = ['overlay', 'modal', 'closeBtn', 'loginForm', 'registerForm'];
    const missing = requiredRefs.filter(ref => !refs[ref]);
    
    if (missing.length > 0) {
      console.error('🔐 Missing required DOM elements:', missing);
      return false;
    }
    return true;
  }

  function setupEventListeners() {
    // Trigger buttons
    if (refs.loginTrigger) {
      refs.loginTrigger.addEventListener('click', () => openModal('login'));
    }
    if (refs.registerTrigger) {
      refs.registerTrigger.addEventListener('click', () => openModal('register'));
    }

    // Alternative triggers (for other pages)
    document.addEventListener('click', (e) => {
      if (e.target.matches('[data-auth-modal]')) {
        e.preventDefault();
        const mode = e.target.dataset.authModal;
        openModal(mode);
      }
    });

    // Close modal
    refs.closeBtn?.addEventListener('click', closeModal);
    refs.overlay?.addEventListener('click', (e) => {
      if (e.target === refs.overlay) closeModal();
    });
    
    // Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && state.isOpen) {
        closeModal();
      }
    });

    // Tab switching
    refs.loginTab?.addEventListener('click', () => switchMode('login'));
    refs.registerTab?.addEventListener('click', () => switchMode('register'));

    // Form switching buttons
    document.addEventListener('click', (e) => {
      if (e.target.matches('.switch-form')) {
        e.preventDefault();
        const mode = e.target.dataset.switch;
        switchMode(mode);
      }
    });

    // Password toggles
    document.addEventListener('click', (e) => {
      if (e.target.closest('.password-toggle')) {
        e.preventDefault();
        togglePasswordVisibility(e.target.closest('.password-toggle'));
      }
    });

    // Form submissions
    refs.loginForm?.addEventListener('submit', handleLoginSubmit);
    refs.registerForm?.addEventListener('submit', handleRegisterSubmit);
  }

  function setupValidation() {
    // Real-time validation for login form
    setupFieldValidation('loginEmail', validateEmail);
    setupFieldValidation('loginPassword', validateLoginPassword);

    // Real-time validation for register form
    setupFieldValidation('registerName', validateName);
    setupFieldValidation('registerEmail', validateEmail);
    setupFieldValidation('registerPassword', validateRegisterPassword);
    setupFieldValidation('confirmPassword', validatePasswordConfirmation);
    setupFieldValidation('acceptTerms', validateTermsAcceptance);
  }

  function setupFieldValidation(fieldId, validator) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    let debounceTimer;

    field.addEventListener('input', () => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        const result = validator(field.value, field);
        showFieldValidation(fieldId, result);
        updateFormSubmitState();
      }, CONFIG.debounceDelay);
    });

    field.addEventListener('blur', () => {
      const result = validator(field.value, field);
      showFieldValidation(fieldId, result);
      updateFormSubmitState();
    });
  }

  function openModal(mode = 'login') {
    state.isOpen = true;
    state.currentMode = mode;
    
    // Update modal content
    updateModalContent();
    
    // Show modal with animation
    refs.overlay.style.display = 'flex';
    refs.overlay?.setAttribute('aria-hidden', 'false');
    document.body.classList.add('auth-modal-open');
    
    // Focus management
    setTimeout(() => {
      const firstInput = refs.modal.querySelector('input:not([type="checkbox"])');
      firstInput?.focus();
    }, CONFIG.animationDuration);

    // Analytics
    logEvent('auth_modal_opened', { mode });
  }

  function closeModal() {
    if (!state.isOpen) return;

    state.isOpen = false;
    refs.overlay?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('auth-modal-open');
    
    setTimeout(() => {
      refs.overlay.style.display = 'none';
      resetForms();
    }, CONFIG.animationDuration);

    // Analytics
    logEvent('auth_modal_closed');
  }

  function switchMode(mode) {
    if (state.currentMode === mode) return;
    
    state.currentMode = mode;
    updateModalContent();
    

    // Analytics
    logEvent('auth_mode_switched', { mode });
  }

  function updateModalContent() {
    const isLogin = state.currentMode === 'login';
    
    // Update title and subtitle
    if (refs.title) {
      refs.title.textContent = isLogin ? 'Kirjaudu sisään' : 'Luo uusi tili';
    }
    if (refs.subtitle) {
      refs.subtitle.textContent = isLogin 
        ? 'Tervetuloa takaisin Huuto247:aan'
        : 'Liity Suomen johtavaan huutokauppaan';
    }

    // Update tabs
    refs.loginTab?.classList.toggle('active', isLogin);
    refs.registerTab?.classList.toggle('active', !isLogin);
    
    // Animate tab indicator
    if (refs.tabIndicator) {
      refs.tabIndicator.className = `auth-tab-indicator ${isLogin ? 'login' : 'register'}`;
    }

    // Update forms with slide animation
    refs.loginForm?.classList.toggle('active', isLogin);
    refs.registerForm?.classList.toggle('active', !isLogin);

    // Clear validation errors
    clearAllValidation();
  }

  function resetForms() {
    refs.loginForm?.reset();
    refs.registerForm?.reset();
    clearAllValidation();
    state.validationErrors.clear();
  }

  // Validation functions
  function validateEmail(value) {
    if (!value?.trim()) {
      return { valid: false, message: 'Sähköposti on pakollinen' };
    }
    if (!patterns.email.test(value)) {
      return { valid: false, message: 'Syötä kelvollinen sähköpostiosoite' };
    }
    return { valid: true };
  }

  function validateName(value) {
    if (!value?.trim()) {
      return { valid: false, message: 'Nimi on pakollinen' };
    }
    if (value.trim().length < 2) {
      return { valid: false, message: 'Nimi on liian lyhyt' };
    }
    if (!patterns.name.test(value)) {
      return { valid: false, message: 'Käytä vain kirjaimia ja välilyöntejä' };
    }
    return { valid: true };
  }

  function validateLoginPassword(value) {
    if (!value) {
      return { valid: false, message: 'Salasana on pakollinen' };
    }
    return { valid: true };
  }

  function validateRegisterPassword(value) {
    if (!value) {
      return { valid: false, message: 'Salasana on pakollinen' };
    }
    if (value.length < CONFIG.minPasswordLength) {
      return { valid: false, message: `Salasanassa oltava vähintään ${CONFIG.minPasswordLength} merkkiä` };
    }
    if (value.length > CONFIG.maxPasswordLength) {
      return { valid: false, message: 'Salasana on liian pitkä' };
    }

    // Update password strength
    updatePasswordStrength(value);
    
    return { valid: true };
  }

  function validatePasswordConfirmation(value) {
    const password = document.getElementById('registerPassword')?.value;
    if (!value) {
      return { valid: false, message: 'Vahvista salasana' };
    }
    if (value !== password) {
      return { valid: false, message: 'Salasanat eivät täsmää' };
    }
    return { valid: true };
  }

  function validateTermsAcceptance(value, field) {
    if (!field?.checked) {
      return { valid: false, message: 'Hyväksy käyttöehdot jatkaaksesi' };
    }
    return { valid: true };
  }

  function updatePasswordStrength(password) {
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    if (!strengthBar || !strengthText) return;

    let strength = 0;
    let strengthClass = 'weak';
    let strengthLabel = 'Heikko';

    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    if (strength >= 4) {
      strengthClass = 'strong';
      strengthLabel = 'Vahva';
    } else if (strength >= 3) {
      strengthClass = 'good';
      strengthLabel = 'Hyvä';
    } else if (strength >= 2) {
      strengthClass = 'fair';
      strengthLabel = 'Kohtalainen';
    }

    strengthBar.className = `strength-fill ${strengthClass}`;
    strengthText.textContent = `Salasanan vahvuus: ${strengthLabel}`;
  }

  function showFieldValidation(fieldId, result) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(fieldId + 'Error');
    
    if (!field) return;

    // Update field state
    field.classList.toggle('error', !result.valid);
    field.classList.toggle('success', result.valid && field.value.trim());

    // Update error message
    if (errorElement) {
      errorElement.textContent = result.message || '';
      errorElement.classList.toggle('visible', !result.valid);
    }

    // Store validation result
    state.validationErrors.set(fieldId, result);
  }

  function clearAllValidation() {
    const fields = ['loginEmail', 'loginPassword', 'registerName', 'registerEmail', 'registerPassword', 'confirmPassword', 'acceptTerms'];
    fields.forEach(fieldId => {
      const field = document.getElementById(fieldId);
      const errorElement = document.getElementById(fieldId + 'Error');
      
      field?.classList.remove('error', 'success');
      if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.remove('visible');
      }
    });
    
    state.validationErrors.clear();
    updateFormSubmitState();
  }

  function updateFormSubmitState() {
    const loginSubmit = document.getElementById('loginSubmit');
    const registerSubmit = document.getElementById('registerSubmit');

    // Login form validation
    if (loginSubmit) {
      const emailValid = state.validationErrors.get('loginEmail')?.valid ?? false;
      const passwordValid = state.validationErrors.get('loginPassword')?.valid ?? false;
      const emailHasValue = document.getElementById('loginEmail')?.value.trim();
      const passwordHasValue = document.getElementById('loginPassword')?.value;
      
      loginSubmit.disabled = !((emailValid && passwordValid) || (!emailHasValue && !passwordHasValue));
    }

    // Register form validation  
    if (registerSubmit) {
      const nameValid = state.validationErrors.get('registerName')?.valid ?? false;
      const emailValid = state.validationErrors.get('registerEmail')?.valid ?? false;
      const passwordValid = state.validationErrors.get('registerPassword')?.valid ?? false;
      const confirmValid = state.validationErrors.get('confirmPassword')?.valid ?? false;
      const termsValid = state.validationErrors.get('acceptTerms')?.valid ?? false;
      
      const allFieldsHaveValue = 
        document.getElementById('registerName')?.value.trim() &&
        document.getElementById('registerEmail')?.value.trim() &&
        document.getElementById('registerPassword')?.value &&
        document.getElementById('confirmPassword')?.value &&
        document.getElementById('acceptTerms')?.checked;
      
      registerSubmit.disabled = !(allFieldsHaveValue && nameValid && emailValid && passwordValid && confirmValid && termsValid);
    }
  }

  function togglePasswordVisibility(button) {
    const targetId = button.dataset.target;
    const passwordField = document.getElementById(targetId);
    const showIcon = button.querySelector('.password-show');
    const hideIcon = button.querySelector('.password-hide');
    
    if (!passwordField) return;

    const isVisible = passwordField.type === 'text';
    passwordField.type = isVisible ? 'password' : 'text';
    
    if (showIcon && hideIcon) {
      showIcon.style.display = isVisible ? 'block' : 'none';
      hideIcon.style.display = isVisible ? 'none' : 'block';
    }
  }

  async function handleLoginSubmit(e) {
    e.preventDefault();
    if (state.isLoading) return;

    const formData = new FormData(e.target);
    const email = formData.get('email')?.trim();
    const password = formData.get('password');
    const remember = formData.get('remember') === 'on';

    // Final validation
    if (!email || !password) {
      showNotification('Täytä kaikki kentät', 'error');
      return;
    }

    try {
      state.isLoading = true;
      setSubmitLoadingState('loginSubmit', true);

      const response = await fetch('/api/auth-modal.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          action: 'login',
          email,
          password,
          remember
        })
      });

      const result = await response.json();
      
      if (result.success) {
        showNotification('Kirjautuminen onnistui!', 'success');
        setTimeout(() => {
          window.location.href = result.redirect || '/';
        }, 1000);
      } else {
        showNotification(result.message || 'Kirjautuminen epäonnistui', 'error');
      }
      
    } catch (error) {
      console.error('Login error:', error);
      showNotification('Verkkovirhe. Yritä uudelleen.', 'error');
    } finally {
      state.isLoading = false;
      setSubmitLoadingState('loginSubmit', false);
    }
  }

  async function handleRegisterSubmit(e) {
    e.preventDefault();
    if (state.isLoading) return;

    const formData = new FormData(e.target);
    const fullName = formData.get('full_name')?.trim();
    const email = formData.get('email')?.trim();
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    const termsAccepted = formData.get('terms') === 'on';

    // Final validation
    if (!fullName || !email || !password || !confirmPassword) {
      showNotification('Täytä kaikki kentät', 'error');
      return;
    }

    if (password !== confirmPassword) {
      showNotification('Salasanat eivät täsmää', 'error');
      return;
    }

    if (!termsAccepted) {
      showNotification('Hyväksy käyttöehdot jatkaaksesi', 'error');
      return;
    }

    try {
      state.isLoading = true;
      setSubmitLoadingState('registerSubmit', true);

      const response = await fetch('/api/auth-modal.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          action: 'register',
          full_name: fullName,
          email,
          password,
          confirm_password: confirmPassword,
          terms: termsAccepted
        })
      });

      const result = await response.json();
      
      if (result.success) {
        showNotification('Rekisteröinti onnistui! Tervetuloa!', 'success');
        setTimeout(() => {
          window.location.href = result.redirect || '/';
        }, 1000);
      } else {
        showNotification(result.message || 'Rekisteröinti epäonnistui', 'error');
      }
      
    } catch (error) {
      console.error('Registration error:', error);
      showNotification('Verkkovirhe. Yritä uudelleen.', 'error');
    } finally {
      state.isLoading = false;
      setSubmitLoadingState('registerSubmit', false);
    }
  }

  function setSubmitLoadingState(submitId, loading) {
    const submit = document.getElementById(submitId);
    if (!submit) return;

    submit.classList.toggle('loading', loading);
    submit.disabled = loading;
  }

  function showNotification(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `auth-toast ${type}`;
    toast.textContent = message;
    
    // Style the toast
    Object.assign(toast.style, {
      position: 'fixed',
      top: '20px',
      right: '20px',
      padding: '16px 24px',
      background: type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6',
      color: 'white',
      borderRadius: '12px',
      boxShadow: '0 8px 32px rgba(0,0,0,0.15)',
      zIndex: '1001',
      fontSize: '14px',
      fontWeight: '600',
      transform: 'translateX(100%)',
      transition: 'transform 0.3s cubic-bezier(0.23, 1, 0.32, 1)',
    });
    
    document.body.appendChild(toast);
    
    // Animate in
    requestAnimationFrame(() => {
      toast.style.transform = 'translateX(0)';
    });
    
    // Remove after delay
    setTimeout(() => {
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }

  function logEvent(eventName, data = {}) {
    console.log(`🔐 Auth Event: ${eventName}`, data);
    // Add analytics tracking here if needed
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Export for global access if needed
  window.HuutoAuth = {
    openModal,
    closeModal,
    switchMode
  };

})();