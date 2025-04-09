<?php
// Start session
session_start();

// This function validates the text length for name and about in the form
function validateTextLength($text, $min = 2, $max = 100)
{
  $length = strlen(trim($text));
  return $length >= $min && $length <= $max;
}

// Form data states
$formData = [
  'name' => '',
  'about' => ''
];

$errors = [
  'name' => '',
  'about' => ''
];

$message = '';

// Form submission logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get and sanitize form data
  $formData['name'] = trim($_POST['name']);
  $formData['about'] = trim($_POST['about']);

  // Validate name
  if (!validateTextLength($formData['name'], 2, 50)) {
    $errors['name'] = "Name must be between 2 and 50 characters.";
  }

  // Validate about
  if (!validateTextLength($formData['about'], 10, 500)) {
    $errors['about'] = "About section must be between 10 and 500 characters.";
  }

  // Check for any errors
  $errorMessages = array_filter($errors);
  if (empty($errorMessages)) {
    // Store in cookies
    setcookie('name', $formData['name'], time() + 3600, '/');
    setcookie('about', $formData['about'], time() + 3600, '/');

    // Store in session
    $_SESSION['user_data'] = $formData;

    $message = "Form submitted successfully!";
    header("Location: form.php?success=1");
    exit();
  } else {
    $message = "Please correct the form errors.";
  }
}

// Load data from cookies if available and form wasn't just submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  if (isset($_COOKIE['name'])) {
    $formData['name'] = $_COOKIE['name'];
  }
  if (isset($_COOKIE['about'])) {
    $formData['about'] = $_COOKIE['about'];
  }
}

// Show success message if redirected after successful submission
if (isset($_GET['success']) && $_GET['success'] == 1) {
  $message = "Form submitted successfully!";
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Assignment #10 Form | My Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-50 p-12">
  <?php if ($message): ?>
    <div
      class="mb-4 p-4 rounded <?= strpos($message, 'error') !== false ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
      <?= htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="form.php">
    <div class="space-y-12">
      <div class="border-b border-gray-900/10 pb-12">
        <h2 class="text-base/7 font-semibold text-gray-900">Assignment #10 Form</h2>
        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-4">
            <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
            <div class="mt-2">
              <div
                class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-green-600">
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($formData['name']); ?>"
                  class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6"
                  placeholder="Enter your name">
              </div>
              <?php if ($errors['name']): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['name']); ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-span-full">
            <label for="about" class="block text-sm/6 font-medium text-gray-900">About Me</label>
            <div class="mt-2">
              <textarea name="about" id="about" rows="3"
                class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-green-600 sm:text-sm/6"
                placeholder="Enter your about me"><?= htmlspecialchars($formData['about']); ?></textarea>
              <?php if ($errors['about']): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['about']); ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-span-full">
            <button type="submit"
              class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-green-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
              Save
            </button>

          </div>
        </div>
      </div>
    </div>
  </form>

</body>

</html>