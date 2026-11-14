/* Website forms (Phase 4): sends the inquiry drawer, the quote builder, the contact form and the newsletter forms to Laravel.
   Loaded after arena.js. It takes over the submit of every form[data-form] in the capture phase, so arena.js stays unchanged
   (its preview-only submit handlers never run). Without JavaScript the forms post normally and the server redirects. */
(function () {
  'use strict';
  if (!window.fetch || !window.FormData) return; // very old browser: forms post the classic way

  var EMAIL = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
  var MSG = {
    419: 'This page has been open a while. Please reload it and send again.',
    429: 'Too many messages in a short time. Please wait a minute and try again.',
    fail: 'Something went wrong on our side. Please try again, or write to us directly.',
    offline: 'We could not reach the atelier. Check your connection and try again.'
  };
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  var csrf = function (f) {
    var m = $('meta[name="csrf-token"]'), i = $('input[name="_token"]', f);
    return (m && m.content) || (i && i.value) || '';
  };

  /* Quote builder (/quote): its last step opens the inquiry drawer. Remember the answers so the drawer sends a quote request. */
  var builder = $('#builder'), quote = null;
  var answers = function () {
    var a = {};
    ['pace', 'mood', 'party', 'length', 'lead', 'stays', 'prio'].forEach(function (n) {
      a[n] = $$('[name="' + n + '"]:checked', builder).map(function (i) { return i.value; }).join(', ');
    });
    return a;
  };
  document.addEventListener('click', function (e) {
    var t = e.target && e.target.closest ? e.target : null;
    if (!t) return;
    if (builder && t.closest('#b-next')) {
      var label = $('#b-next span', builder);
      if (label && label.textContent.trim() === 'Send to the atelier') quote = answers();
    } else if (t.closest('[data-inquiry]')) {
      quote = null; // drawer opened from anywhere else: a plain inquiry
    }
  }, true);

  /* field holder that gets .bad (arena.css shows its .err): the label, or the form for the newsletter */
  var holder = function (el, f) { return el.closest('label') || f; };

  var general = function (f, text) {
    var p = $('[data-form-error]', f);
    if (!text) { if (p) p.remove(); return; }
    if (!p) {
      p = document.createElement('p');
      p.className = 'err'; p.setAttribute('data-form-error', ''); p.setAttribute('role', 'alert');
      p.style.display = 'block';
      var btn = $('button[type=submit]', f);
      if (btn) btn.insertAdjacentElement('afterend', p); else f.appendChild(p);
    }
    p.textContent = text;
  };

  var clear = function (f) {
    general(f, null);
    f.classList.remove('bad');
    $$('label.bad', f).forEach(function (l) { l.classList.remove('bad'); });
  };

  /* same client checks as arena.js: required, email format, checkbox ticked */
  var check = function (f) {
    var ok = true;
    $$('[required]', f).forEach(function (el) {
      var v = el.type === 'checkbox' ? el.checked : el.type === 'email' ? EMAIL.test(el.value) : el.value.trim() !== '';
      if (!v) { holder(el, f).classList.add('bad'); ok = false; }
    });
    return ok;
  };

  var showErrors = function (f, errors) {
    var first = null, other = [];
    Object.keys(errors).forEach(function (name) {
      var el = $('[name="' + name + '"]', f);
      if (!el || el.type === 'hidden') { other.push(errors[name][0]); return; }
      var h = holder(el, f), err = $('.err:not([data-form-error])', h);
      if (!err) { err = document.createElement('span'); err.className = 'err'; h.appendChild(err); }
      err.textContent = errors[name][0];
      h.classList.add('bad');
      if (!first) first = el;
    });
    if (other.length) general(f, other.join(' '));
    if (first) first.focus();
  };

  var busy = function (f, kind, on) {
    var btn = $('button[type=submit]', f);
    if (!btn) return;
    if (on) { btn._html = btn.innerHTML; btn.disabled = true; btn.textContent = kind === 'inquiry' ? 'Sending to Marrakech…' : 'Sending…'; }
    else { btn.disabled = false; if (btn._html) btn.innerHTML = btn._html; }
  };

  var success = function (f, kind, body, brief) {
    f.reset();
    if (kind === 'inquiry') {
      if (body.redirect) { // quote builder: on to the thank-you page, which shows the brief
        try { if (brief) sessionStorage.setItem('wam_brief', brief); } catch (e) {}
        location.href = body.redirect;
        return;
      }
      f.hidden = true;
      var done = $('#inq-done'), title = $('#inq-title');
      if (done) done.hidden = false;
      if (title) title.textContent = 'Your note is with Marrakech.';
      return;
    }
    var ok = $('[data-done]', f);
    if (ok) ok.hidden = false;
  };

  document.addEventListener('submit', function (e) {
    var f = e.target, kind = f && f.getAttribute && f.getAttribute('data-form');
    if (!kind) return;
    e.preventDefault();
    e.stopPropagation(); // arena.js preview handlers must not run
    if (f._sending) return;
    clear(f);
    if (!check(f)) return;

    var data = new FormData(f), url = f.getAttribute('action'), brief = '';
    if (kind === 'inquiry') {
      var box = $('#inq-brief');
      brief = box && !box.hidden ? $('pre', box).textContent : '';
      data.set('brief', brief);
      if (quote) {
        url = f.getAttribute('data-quote-action');
        Object.keys(quote).forEach(function (k) { data.set(k, quote[k]); });
      }
    }
    data.set('source_page', location.pathname);

    f._sending = true; busy(f, kind, true);
    fetch(url, {
      method: 'POST', body: data, credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf(f) }
    }).then(function (r) {
      return r.json().catch(function () { return {}; }).then(function (body) { return { status: r.status, body: body }; });
    }).then(function (res) {
      f._sending = false; busy(f, kind, false);
      if (res.status >= 200 && res.status < 300 && res.body.ok) { if (kind === 'inquiry') quote = res.body.redirect ? null : quote; success(f, kind, res.body, brief); return; }
      if (res.status === 422 && res.body.errors) { showErrors(f, res.body.errors); return; }
      general(f, MSG[res.status] || MSG.fail);
    }).catch(function () {
      f._sending = false; busy(f, kind, false);
      general(f, MSG.offline);
    });
  }, true);

  /* a corrected field loses its error */
  document.addEventListener('input', function (e) {
    var f = e.target.form;
    if (!f || !f.getAttribute('data-form')) return;
    var h = holder(e.target, f);
    if (h !== f) h.classList.remove('bad'); else f.classList.remove('bad');
  });
})();
