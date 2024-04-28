<?php
include_once 'connection.php';

$getUserLevel = $conn->prepare("SELECT security_level FROM users WHERE user_id = :user_id");
$getUserLevel->bindParam(':user_id', $_SESSION['current_user']);
$getUserLevel->execute();
$level = $getUserLevel->fetch(PDO::FETCH_ASSOC);

if ($level['security_level'] == 1) {

    if (isset($_POST['downloadId'])) {
        $downloadId = $_POST['downloadId'];
        $stmt = $conn->prepare("DELETE FROM downloads WHERE id = :downloadId");
        $stmt->bindParam(':downloadId', $downloadId);
        $stmt->execute();

     
        echo "Record deleted successfully";
    } else {
      
        echo "Error: Download ID not provided";
    }
} else {
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-octagon me-1"></i>
    You don\'t have permission to access this service.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>';
}
?>
