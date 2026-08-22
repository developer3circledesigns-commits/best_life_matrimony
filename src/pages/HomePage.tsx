import HeroSection from "@/components/home/HeroSection"
import IntroSection from "@/components/home/IntroSection"
import WhyBestLifeSection from "@/components/home/WhyBestLifeSection"
import FeaturedMatchesSection from "@/components/home/FeaturedMatchesSection"
import ForFamiliesSection from "@/components/home/ForFamiliesSection"
import StatsSection from "@/components/home/StatsSection"
import FaqSection from "@/components/home/FaqSection"
import FinalCtaSection from "@/components/home/FinalCtaSection"

export default function HomePage() {
  return (
    <div className="relative min-h-screen bg-[#0c0205] text-[#fff6e8] selection:bg-[#dcb04a] selection:text-[#3a0c15]">
      {/* 1. HERO — First impression */}
      <HeroSection />

      {/* 2. INTRO — Brand promise and value proposition */}
      <IntroSection />

      {/* 3. WHY BESTLIFE — Key differentiators */}
      <WhyBestLifeSection />

      {/* 4. FEATURED MATCHES — Social proof */}
      <FeaturedMatchesSection />

      {/* 5. FOR FAMILIES — Family collaboration feature */}
      <ForFamiliesSection />

      {/* 6. STATS — Trust indicators */}
      <StatsSection />

      {/* 7. FAQ — Common questions */}
      <FaqSection />

      {/* 8. FINAL CTA — Conversion */}
      <FinalCtaSection />
    </div>
  )
}
