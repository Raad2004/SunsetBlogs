<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Pages/login.html');
    exit();
}

// Database connection
$host = 'localhost';
$dbname = 'sunset_blogs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $title = $_POST['blogTitle'];
        $date = $_POST['blogDate'];
        $author = $_POST['blogAuthor'];
        $additional_authors = $_POST['additionalAuthors'] ?? '';
        $media_links = $_POST['mediaLinks'] ?? '';
        $tags = $_POST['blogTags'] ?? '';
        $category = $_POST['blogCategories'];
        
        // Handle image uploads
        $blog_image = '';
        $thumbnail_image = '';
        
        if (isset($_FILES['blogImage']) && $_FILES['blogImage']['error'] === UPLOAD_ERR_OK) {
            $blog_image = handleImageUpload($_FILES['blogImage']);
        }
        
        if (isset($_FILES['thumbnailImage']) && $_FILES['thumbnailImage']['error'] === UPLOAD_ERR_OK) {
            $thumbnail_image = handleImageUpload($_FILES['thumbnailImage']);
        }
        
        // Get subtitles and content
        $subtitles = $_POST['subtitles'] ?? [];
        $contents = $_POST['subtitles-content'] ?? [];
        
        // Combine subtitles and content into a JSON structure
        $content_json = [];
        for ($i = 0; $i < count($subtitles); $i++) {
            if (!empty($subtitles[$i]) && !empty($contents[$i])) {
                $content_json[] = [
                    'subtitle' => $subtitles[$i],
                    'content' => $contents[$i]
                ];
            }
        }
        
        // Insert into posts table
        $sql = "INSERT INTO posts (user_id, title, date, author, additional_authors, media_links, 
                blog_image, thumbnail_image, tags, category, content) 
                VALUES (:user_id, :title, :date, :author, :additional_authors, :media_links, 
                :blog_image, :thumbnail_image, :tags, :category, :content)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':title' => $title,
            ':date' => $date,
            ':author' => $author,
            ':additional_authors' => $additional_authors,
            ':media_links' => $media_links,
            ':blog_image' => $blog_image,
            ':thumbnail_image' => $thumbnail_image,
            ':tags' => $tags,
            ':category' => $category,
            ':content' => json_encode($content_json)
        ]);
        
        // Redirect to success page or dashboard
        header('Location: Pages/your-work.html');
        exit();
        
    } catch(PDOException $e) {
        die("Error saving post: " . $e->getMessage());
    }
}

function handleImageUpload($file) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        throw new Exception("File is not an image.");
    }
    
    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        throw new Exception("Sorry, your file is too large.");
    }
    
    // Allow certain file formats
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" ) {
        throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        throw new Exception("Sorry, there was an error uploading your file.");
    }
}
?> 