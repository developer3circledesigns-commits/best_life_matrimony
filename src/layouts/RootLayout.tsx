import { useEffect } from "react"
import { Outlet, ScrollRestoration } from "react-router-dom"
import Lenis from "lenis"
import Navbar from "@/components/layout/Navbar"
import Footer from "@/components/layout/Footer"
import { TooltipProvider } from "@/components/ui/tooltip"
import { Toaster } from "@/components/ui/sonner"

declare global {
  interface Window {
    __lenis?: Lenis
  }
}

export default function RootLayout() {
  useEffect(() => {
    const lenis = new Lenis({
      duration: 1.2,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: "vertical",
      gestureOrientation: "vertical",
      smoothWheel: true,
      touchMultiplier: 2,
      syncTouch: true,
      syncTouchLerp: 0.075,
      lerp: 0.1,
      infinite: false,
    })

    // Expose Lenis globally for other components
    window.__lenis = lenis

    function raf(time: number) {
      lenis.raf(time)
      requestAnimationFrame(raf)
    }

    const rafId = requestAnimationFrame(raf)

    return () => {
      cancelAnimationFrame(rafId)
      lenis.destroy()
      window.__lenis = undefined
    }
  }, [])

  return (
    <TooltipProvider>
      <div className="relative min-h-svh bg-[#0c0205] text-[#fff6e8] selection:bg-[#dcb04a] selection:text-[#3a0c15]">
        {/* Dynamic Foreground Page Content */}
        <div className="relative z-10 flex min-h-svh flex-col bg-transparent">
          <ScrollRestoration />
          <Navbar />
          <main className="flex-1 bg-transparent">
            <Outlet />
          </main>
          <Footer />
          <Toaster />
        </div>
      </div>
    </TooltipProvider>
  )
}