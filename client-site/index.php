<?php
require_once 'db/database-connection.php';

class UserProfile
{
    private string $username;
    private string $email;
    private array $interests;

    public function __construct(string $username, string $email, array $interests)
    {
        $this->username = $username;
        $this->email = $email;
        $this->interests = $interests;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getInterests(): array
    {
        return $this->interests;
    }

    public function addInterest(string $interest): void
    {
        // make sure to avoid adding empty strings or duplicates
        $trimmedInterest = trim($interest);
        if (!empty($trimmedInterest) && !in_array($trimmedInterest, $this->interests)) {
            $this->interests[] = $trimmedInterest;
        }
    }
}

class User
{
    private UserProfile $userProfile;
    public function __construct(UserProfile $userProfile)
    {
        $this->userProfile = $userProfile;
    }

    public function getProfile(): UserProfile
    {
        return $this->userProfile;
    }
}

$validationError = null;
$newInterestToAdd = null;

// Handle form submission for adding interest
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_interest'])) {
        if (isset($_POST['new_interest']) && !empty(trim($_POST['new_interest']))) {
            $newInterestToAdd = trim($_POST['new_interest']);
            // Sanitize the interest (also we're goiong to use htmlspecialchars later on display)
            $newInterestToAdd = filter_var($newInterestToAdd, FILTER_SANITIZE_STRING);
        } else {
            $validationError = "Please enter an interest.";
        }
    }
}

// Set the defaults of the username and email
$username = 'No name provided';
$email = 'No email provided';

// Check GET parameters first to allow updating via URL and set/update cookies
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    // Set cookie for 30 days
    setcookie('username', $username, time() + (86400 * 30), "/");
} elseif (isset($_COOKIE['username'])) {
    // If it exists, set it
    $username = $_COOKIE['username'];
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    // Set cookie for 30 days
    setcookie('email', $email, time() + (86400 * 30), "/");
} elseif (isset($_COOKIE['email'])) {
    // If it exists, set it
    $email = $_COOKIE['email'];
}

$usernameFromQueryParameter = htmlspecialchars($username);
$emailFromQueryParameter = htmlspecialchars($email);

// initialize the array of interests
$initialInterests = [];

// create the user profile and the user attached to it
$userProfile = new UserProfile($usernameFromQueryParameter, $emailFromQueryParameter, $initialInterests);
$user = new User($userProfile);

// Add the new interest if it was submitted and validated
if ($newInterestToAdd !== null) {
    $user->getProfile()->addInterest($newInterestToAdd);
}

// Fetch user description from the database
$userDescription = "No description provided yet."; // This is the default message
try {
    $sql = "SELECT description FROM profile WHERE username = :username LIMIT 1";
    $stmt = pdo($pdo, $sql, [':username' => $usernameFromQueryParameter]);
    $profileData = $stmt->fetch();

    if ($profileData && !empty($profileData['description'])) {
        $userDescription = htmlspecialchars($profileData['description']);
    }
} catch (PDOException $e) {
    // log the error for now
    error_log("Database error fetching profile description: " . $e->getMessage());
}


