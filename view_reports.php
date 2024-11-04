<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

// Handle report generation
$reportData = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['report_type'];
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if ($reportType == "service_history") {
        $vehicleId = $_POST['vehicle_id'] ?? '';
        $sql = "SELECT * FROM Services WHERE VehicleID = ? AND ServiceDate BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $vehicleId, $startDate, $endDate);
        $stmt->execute();
        $reportData = $stmt->get_result();
    } elseif ($reportType == "mechanic_history") {
        $employeeId = $_POST['employee_id'] ?? '';
        $sql = "SELECT * FROM Services WHERE EmployeeID = ? AND ServiceDate BETWEEN ? AND ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $employeeId, $startDate, $endDate);
        $stmt->execute();
        $reportData = $stmt->get_result();
    }
    // Add more conditions for other report types here
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
        }
        h2, h3 {
            color: #007bff;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        select, input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
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
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>View Reports</h2>
    <form method="POST" action="view_reports.php">
        <label for="report_type">Select Report Type:</label>
        <select name="report_type" required>
            <option value="">Select Report</option>
            <option value="service_history">Service History by Vehicle</option>
            <option value="mechanic_history">Service History by Mechanic</option>
        </select><br>
        
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required><br>
        
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" required><br>
        
        <label for="vehicle_id">Select Vehicle:</label>
        <select name="vehicle_id">
            <option value="">All Vehicles</option>
            <?php 
            // Fetch all vehicles for the dropdown
            $vehicles = $conn->query("SELECT VehicleID, CONCAT(Make, ' ', Model, ' (', LicensePlate, ')') AS VehicleInfo FROM Vehicles");
            while ($row = $vehicles->fetch_assoc()) { ?>
                <option value="<?php echo $row['VehicleID']; ?>"><?php echo $row['VehicleInfo']; ?></option>
            <?php } ?>
        </select><br>

        <label for="employee_id">Select Mechanic:</label>
        <select name="employee_id">
            <option value="">All Mechanics</option>
            <?php 
            // Fetch all mechanics for the dropdown
            $employees = $conn->query("SELECT EmployeeID, Name FROM Employees");
            while ($row = $employees->fetch_assoc()) { ?>
                <option value="<?php echo $row['EmployeeID']; ?>"><?php echo $row['Name']; ?></option>
            <?php } ?>
        </select><br>
        
        <input type="submit" value="Generate Report">
    </form>

    <?php if (!empty($reportData)): ?>
        <h3>Report Results</h3>
        <table border="1">
            <tr>
                <th>Service ID</th>
                <th>Vehicle ID</th>
                <th>Service Date</th>
                <th>Service Type</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Mechanic ID</th>
            </tr>
            <?php while ($row = $reportData->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['ServiceID']; ?></td>
                    <td><?php echo $row['VehicleID']; ?></td>
                    <td><?php echo $row['ServiceDate']; ?></td>
                    <td><?php echo $row['ServiceType']; ?></td>
                    <td><?php echo $row['Description']; ?></td>
                    <td><?php echo number_format($row['Cost'], 2); ?></td>
                    <td><?php echo $row['EmployeeID']; ?></td> <!-- Displaying the Mechanic ID -->
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>

<?php $conn->close(); ?>
