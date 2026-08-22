"use client"

import { useEffect, useRef } from "react"
import { Sparkles, Heart } from "lucide-react"

const galleryImages = [
  {
    id: "img-1",
    src: "/images/parallax/bride-portrait.jpg",
    alt: "Traditional Indian Bride in Royal Saree",
    speed: -0.12,
    rotate: -3,
    className: "w-64 sm:w-72 md:w-80 lg:w-96 aspect-[3/4] top-[8%] left-[4%] sm:left-[8%] lg:left-[10%]",
    tag: "Grace & Elegance",
  },
  {
    id: "img-2",
    src: "/images/parallax/groom-portrait.jpg",
    alt: "Indian Groom in Royal Sherwani",
    speed: 0.15,
    rotate: 4,
    className: "w-60 sm:w-68 md:w-76 lg:w-88 aspect-[3/4] top-[12%] right-[4%] sm:right-[8%] lg:right-[12%]",
    tag: "Regal Heritage",
  },
  {
    id: "img-3",
    src: "/images/parallax/couple-varmala.jpg",
    alt: "Bride and Groom Varmala Garland Ceremony",
    speed: -0.04,
    rotate: -1,
    className: "w-72 sm:w-84 md:w-96 lg:w-[440px] aspect-[3/4] top-[32%] sm:top-[28%] left-1/2 -translate-x-1/2 z-20",
    tag: "Sacred Beginnings",
    isPrimary: true,
  },
  {
    id: "img-4",
    src: "/images/parallax/couple-walk.jpg",
    alt: "Couple Walking Together in Royal Mandap",
    speed: 0.18,
    rotate: -2,
    className: "w-64 sm:w-72 md:w-80 lg:w-92 aspect-[3/4] bottom-[8%] left-[6%] sm:left-[12%] lg:left-[15%]",
    tag: "Lifelong Partnership",
  },
  {
    id: "img-5",
    src: "/images/parallax/bride-portrait.jpg",
    alt: "Auspicious Celebrations",
    speed: -0.16,
    rotate: 3,
    className: "w-56 sm:w-64 md:w-72 lg:w-80 aspect-[3/4] bottom-[10%] right-[6%] sm:right-[10%] lg:right-[14%]",
    tag: "Eternal Moments",
  },
]

export default function BrandPromiseSection() {
  const sectionRef = useRef<HTMLDivElement>(null)
  const itemsRef = useRef<(HTMLDivElement | null)[]>([])
  const lenisRef = useRef<any>(null)
  const rafIdRef = useRef<number | null>(null)

  useEffect(() => {
    // Get Lenis instance from window
    lenisRef.current = (window as any).__lenis
  }, [])

  useEffect(() => {
    const handleParallax = () => {
      if (!sectionRef.current) return

      // Use Lenis scroll for smooth parallax sync
      lenisRef.current?.scroll

      const rect = sectionRef.current.getBoundingClientRect()
      const sectionCenter = rect.top + rect.height / 2
      const viewportCenter = window.innerHeight / 2
      const offset = (viewportCenter - sectionCenter)

      itemsRef.current.forEach((el, index) => {
        if (!el) return
        const img = galleryImages[index]
        if (!img) return
        const translateY = offset * img.speed
        const rotate = img.rotate + (offset * 0.002)
        el.style.transform = `translate3d(0, ${translateY}px, 0) rotate(${rotate}deg)`
      })
    }

    const onScroll = () => {
      if (rafIdRef.current) cancelAnimationFrame(rafIdRef.current)
      rafIdRef.current = requestAnimationFrame(handleParallax)
    }

    // Listen to Lenis scroll event if available
    if (lenisRef.current) {
      lenisRef.current.on('scroll', onScroll)
    } else {
      window.addEventListener("scroll", onScroll, { passive: true })
    }

    handleParallax()

    return () => {
      if (lenisRef.current) {
        lenisRef.current.off('scroll', onScroll)
      } else {
        window.removeEventListener("scroll", onScroll)
      }
      if (rafIdRef.current) cancelAnimationFrame(rafIdRef.current)
    }
  }, [])

  return (
    <section
      ref={sectionRef}
      className="relative z-20 min-h-[140vh] sm:min-h-[160vh] lg:min-h-[180vh] w-full overflow-hidden rounded-none border-t border-[#f6e6b4]/30 py-28 px-4"
      style={{ backgroundColor: '#800020' }}
      aria-label="Matrimony Visual Parallax Gallery"
    >
      {/* Floating Ambient Glow Orbs */}
      <div className="absolute top-1/4 left-1/3 w-[650px] h-[650px] bg-[#dcb04a]/10 rounded-full blur-[160px] pointer-events-none" />
      <div className="absolute bottom-1/4 right-1/3 w-[700px] h-[700px] bg-[#5a1322]/30 rounded-full blur-[170px] pointer-events-none" />

      {/* Floating Decorative Gold Sparkles / Ring Emblems */}
      <div className="absolute top-[20%] right-[25%] flex items-center justify-center h-12 w-12 rounded-full border border-[#f6e6b4]/40 bg-[#3a0c15]/60 text-[#e3c877] backdrop-blur-md animate-pulse pointer-events-none">
        <Sparkles className="h-6 w-6" />
      </div>
      <div className="absolute bottom-[25%] left-[28%] flex items-center justify-center h-14 w-14 rounded-full border border-[#f6e6b4]/40 bg-[#3a0c15]/60 text-[#e3c877] backdrop-blur-md animate-pulse pointer-events-none">
        <Heart className="h-7 w-7 fill-[#e3c877]/20" />
      </div>

      {/* ── Multi-Plane Parallax Image Showcase ── */}
      <div className="relative h-full w-full max-w-7xl mx-auto min-h-[120vh] sm:min-h-[140vh] lg:min-h-[160vh]">
        {galleryImages.map((item, idx) => (
          <div
            key={item.id}
            ref={(el) => { itemsRef.current[idx] = el }}
            className={`absolute ${item.className} will-change-transform`}
            style={{ willChange: 'transform' }}
          >
            <div
              className={`relative h-full w-full overflow-hidden rounded-3xl border-2 ${
                item.isPrimary
                  ? "border-[#f6e6b4] shadow-[0_24px_60px_-15px_rgba(220,176,74,0.6)]"
                  : "border-[#f6e6b4]/40 shadow-[0_20px_50px_-15px_rgba(58,12,21,0.8)]"
              } bg-[#24060d] group transition-all duration-500 hover:scale-105 hover:border-[#f6e6b4] hover:shadow-[0_28px_70px_-10px_rgba(220,176,74,0.8)]`}
            >
              {/* Image */}
              <img
                src={item.src}
                alt={item.alt}
                loading="lazy"
                className="h-full w-full object-cover select-none transition-transform duration-700 group-hover:scale-110"
              />

              {/* Glass Reflection Highlight */}
              <div className="absolute inset-0 bg-gradient-to-tr from-[#3a0c15]/30 via-transparent to-white/10 pointer-events-none" />

              {/* Subtle Gold Pill Tag */}
              <div className="absolute bottom-4 left-4 inline-flex items-center gap-1.5 rounded-full border border-[#f6e6b4]/50 bg-black/60 px-3.5 py-1 text-[11px] font-semibold text-[#f6e6b4] backdrop-blur-md shadow-lg opacity-90 group-hover:opacity-100 transition-opacity">
                <Sparkles className="h-3 w-3 text-[#e3c877]" />
                <span>{item.tag}</span>
              </div>
            </div>
          </div>
        ))}
      </div>
    </section>
  )
}