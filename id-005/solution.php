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

     // ফাইল আপলোড করার প্রক্রিয়া
    if($_SERVER['REQUEST_METHOD']=== 'POST' && $_GET['action'] === 'upload'){
        
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];

            // ফাইলের নাম, আকার এবং টাইপ যাচাই করা
            $fileName = basename($file['name']);
            $fileSize = $file['size'];
            $targetDirectory = "uploads/";
            $targetFile = $targetDirectory . $fileName;
            // যদি একই নামের ফাইল থেকে থাকে তবে নাম পরিবর্তন করা
            $fileInfo = pathinfo($targetFile);
            $fileBaseName = $fileInfo['filename'];
            $fileExtension = $fileInfo['extension'];
            $counter = 1;

            while (file_exists($targetFile)) {
                $fileName = $fileBaseName . '_' . $counter . '.' . $fileExtension;
                $targetFile = $targetDirectory . $fileName;
                $counter++;
            }

            // ফাইল আপলোড করা
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                // ফাইলের আকারকে কিলোবাইটে রূপান্তর করা
                $fileSizeKB = round($fileSize / 1024, 2) . ' KB';

                // ডাটাবেসে ফাইলের তথ্য সংরক্ষণ করা
                $stmt = $pdo->prepare("INSERT INTO files (name, size,created_at) VALUES (:file_name, :file_size,:created_at)");
                $stmt->execute([
                    ':file_name' => $fileName,
                    ':file_size' => $fileSizeKB,
                    ':created_at' => time()
                ]);

                // রেসপন্স প্রদান করা
                  $data =[
                        'file_name' => $fileName,
                        'size' => $fileSizeKB,
                        'uploaded_at' => date('Y-m-d H:i:s')
                         ];
                

                echo response('success','File uploaded successfully',$data);
            } else {
                echo response('error','Failed to upload file');
            }
        } else {
            echo response('error','No file was uploaded');
        }
    }

    //Get All data in database
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'show') {
        $query = "SELECT * FROM files";
        
        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        // Fetch all results
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Check if any data was fetched
        if (count($result) > 0) {
            // Success response
            echo response('success', 'All files fetched successfully', $result);
        } else {
            // No data found response
            echo response('error', 'No data found');
        }
    }
    

} catch (PDOException $e) {
    response('error', 'Database error: ' . $e->getMessage());
}
    

?>