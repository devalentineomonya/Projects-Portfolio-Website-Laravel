<div class="project-right">
    <div class="title-section">
        <span>Follow Me</span>
    </div>
    <div class="social-media">

        <div class="platform facebook">
            <a href="https://www.facebook.com/devalentineomonya" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-facebook"></i>
                <p>2K</p>
                <span>Followers</span>
            </a>
        </div>

        <div class="platform instagram">
            <a href="http://www.instagram.com/devalentineomonya" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-instagram"></i>
                <p>500</p>
                <span>Followers</span>
            </a>
        </div>

        <div class="platform youtube">
            <a href="http://www.youtube.com/@devalentineomonya" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-youtube"></i>
                <p>1.2K</p>
                <span>Subscribers</span>
            </a>
        </div>

        <div class="platform linkedin">
            <a href="https://www.linkedin.com/in/devalentineomonya/" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-linkedin"></i>
                <p>1K</p>
                <span>Followers</span>
            </a>
        </div>

        <div class="platform telegram">
            <a href="http://t.me/devalentineomonya" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-telegram"></i>
                <p>4K</p>
                <span>Followers</span>
            </a>
        </div>
        <div class="platform github">
            <a href="https://www.github.com/devalentineomonya" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-github"></i>
                <p>1.5K</p>
                <span>Followers</span>
            </a>
        </div>


    </div>
    <div class="most-popular">
        <div class="title-section">
            <span>Most Popular</span>
        </div>
        <div class="popular-projects">
            <?php
            $limit = 5; 
            $currentPage = isset($_GET['popular_page']) ? $_GET['popular_page'] : 1; 
            $offset = ($currentPage - 1) * $limit; 

            $getPopularProjects = $conn->prepare('SELECT * FROM projects ORDER BY RAND() LIMIT :limit OFFSET :offset');
            $getPopularProjects->bindParam(':limit', $limit, PDO::PARAM_INT);
            $getPopularProjects->bindParam(':offset', $offset, PDO::PARAM_INT);
            $getPopularProjects->execute();

            while ($popularProject = $getPopularProjects->fetch(PDO::FETCH_ASSOC)) {
            
            ?>
                <div class="project-container">
                    <div class="project-image">
                        <img src="/images/<?php echo $popularProject['image_name']; ?>" alt="">
                    </div>
                    <div class="project-description">
                        <a href="download.php?project=<?php echo $popularProject['title'] ?>">
                            <p><?php echo $popularProject['title']; ?></p>
                        </a>
                        <?php
                        $dateCreatedArr = new DateTime($popularProject['created_at']);
                        $dateCreated = $dateCreatedArr->format('l jS F Y');
                        ?>
                        <span><?php echo $dateCreated; ?></span>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>

        <div class="navigation">
            <?php
            $totalPopularProjects = $conn->query('SELECT COUNT(*) FROM projects')->fetchColumn();
            $totalPopularPages = ceil($totalPopularProjects / $limit);

            if ($currentPage > 1) {
            ?>
                <a href="?popular_page=<?php echo $currentPage - 1; ?>">
                    <div class="left">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                    </div>
                </a>
            <?php
            }

            if ($currentPage < $totalPopularPages) {
            ?>
                <a href="?popular_page=<?php echo $currentPage + 1; ?>">
                    <div class="right">
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </div>
                </a>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="feature-post">
        <div class="title-section">
            <span>Featured Post</span>
        </div>

        <?php
        $randomPostQuery = $conn->prepare('SELECT * FROM projects ORDER BY RAND() LIMIT 1');
        $randomPostQuery->execute();

        
        if ($randomPostQuery->rowCount() > 0) {
            $featuredPost = $randomPostQuery->fetch(PDO::FETCH_ASSOC);
        ?>
            <div class="post-container">
                <div class="post-image">
                    <img src="/images/<?php echo $featuredPost['image_name']; ?>" alt="">
                </div>
                <div class="post-info">
                    <div class="project-description">
                        <a href="download.php?project=<?php echo $featuredPost['title'] ?>">
                            <p><?php echo $featuredPost['title']; ?></p>
                        </a>
                        <?php
                        $dateCreatedArr = new DateTime($featuredPost['created_at']);
                        $dateCreated = $dateCreatedArr->format('l jS F Y');
                        ?>
                        <span><?php echo $dateCreated ?></span>
                        
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="categories">
        <div class="title-section">
            <span>Categories</span>
        </div>
        <div class="categories-container">
            <table>
                <tbody>
                    <?php
                    $getCategory = $conn->prepare("SELECT * FROM categories ");
                    $getCategory->execute();

                    while ($result = $getCategory->fetch(PDO::FETCH_ASSOC)) {
                        $getProjectCount = $conn->prepare("SELECT COUNT(*) as count FROM projects WHERE category = :category");
                        $getProjectCount->bindParam(':category', $result['name']);
                        $getProjectCount->execute();
                        $count = $getProjectCount->fetch(PDO::FETCH_ASSOC);

                    ?>
                        <tr>
                            <td class="td-8"> <a href="projects.php?category=<?php echo $result['name'] ?>"><?php echo $result['name'] ?></a></td>

                            <td class="td-4"><?php echo $count['count'] ?></td>

                        </tr>
                    <?php
                    }
                    ?>

                </tbody>
            </table>
        </div>

    </div>
</div>