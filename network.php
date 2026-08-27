<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot access user network - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

function fmtProfile($r) {
  $age = null;
  if (!empty($r['date_of_birth'])) {
    $age = (new DateTime())->diff(new DateTime($r['date_of_birth']))->y;
  }
  return [
    'id' => (int) $r['id'],
    'name' => $r['full_name'] ?? '',
    'gender' => strtolower($r['gender'] ?? ''),
    'age' => $age,
    'city' => $r['city'] ?? '',
    'occupation' => $r['occupation'] ?? '',
    'education' => $r['highest_education'] ?? '',
    'photo' => photo_url($r['profile_photo']),
    'note' => $r['note'] ?? '',
    'created_at' => $r['created_at'] ?? '',
  ];
}

$pageTitle = 'My Network — BestLife Matrimony';
$pageDescription = 'Manage your shortlists, interests and blocked members.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<style>
  .nw-wrap{max-width:860px;margin:0 auto;padding:24px 16px 48px;font-family:Inter,system-ui,sans-serif;color:#1a1a1a}
  .nw-tabs{display:flex;gap:8px;border-bottom:2px solid #eee;margin-bottom:20px;flex-wrap:wrap}
  .nw-tab{padding:10px 16px;font-size:14px;font-weight:600;cursor:pointer;background:none;border:none;border-bottom:3px solid transparent;color:#666;margin-bottom:-2px}
  .nw-tab.active{color:#6b1020;border-bottom-color:#6b1020}
  .nw-card{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid #eee;padding:16px;margin-bottom:10px}
  .nw-avatar{width:50px;height:50px;border-radius:50%;overflow:hidden;background:#f0ece6;display:flex;align-items:center;justify-content:center;color:#999;flex-shrink:0}
  .nw-avatar img{width:100%;height:100%;object-fit:cover}
  .nw-avatar i{font-size:24px}
  .nw-mid{flex:1;min-width:0}
  .nw-name{font-weight:700;font-size:15px}
  .nw-meta{font-size:13px;color:#666}
  .nw-note{font-size:12px;color:#999;font-style:italic;margin-top:2px}
  .nw-actions{display:flex;gap:8px;flex-wrap:wrap}
  .nw-btn{height:34px;padding:0 14px;font-size:12px;border:1px solid #ddd;background:#fff;cursor:pointer;border-radius:6px;display:inline-flex;align-items:center;gap:5px}
  .nw-btn-ok{background:#6b1020;color:#fff;border-color:#6b1020}
  .nw-btn-danger{color:#b91c1c;border-color:#efbcbc}
  .nw-empty{background:#fff;border:1px dashed #ddd;padding:40px;text-align:center;color:#888;font-size:14px}
  .nw-head{background:#fff;border:1px solid #eee;padding:24px;margin-bottom:16px}
  .nw-head h1{font-size:22px;margin:0}
  .nw-head p{margin:4px 0 0;color:#666;font-size:13px}
</style>

<main class="bg-[#f4f2ee]" style="min-height:100vh;">
  <div class="nw-wrap">
    <div class="nw-head">
      <h1>My Network</h1>
      <p>Shortlists, interests and connections.</p>
    </div>

    <div class="nw-tabs">
      <button type="button" class="nw-tab active" data-tab="shortlist">Shortlists</button>
      <button type="button" class="nw-tab" data-tab="interests">Interests</button>
      <button type="button" class="nw-tab" data-tab="blocked">Blocked</button>
    </div>

    <!-- Shortlists -->
    <section id="tab-shortlist">
      <div id="shortlistList" style="display:none;">Loading…</div>
    </section>

    <!-- Interests -->
    <section id="tab-interests" style="display:none;">
      <h3 style="font-size:14px;color:#6b1020;border-bottom:1px solid #eee;padding-bottom:6px;margin:0 0 12px;">Received Interests</h3>
      <div id="receivedList"></div>
      <h3 style="font-size:14px;color:#6b1020;border-bottom:1px solid #eee;padding-bottom:6px;margin:20px 0 12px;">Sent Interests</h3>
      <div id="sentList"></div>
    </section>

    <!-- Blocked -->
    <section id="tab-blocked" style="display:none;">
      <div id="blockedList"></div>
    </section>
  </div>
</main>
<script>
(function () {
  var me = (function(){ try{ return parseInt((document.querySelector('meta[name="current-user-id"]')||{}).content||'0',10);}catch(e){return 0;} })();
  var csrf = (function(){ try{ return (document.querySelector('meta[name="csrf-token"]')||{}).content||'';}catch(e){return '';} })();
  var apiFetch = function(u, o) { return fetch(u, o).then(function(r){ return r.json(); }); };

  function esc(s){ return String(s).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

  function card(p, actions) {
    var photo = p.photo ? '<img src="'+esc(p.photo)+'" alt="">' : '<i class="bi bi-person-fill"></i>';
    var meta = [];
    if (p.age) meta.push(p.age + ' yrs');
    if (p.city) meta.push(p.city);
    if (p.occupation) meta.push(p.occupation);
    return '<div class="nw-card">'
      + '<a class="nw-avatar" href="./profile_view.php?id='+p.id+'" style="color:inherit;text-decoration:none;">'+photo+'</a>'
      + '<div class="nw-mid"><a class="nw-name" href="./profile_view.php?id='+p.id+'" style="color:inherit;text-decoration:none;">'+esc(p.name)+'</a>'
      + '<div class="nw-meta">'+esc(meta.join(' · '))+'</div>'
      + (p.note ? '<div class="nw-note">'+esc(p.note)+'</div>' : '')
      + '</div>'
      + '<div class="nw-actions">' + actions + '</div></div>';
  }

  function emptyBox(msg){ return '<div class="nw-empty">'+msg+'</div>'; }

  // Shortlists
  function loadShortlists() {
    apiFetch('./shortlists_api.php?action=list').then(function(d){
      var box = document.getElementById('shortlistList');
      box.style.display = 'block';
      var items = d.shortlists || [];
      if (!items.length) { box.innerHTML = emptyBox('No shortlisted profiles yet. Use "Shortlist" on any profile to save it here.'); return; }
      box.innerHTML = items.map(function(p){
        return card(p, '<button type="button" class="nw-btn" data-act="view" data-id="'+p.id+'">View</button>'
          + '<button type="button" class="nw-btn nw-btn-danger" data-act="unsave" data-id="'+p.id+'">Unsave</button>');
      }).join('');
      box.querySelectorAll('button').forEach(function(b){
        b.addEventListener('click', function(){
          var id = b.getAttribute('data-id');
          if (b.getAttribute('data-act') === 'unsave') {
            apiFetch('./shortlists_api.php?action=toggle', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:JSON.stringify({profile_id:parseInt(id,10)})})
              .then(loadShortlists);
          }
        });
      });
    }).catch(function(){ var b=document.getElementById('shortlistList'); b.style.display='block'; b.innerHTML=emptyBox('Could not load shortlists.'); });
  }

  // Interests
  function loadInterests() {
    apiFetch('./interests_api.php?action=list').then(function(d){
      var r = document.getElementById('receivedList');
      var s = document.getElementById('sentList');
      var rec = d.received || [], snt = d.sent || [];
      if (!rec.length) r.innerHTML = emptyBox('No interests received yet.');
      else r.innerHTML = rec.map(function(p){
        var statusLabel = {pending:'Pending',accepted:'Accepted',declined:'Declined'}[p.status] || p.status;
        var act = '<span class="nw-btn" style="border-color:#6b1020;color:#6b1020;">'+statusLabel+'</span>';
        if (p.status === 'pending') {
          act += '<button type="button" class="nw-btn nw-btn-ok" data-act="accept" data-iid="'+p.id+'">Accept</button>'
            + '<button type="button" class="nw-btn nw-btn-danger" data-act="decline" data-iid="'+p.id+'">Decline</button>';
        }
        act += '<a class="nw-btn" href="./profile_view.php?id='+p.uid+'">View</a>';
        return card(p, act);
      }).join('');
      r.querySelectorAll('button[data-act]').forEach(function(b){
        b.addEventListener('click', function(){
          var body = JSON.stringify({interest_id:parseInt(b.getAttribute('data-iid'),10), status:b.getAttribute('data-act') === 'accept' ? 'accepted':'declined'});
          apiFetch('./interests_api.php?action=respond', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:body}).then(loadInterests);
        });
      });

      if (!snt.length) s.innerHTML = emptyBox('No interests sent yet.');
      else s.innerHTML = snt.map(function(p){
        var statusLabel = {pending:'Pending',accepted:'Accepted',declined:'Declined',withdrawn:'Withdrawn'}[p.status] || p.status;
        var act = '<span class="nw-btn" style="border-color:#6b1020;color:#6b1020;">'+statusLabel+'</span>'
          + '<a class="nw-btn" href="./profile_view.php?id='+p.uid+'">View</a>';
        return card(p, act);
      }).join('');
    }).catch(function(){ document.getElementById('receivedList').innerHTML = emptyBox('Could not load interests.'); });
  }

  // Blocked
  function loadBlocked() {
    apiFetch('./blocks_api.php?action=list').then(function(d){
      var box = document.getElementById('blockedList');
      var items = d.blocked || [];
      if (!items.length) { box.innerHTML = emptyBox('You have not blocked anyone.'); return; }
      box.innerHTML = items.map(function(p){
        return card({id:p.id,name:p.name,photo:p.photo}, '<button type="button" class="nw-btn nw-btn-danger" data-act="unblock" data-id="'+p.id+'">Unblock</button>');
      }).join('');
      box.querySelectorAll('button[data-act]').forEach(function(b){
        b.addEventListener('click', function(){
          apiFetch('./blocks_api.php?action=unblock', {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':csrf},body:JSON.stringify({profile_id:parseInt(b.getAttribute('data-id'),10)})}).then(loadBlocked);
        });
      });
    }).catch(function(){ document.getElementById('blockedList').innerHTML = emptyBox('Could not load blocked members.'); });
  }

  // Tabs
  var tabs = document.querySelectorAll('.nw-tab');
  tabs.forEach(function(t){
    t.addEventListener('click', function(){
      tabs.forEach(function(x){ x.classList.remove('active'); });
      t.classList.add('active');
      ['shortlist','interests','blocked'].forEach(function(n){ document.getElementById('tab-'+n).style.display = (n === t.getAttribute('data-tab')) ? 'block' : 'none'; });
    });
  });

  loadShortlists();
  loadInterests();
  loadBlocked();
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
