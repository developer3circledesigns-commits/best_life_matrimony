import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"
import { ArrowRight } from "lucide-react"
import RotatingCardsCarousel from "@/components/home/RotatingCardsCarousel"

const carouselCards = [
  {
    id: "1",
    image: "/images/parallax/bride-portrait.jpg",
    title: "Register",
    subtitle: "Create your profile with photos & preferences",
    tag: "Step 1 of 4",
  },
  {
    id: "2",
    image: "/images/parallax/groom-portrait.jpg",
    title: "Discover",
    subtitle: "Browse authentic compatible profiles",
    tag: "Step 2 of 4",
  },
  {
    id: "3",
    image: "/images/parallax/couple-varmala.jpg",
    title: "Connect",
    subtitle: "Express interest & start conversations",
    tag: "Step 3 of 4",
  },
  {
    id: "4",
    image: "/images/parallax/couple-walk.jpg",
    title: "Take It Forward",
    subtitle: "Build trust & involve families",
    tag: "Step 4 of 4",
  },
  {
    id: "5",
    image: "/images/parallax/bride-portrait.jpg",
    title: "Verified Profiles",
    subtitle: "Thorough background verification",
    tag: "Trust & Safety",
  },
  {
    id: "6",
    image: "/images/parallax/groom-portrait.jpg",
    title: "Smart Matching",
    subtitle: "AI-powered compatibility engine",
    tag: "Smart Technology",
  },
  {
    id: "7",
    image: "/images/parallax/couple-varmala.jpg",
    title: "Family Involved",
    subtitle: "Privacy-first family connect",
    tag: "For Families",
  },
  {
    id: "8",
    image: "/images/parallax/couple-walk.jpg",
    title: "Success Stories",
    subtitle: "Thousands of happy marriages",
    tag: "Proven Results",
  },
]

export default function HowItWorksSection() {
  return (
    <section className="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#fdf9f1] border-y border-[#e8d9b5]">
      <div className="max-w-6xl mx-auto">
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <p className="text-xs font-bold uppercase tracking-widest text-[#9a3350] mb-3">
            Streamlined 4-Step Process
          </p>
          <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#2b1a1e] leading-tight">
            Finding Your Match Can Be{" "}
            <span className="bg-gradient-to-r from-[#800020] via-[#9a3350] to-[#c9a227] bg-clip-text text-transparent italic">
              Simple.
            </span>
          </h2>
          <p className="mt-4 text-base sm:text-lg text-[#5a3a3f] leading-relaxed">
            A clear, respectful roadmap engineered to take you from initial discovery to lifelong celebration.
          </p>
        </div>

        {/* ── 3D Circular Carousel with Draggable Rotating Cards ── */}
        <div className="relative h-[650px] sm:h-[700px] lg:h-[750px] w-full max-w-7xl mx-auto flex items-center justify-center">
          <RotatingCardsCarousel
            cards={carouselCards}
            radius={420}
            duration={30}
            cardWidth={180}
            cardHeight={220}
            reverse={false}
            draggable={true}
            autoPlay={true}
            mouseWheel={true}
            initialRotation={0}
            cardClassName="perspective-1000"
          />
        </div>

        {/* Footer Quote & Action */}
        <div className="mt-16 rounded-3xl border border-[#f6e6b4]/20 bg-gradient-to-r from-black/60 via-black/40 to-black/60 p-8 text-center backdrop-blur-xl">
          <p className="font-serif text-xl sm:text-2xl text-[#f4e3c9] italic mb-6">
            &ldquo;Your journey starts with a profile. Your future starts with a connection.&rdquo;
          </p>
          <Button
            asChild
            size="lg"
            className="rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-base font-bold text-[#3a0c15] shadow-xl shadow-[#dcb04a]/20 hover:scale-105 transition-all"
          >
            <Link to="/register" className="flex items-center gap-2">
              Create Your Profile
              <ArrowRight className="h-4 w-4" />
            </Link>
          </Button>
        </div>
      </div>
    </section>
  )
}