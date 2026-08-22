"use client"

import { useRef, useEffect, useState, useCallback } from "react"
import { Sparkles, Heart, Shield, Users2, Infinity, Star } from "lucide-react"

interface ScrollStackCard {
  id: string
  eyebrow: string
  title: string
  body: string
  image: string
  accent: string
}

const defaultCards: ScrollStackCard[] = [
  {
    id: "1",
    eyebrow: "PROMISE ONE",
    title: "Authentic Profiles Only",
    body: "Every profile undergoes thorough verification — ID checks, photo validation, and background screening — so you connect with real people seeking genuine relationships.",
    image: "/images/parallax/bride-portrait.jpg",
    accent: "#e3c877",
  },
  {
    id: "2",
    eyebrow: "PROMISE TWO",
    title: "Privacy First, Always",
    body: "Your contact details stay private until you choose to share them. Photo visibility controls, blurred images for non-members, and secure communication channels protect your journey.",
    image: "/images/parallax/groom-portrait.jpg",
    accent: "#dcb04a",
  },
  {
    id: "3",
    eyebrow: "PROMISE THREE",
    title: "Family-Centric Approach",
    body: "Built for Indian families. Parents and guardians can collaborate, shortlist, and manage communications transparently — while respecting the candidate's preferences.",
    image: "/images/parallax/couple-varmala.jpg",
    accent: "#f6e6b4",
  },
  {
    id: "4",
    eyebrow: "PROMISE FOUR",
    title: "Smart Compatibility Matching",
    body: "Beyond basic filters — our engine learns from your preferences, lifestyle values, and family expectations to surface truly compatible matches, not just profile counts.",
    image: "/images/parallax/couple-walk.jpg",
    accent: "#e3c877",
  },
  {
    id: "5",
    eyebrow: "PROMISE FIVE",
    title: "Lifelong Support",
    body: "From first interest to wedding day and beyond — relationship guidance, family meeting support, and a community that celebrates every milestone of your journey together.",
    image: "/images/parallax/bride-portrait.jpg",
    accent: "#dcb04a",
  },
]

const icons = [Sparkles, Heart, Shield, Users2, Infinity, Star]

