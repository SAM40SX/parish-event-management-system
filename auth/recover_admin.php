<?php
include("../config.php");

/* 
======================================================
SACRED HEART SYSTEM CONTROLLER — SECURE PASSWORD RESET
======================================================
This administrative console tool instantly hashes and 
resets target profile credentials using modern Bcrypt algorithms.
======================================================
*/

// Modern, robust hashing method replacing obsolete md5() string generation
$raw_password = "Admin@123";
$secure_hash = password_hash($raw_password, PASSWORD_DEFAULT);
$target_email = "admin@gmail.com";

/* PREPARED TRANSACTION pipeline */
$stmt = $conn->prepare("
    UPDATE users
    SET password = ?
    WHERE email = ?
");

if ($stmt) {
    $stmt->bind_param("ss", $secure_hash, $target_email);
    
    if ($stmt->execute()) {
        if ($conn->affected_rows > 0) {
            echo "✦ Sacred Heart Registry: Administrative credential override complete. Password securely hashed and applied.";
        } else {
            echo "✦ Notice: Database write executed, but no profile matched the email address '$target_email'.";
        }
    } else {
        echo "✕ Error: System failed to execute the cryptographic update entry within the database.";
    }
    
    $stmt->close();
} else {
    echo "✕ Error: Secure query compilation layer failed.";
}
?>