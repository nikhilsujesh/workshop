<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}
?> 
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        /* Reset some default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .dashboard-container {
            width: 80%;
            max-width: 800px;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333333;
        }

        .button-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .dashboard-button {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-align: center;
        }

        .dashboard-button:hover {
            background-color: #0056b3;
        }

        .logout {
            margin-top: 30px;
            color: #ff4d4d;
            text-decoration: none;
        }

        .logout:hover {
            color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome to the Workshop Management System, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Select an option below:</p>
        
        <div class="button-container">
            <a href="manage_customers.php" class="dashboard-button">Manage Customers</a>
            <a href="manage_vehicles.php" class="dashboard-button">Manage Vehicles</a>
            <a href="manage_services.php" class="dashboard-button">Manage Services</a>
            <a href="manage_inventory.php" class="dashboard-button">Manage Inventory</a>
            <a href="manage_employee.php" class="dashboard-button">Manage Employees</a>
            <a href="view_reports.php" class="dashboard-button">View Reports</a>
             <!-- New button for invoice generation -->
        </div>
        
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
