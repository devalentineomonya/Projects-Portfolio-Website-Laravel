<?php
session_start();
include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getUserLevel = $conn->prepare("SELECT security_level FROM users WHERE user_id = :user_id");
    $getUserLevel->bindParam(':user_id', $_SESSION['current_user']);
    $getUserLevel->execute();
    $level = $getUserLevel->fetch(PDO::FETCH_ASSOC);

    if ($level['security_level'] == 1) {

        if (isset($_POST['catId'])) {
            $catId = $_POST['catId'];

            try {

                $sqlDeleteLink = "DELETE FROM projectcategories WHERE category_id = :category_id";
                $stmtDeleteLink = $conn->prepare($sqlDeleteLink);
                $stmtDeleteLink->bindValue(':category_id', $catId, PDO::PARAM_INT);
                $stmtDeleteLink->execute();

                $sqlCheckLinks = "SELECT COUNT(*) FROM projectcategories WHERE category_id = :category_id";
                $stmtCheckLinks = $conn->prepare($sqlCheckLinks);
                $stmtCheckLinks->bindValue(':category_id', $catId, PDO::PARAM_INT);
                $stmtCheckLinks->execute();
                $linkCount = $stmtCheckLinks->fetchColumn();

                if ($linkCount == 0) {
                   
                    $sqlDeleteCategory = "DELETE FROM categories WHERE category_id = :category_id";
                    $stmtDeleteCategory = $conn->prepare($sqlDeleteCategory);
                    $stmtDeleteCategory->bindParam(':category_id', $catId, PDO::PARAM_INT);
                    $stmtDeleteCategory->execute();

                    echo "Category deleted successfully.";
                } else {
                    echo "Error: Category still has links to projects.";
                }

                exit();
            } catch (PDOException $e) {
                
                echo "Database Error: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-1"></i>
        You don\'t have permission to access this service.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
}
