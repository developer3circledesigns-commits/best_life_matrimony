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

  // hide on scroll (like shouldHideOnScroll)
  let lastY = window.scrollY;
  let ticking = false;
  function onScroll(){
    const y = window.scrollY || (window.__lenis && window.__lenis.scroll) || 0;
    const delta = y - lastY;
    // only hide after 80px and scrolling down
    if (y > 80 && delta > 4 && !isOpen) navbar.classList.add('nav-hidden');
    else if (delta < -4 || y <= 80) navbar.classList.remove('nav-hidden');
    lastY = y; ticking=false;
  }
  function requestTick(){ if(!ticking){ ticking=true; requestAnimationFrame(onScroll);} }
  // listen lenis scroll + window scroll
  if (window.__lenis && window.__lenis.on) window.__lenis.on('scroll', requestTick);
  window.addEventListener('scroll', requestTick, { passive:true });
})();
