<?php
session_start();
require_once '../db/database-connection.php';

// If you are not logged in, send back to auth page
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Get order number from query string
$orderNumber = isset($_GET['orderNumber']) ? $_GET['orderNumber'] : null;

// If no order number provided, redirect back to profile
if (!$orderNumber) {
    header("Location: profile.php");
    exit();
}

// Get user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = pdo($pdo, $sql, [$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the updating of the order
    if (isset($_POST['update'])) {
        // Update existing order
        $sql = "UPDATE orders SET total_amount = ?, status = ? WHERE order_number = ? AND user_id = ?";
        $stmt = pdo($pdo, $sql, [
            $_POST['total_amount'],
            $_POST['status'],
            $orderNumber,
            $_SESSION['user_id']
        ]);
        // Log the number of rows affected by the update
        error_log("Rows affected by UPDATE: " . $stmt->rowCount());
        header("Location: order.php?orderNumber=" . $orderNumber);
        exit();
    }

    // Handle the deletion of the order
    if (isset($_POST['delete'])) {
        // Delete order
        $sql = "DELETE FROM orders WHERE order_number = ? AND user_id = ?";
        $stmt = pdo($pdo, $sql, [$orderNumber, $_SESSION['user_id']]);
        // Log the number of rows affected by the delete
        error_log("Rows affected by DELETE: " . $stmt->rowCount());
        header("Location: profile.php");
        exit();
    }
}

// Get specific order
$sql = "SELECT * FROM orders WHERE order_number = ? AND user_id = ?";
$stmt = pdo($pdo, $sql, [$orderNumber, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $orderNumber; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-2xl w-full space-y-8">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Order #<?php echo $orderNumber; ?>
                    </h1>
                    <a href="profile.php" class="text-green-600 hover:text-green-700">Back to Profile</a>
                </div>

                <!-- Edit Order Form -->
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700">Order Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['order_number']); ?>"
                            class="w-full px-3 py-2 border rounded-lg bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-700">Total Amount</label>
                        <input type="number" step="0.01" name="total_amount"
                            value="<?php echo htmlspecialchars($order['total_amount']); ?>"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg">
                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>
                                Pending</option>
                            <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>
                                Processing</option>
                            <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>
                                Delivered</option>
                        </select>
                    </div>
                    <div class="flex space-x-4">
                        <button type="submit" name="update"
                            class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">
                            Update Order
                        </button>
                        <button type="submit" name="delete"
                            class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                            Delete Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>