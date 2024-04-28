<?php
session_start()
?>
<?php
ini_set("error_reporting", 1);
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Cache-Control: pre-check=0, post-check=0", false);
header("Pragma: no-cache");
include_once 'connection.php';


$pageTitles = array(
    "index.php" => "Home",
    "projects.php" => "Projects",
    "search.php" => "Search Results",
    "download.php"=>"Download Project"

);


$currentURL = basename($_SERVER['PHP_SELF']);


$pageTitle = isset($pageTitles[$currentURL]) ? $pageTitles[$currentURL] : "Default Title";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - devalprojects</title>
    <link rel="stylesheet" href="css/public.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/cerulean/bootstrap.min.css">
 
    </style>
</head>

<body>
    <header id="header" class="header sticky-top d-flex align-items-center">
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        <div class="logo-container ml-3">
            <a href="index.php" class="logo d-flex align-items-center">
                <img src="../images/logo.png" alt="Logo">
                <h1>DevalProjects</h1>
                <span>.</span>
            </a>
        </div>
        <div class="container-fluid d-flex align-items-center justify-content-center">
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php
                    $getCategory = $conn->prepare("SELECT * FROM categories LIMIT 8");
                    $getCategory->execute();
                    while ($row = $getCategory->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <li><a href="projects.php?category=<?php echo $row['name'] ?>"><?php echo $row['name'] ?></a></li>
                    <?php
                    }
                    ?>
                </ul>
            </nav>
            <div class="search-form">
                <form class="form-inline" action="search.php" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" id="" placeholder="Search">
                        <button type="submit" class="btn btn-outline-secondary" style="border: none;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