// Handle form for description update/clearing 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Updating the description
    if (isset($_POST['update_description'])) {
        $newDescription = trim($_POST['description'] ?? '');
        try {
            $sql_update = "INSERT INTO profile (username, email, description) VALUES (:username, :email, :description)
                           ON DUPLICATE KEY UPDATE description = :description_update";
            pdo($pdo, $sql_update, [
                ':username' => $user->getProfile()->getUsername(),
                ':email' => $user->getProfile()->getEmail(),
                ':description' => $newDescription,
                ':description_update' => $newDescription
            ]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?username=" . urlencode($username) . "&email=" . urlencode($email));
            exit;
        } catch (PDOException $e) {
            error_log("Database error updating description: " . $e->getMessage());
        }
    }
    // Clear the description
    elseif (isset($_POST['clear_description'])) {
        try {
            $sql_clear = "UPDATE profile SET description = NULL WHERE username = :username";
            pdo($pdo, $sql_clear, [':username' => $username]);
            header("Location: " . $_SERVER['PHP_SELF'] . "?username=" . urlencode($username) . "&email=" . urlencode($email));
            exit;
        } catch (PDOException $e) {
            error_log("Database error clearing description: " . $e->getMessage());
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <!-- Logo -->
            <div class=" flex">
                <a href="/" class="-m-1.5 p-1.5">
                    <span class="sr-only">Hayati Fits Logo</span>
                    <img src="./logo.png" alt="Hayati Fits Logo" class="h-32 w-32" />
                </a>
            </div>
            </div>
            <div class="flex justify-center lg:gap-x-12">
                <!-- Nav Links -->
                <div class=" flex gap-x-12">
                    <a href="https://yallahbaby.vercel.app/contact" target="_blank"
                        class="text-sm/6 font-semibold text-gray-900">Contact</a>
                    <a href="https://yallahbaby.vercel.app/customer-reviews" target="_blank"
                        class="text-sm/6 font-semibold text-gray-900">Customer Reviews</a>
                </div>
            </div>
        </nav>
    </header>
    <div class="min-h-screen bg-gray-50 pt-24">
        <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div id="profile-card" class="mb-8 overflow-hidden rounded-lg bg-white shadow">
                <div class="p-6">
                    <div class="flex flex-col items-start justify-between sm:flex-row sm:items-center">
                        <div id="profile-info">
                            <h1 class="text-2xl font-bold text-gray-900"><?php echo $usernameFromQueryParameter; ?></h1>
                            <p class="mt-1 text-gray-500"><?php echo $emailFromQueryParameter; ?></p>
                        </div>
                    </div>
                    <!-- jQuery Stuff -->
                    <div class="mt-4 space-y-2">
                        <div class="flex space-x-2">
                            <button id="toggle-info-btn"
                                class="rounded bg-blue-500 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Hide
                                Info
                            </button>
                        </div>
                        <div id="profile-visibility-placeholder" class="text-sm text-gray-600 h-5"></div>
                    </div>
                </div>

                <!-- Profile Navigation Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex overflow-x-auto">

                        <button class="border-transparent px-6 text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <a href="https://yallahbaby.vercel.app/account/profile" target="_blank"
                                class="text-decoration-none">
                                My Saved Designs
                            </a>
                        </button>
                        <button
                            class="border-b-2 px-6 py-4 text-sm font-medium whitespace-nowrap border-green-500 text-green-600">
                            Profile
                        </button>
                    </nav>
                </div>
            </div>

            <!-- About Me Section -->
            <div id="about-me-section" class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About Me</h2>


                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Current Bio</h3>
                        <div class="prose max-w-none">
                            <p><?php echo $userDescription; ?></p>
                        </div>
                        <!-- Bio Editing Form -->
                        <form method="POST" action="" class="mt-4 pt-4 border-t border-gray-200">
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Edit Your
                                    Bio Above:</label>
                                <textarea id="description" name="description" rows="4"
                                    class="p-2 shadow-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Tell us something about yourself..."><?php echo $userDescription === 'No description provided yet.' ? '' : $userDescription; ?></textarea>
                            </div>
                            <div class="flex justify-end gap-3 mt-4">
                                <button type="submit" name="update_description" value="1"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Bio
                                </button>
                                <button type="submit" name="clear_description" value="1"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    onclick="return confirm('Are you sure you want to clear your bio?');">
                                    Clear Bio
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Interests Section -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Interests</h3>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php
                            $currentInterests = $user->getProfile()->getInterests();
                            foreach ($currentInterests as $interest):
                                ?>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars($interest); ?>
                                </span>
                            <?php endforeach; ?>

                            <?php if (empty($currentInterests)): ?>
                                <p class="text-gray-500 italic">No interests specified yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Add Interest Form -->
                        <form method="POST" action="" class="mt-4 pt-4 border-t border-gray-200">
                            <label for="new_interest" class="block text-sm font-medium text-gray-700 mb-1">Add a New
                                Interest:
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="new_interest" id="new_interest"
                                    class="p-2 flex-grow shadow-md focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="e.g. Programming, Sleeping, Eating, etc...">
                                <button type="submit" name="add_interest" value="1"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Add Interest
                                </button>
                            </div>
                            <?php if ($validationError): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($validationError); ?></p>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Cookie Information Section -->
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Current Cookies</h3>
                        <?php if (!empty($_COOKIE)): ?>
                            <ul class="list-disc list-inside space-y-1">
                                <?php foreach ($_COOKIE as $name => $value): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars($name); ?>:</strong>
                                        <?php echo htmlspecialchars($value); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No cookies are currently set.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Order History Section - should be loaded from ajax -->
            <div id="order-history-section" class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Mockup Order History</h2>
                    <div id="order-history-content" class="space-y-4">
                        <p class="text-gray-500">Loading order history...</p>
                    </div>
                </div>
            </div>

            <!-- Support Tickets Section - should be loaded from ajax -->
            <div id="support-tickets-section" class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Mockup Support Tickets</h2>
                    <div id="support-tickets-content" class="space-y-3">
                        <p class="text-gray-500">Loading support tickets...</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <footer class="border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
            <div class="flex justify-center gap-x-6 md:order-2">
                <a href="/" class="text-gray-600 hover:text-gray-800">
                    <span class="sr-only">Instagram</span>
                    <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            <p class="mt-8 text-center text-sm/6 text-gray-600 md:order-1 md:mt-0">
                &copy; 2025-2025 Hayati Fits, Inc. All rights reserved.
            </p>
            <p>By Zaid Shahzad | zaid_shahzad@uri.edu</p>
        </div>
    </footer>

    <script src="./final-projects-requirements/jquery-requirement.js"></script>
    <script src="./final-projects-requirements/ajax-requirement.js"></script>
    <script src="./final-projects-requirements/api-requirement.js"></script>
</body>

</html>