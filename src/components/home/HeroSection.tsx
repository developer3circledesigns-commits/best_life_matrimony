import { useRef, useEffect } from "react"
import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { ShieldCheck, HeartHandshake, ArrowRight } from "lucide-react"
import { motion, useScroll, useTransform, useSpring } from "framer-motion"
import brideGroomVideo from "@/images/bride-groom-cinematic-hq.mp4"

export default function HeroSection() {
  const videoRef = useRef<HTMLVideoElement | null>(null)
  const contentRef = useRef<HTMLDivElement | null>(null)
  const lenisRef = useRef<any>(null)
  
  // Premium parallax with spring physics for smoothness
  const { scrollY } = useScroll()
  const smoothScrollY = useSpring(scrollY, { stiffness: 100, damping: 30, restDelta: 0.001 })
  
  // Background layer - slowest movement (depth: 0.10)
  const videoY = useTransform(smoothScrollY, [0, 800], [0, 80])
  
  // Content layer - moderate movement (depth: 0.20)
  const contentY = useTransform(smoothScrollY, [0, 600], [0, -40])
  
  // Fade out as section exits
  const opacity = useTransform(smoothScrollY, [0, 400], [1, 0])
  
  // Subtle scale for depth perception
  const scale = useTransform(smoothScrollY, [0, 500], [1, 0.96])

  useEffect(() => {
    if (videoRef.current) {
      videoRef.current.play().catch(() => {})
    }

    lenisRef.current = window.__lenis

    return () => {
      // Cleanup handled by Framer Motion
    }
  }, [])

  // Reduced motion check
  const prefersReducedMotion = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches

  return (
    <section className="relative min-h-screen h-screen w-full flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-24 pb-20 overflow-hidden bg-[#3a0c15]">
      {/* Background Video - contained within hero only */}
      <motion.div 
        className="absolute inset-0 z-0 overflow-hidden pointer-events-none"
        style={{ y: prefersReducedMotion ? 0 : videoY }}
      >
        <video
          ref={videoRef}
          src={brideGroomVideo}
          autoPlay
          muted
          playsInline
          preload="auto"
          disablePictureInPicture
          className="h-full w-full object-cover select-none"
        />
        <div className="absolute inset-0 bg-[#3a0c15]/20 pointer-events-none" />
      </motion.div>

      {/* Hero Content */}
      <motion.div
        ref={contentRef}
        className="relative z-10 max-w-4xl mx-auto flex flex-col items-center"
        style={{ 
          opacity: prefersReducedMotion ? 1 : opacity,
          scale: prefersReducedMotion ? 1 : scale,
          y: prefersReducedMotion ? 0 : contentY
        }}
        initial={{ opacity: 0, y: 30 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 1, ease: [0.25, 0.1, 0.25, 1] }}
      >
        {/* Main Title */}
        <motion.h1 
          className="font-serif text-4xl sm:text-6xl md:text-7xl font-bold tracking-tight text-[#fff6e8] leading-[1.08]"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.2, ease: [0.25, 0.1, 0.25, 1] }}
        >
          Find Someone Who{" "}
          <span className="bg-gradient-to-r from-[#fbf1d3] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
            Makes Life Better.
          </span>
        </motion.h1>

        {/* Subtitle */}
        <motion.p 
          className="mt-6 max-w-2xl text-lg sm:text-xl font-medium text-[#f4e3c9]"
          initial={{ opacity: 0, y: 15 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.3, ease: [0.25, 0.1, 0.25, 1] }}
        >
          Where meaningful connections become lifelong relationships.
        </motion.p>

        {/* Description */}
        <motion.p 
          className="mt-4 max-w-2xl text-sm sm:text-base leading-relaxed text-[#fff6e8]"
          initial={{ opacity: 0, y: 15 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.4, ease: [0.25, 0.1, 0.25, 1] }}
        >
          Your search for a life partner deserves more than a list of profiles. BestLife Matrimony brings together genuine profiles, meaningful preferences, and trusted connections to help you discover someone truly compatible.
        </motion.p>

        {/* Action Buttons */}
        <motion.div 
          className="mt-9 flex flex-col sm:flex-row items-center justify-center gap-4"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.5, ease: [0.25, 0.1, 0.25, 1] }}
        >
          <Button
            asChild
            size="lg"
            className="h-13 rounded-full border border-[#f6e6b4] bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-base font-bold text-[#3a0c15] transition-all hover:scale-105 hover:brightness-110 active:scale-95 shadow-none w-full sm:w-auto"
          >
            <Link to="/register" className="flex items-center gap-2 justify-center">
              Register Now
              <ArrowRight className="h-4 w-4" />
            </Link>
          </Button>

          <Button
            asChild
            size="lg"
            variant="outline"
            className="h-13 rounded-full border border-[#f6e6b4]/60 bg-[#3a0c15]/80 px-8 text-base font-semibold text-[#fff6e8] backdrop-blur-sm transition-all hover:bg-[#5a1322] hover:border-[#f6e6b4] active:scale-95 shadow-none w-full sm:w-auto"
          >
            <Link to="/matches" className="justify-center">Explore Matches</Link>
          </Button>
        </div>

        {/* Trust Indicators */}
        <motion.div 
          className="mt-14 inline-flex flex-wrap items-center justify-center gap-6 rounded-2xl border border-[#f6e6b4]/40 bg-[#3a0c15]/80 px-6 py-3.5 backdrop-blur-sm text-xs sm:text-sm text-[#f6e6b4]" 
          role="list" 
          aria-label="Platform trust indicators"
          initial={{ opacity: 0, y: 15 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, delay: 0.6, ease: [0.25, 0.1, 0.25, 1] }}
        >
          <div className="flex items-center gap-2" role="listitem">
            <ShieldCheck className="h-4 w-4 text-[#e3c877]" aria-hidden="true" />
            <span>Trusted Profiles</span>
          </div>
          <span className="text-[#e3c877]/60" aria-hidden="true">•</span>
          <div className="flex items-center gap-2" role="listitem">
            <HeartHandshake className="h-4 w-4 text-[#e3c877]" aria-hidden="true" />
            <span>Meaningful Matches</span>
          </div>
          <span className="text-[#e3c877]/60" aria-hidden="true">•</span>
          <div className="flex items-center gap-2" role="listitem">
            <span className="h-4 w-4 text-[#e3c877]" aria-hidden="true">✦</span>
            <span>A Better Way to Begin</span>
          </div>
        </div>
      </motion.div>
    </section>
  )
}