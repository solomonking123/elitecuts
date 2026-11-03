(function () {
  'use strict';

  const STORAGE_KEY = 'eliteCutsAppointments';
  const SERVICES = [
    { name: 'Regular Haircut', price: 2000 },
    { name: 'Skin Fade', price: 3000 },
    { name: 'Haircut and Beard Trim', price: 4000 },
    { name: 'Children’s Haircut', price: 2000 },
    { name: 'Hair Washing', price: 1500 }
  ];
  const STATUSES = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

  function getAppointments() {
    let raw;
    try {
      raw = window.localStorage.getItem(STORAGE_KEY);
    } catch (error) {
      console.error('Unable to access saved appointments.', error);
      throw new Error('Your browser could not access saved appointments. Check your storage settings and try again.');
    }

    if (!raw) return [];
    try {
      const appointments = JSON.parse(raw);
      if (!Array.isArray(appointments)) throw new Error('Saved appointments are not in the expected format.');
      return appointments;
    } catch (error) {
      console.error('Saved appointment data could not be read.', error);
      throw new Error('Saved appointment data could not be read. Clear the corrupted appointment record to continue.');
    }
  }

  function saveAppointments(appointments) {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(appointments));
    } catch (error) {
      console.error('Unable to save appointments.', error);
      throw new Error('Your appointment could not be saved. Check that browser storage is available and try again.');
    }
  }

  function getService(name) {
    return SERVICES.find(function (service) { return service.name === name; }) || null;
  }

  function formatPrice(price) {
    return '₦' + Number(price).toLocaleString('en-NG');
  }

  function formatDate(dateString) {
    const parts = dateString.split('-').map(Number);
    const date = new Date(parts[0], parts[1] - 1, parts[2]);
    return new Intl.DateTimeFormat('en-NG', { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
  }

  function formatTime(timeString) {
    const parts = timeString.split(':').map(Number);
    const date = new Date(2000, 0, 1, parts[0], parts[1]);
    return new Intl.DateTimeFormat('en-NG', { hour: 'numeric', minute: '2-digit' }).format(date);
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (character) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character];
    });
  }

  function showToast(message, type) {
    const region = document.querySelector('.toast-region');
    if (!region) return;
    const toast = document.createElement('div');
    toast.className = 'toast ' + (type || '');
    toast.textContent = message;
    region.appendChild(toast);
    window.setTimeout(function () {
      toast.remove();
    }, 4200);
  }

  function getSlots() {
    const slots = [];
    for (let hour = 9; hour < 18; hour += 1) {
      slots.push(String(hour).padStart(2, '0') + ':00');
    }
    return slots;
  }

  function getTodayLocal() {
    const now = new Date();
    return now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
  }

  function createReference() {
    const suffix = window.crypto && typeof window.crypto.randomUUID === 'function'
      ? window.crypto.randomUUID().replace(/-/g, '').slice(0, 8)
      : Math.random().toString(36).slice(2, 10);
    return 'EC-' + Date.now().toString(36).toUpperCase() + '-' + suffix.toUpperCase();
  }

  function initializeNavigation() {
    const menuButton = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.site-nav');
    if (menuButton && nav) {
      menuButton.addEventListener('click', function () {
        const isOpen = nav.classList.toggle('open');
        menuButton.setAttribute('aria-expanded', String(isOpen));
        menuButton.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
      });
    }

    document.querySelectorAll('[data-current-year]').forEach(function (element) {
      element.textContent = String(new Date().getFullYear());
    });
  }

  window.EliteCuts = {
    STORAGE_KEY: STORAGE_KEY,
    SERVICES: SERVICES,
    STATUSES: STATUSES,
    getAppointments: getAppointments,
    saveAppointments: saveAppointments,
    getService: getService,
    formatPrice: formatPrice,
    formatDate: formatDate,
    formatTime: formatTime,
    escapeHtml: escapeHtml,
    showToast: showToast,
    getSlots: getSlots,
    getTodayLocal: getTodayLocal,
    createReference: createReference
  };

  initializeNavigation();
}());
