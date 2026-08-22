import { lazy, Suspense, type ReactNode } from "react"
import {
  createBrowserRouter,
  RouterProvider,
  Navigate,
} from "react-router-dom"
import RootLayout from "@/layouts/RootLayout"
import HomePage from "@/pages/HomePage"

const AboutPage = lazy(() => import("@/pages/AboutPage"))
const NotFoundPage = lazy(() => import("@/pages/NotFoundPage"))
const MatchesPage = lazy(() => import("@/pages/MatchesPage"))
const AdvertisePage = lazy(() => import("@/pages/AdvertisePage"))
const ContactPage = lazy(() => import("@/pages/ContactPage"))
const RegisterPage = lazy(() => import("@/pages/RegisterPage"))

function withSuspense(node: ReactNode) {
  return <Suspense fallback={<div className="p-8 text-center text-sm text-muted-foreground">Loading...</div>}>{node}</Suspense>
}

const router = createBrowserRouter([
  {
    path: "/",
    element: <RootLayout />,
    children: [
      { index: true, element: <HomePage /> },
      { path: "about", element: withSuspense(<AboutPage />) },
      { path: "matches", element: withSuspense(<MatchesPage />) },
      { path: "advertise", element: withSuspense(<AdvertisePage />) },
      { path: "contact", element: withSuspense(<ContactPage />) },
      { path: "register", element: withSuspense(<RegisterPage />) },
      { path: "404", element: withSuspense(<NotFoundPage />) },
      { path: "*", element: <Navigate to="/404" replace /> },
    ],
  },
])

export default function AppRouter() {
  return <RouterProvider router={router} />
}
