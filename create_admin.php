<?php
$host = "localhost";
$dbname = "student_tracking";
$db_username = "root";
$db_password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CHANGE THESE VALUES
    $username = "Billy";        // new admin username
    $plainPassword = "password123"; // new admin password

    // HASH THE PASSWORD
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // INSERT INTO DATABASE
    $sql = "INSERT INTO admins (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword
    ]);

    echo "<script>alert('Admin created successfully.'); window.location.href='AdminLogin.html';</script>";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>