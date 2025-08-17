<?php
require_once "db.php";
$message = $_POST['message'] ?? '';
$message = trim($message);

// Exact match (or use LIKE for partial match)
$stmt = $conn->prepare("SELECT answer FROM chatbot WHERE question = :question LIMIT 1");
$stmt->bindParam(':question', $message, PDO::PARAM_STR);
$stmt->execute();

if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo $row['answer'];
} else {
    echo "Sorry, I don't understand that.";
}
?>
