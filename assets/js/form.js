'use strict';

let currentStep = 1;

function panelFields(step) {
  return document.querySelectorAll('[data-panel="' + step + '"] input, [data-panel="' + step + '"] select');
}

function clearErrors(panel) {
  panel.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  panel.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}

function validatePanel(step) {
  const panel = document.querySelector('[data-panel="' + step + '"]');
  clearErrors(panel);

  let valid = true;

  panelFields(step).forEach(function (el) {
    const name = el.getAttribute('name');
    const value = el.value.trim();
    let error = '';

    if (el.required) {
      if (el.type === 'checkbox') {
        if (!el.checked) {
          error = 'This field is required.';
        }
      } else if (value === '') {
        error = 'This field is required.';
      }
    }

    if (!error && el.type === 'email' && value !== '') {
      const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRe.test(value)) {
        error = 'Please enter a valid email address.';
      }
    }

    if (error) {
      el.classList.add('is-invalid');
      if (name) {
        const errEl = document.getElementById('err-' + name);
        if (errEl) errEl.textContent = error;
      }
      valid = false;
    }
  });

  return valid;
}

function goTo(step) {
  if (step > currentStep && !validatePanel(currentStep)) {
    return;
  }

  document.querySelectorAll('.cas-cf-panel').forEach(el => el.classList.remove('active'));
  document.querySelector('[data-panel="' + step + '"]').classList.add('active');

  document.querySelectorAll('.cas-cf-step').forEach(function (el) {
    const n = parseInt(el.dataset.step, 10);
    el.classList.toggle('active', n === step);
    el.classList.toggle('completed', n < step);
  });

  document.querySelectorAll('.cas-cf-step-line').forEach(function (el) {
    const n = parseInt(el.dataset.line, 10);
    el.classList.toggle('active', n < step);
  });

  currentStep = step;

  const wrapper = document.getElementById('cas-cf-wrapper');
  if (wrapper) {
    window.scrollTo({top: wrapper.offsetTop - 40, behavior: 'smooth'});
  }
}

document.addEventListener('DOMContentLoaded', function () {

  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-next]')) {
      goTo(parseInt(e.target.closest('[data-next]').dataset.next, 10));
    }
    if (e.target.closest('[data-back]')) {
      goTo(parseInt(e.target.closest('[data-back]').dataset.back, 10));
    }
  });

});