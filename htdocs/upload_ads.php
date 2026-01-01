<?php
// Define the upload directory
$uploadDir = 'ads/'; // Folder to save the uploaded ads
$error = ""; // To store errors
$success = ""; // To store success messages

// Handle the file upload when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ad_file'])) {
    $file = $_FILES['ad_file'];
    $fileName = basename($file['name']); // Get the name of the uploaded file
    $fileTmpPath = $file['tmp_name']; // Temporary file path on server
    $fileSize = $file['size']; // File size
    $fileType = $file['type']; // File MIME type

    // Check if the file size is less than 10MB
    if ($fileSize > 10 * 1024 * 1024) {
        $error = "File size should not exceed 10MB.";
    }
    // Move the file to the 'ads/' directory if validation passes
    else {
        $uploadPath = $uploadDir . $fileName;
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            $success = "File uploaded successfully!";
        } else {
            $error = "There was an error uploading the file.";
        }
    }
}

// Handle file deletion
if (isset($_GET['delete']) && file_exists($uploadDir . $_GET['delete'])) {
    $fileToDelete = $uploadDir . $_GET['delete'];
    if (unlink($fileToDelete)) {
        $success = "File deleted successfully!";
    } else {
        $error = "There was an error deleting the file.";
    }
}

// Get the list of files in the 'ads' directory
$files = array_diff(scandir($uploadDir), array('..', '.')); // Remove '.' and '..' from the list
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Ads</title>
    <style>
        /* General page styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0d1117;
            color: #c9d1d9;
        }

        /* Main Title and Description */
        h1 {
            text-align: center;
            font-size: 36px;
            color: #4ea8de;
            font-family: 'Times New Roman', Times, serif;
            margin-top: 40px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            color: rgb(255, 255, 255);
            font-weight: lighter;
            font-style: italic;
            font-family: cursive;
        }

       /* Ensure that the container has a fixed size */
.file-list {
    margin-top: 30px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.file-item {
    background-color: #161b22;
    border-radius: 8px;
    padding: 15px;
    margin: 10px;
    text-align: center;
    max-width: 300px; /* Fixed width */
    min-width: 300px; /* Ensuring all containers have the same width */
    height: auto; /* Set height to make all containers the same size */
    color: white;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden; /* Ensure content doesn’t overflow */
}

/* Ensure image/video fits inside the container */
.file-item img,
.file-item video {
    width: 100%; /* Make the media take up full width of the container */
    height: auto; /* Set a fixed height for images and videos */
    object-fit: cover; /* Maintain aspect ratio and fill container */
    border-radius: 8px;
}

/* Align the delete button properly */
.file-item a {
    display: inline-block;
    margin-top: 10px;
    background-color: red;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 6px;
    text-align: center;
    transition: background-color 0.3s ease;
}

.file-item a:hover {
    background-color: maroon;
}

        .upload-form {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #161b22;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .upload-form input[type="file"] {
            display: block;
            margin: 15px 0;
            background-color: #222;
            color: #c9d1d9;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #4ea8de;
            transition: background-color 0.3s ease;
        }

        .upload-form input[type="submit"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            background-color: rgb(31, 119, 173);
            color: white;
            font-size: 18px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .upload-form input[type="submit"]:hover {
            background-color: rgb(4, 201, 250);
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

    <h1>Upload Your Ad</h1>

    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- File Upload Form -->
    <div class="upload-form">
        <form action="upload_ads.php" method="POST" enctype="multipart/form-data">
            <label for="ad_file">Choose an ad file (Image or Video):</label>
            <input type="file" name="ad_file" id="ad_file" required>
            <input type="submit" value="Upload Ad">
        </form>
    </div>

    <!-- Display Uploaded Ads -->
    <h2>Uploaded Ads</h2>
    <div class="file-list">
        <?php foreach ($files as $file): ?>
            <div class="file-item">
                <?php
                // Display images or videos
                $filePath = $uploadDir . $file;
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo "<img src='$filePath' alt='Ad Image'>";
                } elseif (in_array(pathinfo($file, PATHINFO_EXTENSION), ['mp4', 'avi', 'mov'])) {
                    echo "<video controls><source src='$filePath' type='video/mp4'></video>";
                }
                ?>
                <a href="?delete=<?php echo $file; ?>" onclick="return confirm('Are you sure you want to delete this ad?')">Delete</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
