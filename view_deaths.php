<?php
require_once 'includes/auth.php';
require_login();

$page_title = 'Death Records - Village Register System';

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$search_params = [];
$param_types = '';

if (!empty($search)) {
    $where_clause = "WHERE d.deceased_name LIKE ? OR d.cause_of_death LIKE ? OR d.family_contact LIKE ? OR d.village LIKE ? OR d.sector LIKE ?";
    $search_term = "%$search%";
    $search_params = [$search_term, $search_term, $search_term, $search_term, $search_term];
    $param_types = 'sssss';
}

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM death_records d " . $where_clause;
$count_result = execute_query($count_query, $param_types, $search_params);
$total_records = $count_result ? $count_result->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_records / $records_per_page);

// Get death records with pagination
$query = "SELECT d.*, u.name as registered_by_name 
          FROM death_records d 
          JOIN users u ON d.registered_by = u.id 
          $where_clause 
          ORDER BY d.registration_date DESC 
          LIMIT $records_per_page OFFSET $offset";

$death_records = execute_query($query, $param_types, $search_params);

include 'includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-cross"></i> Death Records</h2>
            <a href="register_death.php" class="btn btn-danger">
                <i class="fas fa-plus"></i> Register New Death
            </a>
        </div>
        
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by deceased name, cause of death, family contact, village, or sector..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <?php if (!empty($search)): ?>
                    <div class="col-12">
                        <a href="view_deaths.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear Search
                        </a>
                        <span class="text-muted ms-2">Found <?php echo $total_records; ?> record(s)</span>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Records Table -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Death Records (<?php echo $total_records; ?> total)</h5>
            </div>
            <div class="card-body">
                <?php if ($death_records && $death_records->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Deceased Name</th>
                                    <th>Date of Death</th>
                                    <th>Cause of Death</th>
                                    <th>Family Contact</th>
                                    <th>Location</th>
                                    <th>Registered By</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($record = $death_records->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $record['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($record['deceased_name']); ?></strong>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($record['dod'])); ?></td>
                                    <td>
                                        <small><?php echo htmlspecialchars(substr($record['cause_of_death'], 0, 50)) . (strlen($record['cause_of_death']) > 50 ? '...' : ''); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($record['family_contact']); ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo htmlspecialchars($record['village']); ?><br>
                                            <em><?php echo htmlspecialchars($record['sector']); ?></em>
                                        </small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($record['registered_by_name']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo date('M d, Y', strtotime($record['registration_date'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="generate_death_cert.php?id=<?php echo $record['id']; ?>" 
                                               class="btn btn-sm btn-success" title="Generate Certificate">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewModal<?php echo $record['id']; ?>"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal<?php echo $record['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Death Record Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Record ID:</strong> <?php echo $record['id']; ?><br>
                                                        <strong>Deceased Name:</strong> <?php echo htmlspecialchars($record['deceased_name']); ?><br>
                                                        <strong>Date of Death:</strong> <?php echo date('F d, Y', strtotime($record['dod'])); ?><br>
                                                        <strong>Family Contact:</strong> <?php echo htmlspecialchars($record['family_contact']); ?><br>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Village:</strong> <?php echo htmlspecialchars($record['village']); ?><br>
                                                        <strong>Sector:</strong> <?php echo htmlspecialchars($record['sector']); ?><br>
                                                        <strong>Registered By:</strong> <?php echo htmlspecialchars($record['registered_by_name']); ?><br>
                                                        <strong>Registration Date:</strong> <?php echo date('F d, Y g:i A', strtotime($record['registration_date'])); ?>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <strong>Cause of Death:</strong><br>
                                                        <div class="border p-3 mt-2 bg-light">
                                                            <?php echo nl2br(htmlspecialchars($record['cause_of_death'])); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="generate_death_cert.php?id=<?php echo $record['id']; ?>" 
                                                   class="btn btn-success">
                                                    <i class="fas fa-file-pdf"></i> Generate Certificate
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-cross fa-3x text-muted mb-3"></i>
                        <h5>No Death Records Found</h5>
                        <p class="text-muted">
                            <?php echo !empty($search) ? 'No records match your search criteria.' : 'No death records have been registered yet.'; ?>
                        </p>
                        <a href="register_death.php" class="btn btn-danger">
                            <i class="fas fa-plus"></i> Register First Death
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
