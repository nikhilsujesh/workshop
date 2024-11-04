<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

// Handle form submission for adding a new part
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_part'])) {
    $partName = $_POST['part_name'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['cost'];

    $sql = "INSERT INTO Inventory (PartName, Quantity, Cost) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sid", $partName, $quantity, $cost);
    $stmt->execute();
}

// Handle form submission for updating a part
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_part'])) {
    $partId = $_POST['part_id'];
    $partName = $_POST['part_name'];
    $quantity = $_POST['quantity'];
    $cost = $_POST['cost'];

    $sql = "UPDATE Inventory SET PartName = ?, Quantity = ?, Cost = ? WHERE PartID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidi", $partName, $quantity, $cost, $partId);
    $stmt->execute();
}

// Handle deletion of a part
if (isset($_GET['delete'])) {
    $partId = $_GET['delete'];
    $sql = "DELETE FROM Inventory WHERE PartID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $partId);
    $stmt->execute();
}

// Fetch all inventory items
$inventoryItems = $conn->query("SELECT * FROM Inventory");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .form-container {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Manage Inventory</h2>

    <div class="form-container">
        <h3>Add New Part</h3>
        <form action="manage_inventory.php" method="POST">
            <label>Part Name:</label><br>
            <input type="text" name="part_name" required><br>
            <label>Quantity:</label><br>
            <input type="number" name="quantity" min="0" required><br>
            <label>Cost:</label><br>
            <input type="text" name="cost" required><br>
            <input type="submit" name="add_part" value="Add Part">
        </form>
    </div>

    <h3>Inventory List</h3>
    <table>
        <tr>
            <th>Part ID</th>
            <th>Part Name</th>
            <th>Quantity</th>
            <th>Cost</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $inventoryItems->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['PartID']; ?></td>
            <td><?php echo $row['PartName']; ?></td>
            <td><?php echo $row['Quantity']; ?></td>
            <td><?php echo number_format($row['Cost'], 2); ?></td>
            <td>
                <form action="manage_inventory.php" method="POST" style="display:inline;">
                    <input type="hidden" name="part_id" value="<?php echo $row['PartID']; ?>">
                    <input type="text" name="part_name" value="<?php echo $row['PartName']; ?>" required>
                    <input type="number" name="quantity" value="<?php echo $row['Quantity']; ?>" min="0" required>
                    <input type="text" name="cost" value="<?php echo $row['Cost']; ?>" required>
                    <input type="submit" name="update_part" value="Update">
                </form>
                <a href="manage_inventory.php?delete=<?php echo $row['PartID']; ?>" onclick="return confirm('Are you sure you want to delete this part?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>

</body>
</html>

<?php $conn->close(); ?>
