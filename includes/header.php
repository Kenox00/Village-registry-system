<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Village Register System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border: none;
        }
        .btn-primary {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
        .btn-primary:hover {
            background-color: #34495e;
            border-color: #34495e;
        }
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-top: 50px;
        }
        .sidebar {
            background-color: #f8f9fa;
            min-height: calc(100vh - 56px);
            padding: 20px 0;
        }
        .nav-link.active {
            background-color: #2c3e50;
            color: white !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-clipboard-list"></i> Village Register System
            </a>
            
            <?php if (is_logged_in()): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="birthDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-baby"></i> Birth Records
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="register_birth.php">Register Birth</a></li>
                            <li><a class="dropdown-item" href="view_births.php">View Births</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="deathDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cross"></i> Death Records
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="register_death.php">Register Death</a></li>
                            <li><a class="dropdown-item" href="view_deaths.php">View Deaths</a></li>
                        </ul>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['name']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container-fluid">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <?php 
                $error_messages = [
                    'access_denied' => 'Access denied. You do not have permission to view this page.',
                    'login_required' => 'Please login to access this page.',
                    'invalid_credentials' => 'Invalid email or password.',
                    'registration_failed' => 'Registration failed. Please try again.'
                ];
                echo isset($error_messages[$_GET['error']]) ? $error_messages[$_GET['error']] : htmlspecialchars($_GET['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
