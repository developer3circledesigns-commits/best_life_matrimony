<?php
require_once __DIR__ . '/_header.php';
$db = getDB();
$admins = $db->query('SELECT id, full_name, email, phone, is_suspended, email_verified, created_at FROM users WHERE is_admin = 1 ORDER BY id DESC')->fetchAll();
?>
<div class="adm-card adm-form-card" style="margin-bottom:1.5rem;">
  <h3 class="adm-h3">Create Admin User</h3>
  <p class="adm-muted" style="margin:0 0 1rem;font-size:.85rem;">Create a dedicated admin login. Admins can only be created here — regular registrations remain normal users and existing users cannot be promoted.</p>
  <form id="createAdminForm">
    <div class="adm-field">
      <label>Full Name</label>
      <input type="text" name="full_name" maxlength="150" class="adm-input" required placeholder="e.g. Admin_Bestlife_Matrimony">
    </div>
    <div class="adm-field">
      <label>Email (admin login)</label>
      <input type="email" name="email" maxlength="191" class="adm-input" required placeholder="admin@bestlifematrimony.com">
    </div>
    <div class="adm-field">
      <label>Phone</label>
      <input type="tel" name="phone" maxlength="30" class="adm-input" required placeholder="+91 98765 43210">
    </div>
    <div class="adm-field">
      <label>Password (min 8 chars)</label>
      <input type="password" name="password" maxlength="255" class="adm-input" required placeholder="At least 8 characters">
    </div>
    <div class="adm-field">
      <label>Confirm Password</label>
      <input type="password" name="password_confirm" maxlength="255" class="adm-input" required placeholder="Re-enter password">
    </div>
    <button type="submit" class="btn btn-accent">Create Admin</button>
  </form>
</div>

<h3 class="adm-h3">Current Admins (<?php echo count($admins); ?>)</h3>
<div class="adm-card adm-card--scroll">
  <?php if (!$admins): ?>
    <div class="adm-empty">No admins found.</div>
  <?php else: ?>
  <table class="adm-table">
    <thead><tr><th>Admin</th><th>Email / Phone</th><th>Status</th><th>Created</th></tr></thead>
    <tbody>
      <?php foreach ($admins as $a): ?>
      <tr>
        <td class="adm-name">
          <span class="adm-user-avatar"><?php echo strtoupper(mb_substr($a['full_name'], 0, 1)); ?></span>
          <?php echo htmlspecialchars($a['full_name']); ?>
        </td>
        <td><?php echo htmlspecialchars($a['email']); ?><br><span class="adm-cell-sub"><?php echo htmlspecialchars($a['phone'] ?: '—'); ?></span></td>
        <td>
          <?php if ($a['is_suspended']): ?><span class="pill pill-red">Suspended</span><?php else: ?><span class="pill pill-green">Active</span><?php endif; ?>
          <?php if ($a['email_verified']): ?><span class="pill pill-green">Verified</span><?php endif; ?>
        </td>
        <td class="adm-nowrap"><?php echo date('M j, Y', strtotime($a['created_at'])); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<script src="<?php echo asset('js/admin.js'); ?>"></script>
<script>
(function(){
  var form = document.getElementById('createAdminForm');
  if (!form) return;
  form.addEventListener('submit', function(e){
    e.preventDefault();
    var fd = new FormData(form);
    var full_name = (fd.get('full_name')||'').toString().trim();
    var email = (fd.get('email')||'').toString().trim();
    var phone = (fd.get('phone')||'').toString().trim();
    var password = fd.get('password')||'';
    var password_confirm = fd.get('password_confirm')||'';
    if (password !== password_confirm) { alert('Passwords do not match.'); return; }
    var csrf = document.querySelector('meta[name="csrf-token"]');
    csrf = csrf ? csrf.getAttribute('content') : '';
    fetch('./admin_api.php?action=create_admin', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-Token': csrf},
      body: JSON.stringify({full_name: full_name, email: email, phone: phone, password: password})
    }).then(function(r){ return r.json(); }).then(function(d){
      if (d.ok) { alert(d.message); window.location.reload(); }
      else { alert(d.error || 'Failed'); }
    }).catch(function(){ alert('Network error'); });
  });
})();
</script>
<?php adm_close(); ?>
