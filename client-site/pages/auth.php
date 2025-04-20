<?php
session_start();
require_once '../db/database-connection.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle signup form submission
    if (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        $sql = "INSERT INTO users (username, password, email, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
        $stmt = pdo($pdo, $sql, [$username, $password, $email, $first_name, $last_name]);

        $_SESSION['message'] = "Registration successful! Please sign in.";
    }

    // Handle signin form submission
    if (isset($_POST['signin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = ? AND password = ?"; // Direct password comparison
        $stmt = pdo($pdo, $sql, [$username, $password]);
        $user = $stmt->fetch();

        // Once the user is signed in, send them to the profile page
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-xl shadow-lg">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Sign In Form -->
            <div id="signin-form">
                <h2 class="text-2xl font-bold text-center mb-8">Sign In</h2>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Username</label>
                        <input type="text" name="username" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <button type="submit" name="signin"
                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                        Sign In
                    </button>
                </form>
                <p class="text-center mt-4">
                    Don't have an account?
                    <a href="#" onclick="toggleForms()" class="text-green-500">Sign Up</a>
                </p>
            </div>

            <!-- Sign Up Form -->
            <div id="signup-form" class="hidden">
                <h2 class="text-2xl font-bold text-center mb-8">Sign Up</h2>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">First Name</label>
                        <input type="text" name="first_name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700">Last Name</label>
                        <input type="text" name="last_name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700">Username</label>
                        <input type="text" name="username" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700">Password</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <button type="submit" name="signup"
                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                        Sign Up
                    </button>
                </form>
                <p class="text-center mt-4">
                    Already have an account?
                    <a href="#" onclick="toggleForms()" class="text-green-500">Sign In</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function toggleForms() {
            const signinForm = document.getElementById('signin-form');
            const signupForm = document.getElementById('signup-form');
            signinForm.classList.toggle('hidden');
            signupForm.classList.toggle('hidden');
        }
    </script>
</body>

</html>