export const siteConfig = {
  name: "BestLife Matrimony",
  description:
    "A React + TypeScript matrimony platform built with Vite, Tailwind CSS, shadcn/ui and Motion.",
  url: "https://yourdomain.com",
  navItems: [
    { label: "Home", href: "/" },
    { label: "Profile Matches", href: "/matches" },
    { label: "Advertise with us", href: "/advertise" },
    { label: "Contact", href: "/contact" },
  ],
} as const

export type SiteConfig = typeof siteConfig
