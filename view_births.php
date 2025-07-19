<?php
require_once 'includes/auth.php';
require_login();

$page_title = 'Birth Records - Village Register System';

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
    $where_clause = "WHERE b.child_name LIKE ? OR b.mother_name LIKE ? OR b.father_name LIKE ? OR b.village LIKE ? OR b.sector LIKE ?";
    $search_term = "%$search%";
    $search_params = [$search_term, $search_term, $search_term, $search_term, $search_term];
    $param_types = 'sssss';
}

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM birth_records b " . $where_clause;
$count_result = execute_query($count_query, $param_types, $search_params);
$total_records = $count_result ? $count_result->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_records / $records_per_page);

// Get birth records with pagination
$query = "SELECT b.*, u.name as registered_by_name 
          FROM birth_records b 
          JOIN users u ON b.registered_by = u.id 
          $where_clause 
          ORDER BY b.registration_date DESC 
          LIMIT $records_per_page OFFSET $offset";

$birth_records = execute_query($query, $param_types, $search_params);

include 'includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-baby"></i> Birth Records</h2>
            <a href="register_birth.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Register New Birth
            </a>
        </div>
        
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by child name, parents, village, or sector..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    <?php if (!empty($search)): ?>
                    <div class="col-12">
                        <a href="view_births.php" class="btn btn-sm btn-outline-secondary">
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
                <h5><i class="fas fa-list"></i> Birth Records (<?php echo $total_records; ?> total)</h5>
            </div>
            <div class="card-body">
                <?php if ($birth_records && $birth_records->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Child Name</th>
                                    <th>Date of Birth</th>
                                    <th>Gender</th>
                                    <th>Parents</th>
                                    <th>Location</th>
                                    <th>Registered By</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($record = $birth_records->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $record['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($record['child_name']); ?></strong>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($record['dob'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $record['gender'] == 'Male' ? 'primary' : 'pink'; ?>">
                                            <?php echo $record['gender']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Mother:</strong> <?php echo htmlspecialchars($record['mother_name']); ?><br>
                                            <strong>Father:</strong> <?php echo htmlspecialchars($record['father_name']); ?>
                                        </small>
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
                                            <a href="generate_birth_cert.php?id=<?php echo $record['id']; ?>" 
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
                                                <h5 class="modal-title">Birth Record Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Record ID:</strong> <?php echo $record['id']; ?><br>
                                                        <strong>Child Name:</strong> <?php echo htmlspecialchars($record['child_name']); ?><br>
                                                        <strong>Date of Birth:</strong> <?php echo date('F d, Y', strtotime($record['dob'])); ?><br>
                                                        <strong>Gender:</strong> <?php echo $record['gender']; ?><br>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Mother's Name:</strong> <?php echo htmlspecialchars($record['mother_name']); ?><br>
                                                        <strong>Father's Name:</strong> <?php echo htmlspecialchars($record['father_name']); ?><br>
                                                        <strong>Village:</strong> <?php echo htmlspecialchars($record['village']); ?><br>
                                                        <strong>Sector:</strong> <?php echo htmlspecialchars($record['sector']); ?><br>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Registered By:</strong> <?php echo htmlspecialchars($record['registered_by_name']); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Registration Date:</strong> <?php echo date('F d, Y g:i A', strtotime($record['registration_date'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="generate_birth_cert.php?id=<?php echo $record['id']; ?>" 
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
                        <i class="fas fa-baby fa-3x text-muted mb-3"></i>
                        <h5>No Birth Records Found</h5>
                        <p class="text-muted">
                            <?php echo !empty($search) ? 'No records match your search criteria.' : 'No birth records have been registered yet.'; ?>
                        </p>
                        <a href="register_birth.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Register First Birth
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.bg-pink {
    background-color: #e91e63 !important;
}
</style>

<?php include 'includes/footer.php'; ?>
