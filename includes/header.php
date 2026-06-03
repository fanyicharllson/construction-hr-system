<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentRole = $_SESSION['role'] ?? '';
$isPrivilegedUser = in_array($currentRole, ['admin', 'super_admin'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>CIMEN Limited | Human Resource Module</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="site-header navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid px-4 px-lg-5">
            <a class="navbar-brand brand-mark" href="/index.php">
                <span class="brand-badge">CL</span>
                <span class="brand-copy">
                    <strong>CIMEN Limited</strong>
                    <small>Construction and Human Resource System</small>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="siteNav">
                <div class="navbar-nav align-items-lg-center gap-lg-2 mt-3 mt-lg-0">
                    <a class="nav-link" href="/index.php">Home</a>
                    <a class="nav-link" href="/dashboard.php">Dashboard</a>

                    <?php if ($isPrivilegedUser): ?>
                        <div class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle department-toggle" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                                Departments
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end department-menu">
                                <li><a class="dropdown-item" href="/department.php?dept=Management">Management</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Construction">Construction</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Engineering">Engineering</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Safety">Safety</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Procurement">Procurement</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=HR">HR</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Finance">Finance</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Logistics">Logistics</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Maintenance">Maintenance</a></li>
                                <li><a class="dropdown-item" href="/department.php?dept=Quality Control">Quality Control</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="nav-item dropdown">
                            <button class="nav-link dropdown-toggle user-toggle" data-bs-toggle="dropdown" aria-expanded="false" type="button">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end user-menu">
                                <li><span class="dropdown-item-text text-muted small text-uppercase">Role: <?php echo htmlspecialchars($currentRole ?: 'guest'); ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="nav-actions d-flex gap-2 ms-lg-3">
                            <a class="btn btn-ghost" href="/login.php">Login</a>
                            <a class="btn btn-primary btn-brand" href="/register.php">Sign up</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <main class="site-main">
        <div class="inner-wrap">