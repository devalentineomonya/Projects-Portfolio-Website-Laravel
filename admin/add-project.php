<?php
include_once 'partials/header.php';
include_once 'upload.php';
include_once 'connection.php';

$categories = $conn->prepare("SELECT * FROM categories");
$categories->execute();

// Check if project ID is present in the URL
if (isset($_GET['id'])) {
    $projectId = $_GET['id'];

    // Retrieve project details based on project ID
    $getProjectDetails = $conn->prepare("SELECT * FROM projects WHERE project_id = :projectId");
    $getProjectDetails->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    $getProjectDetails->execute();
    $projectDetails = $getProjectDetails->fetch(PDO::FETCH_ASSOC);

    // Check if project details are found
    if ($projectDetails) {
        // Assign project details to variables for prefilling the form
        $prefillTitle = $projectDetails['title'];
        $prefillDescription = $projectDetails['description'];
        $prefillLanguage = $projectDetails['language'];
        $prefillCategory = $projectDetails['category'];
        // Add more variables as needed
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $language = htmlspecialchars($_POST['language']);
    $category = htmlspecialchars($_POST['category']);
    $link = htmlspecialchars($_POST['link']);
    $uploaded = $_FILES['image']['name'];
    $currentDateTime = date("Y-m-d H:i:s");
    $getUserLevel = $conn->prepare("SELECT security_level FROM users WHERE user_id = :user_id");
    $getUserLevel->bindParam(':user_id', $_SESSION['current_user']);
    $getUserLevel->execute();
    $level = $getUserLevel->fetch(PDO::FETCH_ASSOC);
    
    if ($level['security_level'] == 1) {

        if (isset($_POST['add'])) {
            // Add Project
            handleAddProject($conn, $title, $description, $language, $category, $uploaded, $currentDateTime, $link);
        } elseif (isset($_POST['update'])) {
            // Update Project
            handleUpdateProject($conn, $projectId, $title, $description, $language, $category, $uploaded, $currentDateTime, $link);
        }
    }else{
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-1"></i>
        You don\'t have permission to access this service.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
}

function handleAddProject($conn, $title, $description, $language, $category, $uploaded, $currentDateTime, $link)
{
    // Validate input
    if (empty($title) || empty($description) || empty($language) || empty($uploaded) || $category == 'none' || empty($link)) {
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-octagon me-1"></i>
                    All fields must be filled in.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    } else {
        $imageUploadResult = uploadImage($_FILES["image"]);

        if (is_string($imageUploadResult)) {
            // Image upload failed, handle the error.
            $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon me-1"></i>
                            ' . $imageUploadResult . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } else {
            try {
                // Check for duplicate project name
                $checkDuplicate = $conn->prepare("SELECT COUNT(*) FROM projects WHERE title = :title");
                $checkDuplicate->bindParam(':title', $title, PDO::PARAM_STR);
                $checkDuplicate->execute();
                $duplicateCount = $checkDuplicate->fetchColumn();

                if ($duplicateCount > 0) {
                    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-octagon me-1"></i>
                                Project with the same name already exists.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                } else {
                    // Continue with the project insertion
                    $getCat = $conn->prepare("SELECT category_id FROM categories WHERE name = :category");
                    $getCat->bindParam(':category', $category, PDO::PARAM_STR);
                    $getCat->execute();
                    $categoryId = $getCat->fetchColumn();

                    if ($categoryId !== false) {
                        $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description, language, category, link, image_name, created_at) VALUES (:user_id, :title, :description, :language, :category, :link, :image_name, :created_at)");
                        $stmt->bindValue(':user_id', $_SESSION["current_user"]);
                        $stmt->bindValue(':title', $title);
                        $stmt->bindValue(':description', $description);
                        $stmt->bindValue(':language', $language);
                        $stmt->bindValue(':category', $category);
                        $stmt->bindValue(':link', $link);
                        $stmt->bindValue(':image_name', $imageUploadResult['name']);
                        $stmt->bindValue(':created_at', $currentDateTime);
                        $stmt->execute();

                        $projectId = $conn->lastInsertId();

                        $linkproject = $conn->prepare("INSERT INTO projectcategories (project_id, category_id) VALUES (:project_id, :category_id)");
                        $linkproject->bindValue(':project_id', $projectId, PDO::PARAM_INT);
                        $linkproject->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
                        $linkproject->execute();

                        $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Project added successfully!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                        echo '<script type="text/javascript">window.location.href="projects.php"</script>';
                        exit();
                    } else {
                        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-octagon me-1"></i>
                                    Category not found.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                    }
                }
            } catch (PDOException $e) {
                // Database insertion failed, delete the uploaded image
                unlink("../images/" . $imageUploadResult['name']);

                $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-octagon me-1"></i>
                                    Error adding project to the database. ' . $e->getMessage() . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                exit();
            }
        }
    }
}

function handleUpdateProject($conn, $projectId, $title, $description, $language, $category, $uploaded, $currentDateTime, $link)
{
    // Retrieve the previous category ID for the project
    $getPreviousCategory = $conn->prepare("SELECT category_id FROM projectcategories WHERE project_id = :projectId");
    $getPreviousCategory->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    $getPreviousCategory->execute();
    $previousCategoryId = $getPreviousCategory->fetchColumn();

    // Unlink the project from the previous category
    $unlinkPreviousCategory = $conn->prepare("DELETE FROM projectcategories WHERE project_id = :projectId");
    $unlinkPreviousCategory->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    $unlinkPreviousCategory->execute();

    // Get the new category ID
    $getNewCategory = $conn->prepare("SELECT category_id FROM categories WHERE name = :category");
    $getNewCategory->bindParam(':category', $category, PDO::PARAM_STR);
    $getNewCategory->execute();
    $newCategoryId = $getNewCategory->fetchColumn();

    if ($newCategoryId !== false) {
        // Insert the updated project details
        $imageUploadResult = uploadImage($_FILES["image"]);
        $stmt = $conn->prepare("UPDATE projects SET title = :title, description = :description, language = :language, category = :category, link = :link, image_name = :image_name, updated_at = :updated_at WHERE project_id = :projectId");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':language', $language);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':link', $link);
        $stmt->bindValue(':image_name', $imageUploadResult['name']); // Use the existing image name for update
        $stmt->bindValue(':updated_at', $currentDateTime);
        $stmt->bindValue(':projectId', $projectId);
        $stmt->execute();

        // Link the project to the new category
        $linkNewCategory = $conn->prepare("INSERT INTO projectcategories (project_id, category_id) VALUES (:project_id, :category_id)");
        $linkNewCategory->bindValue(':project_id', $projectId, PDO::PARAM_INT);
        $linkNewCategory->bindValue(':category_id', $newCategoryId, PDO::PARAM_INT);
        $linkNewCategory->execute();

        $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i>
                                Project updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
        echo '<script type="text/javascript">window.location.href="projects.php"</script>';
        exit();
    } else {
        // Category not found
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-octagon me-1"></i>
                                Category not found.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
        echo '<script type="text/javascript">window.location.href="projects.php"</script>';
        exit();
    }
}


