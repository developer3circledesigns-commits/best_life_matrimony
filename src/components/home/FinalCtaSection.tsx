import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { ArrowRight } from "lucide-react"

export default function FinalCtaSection() {
  return (
    <section className="relative py-28 px-4 sm:px-6 lg:px-8 bg-[#800020]">
      <div className="max-w-7xl mx-auto">
        <div className="relative overflow-hidden rounded-3xl border border-[#f6e6b4]/40 bg-gradient-to-b from-black/85 via-black/70 to-black/90 p-8 sm:p-16 text-center backdrop-blur-2xl">
          <h2 className="font-serif text-3xl sm:text-5xl md:text-6xl font-bold tracking-tight text-[#fff6e8] leading-tight mb-6">
            Ready to Meet{" "}
            <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
              Someone Special?
            </span>
          </h2>

          <p className="text-base sm:text-lg text-[#f4e3c9]/90 max-w-2xl mx-auto mb-10 leading-relaxed">
            Create your BestLife Matrimony profile and take the first step towards a meaningful relationship and a joyful future.
          </p>

          <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
            <Button
              asChild
              size="lg"
              className="h-13 rounded-full border border-[#f6e6b4]/60 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-10 text-base font-bold text-[#3a0c15] shadow-[0_16px_36px_-12px_rgba(220,176,74,0.85)] hover:scale-105 transition-all w-full sm:w-auto"
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
              className="h-13 rounded-full border border-white/30 bg-white/10 px-9 text-base font-semibold text-[#fff6e8] backdrop-blur-md hover:bg-white/20 hover:border-white/50 transition-all w-full sm:w-auto"
            >
              <Link to="/matches" className="justify-center">Browse Matches</Link>
            </Button>
          </div>

          <div className="mt-12 pt-8 border-t border-white/10 text-xs sm:text-sm font-serif font-bold uppercase tracking-widest text-[#e3c877]/80">
            BestLife Matrimony — Where Connections Become Beginnings.
          </div>
        </div>
      </div>
    </section>
  )
}
