<?php
// password store
$password_plain = "AdPeter#01"; 

// generate hashed password
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT); 

// Output hased pasword
echo "Hashed password: " . $password_hash . "<br>";
echo "Length: " . strlen($password_hash);
?>
