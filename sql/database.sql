
CREATE DATABASE IF NOT EXISTS construction_hr;
USE construction_hr;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    home_address TEXT NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department_id INT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'employee') DEFAULT 'employee',
    reset_token VARCHAR(255),
    reset_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Departments table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Employees table (10 fields as required)
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    department_id INT,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    hire_date DATE,
    salary DECIMAL(10,2),
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Insert default departments
INSERT INTO departments (name) VALUES 
('Management'),
('Construction'),
('Engineering'),
('Safety'),
('Procurement'),
('HR'),
('Finance'),
('Logistics'),
('Maintenance'),
('Quality Control');

-- Insert admin user (password: Admin@123)
INSERT INTO users (full_name, home_address, email, phone, department_id, username, password, role) VALUES 
('System Administrator', 'Main Office', 'admin@construction.com', '1234567890', NULL, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Insert sample employees
INSERT INTO employees (employee_id, full_name, position, department_id, email, phone, hire_date, salary, status) VALUES
('EMP001', 'John Smith', 'Project Manager', 1, 'john.smith@construction.com', '555-0101', '2020-01-15', 85000.00, 'active'),
('EMP002', 'Sarah Johnson', 'Site Engineer', 2, 'sarah.j@construction.com', '555-0102', '2020-03-20', 65000.00, 'active'),
('EMP003', 'Mike Williams', 'Safety Officer', 4, 'mike.w@construction.com', '555-0103', '2020-06-10', 55000.00, 'active'),
('EMP004', 'Lisa Brown', 'HR Manager', 6, 'lisa.b@construction.com', '555-0104', '2019-11-01', 70000.00, 'active'),
('EMP005', 'David Lee', 'Procurement Specialist', 5, 'david.lee@construction.com', '555-0105', '2020-08-15', 58000.00, 'on_leave'),
('EMP006', 'Emma Wilson', 'Accountant', 7, 'emma.w@construction.com', '555-0106', '2020-02-28', 62000.00, 'active'),
('EMP007', 'James Taylor', 'Logistics Coordinator', 8, 'james.t@construction.com', '555-0107', '2020-09-05', 54000.00, 'active'),
('EMP008', 'Maria Garcia', 'Quality Engineer', 10, 'maria.g@construction.com', '555-0108', '2020-04-12', 63000.00, 'active'),
('EMP009', 'Robert Chen', 'Maintenance Lead', 9, 'robert.c@construction.com', '555-0109', '2019-12-01', 59000.00, 'active'),
('EMP010', 'Patricia White', 'Civil Engineer', 3, 'patricia.w@construction.com', '555-0110', '2020-07-19', 72000.00, 'active');