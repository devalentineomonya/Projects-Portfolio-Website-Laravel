<?php
include_once 'includes/header.php';
?>
<?php
$downloadProject = htmlspecialchars($_GET['project']);
if (empty($downloadProject) || strlen($downloadProject) < 1 || $downloadProject == "") {
    echo '<script type="text/javascript">window.location.href="index.php";</script>';
}
?>
<?php
$getProjectInfo = $conn->prepare("SELECT * FROM projects WHERE title = :title");
$getProjectInfo->bindparam(':title', $downloadProject);
$getProjectInfo->execute();
$result = $getProjectInfo->fetch(PDO::FETCH_ASSOC);
?>
<main>
    <div class="page">
        <div class="current-page">
            <span class="page-directory">
                Home><span><?php echo $result['category'] ?>><?php echo $result['title'] ?></span>
            </span>
            <p class="page-name">
                <?php echo $downloadProject ?>
            </p>
        </div>

        <section>
            <div class="projects-section filtered-project">
                <div class="project-left">
                    <div class="download-image-container">
                        <div class="project-image">
                            <img src="./images/<?php echo $result['image_name'] ?>" alt="<?php echo $result['image_name'] ?>">
                        </div>
                        <div class="project-description">
                            <p>
                                <?php echo $result['description'] ?>
                            </p>
                        </div>
                        <div class="download-project">
                            <div class="download">
                                <form id="userInfoForm" action="" method="POST" class="">
                                    <div class="d-none">
                                        <input type="text" id="ipAddress" name="ipAddress">
                                        <input type="text" id="browser" name="browser">
                                        <input type="text" id="device" name="device">
                                        <input type="text" id="city" name="city">
                                        <input type="text" id="region" name="region">
                                        <input type="text" id="country" name="country">
                                        <input type="text" id="latitude" name="latitude">
                                        <input type="text" id="longitude" name="longitude">
                                        <input type="text" id="organization" name="organization">
                                    </div>
                                    <div class="counter-label">
                                        <p id="counterLab">Your download will start in <span id="counter" class="counter-number text-primary">30</span>seconds</p>
                                    </div>
                                    <div class="download-button">

                                        <button id="btnDownload" name="saveDownload" type="submit" value="Submit">
                                            <i class="fa-solid fa-download"></i><span>Download</span>
                                        </button>
                                    </div>
                                </form>
                                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                <script src="../js/getIp.js"></script>

                                <?php

                                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveDownload'])) {
                                    echo '
                                   <script>
                                   document.getElementById("btnDownload").style.display="none";
                                   document.getElementById("counterLab").style.display="block";
                                       function getRandomSeconds() {
                                           const minSeconds = 10;
                                           const maxSeconds = 30;
                                           const divisibleBy = 5;
                                           return Math.floor(Math.random() * ((maxSeconds / divisibleBy) - (minSeconds / divisibleBy) + 1)) * divisibleBy + minSeconds;
                                       }
                               
                                       let seconds = getRandomSeconds();
                               
                                       function countdown() {
                                           document.getElementById("counter").innerHTML = " " + seconds + "";
                                           if (seconds === 0) {
                                            document.getElementById("btnDownload").style.display="block";
                                            document.getElementById("counterLab").style.display="none";
                                               window.location.href="' . $result['link'] . '";
                                           } else {
                                               seconds--;
                                               setTimeout(countdown, 1000);
                                           }
                                       }
                               
                                       countdown();
                                   </script>
                                   ';

                                    
                                    $checkIpStmt = $conn->prepare("SELECT * FROM downloads WHERE userIp = :userIp ORDER BY created_at DESC LIMIT 1");
                                    $checkIpStmt->bindParam(':userIp', $_POST['ipAddress']);
                                    $checkIpStmt->execute();
                                    $lastDownload = $checkIpStmt->fetch(PDO::FETCH_ASSOC);

                                 
                                    $downloads = 1;


                                    if (!$lastDownload) {
                                        $stmt = $conn->prepare("INSERT INTO downloads (userIp, browser, device, city, region, country, latitude, longitude, organization, downloads, created_at) 
                          VALUES (:userIp, :browser, :device, :city, :region, :country, :latitude, :longitude, :organization, :downloads, NOW())");
                                        $addDownload = $conn->prepare("INSERT INTO downloadedproject (project_name, userIp) VALUES (:projectName, :userIp)");
                                        $stmt->bindParam(':userIp', $_POST['ipAddress']);
                                        $stmt->bindParam(':browser', $_POST['browser']);
                                        $stmt->bindParam(':device', $_POST['device']);
                                        $stmt->bindParam(':city', $_POST['city']);
                                        $stmt->bindParam(':region', $_POST['region']);
                                        $stmt->bindParam(':country', $_POST['country']);
                                        $stmt->bindParam(':latitude', $_POST['latitude']);
                                        $stmt->bindParam(':longitude', $_POST['longitude']);
                                        $stmt->bindParam(':organization', $_POST['organization']);
                                        $stmt->bindParam(':downloads', $downloads);


                                        $addDownload->bindParam(':userIp', $_POST['ipAddress']);
                                        $addDownload->bindParam(':projectName', $result['title']);

                                     
                                        $stmt->execute();
                                        $addDownload->execute();

                                        $stmt->execute();
                                        $addDownload->execute();
                                    } else {
                                      
                                        $lastDownloadTime = strtotime($lastDownload['created_at']);
                                        $currentTime = time();

                                        if ($currentTime - $lastDownloadTime > 60) {
                                            $created_at = date('Y-m-d H:i:s'); 
                                            $downloads = $lastDownload['downloads'] + 1;

                                            
                                            $updateCountStmt = $conn->prepare("UPDATE downloads SET downloads = :downloads, created_at = :created_at WHERE userIp = :userIp");
                                            $addDownload = $conn->prepare("INSERT INTO downloadedproject (project_name, userIp) VALUES (:projectName, :userIp)");
                                            $addDownload->bindParam(':userIp', $_POST['ipAddress']);
                                            $addDownload->bindParam(':projectName', $result['title']);
                                            $updateCountStmt->bindParam(':downloads', $downloads);
                                            $updateCountStmt->bindParam(':created_at', $created_at);
                                            $updateCountStmt->bindParam(':userIp', $_POST['ipAddress']);

                                            $addDownload->bindParam(':userIp', $_POST['ipAddress']);
                                            $addDownload->bindParam(':projectName', $result['title']);

                                            $updateCountStmt->execute();
                                            $addDownload->execute();
                                        } else {
                                            echo 'Too many downloads within a short period. Please wait before trying again.';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <div class="contact">
                            <div class="comments-lab">
                                <h3 class="send-comment">
                                    Level Comment
                                </h3>
                            </div>
                            <div class="col-md-12">
                                <div class="card p-4">
                                    <form action="" method="POST" class="php-email-form">
                                        <input type="hidden" name="access_key" value="46e71153-727d-43b2-853b-6525fc87bc7f">

                                        <div class="row gy-4">

                                            <div class="col-md-6">
                                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                                            </div>

                                            <div class="col-md-6">
                                                <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                                            </div>

                                            <div class="col-md-12">
                                                <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                                            </div>

                                            <div class="col-md-12">
                                                <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                                            </div>

                                            <div class="col-md-12 text-center send-comment">
                                                <div class="loading"></div>
                                                <div class="error-message"></div>
                                                <div class="sent-message"></div>

                                                <button name="sendEmail" type="submit">Send Message</button>
                                            </div>

                                        </div>
                                    </form>
                                    <?php
                                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sendEmail'])) {
                                        $_SESSION['fullname'] = htmlspecialchars($_POST['name']);
                                        $_SESSION['useremail'] = htmlspecialchars($_POST['email']);
                                        $_SESSION['subject'] = htmlspecialchars($_POST['subject']);
                                        $_SESSION['message'] = htmlspecialchars($_POST['message']);
                                        echo '<script type="text/javascript">window.location.href="mail.php"</script>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                        <a href="?project=<?php echo $popularProject['title']; ?>">
                                            <p><?php echo $popularProject['title']; ?></p>
                                        </a>
                                        <?php
                                        $dateCreatedArr = new DateTime($popularProject['created_at']);
                                        $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');
                                        ?>
                                        <span><?php echo $dateCreated ?></span>
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
                                <a href="?popular_page=<?php echo $currentPage - 1; ?>&project=<?php echo $downloadProject ?>">
                                    <div class="left">
                                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                                    </div>
                                </a>
                            <?php
                            }

                            if ($currentPage < $totalPopularPages) {
                            ?>
                                <a href="?popular_page=<?php echo $currentPage + 1; ?>&project=<?php echo $downloadProject ?>">
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
                                        <a href="?project=<?php echo $featuredPost['title'] ?>">
                                            <p><?php echo $featuredPost['title']; ?></p>
                                        </a>
                                        <?php
                                        $dateCreatedArr = new DateTime($row['created_at']);
                                        $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');
                                        ?>
                                        <span><?php echo $dateCreated; ?></span>
                                        <!-- Add other post information as needed -->
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


        </section>
    </div>
</main>

</script>
<?php if ($_GET["rel"] != "page") {
    echo "</div>";
} ?>
<?php
include_once 'includes/footer.php';
?>