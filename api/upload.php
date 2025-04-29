<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        
        // Check for errors
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
            
            // Check file type
            if (in_array($file['type'], $allowed_types)) {
                $max_size = 5 * 1024 * 1024; // 5MB
                
                // Check file size
                if ($file['size'] <= $max_size) {
                    $upload_dir = '../uploads/products/';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_name = uniqid() . '_' . basename($file['name']);
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $response = array(
                            'success' => true,
                            'message' => 'File uploaded successfully',
                            'path' => 'uploads/products/' . $file_name
                        );
                    } else {
                        $response = array(
                            'success' => false,
                            'message' => 'Error uploading file'
                        );
                    }
                } else {
                    $response = array(
                        'success' => false,
                        'message' => 'File size exceeds limit (5MB)'
                    );
                }
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error uploading file'
            );
        }
    } else {
        $response = array(
            'success' => false,
            'message' => 'No file uploaded'
        );
    }
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request method'
    );
}

echo json_encode($response);
?> 