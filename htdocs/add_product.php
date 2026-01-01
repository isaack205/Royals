<?php
include('db.php');
session_start();
// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php'); // Redirect to login if not logged in
    exit();
}

// Function to create a slug from product name
function createSlug($text) {
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
    // Convert to lowercase
    $slug = strtolower($slug);
    // Trim hyphens from beginning and end
    $slug = trim($slug, '-');
    return $slug;
}

// Function to generate thumbnail from video (requires ffmpeg)
function generateVideoThumbnail($videoPath, $thumbnailPath) {
    // Check if ffmpeg is available
    if (function_exists('shell_exec')) {
        // Use ffmpeg to extract thumbnail at 1 second mark
        $command = "ffmpeg -i " . escapeshellarg($videoPath) . " -ss 00:00:01 -vframes 1 -q:v 2 " . escapeshellarg($thumbnailPath) . " 2>&1";
        shell_exec($command);
        
        // If thumbnail was created, return true
        if (file_exists($thumbnailPath)) {
            return true;
        }
    }
    
    // Fallback: use a placeholder image
    return false;
}

// Handle form submission and file upload
if (isset($_POST['submit'])) {
    $target_dir = "uploads/";  // Directory for storing uploaded files
    $upload_ok = 1; // Flag to check if upload is successful
    $image_paths = []; // Array to hold image file paths
    $video_paths = []; // Array to hold video file paths
    $video_thumbnail = null; // Main video thumbnail

    // Process uploaded images
    if (!empty($_FILES["image"]["name"][0])) {
        foreach ($_FILES["image"]["name"] as $key => $image_name) {
            $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

            // Generate a unique filename to avoid conflicts
            $unique_image_name = uniqid() . "." . $image_extension;
            $target_file = $target_dir . $unique_image_name;

            // Check if the file is an image
            $image_check = getimagesize($_FILES["image"]["tmp_name"][$key]);
            if ($image_check !== false) {
                $upload_ok = 1;
            } else {
                echo "File " . $image_name . " is not an image.<br>";
                $upload_ok = 0;
            }

            // Check file size (max 10MB for images)
            if ($_FILES["image"]["size"][$key] > 10000000) {
                echo "Sorry, your file " . $image_name . " is too large (max 10MB).<br>";
                $upload_ok = 0;
            }

            // Check file extension
            $allowed_image_extensions = ["jpg", "jpeg", "jfif", "png", "gif", "webp"];
            if (!in_array($image_extension, $allowed_image_extensions)) {
                echo "Sorry, only JPG, JPEG, JFIF, PNG, WEBP & GIF files are allowed.<br>";
                $upload_ok = 0;
            }

            // If everything is ok, try to upload the file
            if ($upload_ok == 1) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"][$key], $target_file)) {
                    $image_paths[] = $unique_image_name;
                } else {
                    echo "Sorry, there was an error uploading your file " . $image_name . ".<br>";
                }
            }
        }
    }

    // Process main video upload
    if (!empty($_FILES["video"]["name"])) {
        $video_name = $_FILES["video"]["name"];
        $video_extension = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
        $unique_video_name = uniqid() . "." . $video_extension;
        $target_video_file = $target_dir . $unique_video_name;
        
        // Check file size (max 50MB for videos)
        if ($_FILES["video"]["size"] > 50000000) {
            echo "Sorry, your video file is too large (max 50MB).<br>";
            $upload_ok = 0;
        }
        
        // Check file extension
        $allowed_video_extensions = ["mp4", "webm", "ogg", "mov", "avi"];
        if (!in_array($video_extension, $allowed_video_extensions)) {
            echo "Sorry, only MP4, WebM, OGG, MOV, and AVI video files are allowed.<br>";
            $upload_ok = 0;
        }
        
        // If everything is ok, try to upload the video
        if ($upload_ok == 1) {
            if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_video_file)) {
                $video_paths[] = $unique_video_name;
                
                // Generate thumbnail for the video
                $thumbnail_name = uniqid() . "_thumb.jpg";
                $thumbnail_path = $target_dir . $thumbnail_name;
                
                if (generateVideoThumbnail($target_video_file, $thumbnail_path)) {
                    $video_thumbnail = $thumbnail_name;
                } else {
                    // Use default video thumbnail
                    $video_thumbnail = "video-thumbnail-default.jpg";
                }
            } else {
                echo "Sorry, there was an error uploading your video file.<br>";
            }
        }
    }

    // Process secondary videos (multiple)
    if (!empty($_FILES["secondary_videos"]["name"][0])) {
        foreach ($_FILES["secondary_videos"]["name"] as $key => $video_name) {
            $video_extension = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
            $unique_video_name = uniqid() . "." . $video_extension;
            $target_video_file = $target_dir . $unique_video_name;
            
            // Check file size (max 50MB for videos)
            if ($_FILES["secondary_videos"]["size"][$key] > 50000000) {
                echo "Sorry, your secondary video file " . $video_name . " is too large (max 50MB).<br>";
                continue;
            }
            
            // Check file extension
            $allowed_video_extensions = ["mp4", "webm", "ogg", "mov", "avi"];
            if (!in_array($video_extension, $allowed_video_extensions)) {
                echo "Sorry, only MP4, WebM, OGG, MOV, and AVI video files are allowed for secondary videos.<br>";
                continue;
            }
            
            // Try to upload the video
            if (move_uploaded_file($_FILES["secondary_videos"]["tmp_name"][$key], $target_video_file)) {
                $video_paths[] = $unique_video_name;
            } else {
                echo "Sorry, there was an error uploading your secondary video file " . $video_name . ".<br>";
            }
        }
    }

    // Check if at least images or video were uploaded successfully
    if (count($image_paths) > 0 || count($video_paths) > 0) {
        // Get form data
        $product_name = mysqli_real_escape_string($connection, $_POST['product_name']);
        $product_description = mysqli_real_escape_string($connection, $_POST['description']);
        $price_ksh = mysqli_real_escape_string($connection, $_POST['price_ksh']);
        $available_sizes = mysqli_real_escape_string($connection, $_POST['available_sizes']);
        $available_colors = mysqli_real_escape_string($connection, $_POST['available_colors']);
        $units_available = mysqli_real_escape_string($connection, $_POST['units_available']);
        $category = mysqli_real_escape_string($connection, $_POST['category']);
        $gender_category = mysqli_real_escape_string($connection, $_POST['gender_category']);

        // Generate slug from product name
        $slug = createSlug($product_name);

        // Generate random rating and sold count
        $rating = round(3.5 + (mt_rand(0, 15) / 10), 1);
        $sold = mt_rand(0, 300);

        // Prepare file paths
        $main_image = count($image_paths) > 0 ? $image_paths[0] : '';
        $secondary_images_json = count($image_paths) > 1 ? json_encode(array_slice($image_paths, 1)) : '[]';
        
        $main_video = count($video_paths) > 0 ? $video_paths[0] : null;
        $secondary_videos_json = count($video_paths) > 1 ? json_encode(array_slice($video_paths, 1)) : '[]';

        // Insert product data into the database
        $sql = "INSERT INTO products (name, slug, description, image, video, video_thumbnail, price_ksh, available_sizes, available_colors, units_available, category, gender_category, secondary_image, secondary_videos, rating, sold) 
                VALUES ('$product_name', '$slug', '$product_description', '$main_image', '$main_video', '$video_thumbnail', '$price_ksh', '$available_sizes', '$available_colors', '$units_available', '$category', '$gender_category', '$secondary_images_json', '$secondary_videos_json', '$rating', '$sold')";

        if ($connection->query($sql) === TRUE) {
            $success_message = "New product added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $connection->error;
        }
    } else {
        $error_message = "Please upload at least one image or video.";
    }
}

