<?php
require_once 'includes/auth.php';
require_login();

$page_title = 'Dashboard - Village Register System';

// Get statistics
$birth_count_query = "SELECT COUNT(*) as count FROM birth_records";
$birth_result = execute_query($birth_count_query);
$birth_count = $birth_result ? $birth_result->fetch_assoc()['count'] : 0;

$death_count_query = "SELECT COUNT(*) as count FROM death_records";
$death_result = execute_query($death_count_query);
$death_count = $death_result ? $death_result->fetch_assoc()['count'] : 0;

// Get recent registrations
$recent_births_query = "SELECT b.*, u.name as registered_by_name FROM birth_records b 
                       JOIN users u ON b.registered_by = u.id 
                       ORDER BY b.registration_date DESC LIMIT 5";
$recent_births = execute_query($recent_births_query);

$recent_deaths_query = "SELECT d.*, u.name as registered_by_name FROM death_records d 
                       JOIN users u ON d.registered_by = u.id 
                       ORDER BY d.registration_date DESC LIMIT 5";
$recent_deaths = execute_query($recent_deaths_query);

include 'includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-12">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        <p class="text-muted">Welcome back, <?php echo $_SESSION['name']; ?>! Here's an overview of the village records.</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><?php echo $birth_count; ?></h4>
                        <p class="mb-0">Total Births</p>
                    </div>
                    <div>
                        <i class="fas fa-baby fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="view_births.php" class="text-white text-decoration-none">
                    View all births <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><?php echo $death_count; ?></h4>
                        <p class="mb-0">Total Deaths</p>
                    </div>
                    <div>
                        <i class="fas fa-cross fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="view_deaths.php" class="text-white text-decoration-none">
                    View all deaths <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><?php echo $birth_count + $death_count; ?></h4>
                        <p class="mb-0">Total Records</p>
                    </div>
                    <div>
                        <i class="fas fa-chart-bar fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="text-white">All registered records</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4><?php echo ucfirst($_SESSION['role']); ?></h4>
                        <p class="mb-0">Your Role</p>
                    </div>
                    <div>
                        <i class="fas fa-user-shield fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <span class="text-white">Access Level</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <a href="register_birth.php" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fas fa-baby"></i> Register New Birth
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="register_death.php" class="btn btn-danger btn-lg w-100 mb-2">
                            <i class="fas fa-cross"></i> Register New Death
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-baby"></i> Recent Birth Registrations</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_births && $recent_births->num_rows > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php while ($birth = $recent_births->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($birth['child_name']); ?></h6>
                                    <small><?php echo date('M d, Y', strtotime($birth['registration_date'])); ?></small>
                                </div>
                                <p class="mb-1">
                                    <small>Born: <?php echo date('M d, Y', strtotime($birth['dob'])); ?> | 
                                    <?php echo htmlspecialchars($birth['village']); ?>, <?php echo htmlspecialchars($birth['sector']); ?></small>
                                </p>
                                <small>Registered by: <?php echo htmlspecialchars($birth['registered_by_name']); ?></small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No birth records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cross"></i> Recent Death Registrations</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_deaths && $recent_deaths->num_rows > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php while ($death = $recent_deaths->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($death['deceased_name']); ?></h6>
                                    <small><?php echo date('M d, Y', strtotime($death['registration_date'])); ?></small>
                                </div>
                                <p class="mb-1">
                                    <small>Died: <?php echo date('M d, Y', strtotime($death['dod'])); ?> | 
                                    <?php echo htmlspecialchars($death['village']); ?>, <?php echo htmlspecialchars($death['sector']); ?></small>
                                </p>
                                <small>Registered by: <?php echo htmlspecialchars($death['registered_by_name']); ?></small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No death records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
