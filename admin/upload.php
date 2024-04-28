
<?php

function uploadImage($file)
{
    $targetDirectory = "../images/";
    $maxFileSize = 2097152;
    $maxWidth = 800;
    $maxHeight = 800;
    if (!is_array($file) || !isset($file["error"]) || !isset($file["tmp_name"])) {
        return "Invalid file input.";
    }
    if ($file["error"] !== 0) {
        return "Error: " . $file["error"];
    }
    $newImageName = "image-" . uniqid() . ".jpg";
    $targetFile = $targetDirectory . $newImageName;
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowedTypes = array("jpg", "jpeg", "png", "gif");

    if (!in_array($imageFileType, $allowedTypes)) {
        return "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
    }
    if ($file["size"] > $maxFileSize) {
        return "Sorry, your file is too large. Maximum file size is " . round($maxFileSize / 1024 / 1024, 2) . "MB.";
    }
    list($width, $height) = getimagesize($file["tmp_name"]);
    if ($width > $maxWidth || $height > $maxHeight) {
        return "Sorry, the image dimensions exceed the allowed maximum of {$maxWidth}x{$maxHeight} pixels.";
    }
    if (!file_exists($targetDirectory)) {
        return "Error: Target directory does not exist.";
    }
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return array('name'=> $newImageName);
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}
