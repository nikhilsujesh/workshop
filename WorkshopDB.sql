CREATE DATABASE WorkshopDB;
USE WorkshopDB;

-- Customers Table
CREATE TABLE Customers (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Contact VARCHAR(15),
    Email VARCHAR(100),
    Address TEXT
);

-- Vehicles Table
CREATE TABLE Vehicles (
    VehicleID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT,
    Model VARCHAR(50),
    Make VARCHAR(50),
    Year YEAR,
    LicensePlate VARCHAR(15),
    FOREIGN KEY (CustomerID) REFERENCES Customers(CustomerID)
);

-- Services Table
CREATE TABLE Services (
    ServiceID INT AUTO_INCREMENT PRIMARY KEY,
    VehicleID INT,
    ServiceDate DATE,
    ServiceType VARCHAR(50),
    Cost DECIMAL(10, 2),
    Description TEXT,
    FOREIGN KEY (VehicleID) REFERENCES Vehicles(VehicleID)
);

-- Inventory Table
CREATE TABLE Inventory (
    PartID INT AUTO_INCREMENT PRIMARY KEY,
    PartName VARCHAR(50),
    Quantity INT,
    Cost DECIMAL(10, 2)
);

-- Employees Table
CREATE TABLE Employees (
    EmployeeID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Role VARCHAR(50),
    Contact VARCHAR(15)
);
-- Create Users Table
CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL
);

INSERT INTO Users (Username, Password) VALUES ('admin', MD5('admin123'));
--there are some additions needed for the manage service table, just dont want to add this here , find it own your own--