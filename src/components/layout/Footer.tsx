import { Link } from "react-router-dom"
import { Heart, Phone, Mail, MapPin, Globe, Share2 } from "lucide-react"

export default function Footer() {
  return (
    <footer className="relative border-t border-[#f6e6b4]/20 bg-black/75 pt-16 pb-12 backdrop-blur-2xl text-[#fff6e8]">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-white/10">
          {/* Col 1: Brand & Promise */}
          <div className="lg:col-span-2 space-y-4">
            <Link to="/" className="inline-flex items-center gap-2.5">
              <div className="flex h-9 w-9 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-[#dcb04a]/20 text-[#e3c877]">
                <Heart className="h-5 w-5 fill-current" />
              </div>
              <span className="font-serif text-2xl font-bold tracking-tight text-[#fff6e8]">
                BestLife Matrimony
              </span>
            </Link>
            <p className="text-sm text-[#f4e3c9]/80 leading-relaxed max-w-sm">
              Find a meaningful connection. Build a beautiful future. Bringing genuine hearts and families together with trust, respect, and privacy.
            </p>
            <div className="flex items-center gap-3 pt-2">
              <a
                href="https://facebook.com"
                target="_blank"
                rel="noreferrer"
                className="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/5 text-[#f6e6b4] hover:bg-[#e3c877] hover:text-[#3a0c15] hover:border-[#e3c877] transition-all"
                aria-label="Facebook"
              >
                <Globe className="h-4 w-4" />
              </a>
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noreferrer"
                className="flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/5 text-[#f6e6b4] hover:bg-[#e3c877] hover:text-[#3a0c15] hover:border-[#e3c877] transition-all"
                aria-label="Instagram"
              >
                <Share2 className="h-4 w-4" />
              </a>
            </div>
          </div>

          {/* Col 2: Quick Links */}
          <div className="space-y-3">
            <h4 className="font-serif text-sm font-bold uppercase tracking-wider text-[#e3c877]">
              Quick Links
            </h4>
            <ul className="space-y-2 text-sm text-[#fff6e8]/80">
              <li>
                <Link to="/" className="hover:text-[#f6e6b4] transition-colors">Home</Link>
              </li>
              <li>
                <Link to="/matches" className="hover:text-[#f6e6b4] transition-colors">Profile Matches</Link>
              </li>
              <li>
                <Link to="/advertise" className="hover:text-[#f6e6b4] transition-colors">Advertise With Us</Link>
              </li>
              <li>
                <Link to="/contact" className="hover:text-[#f6e6b4] transition-colors">Contact Us</Link>
              </li>
              <li>
                <Link to="/about" className="hover:text-[#f6e6b4] transition-colors">About Us</Link>
              </li>
            </ul>
          </div>

          {/* Col 3: For Members */}
          <div className="space-y-3">
            <h4 className="font-serif text-sm font-bold uppercase tracking-wider text-[#e3c877]">
              For Members
            </h4>
            <ul className="space-y-2 text-sm text-[#fff6e8]/80">
              <li>
                <Link to="/register" className="hover:text-[#f6e6b4] transition-colors">Register Now</Link>
              </li>
              <li>
                <Link to="/login" className="hover:text-[#f6e6b4] transition-colors">Login</Link>
              </li>
              <li>
                <Link to="/matches" className="hover:text-[#f6e6b4] transition-colors">Browse Profiles</Link>
              </li>
              <li>
                <Link to="/register" className="hover:text-[#f6e6b4] transition-colors">My Profile</Link>
              </li>
            </ul>
          </div>

          {/* Col 4: Contact & Location */}
          <div className="space-y-3">
            <h4 className="font-serif text-sm font-bold uppercase tracking-wider text-[#e3c877]">
              Contact Us
            </h4>
            <ul className="space-y-2.5 text-xs sm:text-sm text-[#fff6e8]/80">
              <li className="flex items-center gap-2">
                <Phone className="h-4 w-4 text-[#e3c877] shrink-0" />
                <span>+91 98765 43210</span>
              </li>
              <li className="flex items-center gap-2">
                <Mail className="h-4 w-4 text-[#e3c877] shrink-0" />
                <span className="truncate">info@bestlifematrimony.com</span>
              </li>
              <li className="flex items-start gap-2">
                <MapPin className="h-4 w-4 text-[#e3c877] shrink-0 mt-0.5" />
                <span>Chennai, Tamil Nadu, India</span>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom Copyright & Policy Links */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#fff6e8]/60">
          <p>© 2026 BestLife Matrimony. All Rights Reserved.</p>
          <div className="flex flex-wrap items-center gap-4">
            <Link to="/contact" className="hover:text-[#f6e6b4] transition-colors">Privacy Policy</Link>
            <span>•</span>
            <Link to="/contact" className="hover:text-[#f6e6b4] transition-colors">Terms & Conditions</Link>
            <span>•</span>
            <Link to="/contact" className="hover:text-[#f6e6b4] transition-colors">Safety & Security</Link>
            <span>•</span>
            <Link to="/contact" className="hover:text-[#f6e6b4] transition-colors">Refund Policy</Link>
          </div>
        </div>
      </div>
    </footer>
  )
}