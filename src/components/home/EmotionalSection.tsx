import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { Sparkles, ArrowRight, Heart } from "lucide-react"

export default function EmotionalSection() {
  return (
    <section className="relative py-28 px-4 sm:px-6 lg:px-8 overflow-hidden" style={{ backgroundColor: '#800020' }}>
      {/* Decorative center radial glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl h-80 bg-[#dcb04a]/10 rounded-full blur-3xl pointer-events-none -z-10" />

      <div className="max-w-4xl mx-auto text-center">
        {/* Floating Heart Icon */}
        <div className="inline-flex h-16 w-16 items-center justify-center rounded-full border border-[#f6e6b4]/30 bg-black/40 text-[#e3c877] shadow-xl backdrop-blur-md mb-8 animate-pulse">
          <Heart className="h-8 w-8 fill-[#e3c877]/20" />
        </div>

        {/* Headline */}
        <h2 className="font-serif text-3xl sm:text-5xl md:text-6xl font-bold tracking-tight text-[#fff6e8] leading-[1.15] mb-6">
          Two People. Two Stories.{" "}
          <span className="block bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic mt-2">
            One Beautiful Beginning.
          </span>
        </h2>

        {/* Narrative */}
        <p className="text-lg sm:text-xl text-[#f4e3c9] leading-relaxed max-w-2xl mx-auto mb-4">
          Every person has a story. Every family has dreams. And sometimes, two stories come together to create a timeless new chapter.
        </p>

        <p className="text-sm sm:text-base text-[#fff6e8]/80 leading-relaxed max-w-2xl mx-auto mb-10">
          BestLife Matrimony is here to help you take that first step. Whether you&apos;re beginning your search or helping someone you love find their life partner, we&apos;re here to make the journey easier.
        </p>

        {/* Action Button */}
        <Button
          asChild
          size="lg"
          className="h-13 rounded-full border border-[#f6e6b4]/60 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-10 text-base font-bold text-[#3a0c15] shadow-[0_16px_36px_-12px_rgba(220,176,74,0.85)] transition-all hover:scale-105 hover:brightness-110 active:scale-95"
        >
          <Link to="/register" className="flex items-center gap-2">
            <Sparkles className="h-4 w-4" />
            Register Now
            <ArrowRight className="h-4 w-4" />
          </Link>
        </Button>
      </div>
    </section>
  )
}
