<?php
include_once 'partials/header.php';
include_once 'upload.php';

function isCategoryLinkedToProjects($conn, $categoryId)
{
    $sqlCheckLinks = "SELECT COUNT(*) FROM projectcategories WHERE category_id = :category_id";
    $stmtCheckLinks = $conn->prepare($sqlCheckLinks);
    $stmtCheckLinks->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    $stmtCheckLinks->execute();
    $linkCount = $stmtCheckLinks->fetchColumn();
    return $linkCount > 0;
}

function unlinkCategoryFromProjects($conn, $categoryId)
{
    $sqlDeleteLink = "DELETE FROM projectcategories WHERE category_id = :category_id";
    $stmtDeleteLink = $conn->prepare($sqlDeleteLink);
    $stmtDeleteLink->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    $stmtDeleteLink->execute();
}

function recreateCategoryProjectLink($conn, $categoryId, $projectIds)
{
    foreach ($projectIds as $projectId) {
        $sql = "INSERT INTO projectcategories (project_id, category_id) VALUES (:project_id, :category_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

function getProjectIdsByCategory($conn, $categoryId)
{
    $sql = "SELECT project_id FROM projectcategories WHERE category_id = :category_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function handleAddCategory($conn, $categoryName, $categoryDescription, $uploadedImage, $currentDateTime)
{
    // Validate input
    if (empty($categoryName) || empty($categoryDescription) || empty($uploadedImage)) {
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-1"></i>
                All fields must be filled in.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    } else {
        $imageUploadResult = uploadImage($_FILES["category_image"]);

        if (is_string($imageUploadResult)) {
            // Image upload failed, handle the error.
            $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-octagon me-1"></i>
                        ' . $imageUploadResult . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        } else {
            try {
                // Check for duplicate category name
                $checkDuplicate = $conn->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
                $checkDuplicate->bindParam(':name', $categoryName, PDO::PARAM_STR);
                $checkDuplicate->execute();
                $duplicateCount = $checkDuplicate->fetchColumn();

                if ($duplicateCount > 0) {
                    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon me-1"></i>
                            Category with the same name already exists.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                } else {
                    // Continue with the category insertion
                    $stmt = $conn->prepare("INSERT INTO categories (name, description, image_name, created_at, updated_at) VALUES (:name, :description, :image_name, :created_at, :updated_at)");
                    $stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
                    $stmt->bindParam(':description', $categoryDescription, PDO::PARAM_STR);
                    $stmt->bindParam(':image_name', $imageUploadResult['name'], PDO::PARAM_STR);
                    $stmt->bindParam(':created_at', $currentDateTime);
                    $stmt->bindParam(':updated_at', $currentDateTime);
                    $stmt->execute();

                    $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-1"></i>
                            Category added successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    echo '<script type="text/javascript">window.location.href="categories.php"</script>';
                    exit();
                }
            } catch (PDOException $e) {
                // Database insertion failed, delete the uploaded image
                unlink("../images/" . $imageUploadResult['name']);

                $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-octagon me-1"></i>
                                Error adding category to the database. ' . $e->getMessage() . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                exit();
            }
        }
    }
}

function handleUpdateCategory($conn, $categoryId, $categoryName, $categoryDescription, $uploadedImage, $currentDateTime)
{
    // Retrieve the existing category details
    $getCategoryDetails = $conn->prepare("SELECT name, description, image_name FROM categories WHERE category_id = :category_id");
    $getCategoryDetails->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $getCategoryDetails->execute();
    $existingCategoryDetails = $getCategoryDetails->fetch(PDO::FETCH_ASSOC);

    // Check if the category is linked to any projects
    $isLinkedToProjects = isCategoryLinkedToProjects($conn, $categoryId);

    // If the category is linked to projects, delete the link
    if ($isLinkedToProjects) {
        unlinkCategoryFromProjects($conn, $categoryId);
    }

    // Compare existing values with new values
    $updatedFields = [];
    if ($existingCategoryDetails['name'] != $categoryName) {
        $updatedFields['name'] = $categoryName;
    }
    if ($existingCategoryDetails['description'] != $categoryDescription) {
        $updatedFields['description'] = $categoryDescription;
    }
    if (!empty($uploadedImage)) {
        // Get the new image name
        $imageUploadResult = uploadImage($_FILES["category_image"]);
        $updatedFields['image_name'] = $imageUploadResult['name'];

        // Unlink the previous image
        unlink("../images/" . $existingCategoryDetails['image_name']);
    }

    // Update the category details only for the changed fields
    if (!empty($updatedFields)) {
        $setClause = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($updatedFields)));

        // Update the category details
        $stmt = $conn->prepare("UPDATE categories SET $setClause, updated_at = :updated_at WHERE category_id = :category_id");

        // Bind parameters dynamically
        foreach ($updatedFields as $field => $value) {
            $stmt->bindParam(":$field", $value);
        }

        $stmt->bindParam(':updated_at', $currentDateTime);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        // If the category was linked to projects, recreate the link
        if ($isLinkedToProjects) {
            recreateCategoryProjectLink($conn, $categoryId, getProjectIdsByCategory($conn, $categoryId));
        }

        $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i>
                                Category updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
        echo '<script type="text/javascript">window.location.href="categories.php"</script>';
        exit();
    } else {
        // No fields to update
        echo '<script type="text/javascript">window.location.href="categories.php"</script>';
        exit();
    }
}

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><?php echo isset($_GET['id']) ? 'Edit Category' : 'Add Category'; ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Categories</li>
                <li class="breadcrumb-item active"><?php echo isset($_GET['id']) ? 'Edit Category' : 'Add Category'; ?></li>
            </ol>
        </nav>
    </div>

    <?php
    // Check if category ID is present in the URL
    if (!empty($_GET['id']) && isset($_GET['id'])) {
        $categoryId = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $category_name = $result['name'];
            $category_description = $result['description'];
        } else {
            $category_name = "";
            $category_description = "";
        }
    } else {
        $category_name = "";
        $category_description = "";
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $categoryName = htmlspecialchars($_POST['category_name']);
        $categoryDescription = htmlspecialchars($_POST['category_description']);
        if (isset($_FILES['category_image']['name']) && !empty($_FILES['category_image']['name'])) {
            $uploadedImage = $_FILES['category_image']['name'];
        } else {
            $uploadedImage = "";
        }

        $currentDateTime = date("Y-m-d H:i:s");

        if (isset($_POST['add_category'])) {
            // Add Category
            handleAddCategory($conn, $categoryName, $categoryDescription, $uploadedImage, $currentDateTime);
        } elseif (isset($_POST['update_category'])) {
            // Update Category
            handleUpdateCategory($conn, $categoryId, $categoryName, $categoryDescription, $uploadedImage, $currentDateTime);
        }
    }

    $categories = $conn->prepare("SELECT * FROM categories");
    $categories->execute();
    ?>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo isset($_GET['id']) ? 'Edit Category' : 'Add Category'; ?></h5>
                        <p>Fill in all the fields</p>
                        <?php
                        if (isset($_SESSION['error'])) {
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        }
                        ?>

                        <!-- Custom Styled Validation -->
                        <form class="row g-3 needs-validation" enctype="multipart/form-data" method="post" novalidate>

                            <?php if (isset($_GET['id'])) : ?>
                                <input type="hidden" name="category_id" value="<?php echo $_GET['id']; ?>">
                            <?php endif; ?>

                            <div class="col-md-12">
                                <label for="category_name" class="col-sm-12 col-form-label">Category</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" value="<?php echo $category_name ?>" name="category_name" required>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="category_description" class="col-sm-12 col-form-label">Description</label>
                                <div class="col-md-12">
                                    <textarea class="form-control" style="height: 100px" name="category_description" required><?php echo $category_description ?></textarea>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="category_image" class="col-sm-12 col-form-label">Image</label>
                                <div class="col-md-12">
                                    <input class="form-control" type="file" id="formFile" name="category_image" <?php echo isset($categoryId) ? '' : 'required'; ?>>
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-sm-10">
                                    <?php if (isset($categoryId)) : ?>
                                        <button name="update_category" type="submit" class="btn btn-success">Update Category</button>
                                    <?php else : ?>
                                        <button name="add_category" type="submit" class="btn btn-success">Add Category</button>
                                    <?php endif; ?>
                                    <a href="categories.php" type="cancel" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include_once 'partials/footer.php';
?>