import { useRef } from "react"
import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { ArrowRight } from "lucide-react"
import { motion, useInView, useScroll, useTransform } from "framer-motion"

export default function IntroSection() {
  const ref = useRef(null)
  const isInView = useInView(ref, { once: true, amount: 0.3 })
  
  // Subtle parallax for depth
  const { scrollYProgress } = useScroll({
    target: ref,
    offset: ["start end", "end start"]
  })
  const y = useTransform(scrollYProgress, [0, 1], [30, -30])
  const opacity = useTransform(scrollYProgress, [0, 0.2, 0.8, 1], [0, 1, 1, 0])
  
  // Reduced motion check
  const prefersReducedMotion = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches

  return (
    <section ref={ref} className="relative z-20 py-20 sm:py-28 px-4 sm:px-6 lg:px-8 overflow-hidden rounded-t-[2.5rem] -mt-8 border-t border-[#f6e6b4]/25 bg-[#0c0205]">
      <motion.div 
        className="relative max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center"
        style={{ 
          y: prefersReducedMotion ? 0 : y,
          opacity: prefersReducedMotion ? 1 : opacity
        }}
        initial={{ opacity: 0, y: 40 }}
        animate={isInView ? { opacity: 1, y: 0 } : { opacity: 0, y: 40 }}
        transition={{ duration: 0.8, ease: "easeOut" }}
      >
        {/* Left copy */}
        <div className="lg:col-span-7">
          <motion.div 
            initial={{ opacity: 0, x: -30 }}
            animate={isInView ? { opacity: 1, x: 0 } : { opacity: 0, x: -30 }}
            transition={{ duration: 0.8, delay: 0.1, ease: [0.25, 0.1, 0.25, 1] }}
          >
            <div className="inline-flex items-center gap-2 rounded-full border border-[#f6e6b4]/20 bg-white/[0.06] px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#e3c877] backdrop-blur-md">
              Brand Promise
            </div>
          </motion.div>

          <motion.h2 
            className="mt-5 font-serif text-3xl sm:text-4xl lg:text-[42px] font-bold leading-[1.1] tracking-tight text-[#fff6e8]"
            initial={{ opacity: 0, x: -30 }}
            animate={isInView ? { opacity: 1, x: 0 } : { opacity: 0, x: -30 }}
            transition={{ duration: 0.8, delay: 0.2, ease: [0.25, 0.1, 0.25, 1] }}
          >
            Your Best Life Could Begin With{" "}
            <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
              One Connection.
            </span>
          </motion.h2>

          <motion.div 
            className="mt-6 space-y-4 text-[15px] leading-[1.85] text-[#fff6e8]/80"
            initial={{ opacity: 0, x: -30 }}
            animate={isInView ? { opacity: 1, x: 0 } : { opacity: 0, x: -30 }}
            transition={{ duration: 0.8, delay: 0.3, ease: [0.25, 0.1, 0.25, 1] }}
          >
            <p>
              Finding the right life partner is one of life&apos;s most important decisions. At BestLife Matrimony, we make the journey simpler, more personal and more meaningful.
            </p>
            <p>
              Discover profiles based on your preferences, connect with compatible individuals and take the first step towards building a beautiful future together.
            </p>
            <p className="font-medium text-[#f6e6b4]">
              Because the right match isn&apos;t just about finding someone. It&apos;s about finding someone who fits your life.
            </p>
          </motion.div>

          <motion.div 
            className="mt-8"
            initial={{ opacity: 0, y: 20 }}
            animate={isInView ? { opacity: 1, y: 0 } : { opacity: 0, y: 20 }}
            transition={{ duration: 0.8, delay: 0.4, ease: [0.25, 0.1, 0.25, 1] }}
          >
            <Button
              asChild
              size="lg"
              className="rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-[15px] font-bold text-[#3a0c15] shadow-xl shadow-[#dcb04a]/20 hover:scale-[1.02] hover:brightness-110 transition-all w-full sm:w-auto"
            >
              <Link to="/about" className="flex items-center gap-2 justify-center">
                Know More About Us
                <ArrowRight className="h-4 w-4" />
              </Link>
            </Button>
          </motion.div>
        </div>

        {/* Right features list */}
        <div className="lg:col-span-5">
          <motion.div 
            className="relative rounded-[2rem] border border-white/10 bg-gradient-to-br from-white/[0.08] to-white/[0.03] p-6 sm:p-7 backdrop-blur-xl"
            initial={{ opacity: 0, x: 30 }}
            animate={isInView ? { opacity: 1, x: 0 } : { opacity: 0, x: 30 }}
            transition={{ duration: 0.8, delay: 0.2, ease: [0.25, 0.1, 0.25, 1] }}
          >
            <p className="text-xs font-bold uppercase tracking-widest text-[#e3c877]/80 mb-4">Why families trust us</p>
            <ul className="space-y-4">
              {[
                { t: "Genuine, verified profiles", d: "Photo & ID checked before you see them." },
                { t: "Preference-first search", d: "Age, city, education, profession & values." },
                { t: "Privacy with dignity", d: "You control what is visible and when." },
              ].map((r, idx) => (
                <motion.li 
                  key={r.t} 
                  className="flex gap-3 rounded-2xl border border-white/10 bg-black/25 px-4 py-4"
                  initial={{ opacity: 0, y: 20 }}
                  animate={isInView ? { opacity: 1, y: 0 } : { opacity: 0, y: 20 }}
                  transition={{ duration: 0.6, delay: 0.3 + (idx * 0.1), ease: [0.25, 0.1, 0.25, 1] }}
                >
                  <span className="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#e3c877]" />
                  <div>
                    <p className="text-sm font-semibold text-[#fff6e8]">{r.t}</p>
                    <p className="text-xs leading-relaxed text-[#fff6e8]/70">{r.d}</p>
                  </div>
                </motion.li>
              ))}
            </ul>
            <motion.div 
              className="mt-6 rounded-xl bg-[#f6e6b4] px-4 py-3 text-center"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={isInView ? { opacity: 1, scale: 1 } : { opacity: 0, scale: 0.95 }}
              transition={{ duration: 0.6, delay: 0.6, ease: [0.25, 0.1, 0.25, 1] }}
            >
              <p className="font-serif text-sm font-bold text-[#3a0c15]">Trusted Profiles • Meaningful Matches • A Better Way to Begin</p>
            </motion.div>
          </motion.div>
        </div>
      </motion.div>
    </section>
  )
}
