<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

// Handle form submission for adding a new vehicle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerId = $_POST['customer_id'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $licensePlate = $_POST['license_plate'];

    $sql = "INSERT INTO Vehicles (CustomerID, Make, Model, Year, LicensePlate) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $customerId, $make, $model, $year, $licensePlate);
    $stmt->execute();
}

// Fetch all vehicles with customer names
$sql = "SELECT Vehicles.*, Customers.Name AS CustomerName FROM Vehicles JOIN Customers ON Vehicles.CustomerID = Customers.CustomerID";
$result = $conn->query($sql);

// Fetch all customers for the dropdown
$customers = $conn->query("SELECT CustomerID, Name FROM Customers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Vehicles</title>
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
        .add-vehicle-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h2>Manage Vehicles</h2>

    <div class="add-vehicle-form">
        <h3>Add New Vehicle</h3>
        <form action="manage_vehicles.php" method="POST">
            <label>Customer:</label><br>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php while ($row = $customers->fetch_assoc()) { ?>
                    <option value="<?php echo $row['CustomerID']; ?>"><?php echo $row['Name']; ?></option>
                <?php } ?>
            </select><br>
            <label>Make:</label><br>
            <input type="text" name="make" required><br>
            <label>Model:</label><br>
            <input type="text" name="model" required><br>
            <label>Year:</label><br>
            <input type="text" name="year" required><br>
            <label>License Plate:</label><br>
            <input type="text" name="license_plate" required><br>
            <input type="submit" value="Add Vehicle">
        </form>
    </div>

    <h3>Vehicle List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Make</th>
            <th>Model</th>
            <th>Year</th>
            <th>License Plate</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['VehicleID']; ?></td>
            <td><?php echo $row['CustomerName']; ?></td>
            <td><?php echo $row['Make']; ?></td>
            <td><?php echo $row['Model']; ?></td>
            <td><?php echo $row['Year']; ?></td>
            <td><?php echo $row['LicensePlate']; ?></td>
        </tr>
        <?php } ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>

<?php $conn->close(); ?>
