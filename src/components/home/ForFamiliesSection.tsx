import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { Users2, ShieldCheck, HeartHandshake, CheckCircle2, ArrowRight } from "lucide-react"

export default function ForFamiliesSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#fdf9f1] border-y border-[#e8d9b5]">
      <div className="max-w-7xl mx-auto">
        <div className="relative overflow-hidden rounded-3xl border border-[#f6e6b4]/30 bg-black/50 p-8 sm:p-12 lg:p-16 backdrop-blur-xl">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            {/* Left Narrative */}
            <div className="lg:col-span-7 space-y-6">
              <div className="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-wider text-[#e3c877] border border-white/10">
                <Users2 className="h-3.5 w-3.5" />
                <span>Family Collaboration</span>
              </div>

              <h2 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">
                Looking for a Life Partner for{" "}
                <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
                  Someone You Love?
                </span>
              </h2>

              <p className="text-base text-[#fff6e8]/85 leading-relaxed">
                Sometimes, the search isn&apos;t just yours. Parents, siblings, and family members often play a vital and cherished role in finding the right life partner.
              </p>

              <p className="text-base text-[#fff6e8]/85 leading-relaxed">
                BestLife Matrimony makes it easier for families to explore suitable profiles while keeping the individual&apos;s personal preferences and expectations at the heart of the journey.
              </p>

              <div className="pt-2">
                <p className="font-serif text-xl sm:text-2xl font-bold text-[#f6e6b4] tracking-wide">
                  Search Together. Choose Together. Begin Together.
                </p>
              </div>

              <div className="pt-4">
                <Button
                  asChild
                  size="lg"
                  className="rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-base font-bold text-[#3a0c15] shadow-xl shadow-[#dcb04a]/20 hover:scale-105 transition-all"
                >
                  <Link to="/matches" className="flex items-center gap-2">
                    Explore Profiles
                    <ArrowRight className="h-4 w-4" />
                  </Link>
                </Button>
              </div>
            </div>

            {/* Right Highlights Panel */}
            <div className="lg:col-span-5 space-y-4">
              <div className="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-md">
                <div className="flex items-start gap-3">
                  <ShieldCheck className="h-6 w-6 text-[#e3c877] shrink-0 mt-0.5" />
                  <div>
                    <h4 className="font-bold text-sm text-[#fff6e8] mb-1">Parent & Guardian Controls</h4>
                    <p className="text-xs text-[#fff6e8]/75 leading-relaxed">Manage communications on behalf of sons or daughters with full transparency.</p>
                  </div>
                </div>
              </div>

              <div className="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-md">
                <div className="flex items-start gap-3">
                  <HeartHandshake className="h-6 w-6 text-[#e3c877] shrink-0 mt-0.5" />
                  <div>
                    <h4 className="font-bold text-sm text-[#fff6e8] mb-1">Family Value Compatibility</h4>
                    <p className="text-xs text-[#fff6e8]/75 leading-relaxed">Filter matches aligned with cultural heritage, horoscope traditions, and lifestyle.</p>
                  </div>
                </div>
              </div>

              <div className="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-md">
                <div className="flex items-start gap-3">
                  <CheckCircle2 className="h-6 w-6 text-[#e3c877] shrink-0 mt-0.5" />
                  <div>
                    <h4 className="font-bold text-sm text-[#fff6e8] mb-1">100% Privacy Safeguards</h4>
                    <p className="text-xs text-[#fff6e8]/75 leading-relaxed">Confidential phone verification & photo privacy options for peace of mind.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
