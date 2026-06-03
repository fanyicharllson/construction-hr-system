<?php
// index.php - Homepage
require_once 'config/database.php';
include 'includes/header.php';
?>

<div class="hero">
    <h1>Welcome to BuildMaster Construction</h1>
    <p>Your trusted partner in construction excellence</p>
</div>

<div class="company-image">
    <div class="image-container">
        <div class="placeholder-img">
            <div>
                <span style="font-size: 100px;">Home</span>
                <h2 style="margin-top: 20px;">Building Dreams, Creating Landmarks</h2>
                <p style="margin-top: 10px;">30+ Years of Excellence | 500+ Projects Completed | 1000+ Happy Clients</p>
            </div>
        </div>
    </div>
</div>

<div class="container" style="text-align: center; max-width: 800px;">
    <h2>Why Choose Us?</h2>
    <div class="stats">
        <div class="stat-card">
            <h3>30+</h3>
            <p>Years Experience</p>
        </div>
        <div class="stat-card">
            <h3>500+</h3>
            <p>Projects Completed</p>
        </div>
        <div class="stat-card">
            <h3>1000+</h3>
            <p>Happy Clients</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>