<?php
require_once "db.php";

$email = $_POST["email"];

// Generate token
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// Update database
$sql = "UPDATE users
        SET reset_hash_token = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$token_hash, $expiry, $email]);

// Use rowCount() with PDO instead of affected_rows
if ($stmt->rowCount()) {

    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
Click <a href="http://example.com/reset-password.php?token=$token">here</a> 
to reset your password.
END;

    try {
        $mail->send();
        echo "Message sent, please check your inbox.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }

} else {
    echo "No account found with this email.";
}
