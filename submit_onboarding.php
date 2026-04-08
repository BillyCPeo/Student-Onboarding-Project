<?php
$host = "localhost";
$dbname = "student_tracking";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $first_name = trim($_POST["first_name"] ?? "");
        $last_name = trim($_POST["last_name"] ?? "");
        $major = trim($_POST["major"] ?? "");
        $graduation_year = $_POST["graduation_year"] ?? "";        
        $linksArray = $_POST["links"] ?? [];
        $linksArray = array_map('trim', $linksArray);
        $linksArray = array_filter($linksArray);
        $links = implode("\n", $linksArray);
        $consent = isset($_POST["consent"]) ? 1 : 0;

        if (empty($first_name) || empty($last_name) || empty($major) || empty($graduation_year)) {
            die("<script>alert('Please fill in all required fields.'); window.history.back();</script>");
        }

        if ($consent !== 1) {
            die("<script>alert('You must agree before submitting your information.'); window.history.back();</script>");
        }

       $sql = "INSERT INTO student_onboarding 
        (first_name, last_name, major, graduation_year, links, consent_given)
        VALUES (:first_name, :last_name, :major, :graduation_year, :links, :consent_given)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':major' => $major,
            ':graduation_year' => $graduation_year,
            ':links' => $links,
            ':consent_given' => $consent
        ]);

        echo "<script>alert('Student onboarding submitted successfully.'); window.location.href='onboarding.html';</script>";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>