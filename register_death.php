<?php
require_once 'includes/auth.php';
require_login();

$page_title = 'Register Death - Village Register System';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $deceased_name = trim($_POST['deceased_name']);
    $dod = $_POST['dod'];
    $cause_of_death = trim($_POST['cause_of_death']);
    $family_contact = trim($_POST['family_contact']);
    $village = trim($_POST['village']);
    $sector = trim($_POST['sector']);
    
    // Basic validation
    if (empty($deceased_name) || empty($dod) || empty($cause_of_death) || 
        empty($family_contact) || empty($village) || empty($sector)) {
        $error = 'All fields are required.';
    } else {
        // Check if date is not in the future
        if (strtotime($dod) > time()) {
            $error = 'Date of death cannot be in the future.';
        } else {
            // Insert death record
            $query = "INSERT INTO death_records (deceased_name, dod, cause_of_death, family_contact, village, sector, registered_by) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $result = execute_query($query, 'ssssssi', [
                $deceased_name, $dod, $cause_of_death, $family_contact, $village, $sector, $_SESSION['user_id']
            ]);
            
            if ($result) {
                $death_id = $result; // This will be the inserted ID
                header("Location: register_death.php?success=Death registered successfully! Death ID: $death_id");
                exit();
            } else {
                $error = 'Failed to register death. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4><i class="fas fa-cross"></i> Register New Death</h4>
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
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="deceased_name" class="form-label">
                                    <i class="fas fa-user"></i> Deceased Person's Full Name *
                                </label>
                                <input type="text" class="form-control" id="deceased_name" name="deceased_name" 
                                       value="<?php echo isset($_POST['deceased_name']) ? htmlspecialchars($_POST['deceased_name']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide the deceased person's full name.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dod" class="form-label">
                                    <i class="fas fa-calendar"></i> Date of Death *
                                </label>
                                <input type="date" class="form-control" id="dod" name="dod" 
                                       value="<?php echo isset($_POST['dod']) ? $_POST['dod'] : ''; ?>"
                                       max="<?php echo date('Y-m-d'); ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a valid date of death.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="cause_of_death" class="form-label">
                                    <i class="fas fa-stethoscope"></i> Cause of Death *
                                </label>
                                <textarea class="form-control" id="cause_of_death" name="cause_of_death" 
                                          rows="3" required><?php echo isset($_POST['cause_of_death']) ? htmlspecialchars($_POST['cause_of_death']) : ''; ?></textarea>
                                <div class="invalid-feedback">
                                    Please provide the cause of death.
                                </div>
                                <div class="form-text">Provide detailed information about the cause of death.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="family_contact" class="form-label">
                                    <i class="fas fa-phone"></i> Family Contact Person *
                                </label>
                                <input type="text" class="form-control" id="family_contact" name="family_contact" 
                                       value="<?php echo isset($_POST['family_contact']) ? htmlspecialchars($_POST['family_contact']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a family contact person.
                                </div>
                                <div class="form-text">Name and contact information of the family representative.</div>
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
                        <a href="view_deaths.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-list"></i> View Deaths
                        </a>
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save"></i> Register Death
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
