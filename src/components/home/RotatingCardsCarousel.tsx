"use client"

import { useRef, useEffect, useState, useCallback } from "react"
import { motion } from "framer-motion"

export interface RotatingCard {
  id: string
  image: string
  title: string
  subtitle?: string
  tag?: string
  background?: string
  content?: React.ReactNode
}

interface RotatingCardsCarouselProps {
  cards: RotatingCard[]
  radius?: number
  duration?: number
  cardWidth?: number
  cardHeight?: number
  reverse?: boolean
  draggable?: boolean
  autoPlay?: boolean
  onCardClick?: (card: RotatingCard, index: number) => void
  mouseWheel?: boolean
  className?: string
  cardClassName?: string
  initialRotation?: number
}

const DEFAULT_CARDS: RotatingCard[] = [
  {
    id: "1",
    image: "/images/parallax/bride-portrait.jpg",
    title: "Grace & Elegance",
    subtitle: "Traditional Indian Bride",
    tag: "Royal Saree",
  },
  {
    id: "2",
    image: "/images/parallax/groom-portrait.jpg",
    title: "Regal Heritage",
    subtitle: "Indian Groom",
    tag: "Royal Sherwani",
  },
  {
    id: "3",
    image: "/images/parallax/couple-varmala.jpg",
    title: "Sacred Beginnings",
    subtitle: "Varmala Ceremony",
    tag: "Auspicious Union",
  },
  {
    id: "4",
    image: "/images/parallax/couple-walk.jpg",
    title: "Lifelong Partnership",
    subtitle: "Royal Mandap Walk",
    tag: "Together Forever",
  },
  {
    id: "5",
    image: "/images/parallax/bride-portrait.jpg",
    title: "Eternal Moments",
    subtitle: "Auspicious Celebrations",
    tag: "Timeless Memories",
  },
  {
    id: "6",
    image: "/images/parallax/groom-portrait.jpg",
    title: "Noble Traditions",
    subtitle: "Heritage & Culture",
    tag: "Family Values",
  },
  {
    id: "7",
    image: "/images/parallax/couple-varmala.jpg",
    title: "Divine Connection",
    subtitle: "Spiritual Bond",
    tag: "Soul Mates",
  },
  {
    id: "8",
    image: "/images/parallax/couple-walk.jpg",
    title: "Journey Together",
    subtitle: "Path of Life",
    tag: "Endless Love",
  },
]

