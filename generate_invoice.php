<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the Service ID from the URL
$service_id = $_GET['service_id'];

// Fetch service details
$sql = "SELECT Services.*, Vehicles.Make, Vehicles.Model, Customers.Name AS CustomerName, Employees.Name AS MechanicName 
        FROM Services 
        JOIN Vehicles ON Services.VehicleID = Vehicles.VehicleID 
        JOIN Customers ON Vehicles.CustomerID = Customers.CustomerID
        JOIN Employees ON Services.EmployeeID = Employees.EmployeeID
        WHERE Services.ServiceID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

// Fetch parts used for this service
$sqlParts = "SELECT SP.Quantity, I.PartName, I.Cost 
             FROM ServiceParts SP
             JOIN Inventory I ON SP.PartID = I.PartID 
             WHERE SP.ServiceID = ?";
$stmtParts = $conn->prepare($sqlParts);
$stmtParts->bind_param("i", $service_id);
$stmtParts->execute();
$resultParts = $stmtParts->get_result();

// Calculate total parts cost
$partsTotal = 0;
$parts = [];
while ($part = $resultParts->fetch_assoc()) {
    $totalCost = $part['Quantity'] * $part['Cost'];
    $partsTotal += $totalCost;
    $parts[] = [
        'name' => $part['PartName'],
        'quantity' => $part['Quantity'],
        'cost' => $totalCost
    ];
}

// Total service cost
$totalCost = $service['Cost'] + $partsTotal;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice for Service ID: <?php echo $service['ServiceID']; ?></title>
</head>
<body>
    <h2>Invoice for Service ID: <?php echo $service['ServiceID']; ?></h2>
    <p><strong>Customer Name:</strong> <?php echo $service['CustomerName']; ?></p>
    <p><strong>Vehicle:</strong> <?php echo $service['Make'] . ' ' . $service['Model']; ?></p>
    <p><strong>Service Date:</strong> <?php echo $service['ServiceDate']; ?></p>
    <p><strong>Service Type:</strong> <?php echo $service['ServiceType']; ?></p>
    <p><strong>Description:</strong> <?php echo $service['Description']; ?></p>
    <p><strong>Mechanic:</strong> <?php echo $service['MechanicName']; ?></p>
    
    
    <table border="1">

        <?php foreach ($parts as $part) { ?>
            <tr>
                <td><?php echo $part['name']; ?></td>
                <td><?php echo $part['quantity']; ?></td>
                <td><?php echo number_format($part['cost'], 2); ?></td>
            </tr>
        <?php } ?>
    </table>

    <h3>Total Cost</h3>
    <p><strong>Service Cost:</strong> <?php echo number_format($service['Cost'], 2); ?></p>
    
    <button onclick="window.print()">Print Invoice</button>
</body>
</html>
