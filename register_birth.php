<?php
require_once 'includes/auth.php';
require_login();

$page_title = 'Register Birth - Village Register System';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $child_name = trim($_POST['child_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $mother_name = trim($_POST['mother_name']);
    $father_name = trim($_POST['father_name']);
    $village = trim($_POST['village']);
    $sector = trim($_POST['sector']);
    
    // Basic validation
    if (empty($child_name) || empty($dob) || empty($gender) || empty($mother_name) || 
        empty($father_name) || empty($village) || empty($sector)) {
        $error = 'All fields are required.';
    } else {
        // Check if date is not in the future
        if (strtotime($dob) > time()) {
            $error = 'Date of birth cannot be in the future.';
        } else {
            // Insert birth record
            $query = "INSERT INTO birth_records (child_name, dob, gender, mother_name, father_name, village, sector, registered_by) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $result = execute_query($query, 'sssssssi', [
                $child_name, $dob, $gender, $mother_name, $father_name, $village, $sector, $_SESSION['user_id']
            ]);
            
            if ($result) {
                $birth_id = $result; // This will be the inserted ID
                header("Location: register_birth.php?success=Birth registered successfully! Birth ID: $birth_id");
                exit();
            } else {
                $error = 'Failed to register birth. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-baby"></i> Register New Birth</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="child_name" class="form-label">
                                    <i class="fas fa-baby"></i> Child's Full Name *
                                </label>
                                <input type="text" class="form-control" id="child_name" name="child_name" 
                                       value="<?php echo isset($_POST['child_name']) ? htmlspecialchars($_POST['child_name']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide the child's full name.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="dob" class="form-label">
                                    <i class="fas fa-calendar"></i> Date of Birth *
                                </label>
                                <input type="date" class="form-control" id="dob" name="dob" 
                                       value="<?php echo isset($_POST['dob']) ? $_POST['dob'] : ''; ?>"
                                       max="<?php echo date('Y-m-d'); ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a valid date of birth.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars"></i> Gender *
                                </label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a gender.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mother_name" class="form-label">
                                    <i class="fas fa-female"></i> Mother's Full Name *
                                </label>
                                <input type="text" class="form-control" id="mother_name" name="mother_name" 
                                       value="<?php echo isset($_POST['mother_name']) ? htmlspecialchars($_POST['mother_name']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide the mother's full name.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="father_name" class="form-label">
                                    <i class="fas fa-male"></i> Father's Full Name *
                                </label>
                                <input type="text" class="form-control" id="father_name" name="father_name" 
                                       value="<?php echo isset($_POST['father_name']) ? htmlspecialchars($_POST['father_name']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide the father's full name.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="village" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Village *
                                </label>
                                <input type="text" class="form-control" id="village" name="village" 
                                       value="<?php echo isset($_POST['village']) ? htmlspecialchars($_POST['village']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide the village name.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sector" class="form-label">
                                    <i class="fas fa-location-arrow"></i> Sector *
                                </label>
                                <input type="text" class="form-control" id="sector" name="sector" 
                                       value="<?php echo isset($_POST['sector']) ? htmlspecialchars($_POST['sector']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide the sector name.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Registered By
                                </label>
                                <input type="text" class="form-control" 
                                       value="<?php echo $_SESSION['name']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)" 
                                       readonly>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="view_births.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-list"></i> View Births
                        </a>
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Register Birth
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php include 'includes/footer.php'; ?>
