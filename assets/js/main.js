/* Main initialization — refresh handling */
document.addEventListener('DOMContentLoaded', function(){
  document.documentElement.classList.add('is-loaded');
  // scroll restoration
  if('scrollRestoration' in history) history.scrollRestoration = 'manual';
  var v = document.querySelector('[data-hero-video]');
  if (v) {
    var tryPlay = function(){ v.play().catch(function(){}); };
    document.addEventListener('click', tryPlay, {once:true});
    document.addEventListener('touchstart', tryPlay, {once:true, passive:true});
  }
  // ensure ScrollTrigger refresh after DOM ready
  setTimeout(function(){
    if(typeof ScrollTrigger !== 'undefined' && ScrollTrigger.refresh) ScrollTrigger.refresh();
    if(window.__lenis && window.__lenis.resize) window.__lenis.resize();
  }, 100);
});
window.addEventListener('load', function(){
  document.documentElement.classList.add('is-loaded');
  setTimeout(function(){
    if(window.__lenis && window.__lenis.resize) window.__lenis.resize();
    if(typeof ScrollTrigger !== 'undefined' && ScrollTrigger.refresh) ScrollTrigger.refresh();
  }, 100);
  setTimeout(function(){
    if(window.__lenis && window.__lenis.resize) window.__lenis.resize();
    if(typeof ScrollTrigger !== 'undefined' && ScrollTrigger.refresh) ScrollTrigger.refresh();
  }, 600);
});
// handle refresh via keyboard (F5/Ctrl+R) — ensure scroll top is consistent
window.addEventListener('beforeunload', function(){
  if(window.__lenis && window.__lenis.scrollTo) {
    try{ window.__lenis.scrollTo(0, {immediate:true}); }catch(e){}
  }
});
