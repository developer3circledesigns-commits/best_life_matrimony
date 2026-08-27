/* Messages — inbox UI */
(function () {
  'use strict';
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  var me = parseInt((document.querySelector('meta[name="current-user-id"]') || {}).content || '0', 10);
  var initialUser = parseInt(new URLSearchParams(location.search).get('user') || '0', 10);
  var currentOther = 0;
  var body = document.getElementById('threadBody');
  var head = document.getElementById('threadHead');
  var composer = document.getElementById('composer');
  var input = document.getElementById('msgInput');
  var convList = document.getElementById('conversations');
  var unreadTotal = document.getElementById('unreadTotal');

  function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function fmtDate(s) {
    if (!s) return '';
    var d = new Date(String(s).replace(' ', 'T'));
    if (isNaN(d)) return '';
    var today = new Date();
    if (d.toDateString() === today.toDateString()) return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    return d.toLocaleDateString([], { day: 'numeric', month: 'short' });
  }
  function timeFull(s) {
    if (!s) return '';
    var d = new Date(String(s).replace(' ', 'T'));
    if (isNaN(d)) return '';
    return d.toLocaleString([], { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
  }

  function loadConversations() {
    return fetch('./messages_api.php?action=conversations')
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var conversations = data.conversations || [];
        var unread = 0;
        conversations.forEach(function (c) { unread += c.unread; });
        if (unreadTotal) unreadTotal.textContent = unread ? '(' + unread + ')' : '';
        if (!conversations.length) {
          convList.innerHTML = '<div class="msg-empty"><i class="bi bi-inbox"></i><p>No messages yet. Conversations with matched users will appear here.</p></div>';
          return;
        }
        convList.innerHTML = conversations.map(function (c) {
          var photo = c.photo ? '<img src="' + esc(c.photo) + '" alt="">' : '<i class="bi bi-person-circle"></i>';
          var unreadHtml = c.unread ? '<span class="msg-badge">' + c.unread + '</span>' : '';
          var active = c.user_id === currentOther ? ' active' : '';
          return '<button type="button" class="msg-conv' + active + '" data-uid="' + c.user_id + '">'
            + '<span class="msg-avatar">' + photo + '</span>'
            + '<span class="msg-conv-mid"><span class="msg-conv-name">' + esc(c.name) + unreadHtml + '</span>'
            + '<span class="msg-conv-last">' + esc(c.last_body || '') + '</span></span>'
            + '<span class="msg-conv-time">' + esc(fmtDate(c.last_at)) + '</span></button>';
        }).join('');
      })
      .catch(function () { if (!convList.querySelector('.msg-conv')) convList.innerHTML = '<div class="msg-empty">Could not load conversations.</div>'; });
  }

  function openThread(uid) {
    currentOther = uid;
    body.innerHTML = '<div class="msg-empty">Loading…</div>';
    if (convList) convList.querySelectorAll('.msg-conv').forEach(function (c) { c.classList.toggle('active', parseInt(c.getAttribute('data-uid'), 10) === uid); });
    return fetch('./messages_api.php?action=thread&user_id=' + uid)
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.other) {
          var photo = data.other.photo ? '<img src="' + esc(data.other.photo) + '" alt="">' : '<i class="bi bi-person-circle"></i>';
          head.innerHTML = '<span class="msg-avatar">' + photo + '</span><span class="msg-head-name">' + esc(data.other.name) + '</span>';
        } else {
          head.innerHTML = '<span class="msg-head-name">Conversation</span>';
        }
        var msgs = data.messages || [];
        if (!msgs.length) {
          body.innerHTML = '<div class="msg-empty"><i class="bi bi-chat-dots"></i><p>No messages yet — say hello!</p></div>';
        } else {
          body.innerHTML = msgs.map(function (m) {
            var mine = m.sender_id === me;
            return '<div class="msg-bubble ' + (mine ? 'mine' : 'theirs') + '">'
              + '<p class="msg-bubble-text">' + esc(m.body) + '</p>'
              + '<span class="msg-bubble-time">' + esc(timeFull(m.created_at)) + '</span></div>';
          }).join('');
        }
        composer.hidden = false;
        body.scrollTop = body.scrollHeight;
        loadConversations();
      })
      .catch(function () { body.innerHTML = '<div class="msg-empty">Could not load thread.</div>'; });
  }

  if (composer) {
    composer.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = input.value.trim();
      if (!currentOther || !text) return;
      fetch('./messages_api.php?action=send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
        body: JSON.stringify({ receiver_id: currentOther, body: text })
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.ok) {
            input.value = '';
            var bubble = document.createElement('div');
            bubble.className = 'msg-bubble mine';
            var now = new Date().toISOString().replace('T', ' ').slice(0, 19);
            bubble.innerHTML = '<p class="msg-bubble-text">' + esc(text) + '</p><span class="msg-bubble-time">' + esc(timeFull(now)) + '</span>';
            body.appendChild(bubble);
            body.scrollTop = body.scrollHeight;
            loadConversations();
          } else if (data.error) {
            alert(data.error);
          }
        })
        .catch(function () { alert('Could not send message.'); });
    });
  }

  if (convList) convList.addEventListener('click', function (e) {
    var b = e.target.closest('.msg-conv');
    if (b) openThread(parseInt(b.getAttribute('data-uid'), 10));
  });

  loadConversations().then(function () {
    if (initialUser && initialUser > 0) openThread(initialUser);
  });
  setInterval(function () {
    loadConversations();
    if (currentOther) openThread(currentOther);
  }, 20000);
})();
