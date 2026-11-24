<?php
// api/submit.php - Save question answers to database
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Chỉ chấp nhận phương thức POST']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

$user_id = $current_user_id;
$question_id = isset($data['question_id']) ? (int)$data['question_id'] : 0;
$content = isset($data['content']) ? trim($data['content']) : '';

if ($question_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Mã câu hỏi không hợp lệ']);
    exit;
}

if ($content === '') {
    echo json_encode(['success' => false, 'error' => 'Nội dung câu trả lời không được để trống']);
    exit;
}

try {
    // Check if question exists
    $checkQ = $pdo->prepare("SELECT id FROM `question` WHERE id = :qid LIMIT 1");
    $checkQ->execute([':qid' => $question_id]);
    if (!$checkQ->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Câu hỏi không tồn tại trong hệ thống']);
        exit;
    }

    // Check if answer already exists for this user and question
    $checkA = $pdo->prepare("SELECT id FROM `answer` WHERE question_id = :qid AND user_id = :uid LIMIT 1");
    $checkA->execute([':qid' => $question_id, ':uid' => $user_id]);
    $existing = $checkA->fetch();

    if ($existing) {
        // Update existing answer
        $updateStmt = $pdo->prepare("UPDATE `answer` SET content = :content, created_at = CURRENT_TIMESTAMP WHERE id = :id");
        $updateStmt->execute([
            ':content' => $content,
            ':id' => $existing['id']
        ]);
        $answer_id = $existing['id'];
    } else {
        // Insert new answer
        $insertStmt = $pdo->prepare("INSERT INTO `answer` (question_id, user_id, content, created_at) VALUES (:qid, :uid, :content, CURRENT_TIMESTAMP)");
        $insertStmt->execute([
            ':qid' => $question_id,
            ':uid' => $user_id,
            ':content' => $content
        ]);
        $answer_id = $pdo->lastInsertId();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Lưu câu trả lời thành công',
        'answer_id' => $answer_id,
        'question_id' => $question_id,
        'user_id' => $user_id
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Lỗi hệ thống: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
