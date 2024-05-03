<?php

include_once 'connection.php';
$projectsPerPage = 10;


$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    switch ($filter) {
        case 'today':
            $sql = "SELECT * FROM projects WHERE DATE(created_at) = CURDATE()";
            break;
        case 'this_month':
            $sql = "SELECT * FROM projects WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            break;
        case 'this_year':
            $sql = "SELECT * FROM projects WHERE YEAR(created_at) = YEAR(CURDATE())";
            break;
        default:
            $sql = "SELECT * FROM projects";
    }

    // Calculate the starting point for the projects based on the current page
    $start = ($current_page - 1) * $projectsPerPage;

    // Append LIMIT and OFFSET to the SQL query for pagination
    $sql .= " LIMIT $start, $projectsPerPage";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dateCreatedArr = new DateTime($row['created_at']);
            $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');
            $dateUpdatedArr = new DateTime($row['updated_at']);
            $dateUpdated = $dateUpdatedArr->format('F j, Y, g:i a');
?>
            <div class="post-item clearfix p-4">
                <img src="../images/<?php echo $row['image_name'] ?>" alt="">
                <h4><a href="#" onclick="openDownload('<?php echo urlencode($row['title']) ?>')"><?php echo $row['title'] ?></a></h4>
                <h4><?php echo $row['language'] ?></h4>
                <h4><?php echo $row['category'] ?></h4>
                <h4><?php echo $dateCreated ?></h4>
                <h4><?php echo $dateUpdated ?></h4>

                <script>
                    function openDownload(projectTitle) {
                        var baseUrl = window.location.origin;
                        var downloadUrl = baseUrl + '/download.php?project=' + projectTitle;

                        window.location.href = downloadUrl;
                    }
                </script>

                <div class="mt-2">
                    <a href="add-project.php?id=<?php echo $row['project_id']; ?>" class="btn btn-primary">Edit</a>
                    <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-project-id="<?php echo $row['project_id']; ?>">
                        Delete
                    </button>
                </div>
    <?php
        }
    }

    // Pagination logic
    $pagination_sql = "SELECT COUNT(*) as total FROM projects";
    $pagination_stmt = $conn->prepare($pagination_sql);
    $pagination_stmt->execute();
    $total_projects = $pagination_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $projectsPerPage);

    // Display pagination links
    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="?filter=' . $filter . '&page=' . $i . '"><i class="fa-solid fa-chevron-right">' . $i . '</i></a>';
    }
    echo '</div>';
}
    ?>