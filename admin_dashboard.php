<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: AdminLogin.html");
    exit;
}

$host = "localhost";
$dbname = "student_tracking";
$db_username = "root";
$db_password = "";

$students = [];
$search = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   $search = trim($_GET["search"] ?? "");

if (!empty($search)) {
    $sql = "SELECT * FROM student_onboarding
            WHERE first_name LIKE :search
               OR last_name LIKE :search
               OR major LIKE :search
               OR links LIKE :search
               OR graduation_year LIKE :search
            ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':search' => "%$search%"]);
} else {
    $sql = "SELECT * FROM student_onboarding ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
}

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .header {
            background: #2d89ef;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header a {
            color: white;
            text-decoration: none;
            background: #1b5fa7;
            padding: 8px 14px;
            border-radius: 6px;
        }

        .container {
            width: 85%;
            margin: 30px auto;
        }

        .search-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .search-box form {
            display: flex;
            gap: 10px;
        }

        .search-box input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            padding: 10px 18px;
            border: none;
            background: #2d89ef;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .student-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 18px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        .student-card h2 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #2d89ef;
        }

        .student-card p {
            margin: 8px 0;
        }

        .student-card a {
            color: #2d89ef;
            text-decoration: none;
        }

        .student-card a:hover {
            text-decoration: underline;
        }

        .no-results {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Admin Dashboard</h1>
        <div>
            Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?> |
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="search-box">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by name, major, or links..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php if (count($students) > 0): ?>
            <?php foreach ($students as $student): ?>
                <div class="student-card">
                    <h2><?php echo htmlspecialchars($student["first_name"] . " " . $student["last_name"]); ?></h2>
                    <p><strong>Major:</strong> <?php echo htmlspecialchars($student["major"]); ?></p>
                    <p><strong>Graduation Year:</strong> <?php echo htmlspecialchars($student["graduation_year"]); ?></p>
                   <p><strong>Links:</strong></p>
                    <?php if (!empty($student["links"])): ?>
                        <?php
                            preg_match_all('/https?:\/\/[^\s]+|www\.[^\s]+/i', $student["links"], $matches);
                            $validLinks = array_filter(array_map('trim', $matches[0]));
                        ?>

                        <?php if (count($validLinks) > 0): ?>
                            <ul style="margin-top: 5px; padding-left: 20px;">
                                <?php foreach ($validLinks as $link): ?>
                                    <?php
                                        $href = $link;
                                        if (!preg_match('/^https?:\/\//i', $href)) {
                                            $href = 'https://' . $href;
                                        }
                                    ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($href); ?>" target="_blank">
                                            <?php echo htmlspecialchars($link); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No links provided</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>No links provided</p>
                    <?php endif; ?>
                    <p><strong>Date Added:</strong> <?php echo htmlspecialchars($student["created_at"]); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No student profiles found.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>