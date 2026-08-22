import { Heart, Sliders, Sparkles, Shield, Users2, Link2 } from "lucide-react"
import { motion, useInView, useScroll, useTransform } from "framer-motion"
import { useRef } from "react"

const reasons = [
  {
    icon: Heart,
    title: "Genuine Connections",
    desc: "A platform purpose-built for people looking for serious, meaningful, and lifelong relationships.",
  },
  {
    icon: Sliders,
    title: "Preference-Based Discovery",
    desc: "Find profiles according to specific values, qualities, and lifestyle expectations that matter to you.",
  },
  {
    icon: Sparkles,
    title: "Simple & Easy to Use",
    desc: "A clean, modern matrimonial interface without clutter, spam, or unnecessary complexities.",
  },
  {
    icon: Shield,
    title: "Privacy & Respect",
    desc: "Your personal contact details and matrimonial journey are safeguarded with strict privacy controls.",
  },
  {
    icon: Users2,
    title: "For Individuals & Families",
    desc: "A collaborative platform where candidates and families can participate together in the search.",
  },
  {
    icon: Link2,
    title: "Built Around Relationships",
    desc: "We believe matrimonial platforms should help spark real connections—not just hoard profile collections.",
  },
]

export default function WhyBestLifeSection() {
  const ref = useRef(null)
  const isInView = useInView(ref, { once: true, amount: 0.2 })
  
  // Subtle parallax for section depth
  const { scrollYProgress } = useScroll({
    target: ref,
    offset: ["start end", "end start"]
  })
  const y = useTransform(scrollYProgress, [0, 1], [20, -20])
  
  // Reduced motion check
  const prefersReducedMotion = typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches

  return (
    <section ref={ref} className="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#800020]">
      <motion.div 
        className="max-w-7xl mx-auto"
        style={{ y: prefersReducedMotion ? 0 : y }}
      >
        {/* Header */}
        <motion.div 
          className="text-center max-w-3xl mx-auto mb-16"
          initial={{ opacity: 0, y: 30 }}
          animate={isInView ? { opacity: 1, y: 0 } : { opacity: 0, y: 30 }}
          transition={{ duration: 0.8, ease: [0.25, 0.1, 0.25, 1] }}
        >
          <p className="text-xs font-bold uppercase tracking-widest text-[#e3c877] mb-3">
            The BestLife Advantage
          </p>
          <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">
            More Than Profiles. We Focus On{" "}
            <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
              Compatibility.
            </span>
          </h2>
          <p className="mt-4 text-base sm:text-lg text-[#fff6e8]/80 leading-relaxed">
            Choosing a life partner is not about finding the most profiles. It&apos;s about finding the right profile.
          </p>
        </motion.div>

        {/* 6 Reasons Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" role="list">
          {reasons.map((item, index) => {
            const Icon = item.icon
            return (
              <motion.div
                key={index}
                className="relative rounded-2xl border border-white/15 bg-black/40 p-7 backdrop-blur-xl transition-all duration-300 hover:border-[#f6e6b4]/40 hover:-translate-y-1"
                role="listitem"
                initial={{ opacity: 0, y: 40, scale: 0.95 }}
                animate={isInView ? { opacity: 1, y: 0, scale: 1 } : { opacity: 0, y: 40, scale: 0.95 }}
                transition={{ duration: 0.6, delay: 0.1 + (index * 0.08), ease: [0.25, 0.1, 0.25, 1] }}
                whileHover={{ y: -4, transition: { duration: 0.2 } }}
              >
                <div className="flex h-12 w-12 items-center justify-center rounded-xl border border-[#f6e6b4]/30 bg-gradient-to-br from-[#dcb04a]/20 to-black/30 text-[#e3c877] mb-5" aria-hidden="true">
                  <Icon className="h-6 w-6" />
                </div>
                <h3 className="font-serif text-xl font-bold text-[#fff6e8] mb-2">
                  {item.title}
                </h3>
                <p className="text-sm text-[#fff6e8]/75 leading-relaxed">
                  {item.desc}
                </p>
              </motion.div>
            )
          })}
        </div>
      </motion.div>
    </section>
  )
}
