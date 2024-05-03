<?php
include_once 'connection.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    $userId = $_POST['userId'];
    $getUserLevel = $conn->prepare("SELECT security_level FROM users WHERE user_id = :user_id");
    $getUserLevel->bindParam(':user_id', $_SESSION['current_user']);
    $getUserLevel->execute();
    $level = $getUserLevel->fetch(PDO::FETCH_ASSOC);

    if ($level['security_level'] == 1) {
        $getImageSql = "SELECT image_name FROM Users WHERE user_id = :userId";
        $getImageStmt = $conn->prepare($getImageSql);
        $getImageStmt->bindParam(':userId', $userId);
        $getImageStmt->execute();
        $userImage = $getImageStmt->fetch(PDO::FETCH_ASSOC);

        if ($userImage) {
            // Delete the image file from the server
            $imagePath = '../images/' . $userImage['image_name'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Update the database to remove the image reference
            $deleteImageSql = "UPDATE Users SET image_name = NULL WHERE user_id = :userId";
            $deleteImageStmt = $conn->prepare($deleteImageSql);
            $deleteImageStmt->bindParam(':userId', $userId);

            if ($deleteImageStmt->execute()) {
                // Image deletion successful
                echo json_encode(['status' => 'success', 'message' => 'Image deleted successfully.']);
            } else {
                // Image deletion failed
                echo json_encode(['status' => 'error', 'message' => 'Image deletion failed.']);
            }
        } else {
            // User or image not found
            echo json_encode(['status' => 'error', 'message' => 'User or image not found.']);
        }
    } else {
        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon me-1"></i>
        You don\'t have permission to access this service.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
} else {
    // Invalid request
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
