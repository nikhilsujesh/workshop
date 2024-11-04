<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "WorkshopDB");

// Handle form submission for adding a new service
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicleId = $_POST['vehicle_id'];
    $serviceDate = $_POST['service_date'];
    $serviceType = $_POST['service_type'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $employeeId = $_POST['employee_id']; // Get selected EmployeeID

    // Insert service into Services table
    $sql = "INSERT INTO Services (VehicleID, ServiceDate, ServiceType, Description, Cost, EmployeeID) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssdi", $vehicleId, $serviceDate, $serviceType, $description, $cost, $employeeId);
    $stmt->execute();

    // Get the last inserted ServiceID
    $serviceId = $conn->insert_id;

    // Insert parts into ServiceParts table
    if (isset($_POST['part_id']) && isset($_POST['quantity'])) {
        $partIds = $_POST['part_id'];
        $quantities = $_POST['quantity'];

        foreach ($partIds as $index => $partId) {
            if (!empty($partId) && !empty($quantities[$index])) {
                $quantity = intval($quantities[$index]);
                // Insert into ServiceParts
                $sqlParts = "INSERT INTO ServiceParts (ServiceID, PartID, Quantity) VALUES (?, ?, ?)";
                $stmtParts = $conn->prepare($sqlParts);
                $stmtParts->bind_param("iii", $serviceId, $partId, $quantity);
                $stmtParts->execute();
            }
        }
    }
}

// Fetch all services with vehicle and customer names
$sql = "SELECT Services.*, Vehicles.Make, Vehicles.Model, Customers.Name AS CustomerName, Employees.Name AS MechanicName 
        FROM Services 
        JOIN Vehicles ON Services.VehicleID = Vehicles.VehicleID 
        JOIN Customers ON Vehicles.CustomerID = Customers.CustomerID
        JOIN Employees ON Services.EmployeeID = Employees.EmployeeID"; // Join with Employees
$result = $conn->query($sql);

// Fetch all vehicles for the vehicle dropdown
$vehicles = $conn->query("SELECT VehicleID, CONCAT(Make, ' ', Model, ' (', LicensePlate, ')') AS VehicleInfo FROM Vehicles");

// Fetch all inventory items for parts selection
$inventoryItems = $conn->query("SELECT PartID, PartName, Cost FROM Inventory");

// Fetch all employees for the mechanic dropdown
$employees = $conn->query("SELECT EmployeeID, Name FROM Employees");

// Fetch predefined service types and costs
$serviceTypes = [
    "Regular Water Service" => 700,
    "Brake Inspection" => 300
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Services</title>
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
        .add-service-form {
            margin-bottom: 20px;
        }
        .part-row {
            margin-bottom: 10px;
        }
    </style>
    <script>
        function calculateTotal() {
            let totalCost = parseFloat(document.getElementById('service_cost').value) || 0; // Start with service cost
            const partRows = document.querySelectorAll('.part-row');
            partRows.forEach(row => {
                const quantity = parseInt(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                totalCost += quantity * price;
            });
            document.getElementById('total_cost').value = totalCost.toFixed(2);
        }

        function updateServiceCost() {
            const serviceSelect = document.getElementById('service_type');
            const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
            const cost = selectedService.getAttribute('data-cost');
            document.getElementById('service_cost').value = cost;
            calculateTotal(); // Recalculate total cost when service is changed
        }

        function addPartRow() {
            const partsContainer = document.getElementById('parts_container');
            const newRow = document.createElement('div');
            newRow.classList.add('part-row');

            newRow.innerHTML = `
                <label>Part:</label>
                <select class="part" name="part_id[]" onchange="calculateTotal()" required>
                    <option value="">Select Part</option>
                    <?php 
                    $inventoryItems->data_seek(0); // Reset pointer to fetch again
                    while ($item = $inventoryItems->fetch_assoc()) { ?>
                        <option value="<?php echo $item['PartID']; ?>" data-price="<?php echo $item['Cost']; ?>">
                            <?php echo $item['PartName']; ?>
                        </option>
                    <?php } ?>
                </select>
                <label>Quantity:</label>
                <input type="number" class="quantity" min="0" value="0" onchange="calculateTotal()"><br>
                <input type="hidden" class="price" value="0">
            `;

            partsContainer.appendChild(newRow);
        }

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('part')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const priceInput = e.target.closest('.part-row').querySelector('.price');
                priceInput.value = price;
                calculateTotal();
            }
        });
    </script>
