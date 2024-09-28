<?php

$host = 'localhost'; 
$dbname = 'ppc'; 
$username = 'root'; 
$password = ''; 

header("Content-Type: application/json");

function response(string $status, string $message, array $data = []) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit();
}

try {
    // PDO সংযোগ তৈরি
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // GET - সব ডাটা পাওয়া
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare("SELECT id, title, content FROM articles");
        $stmt->execute();

        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($stmt->rowCount() > 0) {
            response('success', 'All articles retrieved successfully', $articles);
        } else {
            response('success', 'No articles found', []);
        }
    }

    // POST - ডাটা ইনসার্ট করা
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $title = $input['title'] ?? null;
        $content = $input['content'] ?? null;
        $createdAt = $input['created_at'] ?? null;

        if (empty($title) || empty($content) || empty($createdAt)) {
            response('error', 'All fields are required');
        }

        $stmt = $pdo->prepare("INSERT INTO articles (title, content, created_at) VALUES (:title, :content, :created_at)");
        $data = ['title' => $title, 'content' => $content, 'created_at' => $createdAt];

        if ($stmt->execute($data)) {
            $data['id'] = $pdo->lastInsertId();
            response('success', 'Article added successfully', $data);
        } else {
            response('error', 'Failed to add the article');
        }
    }

    // UPDATE - ডাটা আপডেট করা
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
    
        // ইনপুট যাচাই করা
        $id = $input['id'] ?? null;
        $title = $input['title'] ?? null;
        $content = $input['content'] ?? null;
    
        if (empty($id) || !is_numeric($id)) {
            response('error', 'Invalid or missing ID', []);
        }
        
        if (empty($title) || empty($content)) {
            response('error', 'Title and content are required', []);
        }
    
        // আপডেট করার জন্য SQL কুয়েরি
        $stmt = $pdo->prepare("UPDATE articles SET title = :title, content = :content WHERE id = :id");
        $stmt->execute(['title' => $title, 'content' => $content, 'id' => $id]);
    
        // আপডেট হওয়া রো চেক করা
        if ($stmt->rowCount() > 0) {
            response('success', 'Article updated successfully', ['id' => $id, 'title' => $title, 'content' => $content]);
        } else {
            response('error', "No article found with ID $id", []);
        }
    }
    

    // DELETE - ডাটা ডিলিট করা
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (empty($id) || !is_numeric($id)) {
            response('error', 'Invalid or missing ID');
        }

        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        if ($stmt->execute([$id])) {
            if ($stmt->rowCount() > 0) {
                response('success', 'Article deleted successfully');
            } else {
                response('error', "No article found with ID $id");
            }
        } else {
            response('error', 'Failed to delete the article');
        }
    }

} catch (PDOException $e) {
    response('error', 'Database error: ' . $e->getMessage());
}
