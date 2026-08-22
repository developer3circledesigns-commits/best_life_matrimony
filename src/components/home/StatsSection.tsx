import { Users, UserCheck, UserPlus2, Globe2 } from "lucide-react"

const stats = [
  {
    icon: Users,
    value: "50,000+",
    label: "Registered Profiles",
    detail: "Verified matrimonial aspirants",
  },
  {
    icon: UserCheck,
    value: "12,500+",
    label: "Active Members",
    detail: "Engaged weekly in discovery",
  },
  {
    icon: UserPlus2,
    value: "1,200+",
    label: "Profiles Added Weekly",
    detail: "Fresh compatible connections",
  },
  {
    icon: Globe2,
    value: "45+",
    label: "Cities & Locations",
    detail: "Pan-India & Global NRI networks",
  },
]

export default function StatsSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#800020]">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <p className="text-xs font-bold uppercase tracking-widest text-[#e3c877] mb-3">
            Real Impact & Growth
          </p>
          <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">
            A Community Built Around{" "}
            <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
              New Beginnings.
            </span>
          </h2>
          <p className="mt-4 text-sm sm:text-base text-[#fff6e8]/75 leading-relaxed">
            Transparent metrics reflecting our dedicated community of individuals and families.
          </p>
        </div>

        {/* 4 Stats Cards Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {stats.map((stat, index) => {
            const Icon = stat.icon
            return (
              <div
                key={index}
                className="relative overflow-hidden rounded-2xl border border-white/15 bg-black/45 p-8 text-center backdrop-blur-xl transition-all duration-300 hover:border-[#f6e6b4]/40 hover:-translate-y-1"
              >
                <div className="flex h-12 w-12 mx-auto items-center justify-center rounded-xl border border-[#f6e6b4]/30 bg-white/5 text-[#e3c877] mb-4">
                  <Icon className="h-6 w-6" />
                </div>
                <div className="font-serif text-3xl sm:text-4xl font-extrabold text-[#f6e6b4] tracking-tight mb-2">
                  {stat.value}
                </div>
                <div className="text-sm font-bold text-[#fff6e8] mb-1">
                  {stat.label}
                </div>
                <div className="text-xs text-[#fff6e8]/60">
                  {stat.detail}
                </div>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}
