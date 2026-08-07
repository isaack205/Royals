<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

require_once 'db.php';
require_once 'mailer_config.php';
use PHPMailer\PHPMailer\Exception;

$message = '';
$messageType = '';

// ── Handle email blast ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_blast'])) {
    $subject = trim($_POST['subject'] ?? '');
    $body    = nl2br(htmlspecialchars(trim($_POST['body'] ?? '')));

    if (empty($subject) || empty($body)) {
        $_SESSION['mail_msg'] = 'Subject and message body are required.';
        $_SESSION['mail_msg_type'] = 'error';
    } else {
        $recipients = $pdo->query("SELECT email FROM mailing_list WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $email) {
            try {
                $mail = createMailer();
                $mail->addAddress($email);
                $mail->Subject = $subject;
                $mail->Body    = "
                    <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#000;color:#f4f4f4;padding:30px;border-radius:10px;'>
                        <div style='text-align:center;margin-bottom:20px;'>
                            <h1 style='color:#00d2ff;font-size:1.8rem;margin:0;'>ROYALS</h1>
                        </div>
                        <div style='background:#111;padding:25px;border-radius:8px;line-height:1.7;'>
                            $body
                        </div>
                        <p style='text-align:center;color:#666;font-size:0.8rem;margin-top:20px;'>
                            Royals Co. | <a href='https://royals.co.ke' style='color:#00d2ff;'>royals.co.ke</a>
                        </p>
                    </div>";
                $mail->AltBody = strip_tags($body);
                $mail->send();
                $sent++;
            } catch (Exception $e) {
                $failed++;
            }
        }
        $_SESSION['mail_msg'] = "Sent to $sent recipient(s)." . ($failed > 0 ? " $failed failed." : '');
        $_SESSION['mail_msg_type'] = ($failed === 0) ? 'success' : 'warning';
    }
    header('Location: admin_mailing_list.php');
    exit();
}

// Read message from session if it exists
if (isset($_SESSION['mail_msg'])) {
    $message = $_SESSION['mail_msg'];
    $messageType = $_SESSION['mail_msg_type'];
    unset($_SESSION['mail_msg'], $_SESSION['mail_msg_type']);
}


// ── Handle delete subscriber ─────────────────────────────────────────
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM mailing_list WHERE id = ?")->execute([$delId]);
    header('Location: admin_mailing_list.php?deleted=1');
    exit;
}

// ── Fetch subscribers ────────────────────────────────────────────────
$subscribers = $pdo->query("SELECT * FROM mailing_list ORDER BY subscribed_at DESC")->fetchAll(PDO::FETCH_ASSOC);

