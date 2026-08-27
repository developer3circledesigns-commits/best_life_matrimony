<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$notice = '';
$noticeType = 'ok';

// Handle compose + send
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['compose'])) {
  if (!hash_equals(csrf_token(), $_POST['_token'] ?? '')) {
    $notice = 'Invalid request session.'; $noticeType = 'err';
  } else {
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $audience = $_POST['audience'] ?? 'all';
    if ($subject === '' || $body === '') {
      $notice = 'Subject and message body are required.'; $noticeType = 'err';
    } else {
      $stmt = $db->query('SELECT email FROM users WHERE email IS NOT NULL AND email != \'\'');
      $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
      $sent = 0;
      foreach ($emails as $email) {
        if (send_email($email, $subject, nl2br(htmlspecialchars($body)), $body)) {
          $sent++;
        }
      }
      $db->prepare('INSERT INTO email_campaigns (subject, body_html, body_text, audience, status, recipients, created_by) VALUES (?, ?, ?, ?, \'sent\', ?, ?)')
         ->execute([$subject, nl2br(htmlspecialchars($body)), $body, $audience, $sent, (int) $_SESSION['user_id']]);
      log_activity((int) $_SESSION['user_id'], 'campaign_send', 'email_campaign', (int) $db->lastInsertId(), 'Sent campaign to ' . $sent . ' recipients');
      $notice = 'Campaign sent to ' . $sent . ' of ' . count($emails) . ' subscribers.';
    }
  }
}

$campaigns = $db->query('SELECT c.*, u.full_name AS uname FROM email_campaigns c LEFT JOIN users u ON u.id = c.created_by ORDER BY c.id DESC LIMIT 50')->fetchAll();
$tosend = (int) $db->query('SELECT COUNT(*) FROM users WHERE email IS NOT NULL AND email != \'\'')->fetchColumn();
?>
<?php if ($notice): ?><div class="adm-alert <?php echo $noticeType; ?>"><?php echo htmlspecialchars($notice); ?></div><?php endif; ?>

<div class="adm-card adm-form-card" style="margin-bottom:1.5rem;">
  <h3 class="adm-h3">Send Email Campaign</h3>
  <p class="adm-muted" style="margin:0 0 1rem;font-size:.85rem;">Will be delivered to <strong><?php echo $tosend; ?></strong> verified email subscribers via the configured mailer.</p>
  <form method="post" action="./campaign.php">
    <input type="hidden" name="compose" value="1">
    <?php csrf_field(); ?>
    <div class="adm-field">
      <label>Subject</label>
      <input type="text" name="subject" maxlength="191" class="adm-input" required placeholder="e.g. New matches waiting for you">
    </div>
    <div class="adm-field">
      <label>Message</label>
      <textarea name="body" rows="6" class="adm-textarea" required placeholder="Write your message here. Newlines will become paragraphs."></textarea>
    </div>
    <div class="adm-field">
      <label>Audience</label>
      <select name="audience" class="adm-select">
        <option value="all">All users</option>
        <option value="active">Active users only</option>
      </select>
    </div>
    <button type="submit" class="btn btn-accent">Send Campaign</button>
  </form>
</div>

<h3 class="adm-h3">Campaign History</h3>
<div class="adm-card adm-card--scroll">
  <?php if (!$campaigns): ?>
    <div class="adm-empty">No campaigns sent yet.</div>
  <?php else: ?>
  <table class="adm-table">
    <thead><tr><th>Subject</th><th>Audience</th><th>Recipients</th><th>Sent By</th><th>Date</th><th class="adm-th-right">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($campaigns as $c): ?>
      <tr>
        <td class="adm-name"><?php echo htmlspecialchars($c['subject']); ?></td>
        <td><span class="pill pill-blue"><?php echo htmlspecialchars($c['audience']); ?></span></td>
        <td><?php echo (int)$c['recipients']; ?></td>
        <td><?php echo htmlspecialchars($c['uname'] ?: '—'); ?></td>
        <td class="adm-nowrap"><?php echo date('M j, Y g:i A', strtotime($c['created_at'])); ?></td>
        <td class="adm-actions">
          <button type="button" data-act="delete_campaign" data-id="<?php echo (int)$c['id']; ?>" class="btn btn-danger btn-sm">Delete</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<script src="<?php echo asset('js/admin.js'); ?>"></script>
<?php adm_close(); ?>
