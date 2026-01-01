<?php
// videoupload.php

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set maximum execution time to 1 hour (adjust as needed)
set_time_limit(3600);

// Set maximum upload size to 500MB (slightly larger than your 400MB files)
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '500M');

// Target directory for uploads
$targetDir = "uploads/";
$uploadOk = 1;

// Create uploads directory if it doesn't exist
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["videoFile"]) && $_FILES["videoFile"]["error"] == UPLOAD_ERR_OK) {
        $fileName = basename($_FILES["videoFile"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        
        // Check if file already exists
        if (file_exists($targetFilePath)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sorry, file already exists.'
            ]);
            $uploadOk = 0;
            exit;
        }
        
        // Allow certain file formats (add more if needed)
        $allowedTypes = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sorry, only MP4, AVI, MOV, WMV, FLV, MKV, WEBM files are allowed.'
            ]);
            $uploadOk = 0;
            exit;
        }
        
        // Check file size (500MB limit)
        if ($_FILES["videoFile"]["size"] > 500 * 1024 * 1024) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Sorry, your file is too large. Maximum 500MB allowed.'
            ]);
            $uploadOk = 0;
            exit;
        }
        
        // Upload the file using chunked approach for better performance
        if ($uploadOk == 1) {
            // Create a temporary file path
            $tmpFilePath = $_FILES["videoFile"]["tmp_name"];
            
            // Use move_uploaded_file for security
            if (move_uploaded_file($tmpFilePath, $targetFilePath)) {
                // Return success response
                echo json_encode([
                    'status' => 'success',
                    'message' => 'The file ' . htmlspecialchars($fileName) . ' has been uploaded.',
                    'filePath' => $targetFilePath,
                    'fileSize' => formatSizeUnits($_FILES["videoFile"]["size"])
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Sorry, there was an error uploading your file.'
                ]);
            }
        }
    } else {
        // Handle upload errors
        $errorMessage = "Sorry, there was an error uploading your file.";
        if (isset($_FILES["videoFile"]["error"])) {
            switch ($_FILES["videoFile"]["error"]) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMessage = "File is too large (max 500MB).";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage = "File upload was incomplete.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMessage = "Missing temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMessage = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMessage = "File upload stopped by extension.";
                    break;
            }
        }
        echo json_encode([
            'status' => 'error',
            'message' => $errorMessage
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}

// Helper function to format file size
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}
?>