export default function ScrollStackSection({ cards = defaultCards }: { cards?: ScrollStackCard[] }) {
  const containerRef = useRef<HTMLDivElement>(null)
  const cardsRef = useRef<(HTMLDivElement | null)[]>([])
  const [scrollProgress, setScrollProgress] = useState(0)
  const lenisRef = useRef<any>(null)
  const rafIdRef = useRef<number | null>(null)

  const cardCount = cards.length
  const itemDistance = 100
  const itemScale = 0.03
  const itemStackDistance = 30
  const stackPosition = 0.2
  const scaleEndPosition = 0.1
  const baseScale = 0.85
  const rotationAmount = 2
  const blurAmount = 4

  const calculateProgress = useCallback((scrollTop: number, start: number, end: number) => {
    if (scrollTop < start) return 0
    if (scrollTop > end) return 1
    return (scrollTop - start) / (end - start)
  }, [])

  const getCardTransforms = useCallback(() => {
    if (!containerRef.current) return

    const rect = containerRef.current.getBoundingClientRect()
    const viewportHeight = window.innerHeight
    // Use Lenis scroll if available, fallback to window.scrollY
    const scrollTop = lenisRef.current?.scroll ?? window.scrollY
    const containerTop = rect.top + scrollTop
    const containerHeight = rect.height

    const stackPositionPx = viewportHeight * stackPosition
    const scaleEndPositionPx = viewportHeight * scaleEndPosition

    const endOffset = containerTop + containerHeight - viewportHeight * 0.5

    cardsRef.current.forEach((cardEl, i) => {
      if (!cardEl) return

      const cardRect = cardEl.getBoundingClientRect()
      const cardTop = cardRect.top + scrollTop

      const triggerStart = cardTop - stackPositionPx - itemStackDistance * i
      const triggerEnd = cardTop - scaleEndPositionPx
      const pinStart = triggerStart
      const pinEnd = endOffset

      const scaleProgress = calculateProgress(scrollTop, triggerStart, triggerEnd)
      const targetScale = baseScale + i * itemScale
      const scale = 1 - scaleProgress * (1 - targetScale)
      const rotation = rotationAmount ? i * rotationAmount * scaleProgress : 0

      let blur = 0
      if (blurAmount) {
        let topCardIndex = 0
        for (let j = 0; j < cardCount; j++) {
          const jCardEl = cardsRef.current[j]
          if (!jCardEl) continue
          const jCardRect = jCardEl.getBoundingClientRect()
          const jCardTop = jCardRect.top + scrollTop
          const jTriggerStart = jCardTop - stackPositionPx - itemStackDistance * j
          if (scrollTop >= jTriggerStart) {
            topCardIndex = j
          }
        }
        if (i < topCardIndex) {
          const depthInStack = topCardIndex - i
          blur = Math.max(0, depthInStack * blurAmount)
        }
      }

      let translateY = 0
      const isPinned = scrollTop >= pinStart && scrollTop <= pinEnd

      if (isPinned) {
        translateY = scrollTop - cardTop + stackPositionPx + itemStackDistance * i
      } else if (scrollTop > pinEnd) {
        translateY = pinEnd - cardTop + stackPositionPx + itemStackDistance * i
      }

      const transform = `translate3d(0, ${translateY}px, 0) scale(${scale}) rotate(${rotation}deg)`
      const filter = blur > 0 ? `blur(${blur}px)` : ''

      cardEl.style.transform = transform
      cardEl.style.filter = filter
      cardEl.style.willChange = 'transform, filter'
      cardEl.style.transformOrigin = 'top center'
      cardEl.style.backfaceVisibility = 'hidden'
      cardEl.style.zIndex = String(cardCount - i)
    })

    const lastCardEl = cardsRef.current[cardCount - 1]
    if (lastCardEl) {
      const lastCardRect = lastCardEl.getBoundingClientRect()
      const lastCardTop = lastCardRect.top + scrollTop
      const lastTriggerStart = lastCardTop - stackPositionPx - itemStackDistance * (cardCount - 1)
      const isComplete = scrollTop >= lastTriggerStart && scrollTop <= endOffset
      setScrollProgress(isComplete ? 1 : calculateProgress(scrollTop, lastTriggerStart, endOffset))
    }
  }, [cardCount, calculateProgress, stackPosition, scaleEndPosition, baseScale, itemScale, itemStackDistance, rotationAmount, blurAmount])

  useEffect(() => {
    // Get Lenis instance
    lenisRef.current = (window as any).__lenis
  }, [])

  useEffect(() => {
    const onScroll = () => {
      if (rafIdRef.current) cancelAnimationFrame(rafIdRef.current)
      rafIdRef.current = requestAnimationFrame(() => {
        getCardTransforms()
      })
    }

    const initCards = () => {
      cardsRef.current.forEach((cardEl, i) => {
        if (!cardEl) return
        if (i < cardCount - 1) {
          cardEl.style.marginBottom = `${itemDistance}px`
        }
        cardEl.style.transformOrigin = 'top center'
        cardEl.style.backfaceVisibility = 'hidden'
        cardEl.style.willChange = 'transform, filter'
        cardEl.style.zIndex = String(cardCount - i)
      })
      getCardTransforms()
    }

    // Use Lenis scroll event if available
    if (lenisRef.current) {
      lenisRef.current.on('scroll', onScroll)
    } else {
      window.addEventListener("scroll", onScroll, { passive: true })
      window.addEventListener("resize", onScroll, { passive: true })
    }

    initCards()
    getCardTransforms()

    return () => {
      if (lenisRef.current) {
        lenisRef.current.off('scroll', onScroll)
      } else {
        window.removeEventListener("scroll", onScroll)
        window.removeEventListener("resize", onScroll)
      }
      if (rafIdRef.current) cancelAnimationFrame(rafIdRef.current)
    }
  }, [getCardTransforms, cardCount, itemDistance])

  return (
    <section
      ref={containerRef}
      className="relative py-24 px-4 sm:px-6 lg:px-8"
      style={{ backgroundColor: '#800020' }}
      aria-label="Our Promises Scroll Stack"
    >
      {/* Ambient Glow */}
      <div className="absolute top-1/4 left-1/3 w-[600px] h-[600px] bg-[#dcb04a]/10 rounded-full blur-[160px] pointer-events-none -z-10" />
      <div className="absolute bottom-1/4 right-1/3 w-[650px] h-[650px] bg-[#5a1322]/30 rounded-full blur-[170px] pointer-events-none -z-10" />

      {/* Header */}
      <div className="relative z-10 max-w-4xl mx-auto mb-20 text-center">
        <p className="text-xs font-bold uppercase tracking-widest text-[#e3c877] mb-3">
          Our Promises
        </p>
        <h2 className="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">
          Five Commitments{" "}
          <span className="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">
            That Define Us.
          </span>
        </h2>
        <p className="mt-4 text-base sm:text-lg text-[#fff6e8]/80 leading-relaxed">
          These aren't just features. They're the foundation of every connection made here.
        </p>
      </div>

      {/* Scroll Stack Cards */}
      <div className="relative z-10 max-w-3xl mx-auto">
        <div className="space-y-0">
          {cards.map((card, index) => {
            const Icon = icons[index % icons.length]
            return (
              <div
                key={card.id}
                ref={(el) => { cardsRef.current[index] = el }}
                className="scroll-stack-card relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#3a0c15] via-[#24060d] to-[#0c0205] border border-[#f6e6b4]/20 shadow-2xl"
                style={{
                  minHeight: '480px',
                  transformStyle: 'preserve-3d',
                }}
              >
                {/* Card Background Image */}
                <div className="absolute inset-0 -z-10">
                  <img
                    src={card.image}
                    alt=""
                    loading="lazy"
                    className="h-full w-full object-cover opacity-30"
                    style={{
                      filter: 'grayscale(100%) contrast(1.2) brightness(0.7)',
                    }}
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent" />
                </div>

                {/* Card Content */}
                <div className="relative h-full flex flex-col p-8 sm:p-12">
                  {/* Top Accent Bar */}
                  <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center gap-2">
                      <div
                        className="h-2 w-10 rounded-full"
                        style={{ backgroundColor: card.accent }}
                      />
                      <span className="text-xs font-bold uppercase tracking-widest text-[#e3c877]">
                        {card.eyebrow}
                      </span>
                    </div>
                    <div className="flex h-12 w-12 items-center justify-center rounded-xl border border-[#f6e6b4]/30 bg-white/5 text-[#e3c877] backdrop-blur-md">
                      <Icon className="h-6 w-6" />
                    </div>
                  </div>

                  {/* Main Content */}
                  <div className="flex-1 flex flex-col justify-center">
                    <h3 className="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#fff6e8] leading-tight mb-4">
                      {card.title}
                    </h3>
                    <p className="text-base sm:text-lg text-[#fff6e8]/85 leading-relaxed max-w-xl">
                      {card.body}
                    </p>
                  </div>

                  {/* Bottom Progress Indicator */}
                  <div className="pt-6 border-t border-white/10 flex items-center justify-between">
                    <div className="flex items-center gap-2 text-xs font-medium text-[#f6e6b4]/70">
                      <span>{index + 1}</span>
                      <span className="h-px w-20 bg-gradient-to-r from-transparent via-[#f6e6b4]/40 to-transparent" />
                      <span>{cardCount}</span>
                    </div>
                    <div className="h-1 w-32 bg-white/10 rounded-full overflow-hidden">
                      <div
                        className="h-full rounded-full transition-all duration-300 ease-out"
                        style={{
                          width: `${((index + 1) / cardCount) * 100}%`,
                          background: `linear-gradient(90deg, ${card.accent}, #f6e6b4)`,
                        }}
                      />
                    </div>
                  </div>
                </div>

                {/* Hover Glow Effect */}
                <div className="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-transparent pointer-events-none group-hover:from-[#e3c877]/10 group-hover:via-transparent group-hover:to-transparent transition-all duration-500" />
              </div>
            )
          })}
        </div>

        {/* Spacer for pin end calculation */}
        <div className="scroll-stack-end h-64 w-full" />
      </div>

      {/* Progress Rail */}
      <div className="relative z-10 max-w-3xl mx-auto mt-16">
        <div className="h-1 w-full bg-white/10 rounded-full overflow-hidden">
          <div
            className="h-full rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#f6e6b4] transition-all duration-300 ease-out"
            style={{ width: `${scrollProgress * 100}%` }}
          />
        </div>
        <p className="text-center mt-3 text-xs text-[#fff6e8]/50 uppercase tracking-wider">
          Scroll to explore all promises
        </p>
      </div>
    </section>
  )
}