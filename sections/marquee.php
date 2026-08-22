<?php // Marquee — continuous running text — after advertise ?>
<section id="marquee" class="relative w-screen overflow-hidden border-y border-white/10 py-4" style="background: #0c0205;">
  <div class="relative flex overflow-hidden">
    <div id="marquee-track" class="flex gap-8 will-change-transform whitespace-nowrap" style="will-change:transform">
      <span class="inline-flex items-center gap-8 text-[14px] tracking-[0.16em] uppercase font-bold text-[#f6e6b4]"><span>BestLife Matrimony</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Genuine Connections</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Meaningful Matches</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Trusted Profiles</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Family First</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Privacy & Respect</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span></span>
      <span class="inline-flex items-center gap-8 text-[14px] tracking-[0.16em] uppercase font-bold text-[#f6e6b4]" aria-hidden="true"><span>BestLife Matrimony</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Genuine Connections</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Meaningful Matches</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Trusted Profiles</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Family First</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Privacy & Respect</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span></span>
      <span class="inline-flex items-center gap-8 text-[14px] tracking-[0.16em] uppercase font-bold text-[#f6e6b4]" aria-hidden="true"><span>BestLife Matrimony</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Genuine Connections</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Meaningful Matches</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Trusted Profiles</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Family First</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span><span>Privacy & Respect</span><span class="w-1.5 h-1.5 bg-[#e3c877] rounded-full"></span></span>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script>
(function(){
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  function initMarquee(){
    var track = document.getElementById('marquee-track');
    if(!track) return;
    var total = track.scrollWidth / 3;
    gsap.set(track,{x:0});
    gsap.to(track,{
      x: -total,
      duration: 18,
      ease: "none",
      repeat: -1,
      modifiers: {
        x: gsap.utils.unitize(function(x){ return parseFloat(x) % total; })
      }
    });
  }
  if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', initMarquee); } else { setTimeout(initMarquee, 80); }
})();
</script>
