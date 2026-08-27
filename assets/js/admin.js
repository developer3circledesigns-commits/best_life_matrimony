// Admin panel interactions — BestLife Matrimony
(function () {
  'use strict';

  function csrf() {
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }

  function toast(msg, ok) {
    var el = document.createElement('div');
    el.textContent = msg;
    el.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 18px;border-radius:10px;color:#fff;z-index:9999;font-weight:600;box-shadow:0 4px 14px rgba(0,0,0,.25);background:' + (ok ? '#15803d' : '#b91c1c');
    document.body.appendChild(el);
    setTimeout(function () { el.remove(); }, 3000);
  }

  function doAction(action, payload, cb) {
    fetch('./admin_api.php?action=' + encodeURIComponent(action), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrf()
      },
      body: JSON.stringify(payload || {})
    })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.ok) {
          toast(d.message || 'Done', true);
          if (cb) { cb(d); }
        } else {
          toast(d.error || 'Action failed', false);
        }
      })
      .catch(function () { toast('Network error', false); });
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-act]');
    if (!btn) return;
    e.preventDefault();
    var action = btn.getAttribute('data-act');
    var id = btn.getAttribute('data-id');
    var payload = { id: id };
    var extra = btn.getAttribute('data-extra');
    if (extra) payload[btn.getAttribute('data-extra-key') || 'status'] = extra;

    if (action === 'delete_campaign') {
      if (!window.confirm('Delete this campaign history entry?')) return;
    }
    if (action === 'make_admin' || action === 'suspend' || action === 'activate' || action === 'dismiss_report' || action === 'approve_media' || action === 'reject_media') {
      var note = '';
      if (action === 'suspend') note = window.prompt('Reason for suspension (shown to user)?', '') || 'Admin action';
      if (action === 'make_admin' && !window.confirm('Grant admin privileges to this user?')) return;
      if (note !== null) payload.note = note;
    }

    if ((action === 'approve_verification' || action === 'reject_verification') && !window.confirm('Confirm this decision?')) return;

    doAction(action, payload, function () {
      if (action !== 'delete_campaign') {
        setTimeout(function () { window.location.reload(); }, 500);
      } else {
        btn.closest('tr').remove();
      }
    });
  });
})();