</head>
<body>
    <h2>Manage Services</h2>

    <div class="add-service-form">
        <h3>Add New Service</h3>
        <form action="manage_services.php" method="POST" onsubmit="calculateTotal()">
            <label>Vehicle:</label><br>
            <select name="vehicle_id" required>
                <option value="">Select Vehicle</option>
                <?php while ($row = $vehicles->fetch_assoc()) { ?>
                    <option value="<?php echo $row['VehicleID']; ?>"><?php echo $row['VehicleInfo']; ?></option>
                <?php } ?>
            </select><br>
            <label>Service Date:</label><br>
            <input type="date" name="service_date" required><br>
            <label>Service Type:</label><br>
            <select id="service_type" name="service_type" onchange="updateServiceCost()" required>
                <option value="">Select Service</option>
                <?php foreach ($serviceTypes as $type => $cost) { ?>
                    <option value="<?php echo $type; ?>" data-cost="<?php echo $cost; ?>"><?php echo $type; ?></option>
                <?php } ?>
            </select><br>
            <input type="hidden" id="service_cost" value="0"><br>
            <label>Description:</label><br>
            <textarea name="description" required></textarea><br>
            
            <!-- New Mechanic Selection -->
            <label>Mechanic:</label><br>
            <select name="employee_id" required>
                <option value="">Select Mechanic</option>
                <?php while ($employee = $employees->fetch_assoc()) { ?>
                    <option value="<?php echo $employee['EmployeeID']; ?>"><?php echo $employee['Name']; ?></option>
                <?php } ?>
            </select><br>

            <h4>Parts Used</h4>
            <div id="parts_container">
                <div class="part-row">
                    <label>Part:</label>
                    <select class="part" name="part_id[]" onchange="calculateTotal()" required>
                        <option value="">Select Part</option>
                        <?php 
                        $inventoryItems->data_seek(0); // Reset pointer to fetch again
                        while ($item = $inventoryItems->fetch_assoc()) { ?>
                            <option value="<?php echo $item['PartID']; ?>" data-price="<?php echo $item['Cost']; ?>">
                                <?php echo $item['PartName']; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <label>Quantity:</label>
                    <input type="number" class="quantity" min="0" value="0" onchange="calculateTotal()"><br>
                    <input type="hidden" class="price" value="0">
                </div>
            </div>

            <button type="button" onclick="addPartRow()">Add Another Part</button><br>
            <label>Total Cost:</label><br>
            <input type="text" id="total_cost" name="cost" readonly><br>

            <input type="submit" value="Add Service">
        </form>
    </div>

    <h3>Existing Services</h3>
    <table>
        <tr>
            <th>Service ID</th>
            <th>Vehicle</th>
            <th>Service Date</th>
            <th>Service Type</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Mechanic</th>
            <th>Invoice</th>
        </tr>
        <?php while ($service = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $service['ServiceID']; ?></td>
                <td><?php echo $service['Make'] . ' ' . $service['Model']; ?></td>
                <td><?php echo $service['ServiceDate']; ?></td>
                <td><?php echo $service['ServiceType']; ?></td>
                <td><?php echo $service['Description']; ?></td>
                <td><?php echo $service['Cost']; ?></td>
                <td><?php echo $service['MechanicName']; ?></td>
                <td><a href="generate_invoice.php?service_id=<?php echo $service['ServiceID']; ?>" target="_blank">Generate Invoice</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
