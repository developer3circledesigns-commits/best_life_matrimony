/* Navigation — mobile toggle, outside click, Esc, body lock, hide-on-scroll parity */
(function(){
  const navbar = document.getElementById('navbar');
  const toggle = document.getElementById('menuToggle');
  const menu = document.getElementById('mobileMenu');
  if (!toggle || !menu) return;
  let isOpen = false;
  function setOpen(open){
    isOpen = open;
    menu.classList.toggle('hidden', !open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.querySelector('.icon-menu')?.classList.toggle('hidden', open);
    toggle.querySelector('.icon-close')?.classList.toggle('hidden', !open);
    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    // scroll lock when menu open (avoid Lenis conflict)
    if (open) {
      document.body.style.overflow = 'hidden';
      if (window.__lenis && window.__lenis.stop) window.__lenis.stop();
    } else {
      document.body.style.overflow = '';
      if (window.__lenis && window.__lenis.start) window.__lenis.start();
    }
  }
  toggle.addEventListener('click', function(){ setOpen(!isOpen); });
  // close on link click
  menu.querySelectorAll('a').forEach(function(a){ a.addEventListener('click', function(){ setOpen(false); }); });
  // outside click
  document.addEventListener('click', function(e){
    if (!isOpen) return;
    if (!navbar.contains(e.target)) setOpen(false);
  });
  // Esc
  document.addEventListener('keydown', function(e){ if (e.key==='Escape' && isOpen) setOpen(false); });

  // UX: top navbar must NEVER hide on vertical scroll (matches.php requirement — from top to bottom always visible)
  // Completely disable hide-on-scroll — keep navbar pinned
  navbar.classList.remove('nav-hidden');
  // No scroll listeners — intentional: navbar stays visible always
})();
