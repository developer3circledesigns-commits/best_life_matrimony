import { useState } from "react"
import { Link, NavLink, useLocation } from "react-router-dom"
import {
  Navbar,
  NavbarBrand,
  NavbarContent,
  NavbarItem,
  NavbarMenuToggle,
  NavbarMenu,
  NavbarMenuItem,
  Button,
} from "@heroui/react"
import { Heart } from "lucide-react"
import { siteConfig } from "@/config/site"
import { cn } from "@/lib/utils"

export default function TopNavbar() {
  const [isMenuOpen, setIsMenuOpen] = useState(false)
  const { pathname } = useLocation()

  return (
    <Navbar
      isMenuOpen={isMenuOpen}
      onMenuOpenChange={setIsMenuOpen}
      shouldHideOnScroll
      classNames={{
        wrapper: "max-w-6xl",
        base: "bg-transparent backdrop-blur-sm border-b border-[#f6e6b4]/15 overflow-x-clip sticky top-0 z-50",
      }}
    >
      <NavbarContent>
        <NavbarMenuToggle aria-label={isMenuOpen ? "Close menu" : "Open menu"} className="sm:hidden text-[#f6e6b4]" />
        <NavbarBrand>
          <Link to="/" className="flex items-center gap-2">
            <Heart className="size-5 text-[#e3c877]" aria-hidden />
            <span className="font-serif text-lg font-semibold tracking-tight text-[#fff6e8]">
              {siteConfig.name}
            </span>
          </Link>
        </NavbarBrand>
      </NavbarContent>

      <NavbarContent className="hidden gap-6 sm:flex" justify="center">
        {siteConfig.navItems.map((item) => (
          <NavbarItem key={item.href} isActive={pathname === item.href}>
            <NavLink
              to={item.href}
              end={item.href === "/"}
              className={({ isActive }) =>
                cn(
                  "text-sm font-medium transition-colors",
                  isActive
                    ? "text-[#e3c877] font-semibold"
                    : "text-[#fff6e8]/75 hover:text-[#e3c877]"
                )
              }
            >
              {item.label}
            </NavLink>
          </NavbarItem>
        ))}
      </NavbarContent>

      <NavbarContent justify="end">
        <NavbarItem className="hidden sm:flex">
          <Link to="/register" className="text-sm font-medium text-[#fff6e8]/80 hover:text-[#e3c877] transition-colors">
            Login
          </Link>
        </NavbarItem>
        <NavbarItem>
          <Button
            as={Link}
            to="/register"
            size="sm"
            className="rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] font-semibold text-[#3a0c15] shadow-md"
          >
            Register Now
          </Button>
        </NavbarItem>
      </NavbarContent>

      <NavbarMenu className="bg-[#2c0710]/95 backdrop-blur-lg pt-6">
        {siteConfig.navItems.map((item) => (
          <NavbarMenuItem key={item.href}>
            <NavLink
              to={item.href}
              end={item.href === "/"}
              onClick={() => setIsMenuOpen(false)}
              className={({ isActive }) =>
                cn("w-full text-lg py-2", isActive ? "font-bold text-[#e3c877]" : "text-[#fff6e8]/80")
              }
            >
              {item.label}
            </NavLink>
          </NavbarMenuItem>
        ))}
      </NavbarMenu>
    </Navbar>
  )
}