import TumbleCarousel from "@/components/home/TumbleCarousel"

const carouselItems = [
  {
    id: "1",
    image: "/images/parallax/bride-portrait.jpg",
    title: "Age & Location",
    subtitle: "Discover profiles that match your preferred age group and location",
    tag: "Proximity & Demographics",
    accent: "#e3c877",
  },
  {
    id: "2",
    image: "/images/parallax/groom-portrait.jpg",
    title: "Education & Profession",
    subtitle: "Find someone whose educational and professional background aligns with your expectations",
    tag: "Career & Intellect",
    accent: "#dcb04a",
  },
  {
    id: "3",
    image: "/images/parallax/couple-varmala.jpg",
    title: "Family & Lifestyle",
    subtitle: "Connect with people who share compatible family values, interests and lifestyles",
    tag: "Cultural Harmony",
    accent: "#f6e6b4",
  },
  {
    id: "4",
    image: "/images/parallax/couple-walk.jpg",
    title: "Personal Preferences",
    subtitle: "Your preferences matter. Discover profiles based on the qualities that are important to you",
    tag: "Tailored Criteria",
    accent: "#e3c877",
  },
  {
    id: "5",
    image: "/images/parallax/bride-portrait.jpg",
    title: "Verified Profiles",
    subtitle: "Every profile undergoes thorough background verification and screening",
    tag: "Trust & Safety",
    accent: "#dcb04a",
  },
  {
    id: "6",
    image: "/images/parallax/groom-portrait.jpg",
    title: "Smart Matching",
    subtitle: "AI-powered compatibility engine learns from your preferences",
    tag: "Smart Technology",
    accent: "#f6e6b4",
  },
  {
    id: "7",
    image: "/images/parallax/couple-varmala.jpg",
    title: "Family Involved",
    subtitle: "Privacy-first family collaboration tools for parents and guardians",
    tag: "For Families",
    accent: "#e3c877",
  },
  {
    id: "8",
    image: "/images/parallax/couple-walk.jpg",
    title: "Success Stories",
    subtitle: "Thousands of happy marriages and lifelong partnerships",
    tag: "Proven Results",
    accent: "#dcb04a",
  },
]

export default function ProfileMatchesSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8">
      {/* Ambient Glow Orbs */}
      <div className="absolute top-1/4 left-1/3 w-[600px] h-[600px] bg-[#dcb04a]/10 rounded-full blur-[160px] pointer-events-none -z-10" />
      <div className="absolute bottom-1/4 right-1/3 w-[650px] h-[650px] bg-[#5a1322]/30 rounded-full blur-[170px] pointer-events-none -z-10" />

      <div className="relative z-10 max-w-6xl mx-auto">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <p className="text-xs font-bold uppercase tracking-widest text-[#e3c877] mb-3">
            Smart Matching Engine
          </p>
          <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">
            Meet Profiles That Could Be Your{" "}
            <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
              Perfect Match.
            </span>
          </h2>
          <p className="mt-4 text-base sm:text-lg text-[#fff6e8]/80 leading-relaxed">
            Your ideal partner may be closer than you think. Explore our growing community of individuals looking for a meaningful relationship and a lifelong partner.
          </p>
        </div>

        {/* Tumble Carousel - Cards that tumble end over end */}
        <TumbleCarousel
          items={carouselItems}
          initialIndex={0}
          cardWidth={300}
          aspectRatio="3 / 4"
          rotation={25}
          verticalOffset={45}
          inactiveScale={0.7}
          visibleRange={2.5}
          borderRadius={16}
          titleBlur={2}
          speed={1}
          showTitles={true}
          showControls={true}
          showCounter={true}
          loop={true}
          autoplay={false}
          enableDrag={true}
          enableKeyboard={true}
        />

        {/* Action Button */}
        <div className="mt-12 text-center">
          <a
            href="/matches"
            className="inline-flex items-center gap-2 rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 py-3.5 text-base font-bold text-[#3a0c15] shadow-xl shadow-[#dcb04a]/20 hover:scale-105 transition-all"
          >
            View Profile Matches
            <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
        </div>
      </div>
    </section>
  )
}