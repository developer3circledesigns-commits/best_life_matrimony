<?php require_once __DIR__ . '/config.php'; ?>
<!-- Navbar — mirrors Navbar.tsx: sticky with backdrop-blur, transparent border, hide-on-scroll removed for PHP but scroll hiding via JS -->
<header id="navbar" class="navbar-root sticky top-0 z-50 border-b border-black/5 bg-white backdrop-blur-sm overflow-x-clip transition-transform duration-300">
  <div class="mx-auto flex h-24 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
    <!-- Left: toggle + brand -->
    <div class="flex items-center gap-3">
      <button id="menuToggle" class="sm:hidden inline-flex h-9 w-9 items-center justify-center rounded-md border border-black/10 bg-black/5 text-[#3a0c15] hover:bg-black/10 transition-colors" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
        <svg class="icon-menu" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        <svg class="icon-close hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M6 18L18 6"/></svg>
      </button>
      <a href="./index.php" class="flex items-center gap-2">
        <img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="w-48 lg:w-64 h-auto object-contain" loading="eager" decoding="async" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline'">
        <span class="font-serif text-lg font-semibold tracking-tight text-[#3a0c15]" style="display:none"><?php echo htmlspecialchars($siteConfig['name']); ?></span>
      </a>
    </div>

    <!-- Center: desktop nav -->
    <nav class="hidden sm:flex items-center gap-6" aria-label="Primary">
      <?php foreach ($siteConfig['navItems'] as $item): 
        $isActive = isActiveRoute($currentPath, $item['href']);
      ?>
        <a href="./<?php echo htmlspecialchars($item['php']); ?>"
           class="text-sm font-medium transition-colors <?php echo $isActive ? 'text-[#8a4a2f] font-semibold' : 'text-[#3a0c15]/70 hover:text-[#8a4a2f]'; ?>">
          <?php echo htmlspecialchars($item['label']); ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Right: auth -->
    <div class="flex items-center gap-3">
      <a href="./register.php" class="hidden sm:inline-flex text-sm font-medium text-[#3a0c15]/70 hover:text-[#8a4a2f] transition-colors">Login</a>
      <a href="./register.php" class="inline-flex h-8 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-5 text-sm font-semibold text-[#3a0c15] shadow-md hover:brightness-110 transition-all">Register Now</a>
    </div>
  </div>

  <!-- Mobile menu panel -->
  <div id="mobileMenu" class="sm:hidden hidden border-t border-black/5 bg-white backdrop-blur-lg">
    <nav class="mx-auto max-w-6xl px-4 py-6 flex flex-col gap-1" aria-label="Mobile">
      <?php foreach ($siteConfig['navItems'] as $item):
        $isActive = isActiveRoute($currentPath, $item['href']);
      ?>
        <a href="./<?php echo htmlspecialchars($item['php']); ?>"
           class="w-full py-2 text-lg <?php echo $isActive ? 'font-bold text-[#8a4a2f]' : 'text-[#3a0c15]/80'; ?>">
          <?php echo htmlspecialchars($item['label']); ?>
        </a>
      <?php endforeach; ?>
      <div class="pt-4 mt-2 border-t border-black/5 flex items-center gap-4">
        <a href="./register.php" class="text-sm font-medium text-[#3a0c15]/70">Login</a>
        <a href="./about.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">About Us</a>
      </div>
    </nav>
  </div>
</header>
