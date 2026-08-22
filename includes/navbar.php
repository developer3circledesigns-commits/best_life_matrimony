<?php require_once __DIR__ . '/config.php'; ?>
<!-- Navbar — mirrors Navbar.tsx: sticky with backdrop-blur, transparent border, hide-on-scroll removed for PHP but scroll hiding via JS -->
<header id="navbar" class="navbar-root sticky top-0 z-50 border-b border-[#f6e6b4]/15 bg-transparent backdrop-blur-sm overflow-x-clip transition-transform duration-300">
  <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
    <!-- Left: toggle + brand -->
    <div class="flex items-center gap-3">
      <button id="menuToggle" class="sm:hidden inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/10 bg-white/5 text-[#f6e6b4] hover:bg-white/10 transition-colors" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
        <svg class="icon-menu" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        <svg class="icon-close hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M6 18L18 6"/></svg>
      </button>
      <a href="./index.php" class="flex items-center gap-2">
        <svg class="size-5 text-[#e3c877]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2.08C10.5 3.5 9.26 3 7.5 3A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        <span class="font-serif text-lg font-semibold tracking-tight text-[#fff6e8]"><?php echo htmlspecialchars($siteConfig['name']); ?></span>
      </a>
    </div>

    <!-- Center: desktop nav -->
    <nav class="hidden sm:flex items-center gap-6" aria-label="Primary">
      <?php foreach ($siteConfig['navItems'] as $item): 
        $isActive = isActiveRoute($currentPath, $item['href']);
      ?>
        <a href="./<?php echo htmlspecialchars($item['php']); ?>"
           class="text-sm font-medium transition-colors <?php echo $isActive ? 'text-[#e3c877] font-semibold' : 'text-[#fff6e8]/75 hover:text-[#e3c877]'; ?>">
          <?php echo htmlspecialchars($item['label']); ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Right: auth -->
    <div class="flex items-center gap-3">
      <a href="./register.php" class="hidden sm:inline-flex text-sm font-medium text-[#fff6e8]/80 hover:text-[#e3c877] transition-colors">Login</a>
      <a href="./register.php" class="inline-flex h-8 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-5 text-sm font-semibold text-[#3a0c15] shadow-md hover:brightness-110 transition-all">Register Now</a>
    </div>
  </div>

  <!-- Mobile menu panel -->
  <div id="mobileMenu" class="sm:hidden hidden border-t border-white/10 bg-[#2c0710]/95 backdrop-blur-lg">
    <nav class="mx-auto max-w-6xl px-4 py-6 flex flex-col gap-1" aria-label="Mobile">
      <?php foreach ($siteConfig['navItems'] as $item):
        $isActive = isActiveRoute($currentPath, $item['href']);
      ?>
        <a href="./<?php echo htmlspecialchars($item['php']); ?>"
           class="w-full py-2 text-lg <?php echo $isActive ? 'font-bold text-[#e3c877]' : 'text-[#fff6e8]/80'; ?>">
          <?php echo htmlspecialchars($item['label']); ?>
        </a>
      <?php endforeach; ?>
      <div class="pt-4 mt-2 border-t border-white/10 flex items-center gap-4">
        <a href="./register.php" class="text-sm font-medium text-[#fff6e8]/80">Login</a>
        <a href="./about.php" class="text-sm text-[#fff6e8]/60 hover:text-[#f6e6b4]">About Us</a>
      </div>
    </nav>
  </div>
</header>
