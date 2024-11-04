<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9; /* Light grey background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
        }
        .login-container {
            background-color: white; /* White background for the login form */
            padding: 20px;
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for depth */
            width: 300px; /* Fixed width for the form */
        }
        h2 {
            text-align: center;
            color: #007bff; /* Blue color for the heading */
        }
        label {
            display: block;
            margin: 10px 0 5px; /* Margin for spacing */
            color: #333; /* Darker color for labels */
        }
        input[type="text"],
        input[type="password"] {
            width: 100%; /* Full width for input fields */
            padding: 10px;
            margin-bottom: 15px; /* Space between inputs */
            border: 1px solid #ddd; /* Light border */
            border-radius: 5px; /* Rounded input corners */
        }
        input[type="submit"] {
            width: 100%; /* Full width for submit button */
            padding: 10px;
            background-color: #007bff; /* Blue background for button */
            color: white; /* White text for button */
            border: none;
            border-radius: 5px; /* Rounded button corners */
            cursor: pointer; /* Pointer cursor on hover */
            font-size: 16px; /* Larger text for button */
        }
        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .error-message {
            color: red; /* Red color for error messages */
            text-align: center; /* Center the error message */
            margin-top: 10px; /* Space above the error message */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="login.php" method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <input type="submit" value="Login">
        </form>
        <?php
        // Check if there’s an error message to display
        if (isset($_GET['error'])) {
            echo "<p class='error-message'>" . htmlspecialchars($_GET['error']) . "</p>";
        }
        ?>
    </div>
</body>
</html>

<?php
session_start(); // Start session for tracking login

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Hash the password for comparison (assuming MD5 was used)
    $hashedPassword = md5($password);

    // Check for matching user
    $sql = "SELECT * FROM Users WHERE Username = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $hashedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Login successful
        $_SESSION['username'] = $username; // Store username in session
        header("Location: dashboard.php"); // Redirect to dashboard
        exit();
    } else {
        // Login failed, redirect back with an error
        header("Location: login.php?error=Invalid username or password");
    }
}
$conn->close();
?>
