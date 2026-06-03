<?php
// index.php - Homepage
require_once 'config/database.php';
 $pageTitle = 'CIMEN Limited | Home';
include 'includes/header.php';
?>

<section class="hero-section mb-4 animate-rise">
    <div class="row g-4 align-items-center">
        <div class="col-lg-7">
            <div class="eyebrow">Cement and construction HR platform</div>
            <h1>Professional workforce management for CIMEN Limited.</h1>
            <p class="lead mt-3 mb-4">Track departments, manage employee records, and keep admin workflows organized in one clean system built for a construction business.</p>

            <div class="hero-actions mb-4">
                <a href="login.php" class="btn btn-accent btn-lg">Employee Login</a>
                <a href="register.php" class="btn btn-outline-light btn-lg">Create Account</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">Open Dashboard</a>
                <?php endif; ?>
            </div>

            <div class="hero-kpis">
                <div class="metric-card">
                    <span>10 Departments</span>
                    <div class="value">Structured access</div>
                </div>
                <div class="metric-card">
                    <span>Admin Controls</span>
                    <div class="value">Secure operations</div>
                </div>
                <div class="metric-card">
                    <span>Employee Records</span>
                    <div class="value">Centralized data</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="hero-visual">
                <div class="visual-panel d-flex flex-column justify-content-between">
                    <div class="visual-header">
                        <div>
                            <p class="visual-title">CIMEN Limited Headquarters</p>
                            <div class="visual-subtitle">Cement, construction, and people operations</div>
                        </div>
                        <span class="visual-badge">Live HR Suite</span>
                    </div>

                    <div class="visual-grid">
                        <div class="visual-tile">
                            <strong>Company overview</strong>
                            <span>Cleanly manage staff information, authentication, and department records.</span>
                        </div>
                        <div class="visual-tile">
                            <strong>Access control</strong>
                            <span>Department tools remain reserved for privileged users and administrators.</span>
                        </div>
                        <div class="visual-tile">
                            <strong>Export ready</strong>
                            <span>Tables are prepared for print-to-PDF workflows when you need reports.</span>
                        </div>
                        <div class="visual-tile">
                            <strong>Responsive UI</strong>
                            <span>The interface adapts from desktop control rooms to mobile field use.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-card animate-rise">
    <div class="section-heading">
        <div class="eyebrow">Why the platform stands out</div>
        <h2>Built for clarity, control, and speed.</h2>
        <p>Every screen now uses a consistent visual system so the application feels like one product, not a collection of stitched pages.</p>
    </div>

    <div class="feature-grid">
        <div class="feature-card">
            <h3>Department access</h3>
            <p>The department dropdown is restricted to privileged users, matching the control model in the brief.</p>
        </div>
        <div class="feature-card">
            <h3>Employee sign up</h3>
            <p>Registration now includes department selection so user accounts can be tied to the company structure.</p>
        </div>
        <div class="feature-card">
            <h3>Operational tables</h3>
            <p>Admin tables are styled for scanning, editing, and future PDF export workflows without visual clutter.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>