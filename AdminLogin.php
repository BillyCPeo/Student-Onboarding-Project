<?php
session_start();

$host = "localhost";
$dbname = "student_tracking";
$db_username = "root";
$db_password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST["username"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if (empty($username) || empty($password)) {
            die("<script>alert('Please enter both username and password.'); window.history.back();</script>");
        }

        $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin["password"])) {
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            echo "<script>alert('Login successful.'); window.location.href='admin_dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid username or password.'); window.history.back();</script>";
            exit;
        }
    } else {
        header("Location: AdminLogin.html");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>