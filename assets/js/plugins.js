/* Consolidated GSAP + ScrollTrigger — loaded once in assets/js/plugins.js */
if (typeof gsap === 'undefined') {
  var s = document.createElement('script');
  s.src = 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js';
  s.onload = function(){ if(typeof ScrollTrigger!=='undefined') ScrollTrigger.registerPlugin(); };
  document.head.appendChild(s);
}