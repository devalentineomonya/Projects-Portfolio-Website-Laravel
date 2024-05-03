<?php
session_start(); // Start the session
include_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['userId'])) {
        $userId = $_POST['userId'];    $getUserLevel = $conn->prepare("SELECT security_level FROM users WHERE user_id = :user_id");
        $getUserLevel->bindParam(':user_id', $_SESSION['current_user']);
        $getUserLevel->execute();
        $level = $getUserLevel->fetch(PDO::FETCH_ASSOC);
        if (isset($_SESSION['current_user']) && $_SESSION['current_user'] == $userId || $level == 1) {
            // Error if trying to delete the current user
            $_SESSION['delete_error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-1"></i>
                You cannot delete the current user or the admin!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            try {
                // Retrieve the image filename from the database
                $sqlSelect = "SELECT image_name FROM Users WHERE user_id = :userId";
                $stmtSelect = $conn->prepare($sqlSelect);
                $stmtSelect->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmtSelect->execute();
                $resultSelect = $stmtSelect->fetch(PDO::FETCH_ASSOC);

                if ($resultSelect) {
                    // Delete the user record
                    $sqlDelete = "DELETE FROM Users WHERE user_id = :userId";
                    $stmtDelete = $conn->prepare($sqlDelete);
                    $stmtDelete->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmtDelete->execute();

                    $imageFilename = $resultSelect['image_name'];
                    if ($imageFilename && file_exists('../images/' . $imageFilename)) {
                        unlink('../images/' . $imageFilename);
                    }
                }
                exit();
            } catch (PDOException $e) {
                // Handle database errors
                echo "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>
