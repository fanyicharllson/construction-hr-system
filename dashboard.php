<?php
// dashboard.php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get statistics
$total_employees = $conn->query("SELECT COUNT(*) as count FROM employees")->fetch_assoc()['count'];
$active_employees = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status='active'")->fetch_assoc()['count'];
$total_departments = $conn->query("SELECT COUNT(*) as count FROM departments")->fetch_assoc()['count'];

include 'includes/header.php';
?>

<div class="hero" style="background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); padding: 50px 20px;">
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>HR Dashboard - Construction Company Management System</p>
</div>

<div class="container" style="max-width: 1200px;">
    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $total_employees; ?></h3>
            <p>Total Employees</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $active_employees; ?></h3>
            <p>Active Employees</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_departments; ?></h3>
            <p>Departments</p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <p>Use the Department dropdown in the header to view and manage department employees.</p>
        <?php if($_SESSION['role'] == 'admin'): ?>
            <p style="color: var(--accent); margin-top: 10px;">✅ You have administrative privileges - you can add, edit, and delete employees.</p>
        <?php else: ?>
            <p style="color: var(--secondary); margin-top: 10px;">👥 You are logged in as an employee - view-only access.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>