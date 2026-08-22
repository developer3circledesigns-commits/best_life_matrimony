import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { Megaphone, Gem, Sparkles, Camera, Building, Utensils, Mail, Plane, Landmark, Shirt, ArrowRight } from "lucide-react"

const categories = [
  { icon: Sparkles, name: "Wedding Services" },
  { icon: Gem, name: "Jewellery" },
  { icon: Shirt, name: "Bridal & Groom Wear" },
  { icon: Sparkles, name: "Beauty & Makeup" },
  { icon: Camera, name: "Photography" },
  { icon: Building, name: "Wedding Venues" },
  { icon: Utensils, name: "Catering" },
  { icon: Mail, name: "Invitations & Events" },
  { icon: Plane, name: "Travel & Honeymoon" },
  { icon: Landmark, name: "Financial & Lifestyle" },
]

export default function AdvertiseBannerSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8" style={{ backgroundColor: '#800020' }}>
      <div className="max-w-6xl mx-auto">
        <div className="relative overflow-hidden rounded-3xl border border-[#f6e6b4]/30 bg-black/55 p-8 sm:p-12 lg:p-16 backdrop-blur-xl shadow-2xl shadow-black/70">
          {/* Header */}
          <div className="text-center max-w-3xl mx-auto mb-12">
            <div className="inline-flex items-center gap-2 rounded-full border border-[#f6e6b4]/30 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-[#e3c877] mb-4">
              <Megaphone className="h-3.5 w-3.5" />
              <span>Partner & Merchant Network</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight mb-4">
              Put Your Brand in Front of a{" "}
              <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
                Growing Matrimonial Community.
              </span>
            </h2>
            <p className="text-sm sm:text-base text-[#fff6e8]/80 leading-relaxed">
              Reach individuals, families, and high-intent audiences who are actively engaged with matrimonial services and wedding planning decisions.
            </p>
          </div>

          {/* 10 Category Badges */}
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5 mb-12">
            {categories.map((cat, index) => {
              const Icon = cat.icon
              return (
                <div
                  key={index}
                  className="flex flex-col items-center justify-center text-center p-4 rounded-xl border border-white/10 bg-white/5 backdrop-blur-md transition-all duration-300 hover:border-[#f6e6b4]/50 hover:bg-[#dcb04a]/10 hover:-translate-y-0.5 group"
                >
                  <Icon className="h-5 w-5 text-[#e3c877] mb-2 group-hover:scale-110 transition-transform" />
                  <span className="text-xs font-medium text-[#fff6e8]/90 group-hover:text-[#f6e6b4]">
                    {cat.name}
                  </span>
                </div>
              )
            })}
          </div>

          {/* Action CTA */}
          <div className="text-center pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p className="font-serif text-lg text-[#f4e3c9]">
              Grow Your Business With BestLife Matrimony.
            </p>
            <Button
              asChild
              size="lg"
              className="rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] shadow-xl shadow-[#dcb04a]/20 hover:scale-105 transition-all"
            >
              <Link to="/advertise" className="flex items-center gap-2">
                Advertise With Us
                <ArrowRight className="h-4 w-4" />
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </section>
  )
}
