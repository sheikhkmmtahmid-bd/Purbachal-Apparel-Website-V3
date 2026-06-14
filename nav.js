/* ============================================================
   PURBACHAL APPAREL LIMITED — Navigation & UI JavaScript
   ============================================================ */

(function () {
  'use strict';

  /* Sticky nav shadow */
  const siteNav = document.querySelector('.site-nav');
  if (siteNav) {
    const onScroll = () => siteNav.classList.toggle('scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* Mobile hamburger / side panel */
  const hamburger  = document.querySelector('.hamburger');
  const mobileNav  = document.querySelector('.mobile-nav');
  const overlay    = document.querySelector('.overlay');
  const mobileClose = document.querySelector('.mobile-nav-close');

  function openMobile() {
    hamburger  && hamburger.classList.add('open');
    mobileNav  && mobileNav.classList.add('open');
    overlay    && overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMobile() {
    hamburger  && hamburger.classList.remove('open');
    mobileNav  && mobileNav.classList.remove('open');
    overlay    && overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  hamburger   && hamburger.addEventListener('click', openMobile);
  mobileClose && mobileClose.addEventListener('click', closeMobile);
  overlay     && overlay.addEventListener('click', closeMobile);

  /* Active nav link by current URL */
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-link, .dropdown-link, .mobile-nav-link, .mobile-dropdown-link').forEach(link => {
    const href = (link.getAttribute('href') || '').split('?')[0];
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });

  /* Scroll reveal */
  const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
  if (revealEls.length) {
    const revealObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(el => revealObs.observe(el));
  }

  /* Counter animation */
  function animateCounter(el, target, suffix, useLocale) {
    const duration = 1800;
    const startTime = performance.now();
    const isDecimal = String(target).includes('.');
    const step = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const val = eased * target;
      const formatted = isDecimal ? val.toFixed(1) : (useLocale ? Math.floor(val).toLocaleString() : Math.floor(val));
      el.textContent = formatted + suffix;
      if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  const statEls = document.querySelectorAll('.stat-value, .hero-stat-value, .workforce-stat-value');
  if (statEls.length) {
    const counterObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const raw = el.textContent.trim();
        const cleanRaw = raw.replace(/,/g, '');
        const num = parseFloat(cleanRaw.replace(/[^0-9.]/g, ''));
        const suffix = cleanRaw.replace(/^[\d.]+/, '');
        if (!isNaN(num)) animateCounter(el, num, suffix, raw.includes(','));
        counterObs.unobserve(el);
      });
    }, { threshold: 0.4 });
    statEls.forEach(el => counterObs.observe(el));
  }

  /* Progress bars */
  document.querySelectorAll('.progress-bar[data-width]').forEach(bar => {
    const barObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.width = entry.target.dataset.width + '%';
          barObs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });
    barObs.observe(bar);
  });

  /* Back-to-top FAB */
  const fabTop = document.querySelector('.fab-top');
  if (fabTop) {
    window.addEventListener('scroll', () => {
      fabTop.classList.toggle('show', window.scrollY > 400);
    }, { passive: true });
    fabTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* Product carousel with arrow navigation (products.html) */
  const filterBtns    = document.querySelectorAll('.filter-btn');
  const carouselPanels = document.querySelectorAll('.carousel-panel');

  if (carouselPanels.length) {
    const ITEM_W = 240 + 14; // item width + gap
    const STEP   = ITEM_W * 3; // scroll 3 items per click

    carouselPanels.forEach(panel => {
      const track    = panel.querySelector('.carousel-track');
      const viewport = panel.querySelector('.carousel-viewport');
      const prevBtn  = panel.querySelector('.carousel-prev');
      const nextBtn  = panel.querySelector('.carousel-next');
      if (!track || !viewport || !prevBtn || !nextBtn) return;

      let offset = 0;

      function maxOffset() {
        return Math.max(0, track.scrollWidth - viewport.clientWidth);
      }
      function moveTo(n) {
        offset = Math.max(0, Math.min(n, maxOffset()));
        track.style.transform = 'translateX(-' + offset + 'px)';
        prevBtn.disabled = offset <= 0;
        nextBtn.disabled = offset >= maxOffset();
      }

      prevBtn.addEventListener('click', () => moveTo(offset - STEP));
      nextBtn.addEventListener('click', () => moveTo(offset + STEP));

      /* Reset on tab switch */
      new MutationObserver(() => {
        if (panel.classList.contains('active')) moveTo(0);
      }).observe(panel, { attributes: true, attributeFilter: ['class'] });

      moveTo(0);
    });

    /* Tab switching */
    function activateTab(filter) {
      filterBtns.forEach(b => b.classList.toggle('active', b.dataset.filter === filter));
      carouselPanels.forEach(p => p.classList.toggle('active', p.dataset.panel === filter));
    }

    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => activateTab(btn.dataset.filter));
    });

    /* Activate tab from URL hash (e.g. products.html#mens) */
    const validTabs = ['kids', 'mens', 'womens'];

    function applyHash() {
      const tab = window.location.hash.replace('#', '');
      if (!validTabs.includes(tab)) return;
      activateTab(tab);
      const target = document.querySelector('.filter-tabs');
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    applyHash();
    window.addEventListener('hashchange', applyHash);
  }

  /* Lightbox — click image to expand, X or Esc to close */
  const lbOverlay = document.getElementById('lb-overlay');
  if (lbOverlay) {
    const lbImg   = document.getElementById('lb-img');
    const lbClose = document.getElementById('lb-close');

    function openLb(src, alt) {
      lbImg.src = src;
      lbImg.alt = alt;
      lbOverlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    function closeLb() {
      lbOverlay.classList.remove('open');
      document.body.style.overflow = '';
      lbImg.src = '';
    }

    document.addEventListener('click', function(e) {
      if (e.target.closest('#lb-overlay')) return;
      const item = e.target.closest('.carousel-item');
      if (!item) return;
      const img = item.querySelector('img');
      if (img) openLb(img.src, img.alt);
    });

    lbClose.addEventListener('click', closeLb);
    lbOverlay.addEventListener('click', function(e) {
      if (e.target === lbOverlay) closeLb();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && lbOverlay.classList.contains('open')) closeLb();
    });
  }

  /* Facility gallery carousel (team.html) */
  const galleryViewport = document.querySelector('.gallery-viewport');
  if (galleryViewport) {
    const galleryTrack = galleryViewport.querySelector('.gallery-track');
    const galleryPrev  = document.querySelector('.gallery-prev');
    const galleryNext  = document.querySelector('.gallery-next');
    let galleryOffset  = 0;

    function galleryMax() {
      return Math.max(0, galleryTrack.scrollWidth - galleryViewport.clientWidth);
    }
    function galleryMove(n) {
      galleryOffset = Math.max(0, Math.min(n, galleryMax()));
      galleryTrack.style.transform = 'translateX(-' + galleryOffset + 'px)';
      galleryPrev.disabled = galleryOffset <= 0;
      galleryNext.disabled = galleryOffset >= galleryMax();
    }

    const slideEl = galleryTrack.querySelector('.gallery-slide');
    function slideStep() {
      return slideEl ? slideEl.offsetWidth + 20 : 680;
    }

    galleryPrev.addEventListener('click', () => galleryMove(galleryOffset - slideStep()));
    galleryNext.addEventListener('click', () => galleryMove(galleryOffset + slideStep()));
    window.addEventListener('resize', () => galleryMove(galleryOffset));
    galleryMove(0);
  }

  /* Mobile accordion for dropdown */
  document.querySelectorAll('.mobile-has-dropdown').forEach(wrapper => {
    const toggle = wrapper.querySelector('.mobile-nav-link');
    const sub    = wrapper.querySelector('.mobile-dropdown');
    if (!toggle || !sub) return;
    sub.style.display = 'none';
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      const open = sub.style.display === 'block';
      sub.style.display = open ? 'none' : 'block';
    });
  });

})();
