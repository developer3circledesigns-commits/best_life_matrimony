import { motion } from "motion/react"
import { Link } from "react-router-dom"
import { Home } from "lucide-react"
import { Button } from "@/components/ui/button"

export default function NotFoundPage() {
  return (
    <section className="mx-auto flex w-full max-w-6xl flex-col items-center justify-center px-4 py-24 text-center sm:px-6">
      <motion.div
        initial={{ opacity: 0, scale: 0.96 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 0.4 }}
        className="rounded-3xl border border-[#f6e6b4]/20 bg-black/35 p-8 sm:p-12 backdrop-blur-md shadow-2xl"
      >
        <p className="font-serif text-7xl font-bold tracking-tight text-[#e3c877]">
          404
        </p>
        <h1 className="mt-4 text-2xl font-semibold text-[#fff6e8]">Page not found</h1>
        <p className="mt-2 text-[#f3e6d8]/80">
          The page you are looking for doesn't exist or has been moved.
        </p>
        <Button asChild size="lg" className="mt-8 rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 font-semibold text-[#3a0c15] shadow-lg">
          <Link to="/">
            <Home className="size-4" /> Back to Home
          </Link>
        </Button>
      </motion.div>
    </section>
  )
}