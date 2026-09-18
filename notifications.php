<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$pageTitle = 'Notifications — BestLife Matrimony';
$pageDescription = 'View your interests, favourites, profile views and account updates.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<style>
  .nt-wrap{max-width:960px;margin:0 auto;padding:24px 16px 24px;font-family:Inter,system-ui,sans-serif;color:#3a0c15;--nt-nav:64px}
  @media(min-width:640px){.nt-wrap{--nt-nav:80px}}
  @media(min-width:1024px){.nt-wrap{--nt-nav:96px}}
  .nt-head{background:#fff;border:1px solid #eee;padding:24px;margin-bottom:16px;position:sticky;top:var(--nt-nav);z-index:30}
  .nt-head h1{font-size:22px;margin:0;color:#3a0c15}
  .nt-head p{margin:4px 0 0;color:#666;font-size:13px}
  .nt-badge{display:inline-block;min-width:22px;text-align:center;background:#8b0000;color:#fff;font-size:11px;font-weight:700;border-radius:999px;padding:2px 8px;margin-left:8px;vertical-align:middle}
  .nt-toolbar{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
  .nt-search{flex:1;min-width:180px;height:38px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px}
  .nt-btn{height:38px;padding:0 16px;font-size:13px;font-weight:600;border-radius:8px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;border:1px solid #ddd;background:#fff;color:#3a0c15}
  .nt-btn-primary{background:#6b1020;border-color:#6b1020;color:#fff}
  .nt-btn:disabled{opacity:.6;cursor:wait}
  .nt-error{display:none;margin-top:10px;font-size:13px;color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:8px 12px}
  .nt-layout{display:grid;grid-template-columns:220px minmax(0,1fr);gap:16px;align-items:start}
  .nt-side{background:#fff;border:1px solid #eee;border-radius:12px;padding:12px;position:sticky;top:calc(var(--nt-nav) + 188px);z-index:20}
  .nt-side h2{font-size:11px;letter-spacing:.08em;color:#999;margin:0 0 8px;padding:0 8px}
  .nt-chip{display:flex;align-items:center;gap:8px;width:100%;text-align:left;font-size:13px;font-weight:600;color:#555;background:none;border:1px solid transparent;border-radius:999px;padding:8px 12px;cursor:pointer}
  .nt-chip:hover{background:#f7f4ee}
  .nt-chip.on{background:#6b1020;color:#fff}
  .nt-chip .cnt{margin-left:auto;font-size:11px;opacity:.7}
  .nt-item{display:flex;gap:12px;align-items:flex-start;background:#fff;border:1px solid #eee;border-radius:12px;padding:16px;margin-bottom:10px}
  .nt-item.unread{background:#fff8ec;border-color:#e8d5a8;border-left:4px solid #dcb04a}
  .nt-ic{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:#f4f2ee;color:#8a4a2f}
  .nt-ic i{font-size:17px}
  .nt-mid{flex:1;min-width:0}
  .nt-msg{font-size:14px;font-weight:600;line-height:1.45}
  .nt-meta{font-size:12px;color:#999;margin-top:2px}
  .nt-row-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
  .nt-link{height:32px;padding:0 14px;font-size:12px;font-weight:600;border:1px solid #ddd;background:#fff;border-radius:6px;display:inline-flex;align-items:center;gap:5px;color:#3a0c15;text-decoration:none;cursor:pointer}
  .nt-link.solid{background:#6b1020;border-color:#6b1020;color:#fff}
  .nt-del{background:none;border:none;color:#ccc;cursor:pointer;font-size:14px;padding:4px}
  .nt-del:hover{color:#b91c1c}
  .nt-empty{background:#fff;border:1px dashed #ddd;border-radius:12px;padding:48px 24px;text-align:center;color:#888;font-size:14px}
  .nt-empty i{font-size:28px;color:#dcb04a;display:block;margin-bottom:10px}
  .nt-list-col{min-width:0;min-height:0}
  .nt-scroll{overflow:visible;max-height:none}
  .nt-item{scroll-margin-top:calc(var(--nt-nav) + 200px)}
  .nt-chip:focus-visible,.nt-btn:focus-visible,.nt-link:focus-visible,.nt-search:focus-visible{outline:2px solid #8a4a2f;outline-offset:2px}
  @media(max-width:640px){
    .nt-wrap{padding:16px 12px 40px}
    .nt-head{padding:16px}
    .nt-head h1{font-size:20px}
    .nt-layout{grid-template-columns:1fr}
    .nt-side{order:2;position:static;max-height:none;overflow:visible}
    .nt-scroll{max-height:none;overflow:visible}
    .nt-chips{display:flex;flex-wrap:wrap;gap:6px}
    .nt-chip{width:auto}
    .nt-item{padding:12px}
  }
</style>

<main class="bg-[#f4f2ee]" style="min-height:100vh;">
  <div class="nt-wrap">
    <div class="nt-head">
      <h1>Notifications<span class="nt-badge" id="ntBadge" style="display:none;">0</span></h1>
      <p>Interests, favourites, profile views and account updates.</p>
      <div class="nt-toolbar">
        <input type="search" id="ntSearch" class="nt-search" placeholder="Search notifications…" aria-label="Search notifications" />
        <button type="button" class="nt-btn nt-btn-primary" id="ntMarkAll"><i class="bi bi-check2-all"></i>Mark all read</button>
        <button type="button" class="nt-btn" id="ntClearRead"><i class="bi bi-trash"></i>Delete read</button>
      </div>
      <p id="ntError" class="nt-error" role="alert"></p>
    </div>

    <div class="nt-layout">
      <aside class="nt-side" aria-label="Notification filters">
        <h2>FILTERS</h2>
        <div class="nt-chips" id="ntFilters" role="group" aria-label="Filter notifications">
          <button type="button" class="nt-chip on" data-f="all" aria-pressed="true"><i class="bi bi-bell"></i>All<span class="cnt" id="cntAll"></span></button>
          <button type="button" class="nt-chip" data-f="interest" aria-pressed="false"><i class="bi bi-heart-fill"></i>Interests<span class="cnt" id="cntInterest"></span></button>
          <button type="button" class="nt-chip" data-f="favourite" aria-pressed="false"><i class="bi bi-star-fill"></i>Favourites<span class="cnt" id="cntFavourite"></span></button>
          <button type="button" class="nt-chip" data-f="view" aria-pressed="false"><i class="bi bi-eye-fill"></i>Profile views<span class="cnt" id="cntView"></span></button>
          <button type="button" class="nt-chip" data-f="verification" aria-pressed="false"><i class="bi bi-patch-check-fill"></i>Verification<span class="cnt" id="cntVerification"></span></button>
          <button type="button" class="nt-chip" data-f="admin" aria-pressed="false"><i class="bi bi-megaphone-fill"></i>Account updates<span class="cnt" id="cntAdmin"></span></button>
          <button type="button" class="nt-chip" data-f="unread" aria-pressed="false"><i class="bi bi-circle-fill" style="font-size:8px;"></i>Unread only<span class="cnt" id="cntUnread"></span></button>
        </div>
      </aside>

      <section class="nt-list-col" aria-live="polite">
        <div id="ntList" class="nt-scroll"></div>
        <div id="ntEmpty" class="nt-empty" style="display:none;">
          <i class="bi bi-bell-slash"></i>
          <strong>No notifications found.</strong>
          <p style="margin:6px 0 0;font-size:13px;">When someone shows interest in your profile, it will appear here.</p>
          <p style="margin:12px 0 0;"><a class="nt-link solid" href="./matches.php">Browse matches</a></p>
        </div>
      </section>
    </div>
  </div>
</main>

<script>
(function () {
  var csrf = (function(){ try{ return (document.querySelector('meta[name="csrf-token"]')||{}).content||''; }catch(e){ return ''; } })();
  var filter = 'all';
  var query = '';
  var items = [];

  var TYPE_LABEL = { interest:'Interest', favourite:'Favourite', view:'Profile view', verification:'Verification', admin:'Account update', general:'Update' };
  var TYPE_ICON = { interest:'bi-heart-fill', favourite:'bi-star-fill', view:'bi-eye-fill', verification:'bi-patch-check-fill', admin:'bi-megaphone-fill', general:'bi-bell-fill' };
  // Destinations for the primary action of each notification type.
  // network.php supports ?tab= deep-links (shortlist|interests|blocked).
  var TYPE_HREF = { interest:'./network.php?tab=interests', favourite:'./network.php?tab=shortlist', view:'./matches.php', verification:'./verify.php', admin:'./profile.php', general:'./matches.php' };
  var TYPE_CTA = { interest:'Respond', favourite:'View shortlists', view:'View matches', verification:'Open verification', admin:'Open profile', general:'View' };

  function esc(s){ return String(s == null ? '' : s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

  function relTime(iso){
    if (!iso) return '';
    var t = new Date(String(iso).replace(' ', 'T'));
    if (isNaN(t)) return esc(iso);
    var s = Math.floor((Date.now() - t.getTime()) / 1000);
    if (s < 60) return 'Just now';
    if (s < 3600) return Math.floor(s/60) + ' min ago';
    if (s < 86400) return Math.floor(s/3600) + ' hr ago';
    var d = Math.floor(s/86400);
    if (d === 1) return 'Yesterday';
    if (d < 7) return d + ' days ago';
    return t.toLocaleDateString();
  }

  function api(url, opts){
    opts = opts || {};
    opts.headers = opts.headers || {};
    if (opts.method === 'POST') {
      opts.headers['X-CSRF-Token'] = csrf;
      opts.headers['Content-Type'] = 'application/x-www-form-urlencoded';
      // Send the token in the body too, so verification never depends on header handling alone.
      if (typeof opts.body === 'string' && opts.body.indexOf('csrf_token=') === -1) {
        opts.body += (opts.body ? '&' : '') + 'csrf_token=' + encodeURIComponent(csrf);
      }
    }
    return fetch(url, opts).then(function(r){
      return r.json().catch(function(){ throw new Error('Request failed. Please refresh the page and try again.'); });
    });
  }

  function showErr(msg){
    var el = document.getElementById('ntError');
    el.textContent = msg;
    el.style.display = 'block';
    clearTimeout(showErr._t);
    showErr._t = setTimeout(function(){ el.style.display = 'none'; }, 5000);
  }

  // Hide the navbar bell badge immediately after a successful update,
  // instead of waiting for its background poll.
  function syncBell(){
    if (items.some(function(n){ return !n.is_read; })) return;
    var badge = document.getElementById('notifBadge');
    if (badge) badge.style.display = 'none';
  }

  function load(){
    api('./notifications_api.php?action=list').then(function(d){
      items = (d && d.notifications) || [];
      render();
    }).catch(function(){
      document.getElementById('ntList').innerHTML = '<div class="nt-empty"><i class="bi bi-exclamation-circle"></i><strong>Could not load notifications.</strong><p style="margin:6px 0 0;font-size:13px;">Please refresh the page and try again.</p></div>';
    });
  }

  function counts(){
    var c = { all: items.length, unread: 0, interest:0, favourite:0, view:0, verification:0, admin:0 };
    items.forEach(function(n){
      if (!n.is_read) c.unread++;
      if (c[n.type] !== undefined) c[n.type]++;
    });
    document.getElementById('cntAll').textContent = c.all ? '(' + c.all + ')' : '';
    document.getElementById('cntUnread').textContent = c.unread ? '(' + c.unread + ')' : '';
    ['interest','favourite','view','verification','admin'].forEach(function(t){
      var el = document.getElementById('cnt' + t.charAt(0).toUpperCase() + t.slice(1));
      if (el) el.textContent = c[t] ? '(' + c[t] + ')' : '';
    });
    var badge = document.getElementById('ntBadge');
    if (c.unread > 0) { badge.style.display = 'inline-block'; badge.textContent = c.unread > 99 ? '99+' : c.unread; }
    else { badge.style.display = 'none'; }
  }

  function filtered(){
    return items.filter(function(n){
      if (filter === 'unread' && n.is_read) return false;
      if (filter !== 'all' && filter !== 'unread' && n.type !== filter) return false;
      if (query && String(n.message || '').toLowerCase().indexOf(query) === -1) return false;
      return true;
    });
  }

  function render(){
    counts();
    var list = filtered();
    var box = document.getElementById('ntList');
    document.getElementById('ntEmpty').style.display = list.length ? 'none' : 'block';
    box.innerHTML = list.map(function(n){
      var label = TYPE_LABEL[n.type] || TYPE_LABEL.general;
      var icon = TYPE_ICON[n.type] || TYPE_ICON.general;
      var href = TYPE_HREF[n.type] || TYPE_HREF.general;
      var cta = TYPE_CTA[n.type] || TYPE_CTA.general;
      return '<article class="nt-item' + (n.is_read ? '' : ' unread') + '">'
        + '<span class="nt-ic"><i class="bi ' + icon + '"></i></span>'
        + '<div class="nt-mid">'
        + '<p class="nt-msg">' + esc(n.message) + '</p>'
        + '<p class="nt-meta">' + esc(label) + ' &middot; ' + esc(relTime(n.created_at)) + '</p>'
        + '<div class="nt-row-actions">'
        + '<a class="nt-link solid" href="' + href + '" data-open="' + n.id + '"> ' + esc(cta) + '</a>'
        + (!n.is_read ? '<button type="button" class="nt-link" data-read="' + n.id + '">Mark read</button>' : '')
        + '<button type="button" class="nt-link" data-del="' + n.id + '">Delete</button>'
        + '</div></div>'
        + '<button type="button" class="nt-del" data-del="' + n.id + '" aria-label="Delete notification"><i class="bi bi-trash"></i></button>'
        + '</article>';
    }).join('');
    box.querySelectorAll('[data-read]').forEach(function(b){
      b.addEventListener('click', function(){ markRead(parseInt(b.getAttribute('data-read'), 10)); });
    });
    box.querySelectorAll('[data-del]').forEach(function(b){
      b.addEventListener('click', function(){ removeOne(parseInt(b.getAttribute('data-del'), 10)); });
    });
    box.querySelectorAll('[data-open]').forEach(function(a){
      // Fire-and-forget: mark read without touching the DOM so the
      // browser navigates to the exact destination undisturbed.
      a.addEventListener('click', function(){
        try { fetch('./notifications_api.php?action=mark_read', { method:'POST', headers:{ 'X-CSRF-Token': csrf, 'Content-Type': 'application/x-www-form-urlencoded' }, body:'id=' + encodeURIComponent(a.getAttribute('data-open')) }); } catch (e) {}
      });
    });
  }

  function markRead(id){
    api('./notifications_api.php?action=mark_read', { method:'POST', body:'id=' + encodeURIComponent(id) }).then(function(d){
      if (!d || !d.ok) throw new Error((d && d.error) || 'Could not update this notification.');
      var n = items.find(function(x){ return x.id === id; });
      if (n) n.is_read = 1;
      render();
      if (!items.some(function(x){ return !x.is_read; })) syncBell();
    }).catch(function(e){ showErr(e.message || 'Could not mark as read.'); });
  }

  function removeOne(id){
    if (!window.confirm('Delete this notification?')) return;
    api('./notifications_api.php?action=delete', { method:'POST', body:'id=' + encodeURIComponent(id) }).then(function(d){
      if (!d || !d.ok) throw new Error((d && d.error) || 'Could not delete this notification.');
      items = items.filter(function(x){ return x.id !== id; });
      render();
    }).catch(function(e){ showErr(e.message || 'Could not delete this notification.'); });
  }

  document.querySelectorAll('#ntFilters .nt-chip').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('#ntFilters .nt-chip').forEach(function(x){ x.classList.remove('on'); x.setAttribute('aria-pressed', 'false'); });
      btn.classList.add('on');
      btn.setAttribute('aria-pressed', 'true');
      filter = btn.getAttribute('data-f');
      render();
    });
  });

  document.getElementById('ntSearch').addEventListener('input', function(e){
    query = e.target.value.trim().toLowerCase();
    render();
  });

  document.getElementById('ntMarkAll').addEventListener('click', function(){
    var btn = this;
    if (btn.disabled) return;
    btn.disabled = true;
    api('./notifications_api.php?action=mark_read', { method:'POST', body:'all=1' }).then(function(d){
      if (!d || !d.ok) throw new Error((d && d.error) || 'Could not mark all as read.');
      // Re-read from the server so the list always reflects the stored state.
      return api('./notifications_api.php?action=list');
    }).then(function(d){
      items = (d && d.notifications) || items.map(function(n){ n.is_read = 1; return n; });
      render();
      syncBell();
    }).catch(function(e){ showErr(e.message || 'Could not mark all as read.'); }).then(function(){ btn.disabled = false; });
  });

  document.getElementById('ntClearRead').addEventListener('click', function(){
    var btn = this;
    var ids = items.filter(function(n){ return n.is_read; }).map(function(n){ return n.id; });
    if (!ids.length) { showErr('There are no read notifications to delete.'); return; }
    if (!window.confirm('Delete all read notifications?')) return;
    btn.disabled = true;
    api('./notifications_api.php?action=delete', { method:'POST', body:'all=1' }).then(function(d){
      if (!d || !d.ok) throw new Error((d && d.error) || 'Could not delete read notifications.');
      items = items.filter(function(n){ return !n.is_read; });
      render();
    }).catch(function(e){ showErr(e.message || 'Could not delete read notifications.'); }).then(function(){ btn.disabled = false; });
  });

  load();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
