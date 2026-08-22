import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { MapPin, Briefcase, GraduationCap, ArrowRight, CheckCircle2 } from "lucide-react"

const sampleProfiles = [
  {
    id: "BLM-1082",
    name: "Dr. Ananya S.",
    age: 27,
    height: "5' 5\"",
    location: "Chennai, Tamil Nadu",
    education: "MBBS, MD (General Medicine)",
    profession: "Consultant Physician",
    religion: "Hindu / Iyer",
    verified: true,
  },
  {
    id: "BLM-1094",
    name: "Karthik R.",
    age: 29,
    height: "5' 11\"",
    location: "Bengaluru, Karnataka",
    education: "B.Tech (IIT Madras), MS",
    profession: "Senior Product Manager",
    religion: "Hindu / Mudaliar",
    verified: true,
  },
  {
    id: "BLM-1120",
    name: "Pooja V.",
    age: 26,
    height: "5' 4\"",
    location: "Coimbatore, Tamil Nadu",
    education: "Chartered Accountant (CA)",
    profession: "Finance Specialist",
    religion: "Hindu / Chettiar",
    verified: true,
  },
  {
    id: "BLM-1145",
    name: "Dr. Siddharth M.",
    age: 31,
    height: "6' 0\"",
    location: "Hyderabad, Telangana",
    education: "MS (Ortho), Fellowship (UK)",
    profession: "Orthopaedic Surgeon",
    religion: "Hindu / Brahmin",
    verified: true,
  },
]

export default function FeaturedMatchesSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#fdf9f1] border-y border-[#e8d9b5]">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 rounded-full border border-[#e8d9b5] bg-white px-4 py-1 text-xs font-semibold uppercase tracking-widest text-[#800020] mb-3 shadow-sm">
            <span>✦</span>
            <span>Curated Showcase</span>
          </div>
          <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#2b1a1e] leading-tight">
            Maybe Your Search{" "}
            <span className="bg-gradient-to-r from-[#800020] via-[#9a3350] to-[#c9a227] bg-clip-text text-transparent italic">
              Ends Here.
            </span>
          </h2>
          <p className="mt-4 text-base sm:text-lg text-[#5a3a3f] leading-relaxed">
            A few profiles. A few possibilities. One meaningful connection.
          </p>
        </div>

        {/* 4 Profile Cards Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {sampleProfiles.map((profile, index) => (
            <div
              key={index}
              className="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-white/15 bg-black/45 p-6 backdrop-blur-xl transition-all duration-300 hover:border-[#f6e6b4]/40 hover:-translate-y-1"
            >
              <div>
                {/* Profile Top Bar */}
                <div className="flex items-center justify-between mb-4">
                  <span className="text-[11px] font-mono font-semibold tracking-wider text-[#f6e6b4]/70 bg-white/5 px-2.5 py-1 rounded-md border border-white/10">
                    {profile.id}
                  </span>
                  {profile.verified && (
                    <span className="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 bg-emerald-950/40 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                      <CheckCircle2 className="h-3 w-3" />
                      Verified
                    </span>
                  )}
                </div>

                {/* Avatar */}
                <div className="mb-5 flex h-20 w-20 mx-auto items-center justify-center rounded-full border-2 border-[#f6e6b4]/40 bg-gradient-to-br from-[#dcb04a]/20 to-black text-2xl font-serif font-bold text-[#f6e6b4]">
                  {profile.name.charAt(0)}
                </div>

                {/* Profile Details */}
                <div className="text-center mb-5">
                  <h3 className="font-serif text-xl font-bold text-[#fff6e8]">
                    {profile.name}
                  </h3>
                  <p className="text-xs text-[#f4e3c9]/80 mt-1">
                    {profile.age} Yrs • {profile.height} • {profile.religion}
                  </p>
                </div>

                <div className="space-y-2.5 text-xs text-[#fff6e8]/80 border-t border-white/10 pt-4">
                  <div className="flex items-center gap-2">
                    <MapPin className="h-3.5 w-3.5 text-[#e3c877] shrink-0" />
                    <span className="truncate">{profile.location}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <GraduationCap className="h-3.5 w-3.5 text-[#e3c877] shrink-0" />
                    <span className="truncate">{profile.education}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Briefcase className="h-3.5 w-3.5 text-[#e3c877] shrink-0" />
                    <span className="truncate">{profile.profession}</span>
                  </div>
                </div>
              </div>

              {/* Action Button */}
              <div className="mt-6 pt-4 border-t border-white/10">
                <Button
                  asChild
                  variant="outline"
                  size="sm"
                  className="w-full rounded-xl border border-[#f6e6b4]/30 bg-white/5 text-xs font-semibold text-[#fff6e8] hover:bg-[#e3c877] hover:text-[#3a0c15] hover:border-[#e3c877] transition-all"
                >
                  <Link to="/matches">View Profile</Link>
                </Button>
              </div>
            </div>
          ))}
        </div>

        {/* Bottom Explore CTA */}
        <div className="mt-12 text-center">
          <Link
            to="/matches"
            className="inline-flex items-center gap-2 text-base font-semibold text-[#800020] hover:text-[#9a3350] transition-colors group"
          >
            <span>Explore More Profiles</span>
            <ArrowRight className="h-4 w-4 group-hover:translate-x-1 transition-transform" />
          </Link>
        </div>
      </div>
    </section>
  )
}
