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
  // Navigation
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-next]')) {
      goTo(parseInt(e.target.closest('[data-next]').dataset.next, 10));
    }
    if (e.target.closest('[data-back]')) {
      goTo(parseInt(e.target.closest('[data-back]').dataset.back, 10));
    }
  });

  // Form submission
  const form = document.getElementById('cas-contact-form');
  const submitBtn = document.getElementById('cas-cf-submit');
  const successMsg = document.getElementById('cas-cf-success');
  const serverErr = document.getElementById('cas-cf-server-error');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!validatePanel(3)) {
      return;
    }

    serverErr.style.display = 'none';
    serverErr.textContent = '';
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';

    const formData = new FormData(form);
    formData.append('action', 'cas_cf_submit');
    formData.append('nonce', casCF.nonce);
    formData.append('newsletter', document.getElementById('cas_newsletter').checked ? '1' : '0');
    formData.append('terms', document.getElementById('cas_terms').checked ? '1' : '0');

    fetch(casCF.ajaxUrl, {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(function (res) {
        if (res.success) {
          form.style.display = 'none';
          document.querySelector('.cas-cf-steps').style.display = 'none';
          successMsg.style.display = 'block';
        } else {
          handleServerErrors(res.data);
          submitBtn.disabled = false;
          submitBtn.textContent = 'Submit ✓';
        }
      })
      .catch(function () {
        serverErr.textContent = 'An unexpected error occurred. Please try again.';
        serverErr.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit ✓';
      });
  });

  // Server-side error handling
  function handleServerErrors(data) {
    if (data && data.message) {
      serverErr.textContent = data.message;
      serverErr.style.display = 'block';
    }

    if (data && data.fields) {
      const stepMap = {
        first_name: 1, last_name: 1, email: 1, phone: 1,
        country: 2, city: 2,
        terms: 3
      };

      let firstStep = null;

      Object.entries(data.fields).forEach(function ([field, msg]) {
        const el = document.querySelector('[name="' + field + '"]');
        if (el) el.classList.add('is-invalid');

        const errEl = document.getElementById('err-' + field);
        if (errEl) errEl.textContent = msg;

        const step = stepMap[field] || 1;
        if (firstStep === null || step < firstStep) {
          firstStep = step;
        }
      });

      if (firstStep !== null) {
        goTo(firstStep);
      }
    }
  }
});