export default function RotatingCardsCarousel({
  cards = DEFAULT_CARDS,
  radius = 360,
  duration = 20,
  cardWidth = 160,
  cardHeight = 190,
  reverse = false,
  draggable = true,
  autoPlay = true,
  onCardClick,
  mouseWheel = false,
  className = "",
  cardClassName = "",
  initialRotation = 0,
}: RotatingCardsCarouselProps) {
  const containerRef = useRef<HTMLDivElement>(null)
  const cardsRef = useRef<(HTMLDivElement | null)[]>([])
  const [rotation, setRotation] = useState(initialRotation)
  const [isDragging, setIsDragging] = useState(false)
  const [dragStart, setDragStart] = useState<{ x: number; rotation: number } | null>(null)
  const animationRef = useRef<number | null>(null)
  const lastTimestampRef = useRef<number>(0)
  const isHoveredRef = useRef(false)

  const cardCount = cards.length
  const angleStep = 360 / cardCount

  const animateRotation = useCallback((timestamp: number) => {
    if (!autoPlay || isDragging || isHoveredRef.current) {
      lastTimestampRef.current = timestamp
      animationRef.current = requestAnimationFrame(animateRotation)
      return
    }

    if (lastTimestampRef.current === 0) {
      lastTimestampRef.current = timestamp
      animationRef.current = requestAnimationFrame(animateRotation)
      return
    }

    const delta = timestamp - lastTimestampRef.current
    const degreesPerMs = (360 / (duration * 1000)) * (reverse ? -1 : 1)
    const newRotation = rotation + degreesPerMs * delta

    setRotation(newRotation)
    lastTimestampRef.current = timestamp
    animationRef.current = requestAnimationFrame(animateRotation)
  }, [autoPlay, duration, reverse, rotation, isDragging])

  useEffect(() => {
    if (autoPlay) {
      animationRef.current = requestAnimationFrame(animateRotation)
    }
    return () => {
      if (animationRef.current) {
        cancelAnimationFrame(animationRef.current)
      }
    }
  }, [animateRotation, autoPlay])

  const handleMouseDown = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!draggable) return
    setIsDragging(true)
    setDragStart({ x: e.clientX, rotation })
    e.preventDefault()
  }

  const handleTouchStart = (e: React.TouchEvent<HTMLDivElement>) => {
    if (!draggable) return
    setIsDragging(true)
    setDragStart({ x: e.touches[0].clientX, rotation })
  }

  const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging || !dragStart) return
    const deltaX = e.clientX - dragStart.x
    const rotationChange = (deltaX / radius) * 50
    setRotation(dragStart.rotation + rotationChange)
  }

  const handleTouchMove = (e: TouchEvent) => {
    if (!isDragging || !dragStart) return
    const deltaX = e.touches[0].clientX - dragStart.x
    const rotationChange = (deltaX / radius) * 50
    setRotation(dragStart.rotation + rotationChange)
  }

  const handleMouseUp = () => {
    setIsDragging(false)
    setDragStart(null)
  }

  const handleWheel = (e: React.WheelEvent<HTMLDivElement>) => {
    if (!mouseWheel) return
    e.preventDefault()
    setRotation((prev) => prev + e.deltaY * 0.5 * (reverse ? -1 : 1))
  }

  const handleMouseEnter = () => {
    isHoveredRef.current = true
  }

  const handleMouseLeaveContainer = () => {
    isHoveredRef.current = false
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
  }, [isDragging, radius, dragStart, rotation])

  const getCardTransform = (index: number) => {
    const cardAngle = initialRotation + index * angleStep + rotation
    const rad = (cardAngle * Math.PI) / 180
    const x = Math.sin(rad) * radius
    const z = Math.cos(rad) * radius
    const scale = Math.max(0.3, 1 - Math.abs(Math.sin(rad)) * 0.3)
    const opacity = Math.max(0.4, 1 - Math.abs(Math.sin(rad)) * 0.5)
    const rotateY = -cardAngle + (reverse ? 180 : 0)

    return {
      transform: `translate3d(${x}px, 0, ${z}px) rotateY(${rotateY}deg) scale(${scale})`,
      zIndex: Math.round(100 + Math.cos(rad) * 50),
      opacity,
    }
  }

  return (
    <div
      ref={containerRef}
      className={`relative w-full h-[500px] sm:h-[600px] lg:h-[700px] cursor-grab active:cursor-grabbing ${className}`}
      onMouseDown={handleMouseDown}
      onTouchStart={handleTouchStart}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeaveContainer}
      onWheel={handleWheel}
      style={{ perspective: "1000px", transformStyle: "preserve-3d" }}
      role="region"
      aria-label="Rotating cards carousel"
    >
      <div
        className="absolute inset-0 flex items-center justify-center"
        style={{
          transform: `rotateX(-15deg) rotateY(0deg)`,
          transformStyle: "preserve-3d",
        }}
      >
        {cards.map((card, index) => (
          <motion.div
            key={card.id}
            ref={(el) => { cardsRef.current[index] = el }}
            className={`absolute cursor-pointer transition-all duration-300 ${cardClassName}`}
            style={{
              width: cardWidth,
              height: cardHeight,
              transformStyle: "preserve-3d",
              ...getCardTransform(index),
            }}
            onClick={() => onCardClick?.(card, index)}
            whileHover={{ scale: 1.05, zIndex: 200 }}
            whileTap={{ scale: 0.95 }}
            animate={{
              opacity: getCardTransform(index).opacity,
            }}
            transition={{ duration: 0.3, ease: "easeOut" }}
          >
            <div
              className="relative h-full w-full rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-[#3a0c15] via-[#24060d] to-[#0c0205] border border-[#f6e6b4]/30 backdrop-blur-md"
              style={{
                boxShadow: `
                  0 25px 50px -12px rgba(0, 0, 0, 0.5),
                  0 0 0 1px rgba(246, 230, 180, 0.1) inset,
                  0 0 60px -20px rgba(220, 176, 74, 0.3)
                `,
              }}
            >
              <div className="absolute inset-0 bg-gradient-to-br from-[#dcb04a]/10 via-transparent to-transparent pointer-events-none" />
              
              <img
                src={card.image}
                alt={card.title}
                loading="lazy"
                className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                style={{
                  filter: "brightness(0.85) contrast(1.1) saturate(1.2)",
                }}
              />

              <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none" />

              <div className="absolute bottom-0 left-0 right-0 p-4 sm:p-5">
                <div className="flex items-center gap-2 mb-2">
                  <span className="inline-flex items-center gap-1 rounded-full border border-[#f6e6b4]/50 bg-black/60 px-2.5 py-0.5 text-[10px] font-semibold text-[#f6e6b4] backdrop-blur-md shadow-lg">
                    <span className="h-1.5 w-1.5 rounded-full bg-[#e3c877]" />
                    <span>{card.tag || "Featured"}</span>
                  </span>
                </div>
                <h3 className="text-lg sm:text-xl font-bold text-[#fff6e8] leading-tight">
                  {card.title}
                </h3>
                {card.subtitle && (
                  <p className="text-sm text-[#f6e6b4]/80 mt-1 font-medium">
                    {card.subtitle}
                  </p>
                )}
              </div>

              <div className="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div className="h-8 w-8 rounded-full bg-black/50 backdrop-blur-md border border-[#f6e6b4]/30 flex items-center justify-center hover:bg-[#dcb04a]/20 hover:border-[#f6e6b4]/50 transition-all">
                  <svg className="h-4 w-4 text-[#f6e6b4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </div>
              </div>
            </div>

            <div
              className="absolute -bottom-3 left-1/2 -translate-x-1/2 h-2 w-20 bg-gradient-to-r from-transparent via-[#dcb04a]/40 to-transparent rounded-full blur-sm pointer-events-none"
              style={{ filter: "blur(4px)" }}
            />
          </motion.div>
        ))}
      </div>

      <div className="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 text-center pointer-events-none">
        <div className="h-px w-20 bg-gradient-to-r from-transparent via-[#f6e6b4]/40 to-transparent" />
        <span className="text-xs text-[#f6e6b4]/60 font-medium tracking-widest uppercase">
          Drag to explore
        </span>
        <div className="h-px w-20 bg-gradient-to-r from-transparent via-[#f6e6b4]/40 to-transparent" />
      </div>

      <div
        className="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent pointer-events-none"
        aria-hidden="true"
      />
    </div>
  )
}