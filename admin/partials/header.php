<?php
session_start();
if (!isset($_SESSION['current_user'])) {
    echo '<script type="text/javascript">window.location.href="login.php";</script>';
}
?>
<?php include_once 'connection.php' ?>
<?php

$currentUserId = $_SESSION['current_user'];
$query = "SELECT image_name, full_name, occupation FROM users WHERE user_id = :userId";
$statement = $conn->prepare($query);
$statement->bindParam(':userId', $currentUserId);
$statement->execute();
$userDetails = $statement->fetch(PDO::FETCH_ASSOC);
if ($userDetails) {
    $userImage = $userDetails['image_name'];
    $userFullName = $userDetails['full_name'];
    $userOccupation = $userDetails['occupation'];
} else {

    echo "Failed to fetch user details.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="description">
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/cerulean/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="/images/logo.ico">


    <!-- Template Main CSS File -->
    <link href="/admin/assets/css/style.css" rel="stylesheet">
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center">
                <img src="images\logo.png" alt="">
                <span class="d-none d-lg-block">DevalProjects</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">



                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="../images/<?php echo $userImage ?>" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $userFullName ?></span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6><?php echo $userFullName ?></h6>
                            <span><?php echo $userOccupation ?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="profile.php?id=<?php echo $_SESSION['current_user'] ?>">
                                <i class="bi bi-person"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="profile.php?id=<?php echo $_SESSION['current_user'] ?>">
                                <i class="bi bi-gear"></i>
                                <span>Account Settings</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </a>
                        </li>

                    </ul>
                </li>

            </ul>
        </nav>

    </header>
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link " href="index.php">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                    <title>Welcome to DevalProjects Dashboard</title>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="projects.php">
                    <i class="bi bi-clipboard2"></i>
                    <span>Projects</span>
                    <title>DevalProjects| Projects Page</title>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="categories.php">
                    <i class="bi bi-folder2-open"></i>
                    <span>Categories</span>
                    <title>DevalProjects | Categories Page</title>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="downloads.php">
                    <i class="bi bi-cloud-arrow-down"></i>
                    <span>Downloads</span>
                    <title>DevalProjects | Downloads Page</title>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="users.php">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                    <title>DevalProjects Users Page</title>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="profile.php?id=<?php echo $_SESSION['current_user'] ?>">
                    <i class="bi bi-person"></i>
                    <span>Profile</span>
                    <title>DevalProjects User Profile Page</title>
                </a>
            </li>

        </ul>

    </aside>