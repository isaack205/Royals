<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$unlockPassword = 'Royals2026';
$unlockError = '';
$unlockSuccess = '';
$newsletterMessage = '';
$newsletterError = '';

if (!isset($_SESSION['site_unlocked'])) {
    $_SESSION['site_unlocked'] = false;
}

$redirectPath = $_GET['redirect'] ?? 'index.php';
if (!is_string($redirectPath) || strpos($redirectPath, 'http://') === 0 || strpos($redirectPath, 'https://') === 0) {
    $redirectPath = 'index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'unlock') {
        $enteredPassword = trim($_POST['site_password'] ?? '');
        if ($enteredPassword === $unlockPassword) {
            $_SESSION['site_unlocked'] = true;
            $unlockSuccess = 'Website unlocked successfully.';
            header('Location: ' . $redirectPath);
            exit;
        }
        $unlockError = 'Incorrect password. Please try again.';
    }

    if ($formType === 'newsletter') {
        $newsletterEmail = trim($_POST['newsletter_email'] ?? '');
        if ($newsletterEmail === '') {
            $newsletterError = 'Enter an email address to subscribe.';
        } elseif (!filter_var($newsletterEmail, FILTER_VALIDATE_EMAIL)) {
            $newsletterError = 'Please enter a valid email address.';
        } else {
            $newsletterFile = __DIR__ . '/newsletter_signups.txt';
            $entry = date('Y-m-d H:i:s') . ' | ' . $newsletterEmail . PHP_EOL;
            @file_put_contents($newsletterFile, $entry, FILE_APPEND | LOCK_EX);
            $newsletterMessage = 'Thanks. You have been added to the newsletter list.';
        }
    }
}

$year = (int)date('Y');
$month = (int)date('n');
$targetTimestamp = mktime(23, 59, 59, $month, 14, $year) * 1000;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royals | Site Locked</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #000000;
            --card: #0f0f0f;
            --text: #f4f4f4;
            --muted: #a8a8a8;
            --accent: #00d2ff;
            --danger: #ff5757;
            --success: #3ddc97;
            --border: #1f1f1f;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            background: #000000;
            color: var(--text);
            font-family: 'Trebuchet MS', 'Segoe UI', sans-serif;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .lock-wrap {
            width: 100%;
            max-width: 520px;
            background: #000000;
            padding: 28px 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            min-height: auto;
        }

        .countdown {
            text-align: center;
            margin-bottom: 16px;
            color: var(--accent);
            letter-spacing: 0.8px;
            font-weight: 700;
        }

        .logo-box {
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
        }

        .logo-box img {
            width: 230px;
            height: 190px;
            border-radius: 50%;
            object-fit: cover;
            background: #050505;
        }

        h1 {
            text-align: center;
            font-size: 1.3rem;
            margin-bottom: 18px;
        }

        .muted {
            text-align: center;
            color: var(--muted);
            margin-bottom: 18px;
            font-size: 0.95rem;
        }

        form {
            display: grid;
            gap: 10px;
            margin-bottom: 18px;
        }

        label {
            font-size: 0.9rem;
            color: var(--muted);
        }

        button {
            border: none;
            background: linear-gradient(90deg, #0099cc, #00d2ff);
            color: #021118;
            border-radius: 10px;
            padding: 11px 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .lock-wrap {
            display: flex;
            flex-direction: column;
            height: auto;
            min-height: auto;
        }

        .lock-wrap a {
            font-size: 1.7rem;
        }

        .lock-section-top {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .lock-section-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .lock-section-center {
            color: #ec0a0a;
        }

        .lock-section-bottom {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
        }

        .newsletter {
            margin-top: 50px;
            border-top: none;
            padding-top: 0;
            margin-bottom: 20px;
        }

        .newsletter h2 {
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        input[type='password'],
        input[type='email'] {
            width: 100%;
            border: 1px solid #eee9e9;
            background: #fbf8f8;
            border-radius: 10px;
            padding: 11px 12px;
            padding-right: 40px;
            outline: none;
            color: #050404;
        }

        input:focus {
            border-color: var(--accent);
        }

        .input-arrow {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #00d2ff;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
        }

        .input-arrow:hover {
            color: #00e6ff;
        }

        .message {
            margin-top: 8px;
            font-size: 0.9rem;
        }

        .error {
            color: var(--danger);
        }

        .success {
            color: var(--success);
        }
    </style>
</head>
<body>
    <div class="lock-wrap">
        <!-- Top Section: Logo -->
        <div class="lock-section-top">
            <div class="logo-box">
                <img src="/uploads/lock.jpeg" alt="Royals Lock Logo">
            </div>
        </div>

        <!-- Center Section: Countdown, Title, Newsletter -->
        <div class="lock-section-center">
            <div class="countdown" id="countdown">Loading countdown...</div>
            <h1>SITE CLOSED</h1>

            <div class="newsletter">
                <form method="POST" action="lock.php?redirect=<?php echo urlencode($redirectPath); ?>" style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="hidden" name="form_type" value="newsletter">
                    <label for="newsletter_email" style="text-align: center;">Be the first to know when we drop.</label>
                    <div class="input-wrapper">
                        <input id="newsletter_email" name="newsletter_email" type="email" placeholder="E-mail">
                        <button type="submit" class="input-arrow" title="Subscribe" aria-label="Subscribe">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <?php if ($newsletterError !== ''): ?>
                        <p class="message error"><?php echo htmlspecialchars($newsletterError); ?></p>
                    <?php endif; ?>
                    <?php if ($newsletterMessage !== ''): ?>
                        <p class="message success"><?php echo htmlspecialchars($newsletterMessage); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Bottom Section: Password Unlock -->
        <div class="lock-section-bottom">
            <form method="POST" action="lock.php?redirect=<?php echo urlencode($redirectPath); ?>" style="display: flex; flex-direction: column; gap: 10px;">
                <input type="hidden" name="form_type" value="unlock">
                <label for="site_password">Are you the store owner?</label>
                <div class="input-wrapper">
                    <input id="site_password" name="site_password" type="password" placeholder="password" required>
                    <button type="submit" class="input-arrow" title="Unlock" aria-label="Unlock">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <?php if ($unlockError !== ''): ?>
                    <p class="message error"><?php echo htmlspecialchars($unlockError); ?></p>
                <?php endif; ?>
                <?php if ($unlockSuccess !== ''): ?>
                    <p class="message success"><?php echo htmlspecialchars($unlockSuccess); ?></p>
                <?php endif; ?>
            </form>
        </div>

        <p style="text-align: center; margin-top: 20px; font-size: 0.9rem;">
            <a href="mailto:info@royals.co.ke" style="color: var(--accent); text-decoration: none;"><i class="fas fa-envelope"></i></a>
        </p>
    </div>

    <script>
        (function () {
            const countdownEl = document.getElementById('countdown');
            const target = <?php echo (int)$targetTimestamp; ?>;

            function updateCountdown() {
                const now = Date.now();
                const diff = target - now;

                if (diff <= 0) {
                    countdownEl.textContent = 'Countdown complete for the 14th.';
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                countdownEl.textContent =
                    days + 'd ' +
                    String(hours).padStart(2, '0') + 'h ' +
                    String(minutes).padStart(2, '0') + 'm ' +
                    String(seconds).padStart(2, '0') + 's ';
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        })();
    </script>
</body>
</html>