// Include the header
include('adminheader.php');
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - BrandX</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0a0a0a;
            --secondary: #1a1a1a;
            --accent: #00d2ff;
            --text: #ffffff;
            --text-secondary: #888888;
            --danger: #ff4757;
            --success: #2ed573;
            --card-bg: #1e1e1e;
            --border-color: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--primary);
            color: var(--text);
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            margin-top: 70px; /* Account for fixed header */
        }
        
        h1 {
            font-size: 2.2rem;
            color: var(--accent);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        
        h1 i {
            margin-right: 0.8rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: rgba(46, 213, 115, 0.2);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .alert-danger {
            background-color: rgba(255, 71, 87, 0.2);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .alert i {
            margin-right: 0.8rem;
            font-size: 1.2rem;
        }
        
        .form-container {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
        }
        
        .form-group {
            margin-bottom: 1.8rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 500;
            color: var(--accent);
            font-size: 1.1rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0, 210, 255, 0.2);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .file-input-label {
            display: inline-block;
            padding: 1.2rem 2rem;
            background-color: rgba(0, 210, 255, 0.1);
            color: var(--accent);
            border: 2px dashed var(--accent);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
            width: 100%;
        }
        
        .file-input-label:hover {
            background-color: rgba(0, 210, 255, 0.2);
        }
        
        .file-input-label i {
            margin-right: 0.8rem;
        }
        
        input[type="file"] {
            position: absolute;
            left: -9999px;
        }
        
        .preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .image-preview {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: transform 0.3s;
        }
        
        .video-preview {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--accent);
            background-color: #000;
        }
        
        .preview-item {
            position: relative;
        }
        
        .preview-item .preview-type {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--accent);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1.2rem 2.5rem;
            background-color: var(--accent);
            color: var(--primary);
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background-color: #00b8e6;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 210, 255, 0.3);
        }
        
        .btn i {
            margin-right: 0.8rem;
        }
        
        /* Form layout */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .section-title {
            font-size: 1.3rem;
            color: var(--text);
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 1rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-container {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" class="form-control" name="product_name" id="product_name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" required></textarea>
                </div>
                
                <!-- Images Section -->
                <h3 class="section-title"><i class="fas fa-images"></i> Product Images</h3>
                <div class="form-group">
                    <label>Main Images (Multiple)</label>
                    <div class="file-input-container">
                        <label class="file-input-label" for="image">
                            <i class="fas fa-cloud-upload-alt"></i> Choose Images (Multiple, max 10MB each)
                        </label>
                        <input type="file" name="image[]" id="image" accept="image/*" multiple>
                    </div>
                    <div class="preview-container" id="imagePreview"></div>
                </div>
                
                <!-- Videos Section -->
                <h3 class="section-title"><i class="fas fa-video"></i> Product Videos</h3>
                
                <div class="form-group">
                    <label>Main Video (Optional, max 50MB)</label>
                    <div class="file-input-container">
                        <label class="file-input-label" for="video">
                            <i class="fas fa-video"></i> Choose Main Video
                        </label>
                        <input type="file" name="video" id="video" accept="video/*">
                    </div>
                    <div class="preview-container" id="videoPreview"></div>
                </div>
                
                <div class="form-group">
                    <label>Additional Videos (Optional, max 50MB each)</label>
                    <div class="file-input-container">
                        <label class="file-input-label" for="secondary_videos">
                            <i class="fas fa-video"></i> Choose Additional Videos (Multiple)
                        </label>
                        <input type="file" name="secondary_videos[]" id="secondary_videos" accept="video/*" multiple>
                    </div>
                    <div class="preview-container" id="secondaryVideosPreview"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price_ksh">Price (Ksh)</label>
                        <input type="number" step="0.01" class="form-control" name="price_ksh" id="price_ksh" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="units_available">Units Available</label>
                        <input type="number" class="form-control" name="units_available" id="units_available" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="available_sizes">Available Sizes (comma separated)</label>
                        <input type="text" class="form-control" name="available_sizes" id="available_sizes" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="available_colors">Available Colors (comma separated)</label>
                        <input type="text" class="form-control" name="available_colors" id="available_colors" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Brand</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="">Select Brand</option>
                            <option value="nike">Nike</option>
                            <option value="jordan">Jordan</option>
                            <option value="adidas">Adidas</option>
                            <option value="gucci">Gucci</option>
                            <option value="converse">Converse</option>
                            <option value="puma">Puma</option>
                            <option value="fila">Fila</option>
                            <option value="newbalance">New Balance</option>
                            <option value="timberland">Timberland</option>
                            <option value="clarks">Clarks</option>
                            <option value="vans">Vans</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender_category">Gender Category</label>
                        <select class="form-control" name="gender_category" id="gender_category" required>
                            <option value="all">All</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="children">Children</option>
                            <option value="unisex">Unisex</option>
                            <option value="casual">Casual</option>
                            <option value="formal">Formal</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn">
                    <i class="fas fa-plus-circle"></i> Add Product
                </button>
            </form>
        </div>
    </div>

    <script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = '';
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    const previewDiv = document.createElement('div');
                    previewDiv.classList.add('preview-item');
                    
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.classList.add('image-preview');
                    
                    const typeBadge = document.createElement('div');
                    typeBadge.classList.add('preview-type');
                    typeBadge.textContent = 'IMG';
                    
                    previewDiv.appendChild(img);
                    previewDiv.appendChild(typeBadge);
                    previewContainer.appendChild(previewDiv);
                }
                
                reader.readAsDataURL(file);
            });
        }
    });

    // Main video preview functionality
    document.getElementById('video').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('videoPreview');
        previewContainer.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const videoURL = URL.createObjectURL(file);
            
            const previewDiv = document.createElement('div');
            previewDiv.classList.add('preview-item');
            
            const video = document.createElement('video');
            video.src = videoURL;
            video.controls = true;
            video.classList.add('video-preview');
            
            const typeBadge = document.createElement('div');
            typeBadge.classList.add('preview-type');
            typeBadge.textContent = 'VIDEO';
            
            previewDiv.appendChild(video);
            previewDiv.appendChild(typeBadge);
            previewContainer.appendChild(previewDiv);
        }
    });

    // Secondary videos preview functionality
    document.getElementById('secondary_videos').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('secondaryVideosPreview');
        previewContainer.innerHTML = '';
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                const videoURL = URL.createObjectURL(file);
                
                const previewDiv = document.createElement('div');
                previewDiv.classList.add('preview-item');
                
                const video = document.createElement('video');
                video.src = videoURL;
                video.controls = true;
                video.classList.add('video-preview');
                
                const typeBadge = document.createElement('div');
                typeBadge.classList.add('preview-type');
                typeBadge.textContent = 'VIDEO';
                
                previewDiv.appendChild(video);
                previewDiv.appendChild(typeBadge);
                previewContainer.appendChild(previewDiv);
            });
        }
    });
    </script>
</body>
</html>

<?php
// Close DB connection
$connection->close();
?>