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
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];

$departmentSql = "SELECT d.name FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.id = ? LIMIT 1";
$departmentStmt = $conn->prepare($departmentSql);
$departmentStmt->bind_param('i', $_SESSION['user_id']);
$departmentStmt->execute();
$departmentResult = $departmentStmt->get_result();
$userDepartment = $departmentResult->fetch_assoc()['name'] ?? null;

$pageTitle = 'CIMEN Limited | Dashboard';

include 'includes/header.php';
?>

<section class="hero-section mb-4 animate-rise">
    <div class="row g-4 align-items-center">
        <div class="col-lg-7">
            <div class="eyebrow">Control center</div>
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>.</h1>
            <p class="lead mt-3 mb-4">This dashboard keeps the HR module organized for the current account and role.</p>

            <div class="hero-actions">
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin'): ?>
                    <a href="admin/manage_employees.php" class="btn btn-accent btn-lg">Manage Employees</a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] === 'super_admin'): ?>
                    <a href="admin/manage_users.php" class="btn btn-outline-light btn-lg">Manage Users</a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-secondary btn-lg">Home</a>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="hero-visual">
                <div class="visual-panel">
                    <div class="visual-header">
                        <div>
                            <p class="visual-title">Session overview</p>
                            <div class="visual-subtitle"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $_SESSION['role']))); ?> account</div>
                        </div>
                        <span class="visual-badge"><?php echo htmlspecialchars($userDepartment ?: 'No department'); ?></span>
                    </div>

                    <div class="visual-grid">
                        <div class="visual-tile">
                            <strong><?php echo (int) $total_employees; ?></strong>
                            <span>Total employees</span>
                        </div>
                        <div class="visual-tile">
                            <strong><?php echo (int) $active_employees; ?></strong>
                            <span>Active employees</span>
                        </div>
                        <div class="visual-tile">
                            <strong><?php echo (int) $total_departments; ?></strong>
                            <span>Departments</span>
                        </div>
                        <div class="visual-tile">
                            <strong><?php echo (int) $total_users; ?></strong>
                            <span>User accounts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-card animate-rise">
    <div class="section-heading">
        <div class="eyebrow">Quick status</div>
        <h2>Everything is organized around your role.</h2>
        <p>Use the navigation to move into departments, employee management, or user administration.</p>
    </div>

    <div class="feature-grid">
        <div class="feature-card">
            <h3>Profile access</h3>
            <p>Department assignment is tied to the account so the UI can surface role-specific context.</p>
        </div>
        <div class="feature-card">
            <h3>Workflow control</h3>
            <p>Admins and super admins get direct routes to their management tools from the header and dashboard.</p>
        </div>
        <div class="feature-card">
            <h3>Consistent layout</h3>
            <p>The redesigned system uses one visual language across home, authentication, and management screens.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>