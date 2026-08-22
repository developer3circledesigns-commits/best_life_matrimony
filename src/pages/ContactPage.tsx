import { motion } from "motion/react"
import { Link } from "react-router-dom"
import { Button } from "@/components/ui/button"

export default function ContactPage() {
  return (
    <section className="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
      <motion.div
        initial={{ opacity: 0, y: 16 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        className="max-w-3xl rounded-3xl border border-[#f6e6b4]/20 bg-black/35 p-8 sm:p-12 backdrop-blur-md shadow-2xl"
      >
        <h1 className="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">
          Contact us
        </h1>
        <p className="mt-6 text-lg text-[#f3e6d8]/90">
          Get in touch with the BestLife Matrimony team.
        </p>
        <Button asChild size="lg" className="mt-8 rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 font-semibold text-[#3a0c15] shadow-lg">
          <Link to="/">Back to Home</Link>
        </Button>
      </motion.div>
    </section>
  )
}