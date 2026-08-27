<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();
require_approved();

// Admin users cannot access user messages - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$initialUserId = isset($_GET['user']) ? (int) $_GET['user'] : 0;

$pageTitle = 'Messages — BestLife Matrimony';
$pageDescription = 'Your messages on BestLife Matrimony.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">'
  . '<link rel="stylesheet" href="./assets/css/messages.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]" style="min-height:100vh;">
  <div class="msg-wrap">
    <div class="msg-layout">
      <!-- Conversations list -->
      <aside class="msg-list" id="convList">
        <div class="msg-list-head">
          <h2 class="msg-title">Messages</h2>
          <span class="msg-count" id="unreadTotal"></span>
        </div>
        <div id="conversations" class="msg-convs"><div class="msg-empty">Loading…</div></div>
      </aside>

      <!-- Thread -->
      <section class="msg-thread" id="threadPanel">
        <div class="msg-thread-head" id="threadHead"></div>
        <div class="msg-thread-body" id="threadBody">
          <div class="msg-empty"><i class="bi bi-chat-dots"></i><p>Select a conversation to start messaging.</p></div>
        </div>
        <form class="msg-composer" id="composer" hidden>
          <input type="text" id="msgInput" placeholder="Type your message…" maxlength="2000" autocomplete="off">
          <button type="submit" class="msg-send" aria-label="Send"><i class="bi bi-send"></i></button>
        </form>
      </section>
    </div>
  </div>
</main>
<?php
require_once __DIR__ . '/includes/footer.php';
$pageScripts = '<script src="' . asset('js/messages.js') . '"></script>';
require_once __DIR__ . '/includes/scripts.php';
?>
