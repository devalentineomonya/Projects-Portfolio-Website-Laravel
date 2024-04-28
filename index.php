<?php
if (!isset($_GET["rel"]) || $_GET["rel"] !== "page") {
    include_once 'includes/header.php';
}

?>
<main>
    <div class="page">
        <div class="hero">
            <?php
            $sql = "SELECT * FROM projects ORDER BY RAND() LIMIT 4";
            $result = $conn->query($sql);

                
                $card1 = $result->fetch(PDO::FETCH_ASSOC);
                $card3 = $result->fetch(PDO::FETCH_ASSOC);
                $card4 = $result->fetch(PDO::FETCH_ASSOC);
                $card3 = $result->fetch(PDO::FETCH_ASSOC);

            ?>
            <div class="d-grid-col hero-main">
                <div class="grid-left">
                    <div class="left-image">
                        <div class="image">
                            <img src="/images/<?php echo $card1['image_name']?>" alt="">
                            <span><?php echo $card1['title']?></span>
                            <div class="gradient-overlay"></div>
                        </div>
                    </div>
                </div>
                <div class="grid-right">
                    <div class="d-grid-row">
                        <div class="upper">
                            <div class="upper-img">
                                <div class="image">
                                    <img src="/images/<?php echo $card2['image_name']?>" alt="">
                                    <span><?php echo $card2['title']?></span>
                                    <div class="gradient-overlay"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid-col">
                            <div class="hero-col">
                                <div class="col-content">
                                    <div class="image">
                                    <img src="/images/<?php echo $card3['image_name']?>" alt="">
                                    <span><?php echo $card3['title']?></span>
                                        <div class="gradient-overlay"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-col">
                                <div class="col-content">
                                    <div class="image">
                                    <img src="/images/<?php echo $card4['image_name']?>" alt="">
                                    <span><?php echo $card4['title']?></span>
                                        <div class="gradient-overlay"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section>

            <div class="projects-section">
                <div class="project-left">
                    <div class="title-section">
                        <span>Recently Updated</span>
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
                           $dateCreated =$dateCreatedArr->format('F j, Y, g:i a');
                        ?>
                            <div class="card">
                                <a class="card-link" href="download.php?project=<?php echo $project['title']?>">
                                    <img src="/images/<?php echo $project['image_name'] ?>" alt="">
                                    <div class="card-content">
                                        <p><?php echo $project['title']; ?></p>
                                        <p>Posted on: <?php echo $dateCreated ?></p>
                                        <a class="view-more" href="download.php?project=<?php echo $project['title']?>">View More</a>
                                        <span><?php echo $project['language']; ?></span>
                                       
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

                include_once 'includes/project-right.php'
                ?>
            </div>
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