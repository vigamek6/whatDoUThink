<?php
// db.php - Database connection & seeder (Supports MySQL & Auto-fallback to SQLite for Cloud/Render)

// Ensure session directory is valid and writable
$sess_dir = __DIR__ . '/sessions';
if (!is_dir($sess_dir)) {
    @mkdir($sess_dir, 0777, true);
}
if (is_dir($sess_dir) && is_writable($sess_dir)) {
    @ini_set('session.save_path', $sess_dir);
}

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// 1. Read environment variables if available (e.g. on Render / Cloud)
$db_driver = getenv('DB_CONNECTION') ?: 'auto';
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3306';
$db_name = getenv('DB_NAME') ?: 'questionforyou';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

$pdo = null;
$is_sqlite = false;

// Attempt MySQL connection if not explicitly set to sqlite
if ($db_driver !== 'sqlite') {
    try {
        $pdo = new PDO("mysql:host={$db_host};port={$db_port};charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 3, // fast timeout for fallback
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$db_name}`;");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `question` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `content` TEXT NOT NULL,
                `user_id` INT NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `answer` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `question_id` INT NOT NULL,
                `user_id` INT NOT NULL,
                `content` TEXT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (`question_id`) REFERENCES `question`(`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

    } catch (Throwable $e) {
        // If MySQL is not available (e.g. running on Render without external MySQL), fallback to SQLite
        $pdo = null;
    }
}

// Fallback to SQLite (Zero-config, works everywhere on Render/Cloud immediately!)
if (!$pdo) {
    $is_sqlite = true;
    $sqlite_file = __DIR__ . '/database.sqlite';
    $pdo = new PDO("sqlite:" . $sqlite_file, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec("PRAGMA foreign_keys = ON;");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS question (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            user_id INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS answer (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            question_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            content TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (question_id) REFERENCES question(id) ON DELETE CASCADE
        );
    ");
}

// Seed the 6 questions if table is empty
$count = $pdo->query("SELECT COUNT(*) FROM question")->fetchColumn();
if ($count == 0) {
    $questions = [
        [
            'id' => 1,
            'title' => 'Câu 1: Em muốn tôi tán em kiểu gì?',
            'content' => 'Em hay nhỉ? Tôi thích em từ online cho tới offline. Tại sao tôi thích em á? Ban đầu là do e vui tính còn h thi vì em làm tôi bực mình, cáu bẩn, khó chịu, cục súc nhưng vậy tôi lại nghĩ về em nhiều hơn :v Thế em muốn tôi tán em kiểu gì đây dù là gì thì tôi vẫn muốn có em bằng được!?',
        ],
        [
            'id' => 2,
            'title' => 'Câu 2: Nói có là không, không là có?',
            'content' => 'Em thuộc tuýp người nói có là không, không là có à?',
        ],
        [
            'id' => 3,
            'title' => 'Câu 3: Chủ động hay lạnh lùng?',
            'content' => 'Em thích người chủ động hay hơi lạnh lùng một chút?',
        ],
        [
            'id' => 4,
            'title' => 'Câu 4: Cảm giác được trân trọng',
            'content' => 'Điều gì khiến em cảm thấy được trân trọng?',
        ],
        [
            'id' => 5,
            'title' => 'Câu 5: Điều ít khi nói ra',
            'content' => 'Có điều gì em muốn người khác hiểu về mình nhưng ít khi nói ra không? (last question)',
        ],
        [
            'id' => 6,
            'title' => 'Bonus: Em có muốn hỏi tôi cái gì không?',
            'content' => 'Ô quên mất tiêu!!! Em có muốn hỏi tôi cái gì không :3 ?',
        ],
    ];

    $stmt = $pdo->prepare("INSERT INTO question (id, title, content, user_id) VALUES (:id, :title, :content, 1)");
    foreach ($questions as $q) {
        $stmt->execute([
            ':id' => $q['id'],
            ':title' => $q['title'],
            ':content' => $q['content'],
        ]);
    }
}

// Session user ID generation for respondent
if (!isset($_SESSION['crush_user_id'])) {
    if (isset($_COOKIE['crush_user_id']) && is_numeric($_COOKIE['crush_user_id'])) {
        $_SESSION['crush_user_id'] = (int)$_COOKIE['crush_user_id'];
    } else {
        $new_id = rand(100000, 999999);
        $_SESSION['crush_user_id'] = $new_id;
        if (!headers_sent()) {
            @setcookie('crush_user_id', $new_id, time() + (86400 * 30), "/");
        }
    }
}
$current_user_id = (int)$_SESSION['crush_user_id'];
