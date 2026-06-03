 
<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Company HR System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <div class="logo-img">
                    <span>🏗️</span>
                </div>
                <div class="logo-text">
                    <h1>BuildMaster Construction</h1>
                    <p>Building Excellence Since 1995</p>
                </div>
            </div>
            
            <div class="dropdown">
                <button class="dropbtn">Department ▼</button>
                <div class="dropdown-content">
                    <a href="department.php?dept=Management">Management</a>
                    <a href="department.php?dept=Construction">Construction</a>
                    <a href="department.php?dept=Engineering">Engineering</a>
                    <a href="department.php?dept=Safety">Safety</a>
                    <a href="department.php?dept=Procurement">Procurement</a>
                    <a href="department.php?dept=HR">HR</a>
                    <a href="department.php?dept=Finance">Finance</a>
                    <a href="department.php?dept=Logistics">Logistics</a>
                    <a href="department.php?dept=Maintenance">Maintenance</a>
                    <a href="department.php?dept=Quality Control">Quality Control</a>
                </div>
            </div>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" style="color: white; margin-left: 15px;">Logout</a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <main>
<?php
?>