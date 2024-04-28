<?php
include_once 'includes/header.php';
?>
<?php
$search = htmlspecialchars($_GET['search']);
if (empty($search) || strlen($search) < 1 || $search == "") {
    echo '<script type="text/javascript">window.location.href="index.php";</script>';
}
?>
<main>
    <div class="page">
        <div class="current-page">
            <span class="page-directory">
                Home><span>search - <?php echo $search ?> </span>
            </span>
            <p class="search-text">
                <span class="default-text"><?php echo $search ?></span>-<span>Search result</span>
            </p>
        </div>
        <div class="search-form" style="width: 90%;">
            <form class="form-inline" action="search.php" method="GET">
                <div class="input-group search-input-group">
                    <input type="text" class="form-control" name="search" id="" placeholder="Search">
                    <button type="submit" class="btn btn-outline-secondary" style="border: none;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <section>
            <div class="projects-section filtered-project">
                <div class="project-left">
                    <?php
                    $limit = 6;
                    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; 
                    $offset = ($currentPage - 1) * $limit; 

                    $getProjects = $conn->prepare(
                        '
                        SELECT * 
                        FROM projects 
                        WHERE 
                            category LIKE :search OR 
                            description LIKE :search OR
                            title LIKE :search OR
                            created_at LIKE :search OR
                            language LIKE :search
                        LIMIT :limit OFFSET :offset'
                    );

                    $searchValue = '%' . $search . '%';

                    $getProjects->bindValue(':search', $searchValue);
                    $getProjects->bindParam(':limit', $limit, PDO::PARAM_INT);
                    $getProjects->bindParam(':offset', $offset, PDO::PARAM_INT);
                    $getProjects->execute();
                    if ($getProjects->rowCount() > 0) {

                    ?>

                        <div class="card-container">
                            <?php
                            while ($project = $getProjects->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <div class="card">
                                    <a class="card-link" href="projects.php?project=<?php echo $project['title'] ?>">
                                        <img src="/images/<?php echo $project['image_name'] ?>" alt="">
                                        <div class="card-content">
                                            <p><?php echo $project['title']; ?></p>
                                            <?php
                                            $dateCreatedArr = new DateTime($project['created_at']);
                                            $dateCreated = $dateCreatedArr->format('l jS F Y');
                                            ?>
                                            <p>Posted on: <?php echo $dateCreated; ?></p>
                                            <a class="view-more" href="download.php?project=<?php echo $project['title'] ?>">View More</a>
                                            <span><?php echo $project['language']; ?></span>
                                            <!-- Add other project information as needed -->
                                        </div>
                                    </a>
                                </div>
                            <?php
                            }
                            ?>
                        </div>

                        <div id="hiddenIfNull" class="navigation pagination">
                            <?php
                         
                            $totalProjects = $conn->query('SELECT COUNT(*) FROM projects')->fetchColumn();
                            $totalPages = ceil($totalProjects / $limit);

                            $startPage = max(1, $currentPage - 1);
                            $endPage = min($totalPages, $startPage + 2);

                            if ($startPage > 1) {
                            ?>
                                <div class="pagination-link nav-to-page">
                                    <span><a href="?page=1">1</a></span>
                                </div>
                                <?php
                                if ($startPage > 2) {
                                ?>
                                    <div class="pagination-link">
                                        <span>...</span>
                                    </div>
                                <?php
                                }
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $activeClass = ($i == $currentPage) ? 'active' : '';
                                ?>
                                <div class="pagination-link nav-to-page <?php echo $activeClass; ?>">
                                    <span><a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a></span>
                                </div>
                                <?php
                            }

                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                ?>
                                    <div class="pagination-link">
                                        <span>...</span>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="pagination-link nav-to-page">
                                    <span><a href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a></span>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="empty-card-container">
                            <div class="empty-card">
                                <div class="exclamation-icon">
                                    <i class="fa fa-exclamation" aria-hidden="true"></i>
                                </div>
                                <span>No Results Found</span>
                                <p>
                                    We couldn't find any projects for your search. <br>
                                    Our apologies for any inconvenience this may have caused.
                                </p>
                            </div>
                        </div>

                        <div class="project-left">
                            <div class="title-section">
                                <span>You might also like</span>
                            </div>
                            <div class="card-container">
                                <?php
                                $limit = 6; 
                                $currentPage = isset($_GET['page']) ? $_GET['page'] : 1; 
                                $offset = ($currentPage - 1) * $limit; 

                                $getProjects = $conn->prepare('SELECT * FROM projects LIMIT :limit OFFSET :offset');
                                $getProjects->bindParam(':limit', $limit, PDO::PARAM_INT);
                                $getProjects->bindParam(':offset', $offset, PDO::PARAM_INT);
                                $getProjects->execute();

                                while ($project = $getProjects->fetch(PDO::FETCH_ASSOC)) {
                                    $dateCreatedArr = new DateTime($project['created_at']);
                                    $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');
                                ?>
                                    <div class="card">
                                        <a class="card-link" href="download.php?project=<?php echo $project['title'] ?>">
                                            <img src="/images/<?php echo $project['image_name'] ?>" alt="">
                                            <div class="card-content">
                                                <p><?php echo $project['title']; ?></p>
                                                <p>Posted on: <?php echo $dateCreated ?></p>
                                                <a class="view-more" href="download.php?project=<?php echo $project['title'] ?>">View More</a>
                                                <span><?php echo $project['language']; ?></span>
                                                <!-- Add other project information as needed -->
                                            </div>
                                        </a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>

                            <div class="navigation pagination">
                                <?php
                             
                                $totalProjects = $conn->query('SELECT COUNT(*) FROM projects')->fetchColumn();
                                $totalPages = ceil($totalProjects / $limit);

                                $startPage = max(1, $currentPage - 1);
                                $endPage = min($totalPages, $startPage + 2);

                                if ($startPage > 1) {
                                ?>
                                    <div class="pagination-link nav-to-page">
                                        <span><a href="?page=1">1</a></span>
                                    </div>
                                    <?php
                                    if ($startPage > 2) {
                                    ?>
                                        <div class="pagination-link">
                                            <span>...</span>
                                        </div>
                                    <?php
                                    }
                                }

                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $activeClass = ($i == $currentPage) ? 'active' : '';
                                    ?>
                                    <div class="pagination-link nav-to-page <?php echo $activeClass; ?>">
                                        <span><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></span>
                                    </div>
                                    <?php
                                }

                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) {
                                    ?>
                                        <div class="pagination-link">
                                            <span>...</span>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="pagination-link nav-to-page">
                                        <span><a href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a></span>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <?php
                include_once 'includes/project-right.php'
                ?>

            </div>


        </section>
    </div>
</main>
<?php if ($_GET["rel"] != "page") {
    echo "</div>";
} ?>
<?php
include_once 'includes/footer.php';
?>