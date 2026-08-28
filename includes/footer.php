<footer class="relative border-t border-[#3a0c15]/10 bg-[#fdf9f1] pt-16 pb-12 text-[#3a0c15]">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-10 pb-12 border-b border-[#3a0c15]/10">
      <!-- Brand -->
      <div class="lg:col-span-2 space-y-4">
        <a href="./index.php" class="inline-flex items-center gap-2.5">
          <img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-14 lg:h-16 w-auto object-contain" loading="lazy" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex'">
          <span class="hidden font-serif text-2xl font-bold tracking-tight text-[#3a0c15] items-center gap-2.5"><span class="flex h-9 w-9 items-center justify-center rounded-full border border-[#3a0c15]/15 bg-[#3a0c15]/5 text-[#8a4a2f]"><svg class="h-5 w-5 fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2.08C10.5 3.5 9.26 3 7.5 3A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" fill="currentColor"/></svg></span>BestLife Matrimony</span>
        </a>
        <p class="text-sm text-[#3a0c15]/70 leading-relaxed max-w-sm">Find a meaningful connection. Build a beautiful future.</p>
      </div>

      <!-- Quick Links -->
      <div class="space-y-3">
        <h4 class="font-serif text-sm font-bold uppercase tracking-wider text-[#8a4a2f]">Quick Links</h4>
        <ul class="space-y-2 text-sm text-[#3a0c15]/70">
          <li><a href="./index.php" class="hover:text-[#8a4a2f] transition-colors">Home</a></li>
          <li><a href="./matches.php" class="hover:text-[#8a4a2f] transition-colors">Profile Matches</a></li>
          <li><a href="./advertise.php" class="hover:text-[#8a4a2f] transition-colors">Advertise With Us</a></li>
          <li><a href="./contact.php" class="hover:text-[#8a4a2f] transition-colors">Contact Us</a></li>
        </ul>
      </div>

      <!-- For Members -->
      <div class="space-y-3">
        <h4 class="font-serif text-sm font-bold uppercase tracking-wider text-[#8a4a2f]">For Members</h4>
        <ul class="space-y-2 text-sm text-[#3a0c15]/70">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <li><a href="./profile.php" class="hover:text-[#8a4a2f] transition-colors">My Profile</a></li>
            <li><a href="./matches.php" class="hover:text-[#8a4a2f] transition-colors">Browse Profiles</a></li>
            <li><a href="./logout.php" class="hover:text-[#8a4a2f] transition-colors">Logout</a></li>
            <?php if (function_exists('is_admin') && is_admin()): ?>
              <li><a href="./admin/index.php" class="font-semibold text-[#8a4a2f] hover:text-[#3a0c15] transition-colors">Admin Panel</a></li>
            <?php endif; ?>
          <?php else: ?>
            <li><a href="./register.php" class="hover:text-[#8a4a2f] transition-colors">Register Now</a></li>
            <li><a href="./login.php" class="hover:text-[#8a4a2f] transition-colors">Login</a></li>
            <li><a href="./matches.php" class="hover:text-[#8a4a2f] transition-colors">Browse Profiles</a></li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Information -->
      <div class="space-y-3">
        <h4 class="font-serif text-sm font-bold uppercase tracking-wider text-[#8a4a2f]">Information</h4>
        <ul class="space-y-2 text-sm text-[#3a0c15]/70">
          <li><a href="./about.php" class="hover:text-[#8a4a2f] transition-colors">About Us</a></li>
          <li><a href="./privacy-policy.php" class="hover:text-[#8a4a2f] transition-colors">Privacy Policy</a></li>
          <li><a href="./terms.php" class="hover:text-[#8a4a2f] transition-colors">Terms & Conditions</a></li>
          <li><a href="./safety-tips.php" class="hover:text-[#8a4a2f] transition-colors">Safety Tips</a></li>
          <li><a href="./contact.php" class="hover:text-[#8a4a2f] transition-colors">Help & Support</a></li>
        </ul>
      </div>

      <!-- Contact + Follow -->
      <div class="space-y-5 lg:col-span-2">
        <div class="space-y-3">
          <h4 class="font-serif text-sm font-bold uppercase tracking-wider text-[#8a4a2f]">Contact</h4>
          <ul class="space-y-2.5 text-xs sm:text-sm text-[#3a0c15]/70">
            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-[#8a4a2f] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.18 2 2 0 0 1 4.08 2h3a2 2 0 0 1 2 1.72c.12 1.33.4 2.63.84 3.89a2 2 0 0 1-.58 2.11l-1.34 1.34a16 16 0 0 0 6 6l1.34-1.34a2 2 0 0 1 2.11-.58c1.26.44 2.56.72 3.89.84A2 2 0 0 1 22 16.92Z"/></svg><span class="whitespace-nowrap">Phone: <a href="tel:+917338877275" class="hover:text-[#8a4a2f] hover:underline">+91 7338877275</a> / <a href="tel:+917200005622" class="hover:text-[#8a4a2f] hover:underline">+91 7200005622</a></span></li>
            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-[#8a4a2f] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg><span class="whitespace-nowrap">Email: info@bestlifematrimony.com</span></li>
            <li class="flex items-start gap-2"><svg class="h-4 w-4 text-[#8a4a2f] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><a href="https://www.google.com/maps?q=13.054376,80.1927475&z=17&hl=en" target="_blank" rel="noopener noreferrer" class="hover:text-[#8a4a2f] hover:underline">No:2(24/1) kaliamman koil Street,<br>virugambakkam main road, <span class="whitespace-nowrap">chennai-600092.</span><br>Land mark Sundar C mahall</a></li>
          </ul>
          <!-- Footer Map -->
          <div class="mt-4 overflow-hidden rounded-none border border-[#3a0c15]/10" style="border-radius:0">
            <iframe src="https://www.google.com/maps?q=13.054376,80.1927475&z=17&hl=en&output=embed" width="100%" height="180" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="BestLife Matrimony Location"></iframe>
            <a href="https://www.google.com/maps?q=13.054376,80.1927475&z=17&hl=en" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-1.5 bg-[#3a0c15] py-2 text-xs font-semibold text-white hover:bg-[#5a1a25] transition-colors"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg> View on Google Maps</a>
          </div>
        </div>
        <div class="space-y-3">
          <h4 class="font-serif text-sm font-bold uppercase tracking-wider text-[#8a4a2f]">Follow Us</h4>
          <div class="flex items-center gap-3">
            <a href="https://facebook.com" target="_blank" rel="noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#3a0c15]/10 bg-white text-[#8a4a2f] hover:bg-[#3a0c15] hover:text-white hover:border-[#3a0c15] transition-all" aria-label="Facebook">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 8h3V3h-3c-2.76 0-5 2.24-5 5v3H6v4h3v5h4v-5h3l1-4h-4V8c0-.55.45-1 1-1Z"/></svg>
            </a>
            <a href="https://instagram.com" target="_blank" rel="noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#3a0c15]/10 bg-white text-[#8a4a2f] hover:bg-[#3a0c15] hover:text-white hover:border-[#3a0c15] transition-all" aria-label="Instagram">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
            </a>
            <a href="https://youtube.com" target="_blank" rel="noreferrer" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#3a0c15]/10 bg-white text-[#8a4a2f] hover:bg-[#3a0c15] hover:text-white hover:border-[#3a0c15] transition-all" aria-label="YouTube">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M23 12c0 3.04-.82 5.37-2.45 7-1.64 1.64-3.97 2.45-7 2.45h-3c-3.04 0-5.37-.82-7-2.45C2.82 17.37 2 15.04 2 12s.82-5.37 2.45-7C6.09 3.36 8.42 2.55 11.45 2.55h3c3.04 0 5.37.82 7 2.45C22.18 6.63 23 8.96 23 12ZM10 16.5l6-4.5-6-4.5v9Z"/></svg>
            </a>
          </div>
          <p class="text-xs text-[#3a0c15]/50">Facebook | Instagram | YouTube</p>
        </div>
      </div>
    </div>

    <div class="pt-8 flex flex-col items-center gap-2 text-xs text-[#3a0c15]/60 text-center border-t border-[#3a0c15]/10 mt-2">
      <p>© 2026 BestLife Matrimony. All Rights Reserved.</p>
    </div>
  </div>
</footer>
<?php // closing wrappers opened in header ?>
    </div>
  </div>
