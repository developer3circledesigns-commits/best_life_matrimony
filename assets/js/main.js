/* Main initialization order:
   1. DOM loaded
   2. Navigation
   3. Lenis already initialized before
   4. Parallax
   5. Scroll reveals
   6. Resize handling
*/
document.addEventListener('DOMContentLoaded', function(){
  // Add loaded class for any CSS entrance trumping
  document.documentElement.classList.add('is-loaded');
  // Ensure video autoplay after user gesture fallback
  const v = document.querySelector('[data-hero-video]');
  if (v) {
    const tryPlay = function(){ v.play().catch(function(){}); };
    document.addEventListener('click', tryPlay, {once:true});
    document.addEventListener('touchstart', tryPlay, {once:true, passive:true});
  }
});
