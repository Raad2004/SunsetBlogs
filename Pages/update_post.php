<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/admin_functions.php';
requireLogin();

// Set the response header to JSON
header('Content-Type: application/json');

// Check if the user is admin
if (!isAdmin()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Get the POST data from FormData
$post_id = $_POST['post_id'] ?? null;
$title = $_POST['title'] ?? null;
$content = $_POST['content'] ?? null;
$category = $_POST['category'] ?? null;
$date = $_POST['date'] ?? null;
$author = $_POST['author'] ?? null;
$additional_authors = $_POST['additional_authors'] ?? null;
$media_links = $_POST['media_links'] ?? null;
$tags = $_POST['tags'] ?? null;

// Validate required fields
if (!$post_id || !$title || !$content || !$category) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

try {
    // Handle blog image upload
    $blog_image_path = null;
    if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../file_uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['blog_image']['name'], PATHINFO_EXTENSION);
        $blog_image_filename = 'blog_' . uniqid() . '.' . $file_extension;
        $blog_image_path = 'file_uploads/' . $blog_image_filename;
        
        // Delete old blog image if it exists
        $stmt = $pdo->prepare("SELECT blog_image FROM posts WHERE post_id = ?");
        $stmt->execute([$post_id]);
        $old_image = $stmt->fetchColumn();
        if ($old_image && file_exists('../' . $old_image)) {
            unlink('../' . $old_image);
        }
        
        move_uploaded_file($_FILES['blog_image']['tmp_name'], $upload_dir . $blog_image_filename);
    }

    // Handle thumbnail image upload
    $thumbnail_path = null;
    if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../file_uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['thumbnail_image']['name'], PATHINFO_EXTENSION);
        $thumbnail_filename = 'thumb_' . uniqid() . '.' . $file_extension;
        $thumbnail_path = 'file_uploads/' . $thumbnail_filename;
        
        // Delete old thumbnail if it exists
        $stmt = $pdo->prepare("SELECT thumbnail_image FROM posts WHERE post_id = ?");
        $stmt->execute([$post_id]);
        $old_thumbnail = $stmt->fetchColumn();
        if ($old_thumbnail && file_exists('../' . $old_thumbnail)) {
            unlink('../' . $old_thumbnail);
        }
        
        move_uploaded_file($_FILES['thumbnail_image']['tmp_name'], $upload_dir . $thumbnail_filename);
    }

    // Update post in database
    $sql = "UPDATE posts SET 
            title = :title, 
            content = :content, 
            category = :category, 
            date = :date, 
            author = :author, 
            additional_authors = :additional_authors, 
            media_links = :media_links, 
            tags = :tags";
    
    $params = [
        'title' => $title,
        'content' => $content,
        'category' => $category,
        'date' => $date,
        'author' => $author,
        'additional_authors' => $additional_authors,
        'media_links' => $media_links,
        'tags' => $tags,
        'post_id' => $post_id
    ];

    // Add image paths to update query if new images were uploaded
    if ($blog_image_path) {
        $sql .= ", blog_image = :blog_image";
        $params['blog_image'] = $blog_image_path;
    }
    if ($thumbnail_path) {
        $sql .= ", thumbnail_image = :thumbnail_image";
        $params['thumbnail_image'] = $thumbnail_path;
    }

    $sql .= " WHERE post_id = :post_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Post updated successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating post: ' . $e->getMessage()]);
}
?> 