include('adminheader.php');
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailing List - Royals Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0a0a0a; --secondary: #1a1a1a;
            --accent: #00d2ff; --text: #ffffff;
            --text-secondary: #888; --danger: #ff4757;
            --success: #2ed573; --warning: #ff9f43;
            --card-bg: #1e1e1e; --border: rgba(255,255,255,0.1);
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body { background:var(--primary); color:var(--text); padding-top:80px; }
        .container { max-width:1100px; margin:0 auto; padding:2rem; }
        h2 { color:var(--accent); margin-bottom:1.5rem; display:flex; align-items:center; gap:.8rem; }
        .stats { display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
        .stat { background:var(--card-bg); border:1px solid var(--border); border-radius:10px; padding:1rem 1.5rem; }
        .stat span { display:block; color:var(--text-secondary); font-size:.85rem; }
        .stat strong { font-size:1.8rem; color:var(--accent); }
        .card { background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:1.5rem; margin-bottom:2rem; }
        .card h3 { color:var(--accent); margin-bottom:1rem; }
        input, textarea {
            width:100%; background:#111; border:1px solid var(--border);
            color:#fff; border-radius:8px; padding:.75rem 1rem;
            margin-bottom:.8rem; font-family:inherit; font-size:.95rem;
        }
        input:focus, textarea:focus { outline:none; border-color:var(--accent); }
        textarea { min-height:140px; resize:vertical; }
        .btn { padding:.8rem 1.5rem; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:.9rem; }
        .btn-accent { background:linear-gradient(90deg,#0099cc,#00d2ff); color:#000; }
        .btn-danger { background:var(--danger); color:#fff; padding:.4rem .8rem; border-radius:6px; font-size:.8rem; border:none; cursor:pointer; }
        .alert { padding:1rem 1.2rem; border-radius:8px; margin-bottom:1.5rem; font-weight:500; }
        .alert.success { background:rgba(46,213,115,.15); border:1px solid var(--success); color:var(--success); }
        .alert.error   { background:rgba(255,71,87,.15);  border:1px solid var(--danger);  color:var(--danger);  }
        .alert.warning { background:rgba(255,159,67,.15); border:1px solid var(--warning); color:var(--warning); }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:.75rem 1rem; color:var(--text-secondary); font-size:.85rem; border-bottom:1px solid var(--border); }
        td { padding:.75rem 1rem; border-bottom:1px solid var(--border); font-size:.9rem; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:rgba(255,255,255,.03); }
        .badge { padding:.2rem .6rem; border-radius:20px; font-size:.75rem; font-weight:600; }
        .badge-lock { background:rgba(0,210,255,.15); color:var(--accent); }
        .empty { text-align:center; padding:3rem; color:var(--text-secondary); }
        .empty i { font-size:2.5rem; margin-bottom:1rem; display:block; }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-envelope-open-text"></i> Mailing List</h2>

    <?php if ($message): ?>
        <div class="alert <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert error"><i class="fas fa-trash"></i> Subscriber removed.</div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats">
        <div class="stat">
            <span>Total Subscribers</span>
            <strong><?= count($subscribers) ?></strong>
        </div>
        <div class="stat">
            <span>Source: Lockscreen</span>
            <strong><?= count(array_filter($subscribers, fn($s) => $s['source'] === 'lockscreen')) ?></strong>
        </div>
    </div>

    <!-- Email Blast Form -->
    <div class="card">
        <h3><i class="fas fa-paper-plane"></i> Send Email Blast</h3>
        <form method="POST" id="emailBlastForm" onsubmit="handleEmailSubmit(event)">
            <input type="text" name="subject" placeholder="Email subject..." required>
            <textarea name="body" placeholder="Write your message here...&#10;&#10;It will be styled automatically with Royals branding." required></textarea>
            <button type="submit" name="send_blast" id="submitBlastBtn" class="btn btn-accent">
                <i class="fas fa-paper-plane"></i> Send to All <?= count($subscribers) ?> Subscribers
            </button>
        </form>
    </div>

    <!-- Subscriber Table -->
    <div class="card">
        <h3><i class="fas fa-list"></i> Subscribers</h3>
        <?php if (empty($subscribers)): ?>
            <div class="empty">
                <i class="fas fa-inbox"></i>
                No subscribers yet. Emails collected from the lockscreen will appear here.
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Email</th>
                    <th>Source</th>
                    <th>Subscribed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscribers as $i => $sub): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($sub['email']) ?></td>
                    <td><span class="badge badge-lock"><?= htmlspecialchars($sub['source']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($sub['subscribed_at'])) ?></td>
                    <td>
                        <a href="?delete=<?= $sub['id'] ?>" class="btn-danger"
                           onclick="return confirm('Remove this subscriber?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <p style="margin-top:1rem;"><a href="admin_dashboard.php" style="color:var(--accent);"><i class="fas fa-arrow-left"></i> Back to Dashboard</a></p>
    <script>
    function handleEmailSubmit(e) {
        const btn = document.getElementById('submitBlastBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending emails... Please wait...';
        
        // Let the form submit naturally now that we've updated the button
        return true;
    }
    </script>
</body>
</html>
<?php $connection->close(); ?>

