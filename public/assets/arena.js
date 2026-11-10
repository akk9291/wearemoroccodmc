/* We Are Morocco — site behaviour (header, menu, reveals, FAQ, journeys, stories, builder, inquiry modal, filters, forms). */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* header: transparent -> ink/70 blur after 24px */
  var hdr = $('.site-header');
  var onScroll = function () { if (hdr) hdr.classList.toggle('is-scrolled', window.scrollY > 24); };
  window.addEventListener('scroll', onScroll, { passive: true }); onScroll();

  /* mobile menu */
  var burger = $('.burger');
  var setMenu = function (open) {
    if (!hdr) return;
    hdr.classList.toggle('menu-open', open);
    if (burger) burger.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  };
  if (burger) burger.addEventListener('click', function () { setMenu(!hdr.classList.contains('menu-open')); });
  $$('.mnav a').forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });

  /* scroll reveal: IntersectionObserver, threshold .14, rootMargin -48px, once */
  var revealEls = $$('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
    }, { threshold: .14, rootMargin: '0px 0px -48px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else { revealEls.forEach(function (el) { el.classList.add('is-visible'); }); }

  /* FAQ accordion */
  $$('.faq-item').forEach(function (it) {
    var q = $('.faq-q', it);
    q.addEventListener('click', function () { var o = !it.classList.contains('open'); it.classList.toggle('open', o); q.setAttribute('aria-expanded', String(o)); });
  });

  /* journey selector */
  var J = window.WAM_JOURNEYS;
  var jbtns = $$('.jbtn');
  if (J && jbtns.length) {
    var show = function (i) {
      var j = J[i];
      jbtns.forEach(function (b, k) { b.setAttribute('aria-selected', String(k === i)); b.tabIndex = k === i ? 0 : -1; });
      $('#j-img').src = j.img; $('#j-img').alt = j.name;
      $('#j-route').textContent = j.route;
      $('#j-sum').textContent = j.summary;
      var ul = $('#j-inc'); ul.innerHTML = '';
      j.includes.forEach(function (x) { var li = document.createElement('li'); li.textContent = x; ul.appendChild(li); });
      var a = $('#j-cta'); a.href = j.href; $('span', a).textContent = j.cta;
    };
    jbtns.forEach(function (b, i) {
      b.addEventListener('click', function () { show(i); });
      b.addEventListener('keydown', function (e) {
        var d = (e.key === 'ArrowDown' || e.key === 'ArrowRight') ? 1 : (e.key === 'ArrowUp' || e.key === 'ArrowLeft') ? -1 : 0;
        if (d) { e.preventDefault(); var n = (i + d + jbtns.length) % jbtns.length; show(n); jbtns[n].focus(); }
      });
    });
  }

  /* guest stories */
  var S = window.WAM_STORIES;
  var sdots = $$('.tabdots button');
  if (S && sdots.length) {
    sdots.forEach(function (d, i) { d.addEventListener('click', function () {
      var s = S[i];
      $('#s-img').src = s.img; $('#s-img').alt = s.name; $('#s-name').textContent = s.name; $('#s-role').textContent = s.role;
      $('#s-quote').textContent = s.quote; $('#s-trip').textContent = s.trip;
      sdots.forEach(function (o, k) { o.setAttribute('aria-pressed', String(k === i)); });
    }); });
  }

  /* inquiry modal */
  var modal = $('#inquiry');
  var lastFocus = null;
  var openModal = function (intent, brief) {
    if (!modal) return;
    lastFocus = document.activeElement;
    $('#inq-form', modal).hidden = false; $('#inq-done', modal).hidden = true;
    $('#inq-title', modal).textContent = 'Begin a private conversation.';
    if (intent) { var sel = $('#inq-intent', modal); if (sel) sel.value = intent; }
    var box = $('#inq-brief', modal);
    if (brief) { $('#inq-note', modal).value = brief; if (box) { box.hidden = false; $('pre', box).textContent = brief; } }
    else if (box) box.hidden = true;
    modal.hidden = false; document.body.style.overflow = 'hidden';
    if (hdr) hdr.classList.remove('menu-open');
    setTimeout(function () { var f = $('input', modal); if (f) f.focus(); }, 60);
    var fab = $('.fab'); if (fab) fab.hidden = true;
  };
  var closeModal = function () {
    if (!modal || modal.hidden) return;
    modal.hidden = true; document.body.style.overflow = '';
    var fab = $('.fab'); if (fab) fab.hidden = false;
    if (lastFocus) lastFocus.focus();
  };
  $$('[data-inquiry]').forEach(function (b) { b.addEventListener('click', function (e) { e.preventDefault(); openModal(b.getAttribute('data-inquiry')); }); });
  $$('[data-close]').forEach(function (b) { b.addEventListener('click', closeModal); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeModal(); setMenu(false); } });
  var inq = $('#inq-form');
  if (inq) inq.addEventListener('submit', function (e) {
    e.preventDefault();
    var ok = true;
    $$('[required]', inq).forEach(function (el) {
      var v = el.type === 'email' ? /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(el.value) : el.value.trim() !== '';
      el.closest('label').classList.toggle('bad', !v); if (!v) ok = false;
    });
    if (!ok) return;
    var btn = $('button[type=submit]', inq); var t = btn.innerHTML; btn.disabled = true; btn.textContent = 'Sending to Marrakech…';
    setTimeout(function () {
      btn.disabled = false; btn.innerHTML = t; inq.reset(); inq.hidden = true;
      $('#inq-done').hidden = false; $('#inq-title').textContent = 'Your note is with Marrakech.';
    }, 700);
  });

  /* builder (6 steps) */
  var B = $('#builder');
  if (B) {
    var steps = $$('.b-step', B), cur = 0, bars = $$('.b-prog i', B);
    var val = function (name) { var c = $$('[name="' + name + '"]:checked', B).map(function (i) { return i.value; }); return c.join(', '); };
    var draft = function () {
      var d = { Pace: val('pace'), Mood: val('mood'), Travelers: val('party'), Length: val('length'), 'Lead destination': val('lead'), Stays: val('stays'), Priorities: val('prio') };
      var av = $('#b-avoid', B).value.trim(); if (av) d.Avoid = av;
      return d;
    };
    var render = function () {
      steps.forEach(function (s, i) { s.hidden = i !== cur; });
      bars.forEach(function (b, i) { b.classList.toggle('done', i <= cur); });
      $('#b-count', B).textContent = 'Step ' + String(cur + 1).padStart(2, '0') + ' / 0' + steps.length;
      $('#b-back', B).style.visibility = cur ? 'visible' : 'hidden';
      $('#b-next span', B).textContent = cur === steps.length - 1 ? 'Send to the atelier' : 'Continue';
      var d = draft(), dl = $('#b-draft', B); dl.innerHTML = '';
      Object.keys(d).forEach(function (k) { if (!d[k]) return; var row = document.createElement('div'); row.innerHTML = '<dt></dt><dd></dd>'; row.firstChild.textContent = k; row.lastChild.textContent = d[k]; dl.appendChild(row); });
    };
    B.addEventListener('change', render); $('#b-avoid', B).addEventListener('input', render);
    $('#b-back', B).addEventListener('click', function () { if (cur) { cur--; render(); } });
    $('#b-next', B).addEventListener('click', function () {
      if (cur < steps.length - 1) { cur++; render(); var t = B.getBoundingClientRect().top + window.scrollY - 90; if (window.scrollY > t) window.scrollTo({ top: t, behavior: 'smooth' }); return; }
      var d = draft(); var brief = Object.keys(d).map(function (k) { return k + ': ' + d[k]; }).join('\n');
      if (modal) openModal('custom', brief);
      else { try { sessionStorage.setItem('wam_brief', brief); } catch (e) {} location.href = 'thank-you.html'; }
    });
    render();
  }

  /* chip filters: [data-filter-group] with button.chip[data-value]; items [data-item] carry data-<group> */
  var groups = $$('[data-filter-group]');
  if (groups.length) {
    var target = $(groups[0].getAttribute('data-target'));
    var apply = function () {
      var active = {}, shown = 0;
      groups.forEach(function (g) { var on = $('.chip[aria-pressed="true"]', g); active[g.getAttribute('data-filter-group')] = on ? on.getAttribute('data-value') : 'all'; });
      $$('[data-item]', target).forEach(function (el) {
        var ok = Object.keys(active).every(function (k) { return active[k] === 'all' || (' ' + (el.getAttribute('data-' + k) || '') + ' ').indexOf(' ' + active[k] + ' ') > -1; });
        el.hidden = !ok; if (ok) shown++;
      });
      var empty = $('[data-empty]'); if (empty) empty.hidden = shown > 0;
    };
    groups.forEach(function (g) { $$('.chip', g).forEach(function (c) { c.addEventListener('click', function () {
      $$('.chip', g).forEach(function (o) { o.setAttribute('aria-pressed', String(o === c)); }); apply();
    }); }); });
  }

  /* simple forms: contact + newsletter */
  $$('form[data-simple]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      e.preventDefault(); var ok = true;
      $$('[required]', f).forEach(function (el) {
        var v = el.type === 'checkbox' ? el.checked : el.type === 'email' ? /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(el.value) : el.value.trim() !== '';
        var w = el.closest('label'); if (w) w.classList.toggle('bad', !v); if (!v) ok = false;
      });
      if (!ok) return; var done = $('[data-done]', f); if (done) done.hidden = false; f.reset();
    });
  });

  /* thank-you page: show brief if one was sent from the quote builder */
  var tb = $('#ty-brief');
  if (tb) { try { var br = sessionStorage.getItem('wam_brief'); if (br) { $('pre', tb).textContent = br; tb.hidden = false; } } catch (e) {} }
})();
