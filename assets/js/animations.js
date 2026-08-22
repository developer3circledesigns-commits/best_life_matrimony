/* Scroll reveal — replaces Framer Motion useInView + variants, uses IntersectionObserver */
(function(){
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  // If reduced motion, immediately show all reveals
  if (prefersReduced) {
    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .stagger').forEach(function(el){ el.classList.add('is-visible'); });
    return;
  }

  const observer = new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        // once: true behavior like React's useInView once
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });

  document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .stagger').forEach(function(el){
    observer.observe(el);
  });

  // FAQ accordion logic
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(function(item){
    const btn = item.querySelector('.faq-trigger');
    if (!btn) return;
    btn.addEventListener('click', function(){
      const isOpen = item.classList.contains('is-open');
      // close others? original allows one open — toggling single, but keep multiple? We'll enforce single-open like React's openIndex
      faqItems.forEach(function(i){ i.classList.remove('is-open'); i.querySelector('.faq-trigger')?.setAttribute('aria-expanded','false'); });
      if (!isOpen) {
        item.classList.add('is-open');
        btn.setAttribute('aria-expanded','true');
      }
    });
  });

  // Ensure first FAQ open by default (matches React useState(0))
  // Already rendered with .is-open on first item; JS maintains sync

  // Counter / stats subtle pop on view — add is-visible already handles

  // Handle resize debounced refresh for lenis if needed
  let resizeTimer;
  window.addEventListener('resize', function(){
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function(){
      // no ScrollTrigger to refresh, but force re-check IO
      document.querySelectorAll('.reveal').forEach(function(el){
        if (!el.classList.contains('is-visible')){
          // re-observe
          observer.observe(el);
        }
      });
    }, 150);
  }, {passive:true});
})();
