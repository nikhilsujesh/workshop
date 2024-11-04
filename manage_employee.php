<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

// Handle form submission for adding a new employee
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeName = $_POST['employee_name'];
    $role = $_POST['role'];
    $contact = $_POST['contact'];

    // Insert employee record
    $sql = "INSERT INTO Employees (Name, Role, Contact) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $employeeName, $role, $contact);
    $stmt->execute();
}

// Handle employee deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $deleteSql = "DELETE FROM Employees WHERE EmployeeID = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $deleteId);
    $deleteStmt->execute();
}

// Fetch all employees
$employees = $conn->query("SELECT * FROM Employees");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Employees</title>
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
    </style>
</head>
<body>
    <h2>Manage Employees</h2>

    <div class="add-employee-form">
        <h3>Add New Employee</h3>
        <form action="manage_employee.php" method="POST">
            <label>Name:</label><br>
            <input type="text" name="employee_name" required><br>
            <label>Role:</label><br>
            <input type="text" name="role" required><br>
            <label>Contact:</label><br>
            <input type="text" name="contact" required><br>
            <input type="submit" value="Add Employee">
        </form>
    </div>

    <h3>Employee List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Role</th>
            <th>Contact</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $employees->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['EmployeeID']; ?></td>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Role']; ?></td>
            <td><?php echo $row['Contact']; ?></td>
            <td><a href="manage_employee.php?delete_id=<?php echo $row['EmployeeID']; ?>" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a></td>
        </tr>
        <?php } ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>

<?php $conn->close(); ?>
