<!-- Lenis Smooth Scroll (single instance) -->
<script src="https://cdn.jsdelivr.net/npm/lenis@1.3.26/dist/lenis.min.js"></script>
<script src="<?php echo asset('js/lenis.js'); ?>"></script>
<script src="<?php echo asset('js/navigation.js'); ?>"></script>
<script src="<?php echo asset('js/parallax.js'); ?>"></script>
<script src="<?php echo asset('js/animations.js'); ?>"></script>
<script src="<?php echo asset('js/main.js'); ?>"></script>
<script>
  (function () {
    /* Logout confirmation (UX #2) */
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href]');
      if (!a) return;
      if (/logout\.php/i.test(a.getAttribute('href'))) {
        if (!window.confirm('Are you sure you want to log out?')) {
          e.preventDefault();
          e.stopPropagation();
        }
      }
    });
  })();
</script>
<style>
  /* Back-to-top button (UX #6) — shared across pages — stacked above WhatsApp */
  #backToTop {
    position: fixed;
    right: 20px;
    bottom: 88px; /* 20 + 56 (WA) + 12 gap = 88 — stacked one above other */
    z-index: 9998;
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, #dcb04a, #e3c877 55%, #dcb04a);
    color: #3a0c15;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(43,26,30,.25);
    opacity: 0;
    visibility: hidden;
    transform: translateY(12px);
    transition: opacity .25s, transform .25s, visibility .25s;
  }
  @media(max-width:480px){ #backToTop{ right:16px; bottom:80px; } }
  #backToTop.show { opacity: 1; visibility: visible; transform: translateY(0); }
  #backToTop:hover { filter: brightness(1.06); }
</style>
<script>
  (function () {
    var btn = document.createElement('button');
    btn.id = 'backToTop';
    btn.type = 'button';
    btn.setAttribute('aria-label', 'Back to top');
    btn.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
    document.body.appendChild(btn);
    var ticking = false;
    function onScroll() {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        var y = window.scrollY || document.documentElement.scrollTop;
        btn.classList.toggle('show', y > 400);
        ticking = false;
      });
    }
    btn.addEventListener('click', function () {
      if (window.__lenis && window.__lenis.scrollTo) { window.__lenis.scrollTo(0); }
      else { window.scrollTo({ top: 0, behavior: 'smooth' }); }
    });
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  })();
</script>
<script>
  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = btn.previousElementSibling;
      var icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
        btn.setAttribute('aria-label', 'Hide password');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
        btn.setAttribute('aria-label', 'Show password');
      }
    });
  });
</script>
<script>
  (function () {
    var userId = (document.querySelector('meta[name="current-user-id"]') || {}).content || '0';
    if (!userId || userId === '0') return;

    // Account dropdown toggle
    var btn = document.getElementById('acctMenuBtn');
    var menu = document.getElementById('acctMenu');
    if (btn && menu) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('hidden');
      });
      document.addEventListener('click', function (e) {
        if (!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.add('hidden');
      });
    }

    // Notifications
    var bell = document.getElementById('notifBell');
    var badge = document.getElementById('notifBadge');
    if (!bell) return;

    function pollNotifications() {
      fetch('./notifications_api.php?action=list')
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var unread = (data && data.unread) || 0;
          var items = (data && data.notifications) || [];
          if (badge) {
            if (unread > 0) {
              badge.textContent = unread > 99 ? '99+' : unread;
              badge.style.display = 'flex';
            } else {
              badge.style.display = 'none';
            }
          }
          bell._items = items;
          bell._unread = unread;
        })
        .catch(function () {});
    }

    var panel = document.createElement('div');
    panel.id = 'notifPanel';
    panel.className = 'hidden absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-black/5 bg-white p-2 shadow-xl z-50 text-left';
    panel.style.zIndex = '10000';
    bell.parentNode.appendChild(panel);

    function renderPanel() {
      var items = bell._items || [];
      if (!items.length) {
        panel.innerHTML = '<p class="px-3 py-4 text-sm text-[#3a0c15]/60">No notifications yet.</p>';
        return;
      }
      panel.innerHTML = items.map(function (n) {
        var strip = n.message || '';
        return '<a href="#" class="notif-item block rounded-lg px-3 py-2 text-sm text-[#3a0c15] hover:bg-black/5 ' + (n.is_read ? '' : 'border-l-2 border-[#8b0000] font-medium') + '" data-id="' + n.id + '">' + strip + '</a>';
      }).join('');
      var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
      var itemsEls = panel.querySelectorAll('.notif-item');
      itemsEls.forEach(function (el) {
        el.addEventListener('click', function (e) {
          e.preventDefault();
          var id = el.getAttribute('data-id');
          el.classList.remove('border-l-2', 'border-[#8b0000]', 'font-medium');
          fetch('./notifications_api.php?action=mark_read', {
            method: 'POST',
            headers: { 'X-CSRF-Token': csrf, 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
          }).then(function () { pollNotifications(); }).catch(function () {});
          openNotification(el.textContent.trim());
        });
      });
    }

    function openNotification(message) {
      // Map simple message keywords to known destinations
      var t = message.toLowerCase();
      var href = './profile.php';
      if (t.indexOf('message') !== -1) href = './messages.php';
      else if (t.indexOf('favourite') !== -1 || t.indexOf('interest') !== -1) href = './matches.php';
      else if (t.indexOf('match') !== -1) href = './matches.php';
      window.location.href = href;
    }

    bell.addEventListener('click', function (e) {
      e.stopPropagation();
      var wasHidden = panel.classList.contains('hidden');
      if (wasHidden) {
        renderPanel();
        panel.classList.remove('hidden');
      } else {
        panel.classList.add('hidden');
      }
    });
    document.addEventListener('click', function (e) {
      if (!panel.contains(e.target) && !bell.contains(e.target)) panel.classList.add('hidden');
    });

    pollNotifications();
    setInterval(pollNotifications, 30000);
  })();
</script>
<?php echo isset($pageScripts) ? $pageScripts : ''; ?>
</body>
</html>
