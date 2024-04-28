<?php
include_once 'partials/header.php';

?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <?php
    if (isset($_SESSION['current_user']) && isset($_SESSION['success'])) {
        echo $_SESSION['success'];
        unset($_SESSION['success']);
    }
    ?>

    <section class="section dashboard">
        <div class="row">

            <!-- Left side columns -->
            <div class="col-lg-8">
                <div class="row">

                    <div class="col-xxl-4 col-md-6">
                        <a href="projects.php">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Projects <span>| All</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-clipboard2"></i>
                                        </div>
                                        <div class="ps-3">
                                            <?php
                                            $projectCount = $conn->query("SELECT COUNT(*) AS count FROM projects");
                                            $projectCount->execute();
                                            $count = $projectCount->fetchColumn();
                                            ?>
                                            <h6><?php echo $count ?></h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </a><!-- End Sales Card -->
                    </div>
                    <div class="col-xxl-4 col-md-6">
                        <a href="categories.php">
                            <div class="card info-card revenue-card">


                                <div class="card-body">
                                    <h5 class="card-title">Categories <span>| All</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-folder2-open"></i>
                                        </div>
                                        <div class="ps-3">
                                            <?php
                                            $projectCount = $conn->query("SELECT COUNT(*) AS count FROM categories");
                                            $projectCount->execute();
                                            $count = $projectCount->fetchColumn();
                                            ?>
                                            <h6><?php echo $count ?></h6>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </a>
                    </div>

                    <div class="col-xxl-4 col-xl-12">
                        <a href="users.php">

                            <div class="card info-card customers-card">

                                <div class="card-body">
                                    <h5 class="card-title">Users <span>| All</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div class="ps-3">
                                            <?php
                                            $projectCount = $conn->query("SELECT COUNT(*) AS count FROM users");
                                            $projectCount->execute();
                                            $count = $projectCount->fetchColumn();
                                            ?>
                                            <h6><?php echo $count ?></h6>

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </a>
                    </div>

                    <!-- Top Selling -->
                    <div class="col-12">
                        <div class="card top-selling overflow-auto">

                            <div class="card-body pb-0">
                                <h5 class="card-title">Top Projects <span>| Recent</span></h5>

                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th scope="col">Preview</th>
                                            <th scope="col">Project</th>
                                            <th scope="col">Date Added</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $getProjects = $conn->query("SELECT * FROM projects LIMIT 5");

                                        while ($project = $getProjects->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                            <tr>
                                                <th scope="row">
                                                    <a href="#" onclick="openDownload('<?php echo urlencode($project['title']) ?>')"><img src="../images/<?php echo $project['image_name'] ?>" alt=""></a>
                                                </th>
                                                <td>
                                                    <a href="#" onclick="openDownload('<?php echo urlencode($project['title']) ?>')" <?php echo $project['title'] ?> class="text-primary fw-bold"><?php echo $project['title'] ?></a>
                                                </td>
                                                <?php

                                                $dateCreatedArr = new DateTime($project['created_at']);
                                                $dateCreated = $dateCreatedArr->format('l jS F Y');
                                                ?>

                                                <td><?php echo $dateCreated ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div><!-- End Top Selling -->

                </div>
            </div><!-- End Left side columns -->


            <div class="col-lg-4">
                <div class="card">
                    <div class="filter">
                        <a id="filterDropdown" class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                            </li>
                            <li><a class="dropdown-item" href="#" onclick="filterData('all')">All</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterData('today')">Today</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterData('this_month')">This Month</a></li>
                            <li><a class="dropdown-item" href="#" onclick="filterData('this_year')">This Year</a></li>
                        </ul>
                    </div>

                    <div class="card-body pb-3">
                        <h5 class="card-title">Projects Updates <span id="filterLabel">| All</span></h5>
                        <div id="newsContainer" class="news">
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM projects LIMIT 10");
                            $stmt->execute();

                            if ($stmt) {
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                    <div class="post-item clearfix">
                                        <img src="../images/<?php echo $row['image_name'] ?>" alt="">
                                        <h4><a href="#" onclick="openDownload('<?php echo urlencode($row['title']) ?>')"><?php echo $row['title'] ?></a></h4>

                                        <script>
                                            function openDownload(projectTitle) {
                                                var baseUrl = window.location.origin;
                                                var downloadUrl = baseUrl + '/download.php?project=' + projectTitle;

                                                window.location.href = downloadUrl;
                                            }
                                        </script>
                                        <p><?php echo $row['description'] ?></p>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            <script>
                function filterData(filter) {
                    var filterLabel = $('#filterLabel');
                    var filterDropdown = $('#filterDropdown');

     
                    filterLabel.text('| ' + filter.replace('_', ' '));


                    $.ajax({
                        url: 'filter.php',
                        type: 'GET',
                        data: {
                            filter: filter
                        },
                        success: function(data) {
                            $('#newsContainer').html(data);
                        }
                    });
                }
            </script>

    </section>

</main><!-- End #main -->



<?php
include_once 'partials/footer.php'
?>