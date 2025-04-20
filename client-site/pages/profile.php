<?php
session_start();
require_once '../db/database-connection.php';

// Handle logout and send them back to the auth page
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit();
}

// Handle random order creation
if (isset($_GET['create_random_order'])) {
    $user_id = $_SESSION['user_id'];
    $order_number = 'ORD' . strtoupper(uniqid());
    $total_amount = rand(1000, 50000) / 100; // random amount between $10 - $500
    $status = ['pending', 'processing', 'completed'][rand(0, 2)];

    $sql = "INSERT INTO orders (user_id, order_number, total_amount, status, order_date) VALUES (?, ?, ?, ?, NOW())";
    pdo($pdo, $sql, [$user_id, $order_number, $total_amount, $status]);

    header("Location: profile.php");
    exit();
}

// If you are not logged in, send them back to the auth page
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Get the users data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = pdo($pdo, $sql, [$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get the users orders
$sql = "SELECT * FROM orders WHERE user_id = ?";
$stmt = pdo($pdo, $sql, [$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($user['username']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-2xl w-full space-y-8">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <!-- Profile Header -->
                <div class="text-center mb-8">
                    <div class="h-24 w-24 rounded-full bg-green-600 mx-auto mb-4 flex items-center justify-center">
                        <span class="text-3xl text-white font-bold">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                    </h1>
                    <p class="text-gray-500">@<?php echo htmlspecialchars($user['username']); ?></p>
                </div>

                <!-- Profile Details -->
                <div class="border-t border-gray-200 pt-6">
                    <dl class="divide-y divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-500">Username</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Recent Orders -->
                <div class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Recent Orders</h2>
                        <a href="?create_random_order=true"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            Create Random Order
                        </a>
                    </div>
                    <?php if (count($orders) > 0): ?>
                        <div class="space-y-4">
                            <?php foreach ($orders as $order): ?>
                                <a href="order.php?orderNumber=<?php echo htmlspecialchars($order['order_number']); ?>"
                                    class="block border rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-green-600">Order
                                                #<?php echo htmlspecialchars($order['order_number']); ?></p>
                                            <p class="text-sm text-gray-500">
                                                <?php echo date('F j, Y', strtotime($order['order_date'])); ?>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium">$<?php echo number_format($order['total_amount'], 2); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo ucfirst($order['status']); ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No orders found.</p>
                    <?php endif; ?>
                </div>

                <!-- Logout Button -->
                <div class="mt-8 text-center">
                    <a href="?logout=true"
                        class="inline-block bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Sign Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>