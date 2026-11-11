/* ==========================================================================
   RuangLomba — main.js
   Menu mobile ditangani Bootstrap (navbar-toggler + data-bs-toggle="collapse").
   File ini hanya berisi interaksi yang tidak disediakan Bootstrap:
   scroll progress, reveal-on-scroll, shadow navbar, dan dummy LombaMatch AI.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------------------
     1. Scroll progress bar (garis tipis di atas navbar)
     --------------------------------------------------------------------- */
  var progressBar = document.querySelector('.scroll-progress');

  function updateProgress() {
    if (!progressBar) return;
    var scrollTop = window.scrollY;
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    var pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    progressBar.style.width = pct + '%';
  }

  window.addEventListener('scroll', updateProgress, { passive: true });
  updateProgress();

  /* ---------------------------------------------------------------------
     2. Reveal-on-scroll (IntersectionObserver)
     --------------------------------------------------------------------- */
  var revealEls = document.querySelectorAll('.reveal');

  if (prefersReducedMotion) {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
  } else if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
    );
    revealEls.forEach(function (el) { observer.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* ---------------------------------------------------------------------
     3. Navbar: shadow tipis saat halaman discroll
     --------------------------------------------------------------------- */
  var navbar = document.querySelector('.navbar-ruang');

  function updateNavbarShadow() {
    if (!navbar) return;
    navbar.style.boxShadow = window.scrollY > 8 ? '0 1px 0 rgba(17,17,17,0.04)' : 'none';
  }

  window.addEventListener('scroll', updateNavbarShadow, { passive: true });
  updateNavbarShadow();

  /* ---------------------------------------------------------------------
     4. LombaMatch AI — dummy "Temukan Lomba" interaction
     --------------------------------------------------------------------- */
  var matchForm = document.querySelector('.match-panel');
  var matchButton = matchForm ? matchForm.querySelector('.btn') : null;
  var matchResult = document.querySelector('.match-result');

  if (matchButton && matchResult) {
    matchButton.addEventListener('click', function (e) {
      e.preventDefault();
      matchResult.style.opacity = '0';
      matchResult.style.transform = 'translateY(8px)';
      matchResult.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
      matchResult.style.display = 'flex';
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          matchResult.style.opacity = '1';
          matchResult.style.transform = 'translateY(0)';
        });
      });
    });
  }
});