?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1><?php echo isset($projectId) ? 'Edit Project' : 'Add Project'; ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Projects</li>
                <li class="breadcrumb-item active"><?php echo isset($projectId) ? 'edit-Project' : 'add-Project'; ?></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Fill in the Projects Details</h5>

                        <?php
                        if (isset($_SESSION['error'])) {
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        };
                        ?>

                        <!-- General Form Elements -->
                        <form action="" method="post" enctype="multipart/form-data">
                            <!-- Add hidden input field for project ID -->
                            <?php if (isset($projectId)) : ?>
                                <input type="hidden" name="projectId" value="<?php echo $projectId; ?>">
                            <?php endif; ?>

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" required value="<?php echo isset($prefillTitle) ? $prefillTitle : ''; ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" style="height: 100px" name="description" required><?php echo isset($prefillDescription) ? $prefillDescription : ''; ?></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Language</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="language" required value="<?php echo isset($prefillLanguage) ? $prefillLanguage : ''; ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Url</label>
                                <div class="col-sm-10">
                                    <input class="form-control" name="link" type="url" data-bv-uri-message="The website address is not valid" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Category</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="category" required>
                                        <option value="none">Select an option</option>
                                        <?php
                                        while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = ($prefillCategory == $row['name']) ? 'selected' : '';
                                            echo "<option value='{$row['name']}' {$selected}>{$row['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">File Upload</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" id="formFile" name="image" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-10">
                                    <?php if (isset($projectId)) : ?>
                                        <button name="update" type="submit" class="btn btn-success">Update Project</button>
                                    <?php else : ?>
                                        <button name="add" type="submit" class="btn btn-success">Add Project</button>
                                    <?php endif; ?>
                                    <a href="projects.php" type="cancel" class="btn btn-danger">Cancel </a>
                                </div>
                            </div>
                        </form><!-- End General Form Elements -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    $(document).ready(function() {
        $('#html5Form').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            }
        });
    });
</script>

<?php include_once 'partials/footer.php'; ?>