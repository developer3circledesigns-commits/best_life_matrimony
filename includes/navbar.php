<?php
require_once __DIR__ . '/config.php';
// Ensure admin helpers (is_admin, current_user, etc.) are available for the
// account-dropdown Admin link. db.php requires config.php via require_once,
// so this is safe and never double-loads.
require_once __DIR__ . '/db.php';
$navUserId = $_SESSION['user_id'] ?? null;
?>
<!-- Navbar — mirrors Navbar.tsx: sticky with backdrop-blur, transparent border, hide-on-scroll removed for PHP but scroll hiding via JS -->
<header id="navbar" class="navbar-root sticky top-0 z-50 border-b border-black/5 bg-white backdrop-blur-sm overflow-x-clip transition-transform duration-300" style="z-index: 9999;">
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
        if ($navUserId && $item['php'] === 'profile.php') continue;
        $isActive = isActiveRoute($currentPath, $item['href']);
      ?>
        <a href="./<?php echo htmlspecialchars($item['php']); ?>"
           class="text-sm font-medium transition-colors <?php echo $isActive ? 'text-[#8a4a2f] font-semibold' : 'text-[#3a0c15]/70 hover:text-[#8a4a2f]'; ?>">
          <?php echo htmlspecialchars($item['label']); ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <!-- Right: auth / profile -->
    <div class="flex items-center gap-3">
      <?php if ($navUserId): ?>
        <a href="./messages.php" class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/10 bg-black/5 text-[#3a0c15] hover:bg-black/10 transition-colors" title="Messages" aria-label="Messages">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </a>
        <div class="relative">
          <button type="button" id="notifBell" class="hidden sm:inline-flex relative h-9 w-9 items-center justify-center rounded-full border border-black/10 bg-black/5 text-[#3a0c15] hover:bg-black/10 transition-colors" title="Notifications" aria-label="Notifications">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span id="notifBadge" class="absolute -top-1 -right-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-[#8b0000] px-1 text-[10px] font-bold text-white">0</span>
          </button>
        </div>
        <a href="./profile.php" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] text-[#3a0c15] shadow-md hover:brightness-110 transition-all" title="Profile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </a>
        <div class="relative">
          <button type="button" id="acctMenuBtn" class="hidden sm:inline-flex text-sm font-medium text-[#3a0c15]/70 hover:text-[#8a4a2f] transition-colors">Account <svg class="inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg></button>
          <div id="acctMenu" class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-black/5 bg-white p-2 shadow-xl z-50" style="z-index: 10000;">
            <a href="./network.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#3a0c15] hover:bg-black/5">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              My Network
            </a>
            <a href="./who_viewed_me.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#3a0c15] hover:bg-black/5">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Who Viewed Me
            </a>
            <a href="./verify.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#3a0c15] hover:bg-black/5">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/><path d="m9 12 2 2 4-4"/></svg>
              Verify Account
            </a>
            <?php if (is_admin()): ?>
            <a href="./admin/index.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-[#3a0c15] hover:bg-black/5">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
              Admin Panel
            </a>
            <?php endif; ?>
            <a href="./change_password.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#3a0c15] hover:bg-black/5">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Change Password
            </a>
            <a href="./account_delete.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-700 hover:bg-red-50">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg>
              Delete Account
            </a>
            <a href="./logout.php" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-[#3a0c15] hover:bg-black/5">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
              Logout
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="./login.php" class="hidden sm:inline-flex text-sm font-medium text-[#3a0c15]/70 hover:text-[#8a4a2f] transition-colors">Login</a>
        <a href="./register.php" class="inline-flex h-8 items-center justify-center rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-5 text-sm font-semibold text-[#3a0c15] shadow-md hover:brightness-110 transition-all">Register Now</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Mobile menu panel -->
  <div id="mobileMenu" class="sm:hidden hidden border-t border-black/5 bg-white backdrop-blur-lg">
    <nav class="mx-auto max-w-6xl px-4 py-6 flex flex-col gap-1" aria-label="Mobile">
      <?php foreach ($siteConfig['navItems'] as $item):
        if ($navUserId && $item['php'] === 'profile.php') continue;
        $isActive = isActiveRoute($currentPath, $item['href']);
      ?>
        <a href="./<?php echo htmlspecialchars($item['php']); ?>"
           class="w-full py-2 text-lg <?php echo $isActive ? 'font-bold text-[#8a4a2f]' : 'text-[#3a0c15]/80'; ?>">
          <?php echo htmlspecialchars($item['label']); ?>
        </a>
      <?php endforeach; ?>
      <?php if ($navUserId): ?>
        <div class="pt-4 mt-2 border-t border-black/5 flex items-center gap-4">
          <?php if (is_admin()): ?><a href="./admin/index.php" class="text-sm font-semibold text-[#8a4a2f]">Admin Panel</a><?php endif; ?>
          <a href="./network.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">My Network</a>
          <a href="./who_viewed_me.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">Who Viewed Me</a>
          <a href="./messages.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">Messages</a>
          <a href="./verify.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">Verify Account</a>
          <a href="./change_password.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">Change Password</a>
          <a href="./account_delete.php" class="text-sm text-red-700">Delete Account</a>
          <a href="./about.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">About Us</a>
          <a href="./logout.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">Logout</a>
        </div>
      <?php else: ?>
        <div class="pt-4 mt-2 border-t border-black/5 flex items-center gap-4">
          <a href="./login.php" class="text-sm font-medium text-[#3a0c15]/70">Login</a>
          <a href="./about.php" class="text-sm text-[#3a0c15]/60 hover:text-[#8a4a2f]">About Us</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>
