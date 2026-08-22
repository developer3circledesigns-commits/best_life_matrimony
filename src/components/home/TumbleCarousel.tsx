"use client"

import { useRef, useEffect, useState, useCallback } from "react"
import { motion } from "framer-motion"
import { ChevronLeft, ChevronRight } from "lucide-react"

export interface TumbleCarouselItem {
  id: string
  image: string
  title: string
  subtitle: string
  tag: string
  accent: string
}

interface TumbleCarouselProps {
  items: TumbleCarouselItem[]
  initialIndex?: number
  cardWidth?: number
  aspectRatio?: string
  rotation?: number
  verticalOffset?: number
  inactiveScale?: number
  visibleRange?: number
  borderRadius?: number
  titleBlur?: number
  speed?: number
  showTitles?: boolean
  showControls?: boolean
  showCounter?: boolean
  loop?: boolean
  autoplay?: boolean
  autoplayDelay?: number
  enableDrag?: boolean
  enableKeyboard?: boolean
  onIndexChange?: (index: number) => void
  className?: string
}

const defaultItems: TumbleCarouselItem[] = [
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

export default function TumbleCarousel({
  items = defaultItems,
  initialIndex = 0,
  cardWidth = 280,
  aspectRatio = "3 / 4",
  rotation = 25,
  verticalOffset = 45,
  inactiveScale = 0.7,
  visibleRange = 2.5,
  borderRadius = 16,
  titleBlur = 2,
  speed = 1,
  showTitles = true,
  showControls = true,
  showCounter = true,
  loop = true,
  autoplay = false,
  autoplayDelay = 4000,
  enableDrag = true,
  enableKeyboard = true,
  onIndexChange,
  className = "",
}: TumbleCarouselProps) {
  const containerRef = useRef<HTMLDivElement>(null)
  const cardsRef = useRef<(HTMLDivElement | null)[]>([])
  const [activeIndex, setActiveIndex] = useState(initialIndex)
  const [isDragging, setIsDragging] = useState(false)
  const [dragStart, setDragStart] = useState<{ x: number; index: number } | null>(null)
  const autoplayRef = useRef<number | null>(null)
  const lenisRef = useRef<any>(null)

  const itemCount = items.length

  useEffect(() => {
    lenisRef.current = (window as any).__lenis
  }, [])

  useEffect(() => {
    if (autoplay && !isDragging) {
      autoplayRef.current = window.setInterval(() => {
        goToNext()
      }, autoplayDelay)
    }
    return () => {
      if (autoplayRef.current) clearInterval(autoplayRef.current)
    }
  }, [autoplay, autoplayDelay, isDragging, activeIndex])

  const getClampedIndex = useCallback((index: number) => {
    if (loop) {
      return ((index % itemCount) + itemCount) % itemCount
    }
    return Math.max(0, Math.min(itemCount - 1, index))
  }, [itemCount, loop])

  const goToIndex = useCallback((newIndex: number) => {
    const clamped = getClampedIndex(newIndex)
    if (clamped !== activeIndex) {
      setActiveIndex(clamped)
      onIndexChange?.(clamped)
    }
  }, [activeIndex, getClampedIndex, onIndexChange])

  const goToNext = useCallback(() => {
    goToIndex(activeIndex + 1)
  }, [activeIndex, goToIndex])

  const goToPrev = useCallback(() => {
    goToIndex(activeIndex - 1)
  }, [activeIndex, goToIndex])

  const handleKeyDown = useCallback((e: KeyboardEvent) => {
    if (!enableKeyboard) return
    if (e.key === "ArrowRight") goToNext()
    if (e.key === "ArrowLeft") goToPrev()
  }, [enableKeyboard, goToNext, goToPrev])

  useEffect(() => {
    if (enableKeyboard) {
      window.addEventListener("keydown", handleKeyDown)
    }
    return () => window.removeEventListener("keydown", handleKeyDown)
  }, [handleKeyDown, enableKeyboard])

  const handleMouseDown = (e: React.MouseEvent) => {
    if (!enableDrag) return
    setIsDragging(true)
    setDragStart({ x: e.clientX, index: activeIndex })
    e.preventDefault()
  }

  const handleTouchStart = (e: React.TouchEvent) => {
    if (!enableDrag) return
    setIsDragging(true)
    setDragStart({ x: e.touches[0].clientX, index: activeIndex })
  }

  const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging || !dragStart) return
    const deltaX = dragStart.x - e.clientX
    if (Math.abs(deltaX) > 50) {
      if (deltaX > 0) goToNext()
      else goToPrev()
      setIsDragging(false)
      setDragStart(null)
    }
  }

  const handleTouchMove = (e: TouchEvent) => {
    if (!isDragging || !dragStart) return
    const deltaX = dragStart.x - e.touches[0].clientX
    if (Math.abs(deltaX) > 50) {
      if (deltaX > 0) goToNext()
      else goToPrev()
      setIsDragging(false)
      setDragStart(null)
    }
  }

  const handleMouseUp = () => {
    setIsDragging(false)
    setDragStart(null)
  }

  useEffect(() => {
    if (isDragging) {
      window.addEventListener("mousemove", handleMouseMove)
      window.addEventListener("mouseup", handleMouseUp)
      window.addEventListener("touchmove", handleTouchMove, { passive: true })
      window.addEventListener("touchend", handleMouseUp)
      return () => {
        window.removeEventListener("mousemove", handleMouseMove)
        window.removeEventListener("mouseup", handleMouseUp)
        window.removeEventListener("touchmove", handleTouchMove)
        window.removeEventListener("touchend", handleMouseUp)
      }
    }
  }, [isDragging, handleMouseMove, handleTouchMove, handleMouseUp])

  const getCardStyle = (index: number) => {
    const relativeIndex = index - activeIndex

    let normalizedRelative = relativeIndex
    if (loop) {
      const half = itemCount / 2
      if (relativeIndex > half) normalizedRelative = relativeIndex - itemCount
      if (relativeIndex < -half) normalizedRelative = relativeIndex + itemCount
    }

    const absRelative = Math.abs(normalizedRelative)
    const isActive = normalizedRelative === 0
    const isVisible = absRelative <= visibleRange

    if (!isVisible) {
      return { opacity: 0, zIndex: 0, visible: false as const }
    }

    const scale = isActive ? 1 : inactiveScale + (1 - inactiveScale) * Math.max(0, 1 - absRelative / visibleRange)
    const rotateX = normalizedRelative * rotation
    const translateY = normalizedRelative * (verticalOffset / 100) * cardWidth * (parseFloat(aspectRatio.split("/")[1]) / parseFloat(aspectRatio.split("/")[0]))
    const translateZ = -absRelative * 100
    const blur = absRelative * titleBlur
    const opacity = Math.max(0.1, 1 - absRelative / visibleRange)

    return {
      transform: `translate3d(0, ${translateY}px, ${translateZ}px) rotateX(${rotateX}deg) scale(${scale})`,
      opacity,
      zIndex: itemCount - Math.round(absRelative * 10),
      filter: `blur(${blur}px)`,
      visible: true as const,
      isActive,
    }
  }

  return (
    <div
      ref={containerRef}
      className={`relative w-full ${className}`}
      onMouseDown={handleMouseDown}
      onTouchStart={handleTouchStart}
      onMouseEnter={() => { if (autoplay) setIsDragging(true) }}
      onMouseLeave={() => { if (autoplay) setIsDragging(false) }}
      role="region"
      aria-label="Tumble carousel"
      tabIndex={enableKeyboard ? 0 : undefined}
      onKeyDown={(e: React.KeyboardEvent<HTMLDivElement>) => {
        if (!enableKeyboard) return
        if (e.key === "ArrowRight") goToNext()
        if (e.key === "ArrowLeft") goToPrev()
      }}
    >
      <div
        className="relative flex items-center justify-center gap-4"
        style={{
          height: cardWidth * (parseFloat(aspectRatio.split("/")[1]) / parseFloat(aspectRatio.split("/")[0])) + 120,
          perspective: "1200px",
          transformStyle: "preserve-3d",
        }}
      >
{items.map((item, index) => {
            const style = getCardStyle(index)
            const isActive = index === activeIndex

            if (!style.visible) return null

            return (
              <motion.div
                key={item.id}
                ref={(el) => { cardsRef.current[index] = el }}
                className="relative cursor-pointer"
                style={{
                  width: cardWidth,
                  aspectRatio,
                  transformStyle: "preserve-3d",
                  transform: style.transform,
                  opacity: style.opacity,
                  filter: style.filter,
                  zIndex: style.zIndex,
                  pointerEvents: style.isActive ? "auto" : "none",
                }}
                animate={{
                  transform: style.transform,
                  opacity: style.opacity,
                  filter: style.filter,
                }}
                transition={{
                  duration: 0.6 * speed,
                  ease: [0.25, 0.46, 0.45, 0.94],
                }}
                whileHover={style.isActive ? { scale: 1.02, zIndex: itemCount + 1 } : {}}
                onClick={() => !style.isActive && goToIndex(index)}
              >
              <div
                className="relative h-full w-full overflow-hidden"
                style={{
                  borderRadius,
                  boxShadow: isActive
                    ? "0 30px 60px -15px rgba(220, 176, 74, 0.4), 0 0 0 1px rgba(246, 230, 180, 0.2) inset"
                    : "0 20px 40px -10px rgba(0, 0, 0, 0.5)",
                }}
              >
                <img
                  src={item.image}
                  alt={item.title}
                  loading="lazy"
                  className="h-full w-full object-cover transition-transform duration-700"
                  style={{
                    filter: isActive ? "brightness(0.9) contrast(1.1) saturate(1.1)" : "grayscale(100%) brightness(0.7) contrast(1.2)",
                    transform: isActive ? "scale(1)" : "scale(1.05)",
                  }}
                />

                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent pointer-events-none" />

                {showTitles && isActive && (
                  <div className="absolute bottom-0 left-0 right-0 p-6">
                    <div className="mb-3">
                      <span className="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-black/60 px-3 py-1 text-[11px] font-semibold text-[#f6e6b4] backdrop-blur-md shadow-lg">
                        <span className="h-1.5 w-1.5 rounded-full" style={{ backgroundColor: item.accent }} />
                        <span>{item.tag}</span>
                      </span>
                    </div>
                    <h3 className="font-serif text-xl sm:text-2xl font-bold text-[#fff6e8] leading-tight mb-2">
                      {item.title}
                    </h3>
                    <p className="text-sm text-[#f4e3c9] leading-relaxed">
                      {item.subtitle}
                    </p>
                  </div>
                )}
              </div>

              <div
                className="absolute -bottom-4 left-1/2 -translate-x-1/2 h-2 w-24 bg-gradient-to-r from-transparent via-white/20 to-transparent rounded-full blur-sm pointer-events-none"
              />
            </motion.div>
          )
        })}
      </div>

      {/* Controls */}
      {showControls && (
        <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-6 z-10">
          <button
            onClick={goToPrev}
            className="flex h-12 w-12 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-black/60 text-[#e3c877] backdrop-blur-md hover:bg-[#dcb04a]/20 hover:border-[#f6e6b4]/60 hover:text-[#fff6e8] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#e3c877]"
            aria-label="Previous"
            disabled={!loop && activeIndex === 0}
            style={{ opacity: (!loop && activeIndex === 0) ? 0.4 : 1, pointerEvents: (!loop && activeIndex === 0) ? "none" : "auto" }}
          >
            <ChevronLeft className="h-5 w-5" />
          </button>

          {showCounter && (
            <div className="text-center">
              <p className="font-serif text-2xl font-bold text-[#e3c877]">
                {activeIndex + 1}
                <span className="text-[#f6e6b4]/60 text-lg font-normal"> / {itemCount}</span>
              </p>
            </div>
          )}

          <button
            onClick={goToNext}
            className="flex h-12 w-12 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-black/60 text-[#e3c877] backdrop-blur-md hover:bg-[#dcb04a]/20 hover:border-[#f6e6b4]/60 hover:text-[#fff6e8] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#e3c877]"
            aria-label="Next"
            disabled={!loop && activeIndex === itemCount - 1}
            style={{ opacity: (!loop && activeIndex === itemCount - 1) ? 0.4 : 1, pointerEvents: (!loop && activeIndex === itemCount - 1) ? "none" : "auto" }}
          >
            <ChevronRight className="h-5 w-5" />
          </button>
        </div>
      )}

      {/* Drag hint */}
      {enableDrag && (
        <p className="absolute bottom-3 left-1/2 -translate-x-1/2 text-center text-xs text-[#f6e6b4]/50 uppercase tracking-widest">
          Drag or use arrow keys to navigate
        </p>
      )}
    </div>
  )
}