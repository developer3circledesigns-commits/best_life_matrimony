import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { Sparkles, ArrowRight, LogIn } from "lucide-react"

export default function MidCtaSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#fdf9f1] border-y border-[#e8d9b5]">
      <div className="max-w-5xl mx-auto">
        <div className="relative overflow-hidden rounded-3xl border border-[#f6e6b4]/40 bg-gradient-to-br from-black/80 via-black/60 to-black/80 p-8 sm:p-14 text-center backdrop-blur-2xl shadow-2xl shadow-black/80">
          <div className="inline-flex items-center gap-2 rounded-full border border-[#f6e6b4]/30 bg-black/40 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-[#f6e6b4] mb-6">
            <Sparkles className="h-3.5 w-3.5 text-[#e3c877]" />
            <span>Take The First Step</span>
          </div>

          <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight mb-4">
            Your Search For The Right Person{" "}
            <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
              Starts Today.
            </span>
          </h2>

          <p className="text-base sm:text-lg text-[#f4e3c9]/90 max-w-2xl mx-auto mb-8 leading-relaxed">
            Don&apos;t wait for the perfect moment. Create your profile, discover meaningful matches, and take the first step towards your Best Life.
          </p>

          <div className="flex flex-wrap items-center justify-center gap-4">
            <Button
              asChild
              size="lg"
              className="h-13 rounded-full border border-[#f6e6b4]/60 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-9 text-base font-bold text-[#3a0c15] shadow-[0_16px_36px_-12px_rgba(220,176,74,0.85)] hover:scale-105 transition-all"
            >
              <Link to="/register" className="flex items-center gap-2">
                REGISTER NOW
                <ArrowRight className="h-4 w-4" />
              </Link>
            </Button>

            <Button
              asChild
              size="lg"
              variant="outline"
              className="h-13 rounded-full border border-white/30 bg-white/10 px-8 text-base font-semibold text-[#fff6e8] backdrop-blur-md hover:bg-white/20 hover:border-white/50 transition-all"
            >
              <Link to="/login" className="flex items-center gap-2">
                <LogIn className="h-4 w-4" />
                Login
              </Link>
            </Button>
          </div>

          <p className="mt-6 text-xs text-[#f6e6b4]/70 font-medium">
            Already have an account? Log in to view incoming interests and profile updates.
          </p>
        </div>
      </div>
    </section>
  )
}
