import { HeroUIProvider } from "@heroui/react"
import AppRouter from "@/router"

export default function App() {
  return (
    <HeroUIProvider>
      <AppRouter />
    </HeroUIProvider>
  )
}