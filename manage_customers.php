<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

// Handle form submission for adding a new customer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $sql = "INSERT INTO Customers (Name, Contact, Email, Address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $contact, $email, $address);
    $stmt->execute();
}

// Fetch all customers
$sql = "SELECT * FROM Customers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Customers</title>
    <style>
        /* Reuse previous CSS styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }
        /* Table styles */
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
        .add-customer-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Manage Customers</h2>

    <div class="add-customer-form">
        <h3>Add New Customer</h3>
        <form action="manage_customers.php" method="POST">
            <label>Name:</label><br>
            <input type="text" name="name" required><br>
            <label>Contact:</label><br>
            <input type="text" name="contact" required><br>
            <label>Email:</label><br>
            <input type="email" name="email" required><br>
            <label>Address:</label><br>
            <textarea name="address" required></textarea><br>
            <input type="submit" value="Add Customer">
        </form>
    </div>

    <h3>Customer List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Address</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['CustomerID']; ?></td>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Contact']; ?></td>
            <td><?php echo $row['Email']; ?></td>
            <td><?php echo $row['Address']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>

<?php $conn->close(); ?>
