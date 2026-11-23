/* ==========================================================================
   RuangLomba — main.js
   Menu mobile ditangani Bootstrap (navbar-toggler + data-bs-toggle="collapse").
   File ini hanya berisi interaksi yang tidak disediakan Bootstrap:
   scroll progress, reveal-on-scroll, shadow navbar, dan dummy LombaMatch AI.
   ========================================================================== */

window.showRlToast = function (message, type) {
  var toast = document.createElement('div');
  var iconWrap = document.createElement('span');
  var icon = document.createElement('i');
  var text = document.createElement('div');
  var close = document.createElement('button');
  var isError = type === 'error';

  toast.className = 'rl-toast ' + (isError ? 'is-error' : 'is-success');
  toast.setAttribute('role', 'status');
  toast.setAttribute('aria-live', 'polite');
  iconWrap.className = 'rl-toast-ico';
  icon.className = 'fa-solid ' + (isError ? 'fa-circle-exclamation' : 'fa-circle-check');
  icon.setAttribute('aria-hidden', 'true');
  text.className = 'rl-toast-msg';
  text.textContent = message;
  close.className = 'rl-toast-close';
  close.type = 'button';
  close.setAttribute('aria-label', 'Tutup');
  close.textContent = '×';
  iconWrap.appendChild(icon);
  toast.appendChild(iconWrap);
  toast.appendChild(text);
  toast.appendChild(close);
  document.body.appendChild(toast);

  function hide() {
    toast.classList.remove('show');
    window.setTimeout(function () { toast.remove(); }, 350);
  }

  close.addEventListener('click', hide);
  window.requestAnimationFrame(function () { toast.classList.add('show'); });
  window.setTimeout(hide, 5000);
};

document.addEventListener('DOMContentLoaded', function () {
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var confirmModalEl = document.getElementById('rlConfirmModal');

  if (confirmModalEl && window.bootstrap) {
    var confirmModal = window.bootstrap.Modal.getOrCreateInstance(confirmModalEl);
    var confirmTitle = document.getElementById('rlConfirmTitle');
    var confirmMessage = document.getElementById('rlConfirmMessage');
    var confirmAccept = document.getElementById('rlConfirmAccept');
    var pendingForm = null;
    var confirmTrigger = null;

    function restoreConfirmFocus() {
      if (confirmTrigger && confirmTrigger !== document.body && document.contains(confirmTrigger)) {
        confirmTrigger.focus();
      }
    }

    document.addEventListener('submit', function (event) {
      var form = event.target;
      if (!form.matches || !form.matches('form[data-confirm-message]')) return;
      if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
      }

      event.preventDefault();
      pendingForm = form;
      confirmTrigger = event.submitter || document.activeElement;
      confirmTitle.textContent = form.dataset.confirmTitle || 'Konfirmasi tindakan';
      confirmMessage.textContent = form.dataset.confirmMessage;
      confirmAccept.textContent = form.dataset.confirmLabel || 'Ya, lanjutkan';
      confirmModal.show();
    });

    confirmAccept.addEventListener('click', function () {
      if (!pendingForm) return;
      var form = pendingForm;
      pendingForm = null;
      form.dataset.confirmed = 'true';
      confirmModal.hide();
      if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
      } else {
        form.submit();
      }
      if (form.dataset.confirmed === 'true') delete form.dataset.confirmed;
    });

    confirmModalEl.addEventListener('hide.bs.modal', function () {
      window.setTimeout(restoreConfirmFocus, 0);
    });

    confirmModalEl.addEventListener('hidden.bs.modal', function () {
      restoreConfirmFocus();
      confirmTrigger = null;
      pendingForm = null;
    });
  }

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