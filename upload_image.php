<?php
// upload_image.php
include('includes/dbconn.php');

$blendId = $_POST['blend_id'] ?? 0;

if ($blendId <= 0) {
    echo "Invalid blend ID.";
    exit;
}

if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($_FILES['image']['tmp_name']);

    if (in_array($fileType, $allowedTypes)) {
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $extension;
        $uploadDir = 'uploads/images/';
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            // Insert into blend_images
            $stmt = $pdo->prepare("INSERT INTO blend_images (blend_id, image_path) VALUES (?, ?)");
            $stmt->execute([$blendId, $uploadPath]);
            echo "Image uploaded successfully.";
        } else {
            error_log("Failed to move uploaded file for blend ID: $blendId");
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Unsupported file type. Allowed types: JPEG, PNG, GIF, WEBP.";
    }
} else {
    // Log the specific upload error
    error_log("File upload error code: " . $_FILES['image']['error'] . " for blend ID: $blendId");
    echo "File upload error.";
}
?>
