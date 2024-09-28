<?php

header("Content-Type: application/json");
date_default_timezone_set("Asia/Dhaka");

$host = 'localhost'; 
$dbname = 'ppc'; 
$username = 'root'; 
$password = ''; 



function response(string $status, string $message, array $data = []) {
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    die();
}

try { 
    // PDO সংযোগ তৈরি
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Upload a file POST
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action']=== 'upload'){
        if(isset($_FILES['file'])){
            
            $name = $_FILES['file']['name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];
            $tmp_name = $_FILES['file']['tmp_name'];
            $targetDirectory = "uploads/";
            $path = $targetDirectory . time() .'-'. $name;
            $curentTime = $curentTime = date('Y-m-d H:i:s', time());
            $fileSizeKB = round($size / 1024, 2) . ' KB';
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            


            


            // Allowed file types
            $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
            // Allowed extension types
            $allowed_extension = ['pdf','jpeg','png'];
            // Checking the file type
            if (!in_array($type, $allowed_types) || !in_array($extension,$allowed_extension)) {
                response('error','Only PDF, JPEG, or PNG files are allowed.');
            } 
 
            // 5MB = 5 * 1024 * 1024 bytes
            $max_file_size = 5 * 1024 * 1024; // 5MB
            // Checking the file size
            if ($size > $max_file_size) {
                response('error','File size cannot exceed 5 MB.');        
            }
             
            if (move_uploaded_file($tmp_name, $path)) {
                $stmt = $pdo->prepare("INSERT INTO filestwo (name, path, size, created_at) VALUES (:name,:path,:size,:created_at)");
                $data = ['name' => $name,'path' => $path,'size' => $fileSizeKB,'created_at' => $curentTime];
                if($stmt->execute($data)){
                    $data = array_merge(['id'=>$pdo->lastInsertId()],$data);
                    response('Success','File uploaded successfully.',$data);
                }else{
                    unlink($path);
                    response('Error','Some error please try agien.');
                }
            }else{
                response('Error','File upload failed.');
            }

        }
    }

    //Get all file
    if($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action']=== 'get'){
        $query = "SELECT * FROM filestwo";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($data) > 0){
            response('Success','All data get successfuly.',$data);
        }

        response('Error','no data found in database.');
    }
    
    //Delete a file
    if($_SERVER['REQUEST_METHOD'] === 'DELETE' && $_GET['action']=== 'delete'){
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'];
        $query = "SELECT path FROM filestwo WHERE id = ?";
        $stmt = $pdo->prepare($query);
        if($stmt->execute([$id])){
           $data = $stmt->fetch(PDO::FETCH_ASSOC);
           if($data){
            $path = $data['path'];
           $query = "DELETE FROM filestwo WHERE id = ?";
           $stmt = $pdo->prepare($query);
           if($stmt->execute([$id])){
            if(file_exists($path)){
                unlink($path);
                response('Success',"Id $id Delete data successfuly.");
            }
           }
           }
        }
        
        response('Error',"No data found this id $id");
    }
    

} catch (PDOException $e) {
    response('error', 'Database error: ' . $e->getMessage());
}
    

?>