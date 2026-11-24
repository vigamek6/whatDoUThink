<?php
require_once __DIR__ . '/db.php';

// Handle delete action if requested
$message = '';
if (isset($_POST['action']) && $_POST['action'] === 'delete_user' && isset($_POST['target_user_id'])) {
    $target_uid = (int)$_POST['target_user_id'];
    $delStmt = $pdo->prepare("DELETE FROM `answer` WHERE user_id = :uid");
    $delStmt->execute([':uid' => $target_uid]);
    $message = "Đã xóa câu trả lời của session #{$target_uid} thành công!";
}

if (isset($_POST['action']) && $_POST['action'] === 'clear_all') {
    $pdo->exec("TRUNCATE TABLE `answer`");
    $message = "Đã xóa sạch toàn bộ câu trả lời!";
}

// Fetch all answers with question info
$query = "
    SELECT 
        a.id AS answer_id,
        a.user_id,
        a.question_id,
        a.content AS answer_content,
        a.created_at AS answered_at,
        q.title AS question_title,
        q.content AS question_content
    FROM `answer` a
    JOIN `question` q ON a.question_id = q.id
    ORDER BY a.user_id DESC, a.question_id ASC
";
$stmt = $pdo->query($query);
$rawAnswers = $stmt->fetchAll();

// Group answers by user_id
$grouped = [];
foreach ($rawAnswers as $ans) {
    $uid = $ans['user_id'];
    if (!isset($grouped[$uid])) {
        $grouped[$uid] = [
            'user_id' => $uid,
            'answers' => [],
            'last_active' => $ans['answered_at']
        ];
    }
    $grouped[$uid]['answers'][] = $ans;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Xem Câu Trả Lời Của Crush 🔥</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📜</text></svg>">
</head>
<body class="bg-[#0b0709] text-gray-100 min-h-screen py-8 px-4 font-sans">
    
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div class="manga-panel p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🔥</span>
                <div>
                    <h1 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500 font-title">
                        HỘP THƯ BÍ MẬT TỪ CRUSH
                    </h1>
                    <p class="text-xs text-neutral-400 mt-0.5">
                        Theo dõi câu trả lời trực tiếp được lưu trong database <code class="text-amber-400 font-mono">questionforyou</code>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="index.php" class="px-4 py-2 rounded-xl bg-neutral-900 border border-orange-500/40 text-xs font-bold text-amber-400 hover:border-amber-400 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Về trang trắc nghiệm</span>
                </a>
                <button onclick="window.location.reload()" class="px-4 py-2 rounded-xl bg-orange-600/20 border border-orange-500 text-xs font-bold text-orange-400 hover:bg-orange-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Tải lại dữ liệu</span>
                </button>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if ($message): ?>
            <div class="p-4 rounded-xl bg-amber-950/60 border border-amber-500/50 text-amber-300 text-sm font-semibold flex items-center gap-2">
                <span>✨</span>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        <?php endif; ?>

        <!-- Empty State -->
        <?php if (empty($grouped)): ?>
            <div class="manga-panel p-12 text-center flex flex-col items-center gap-4">
                <span class="text-5xl">💌</span>
                <h3 class="text-lg font-bold text-gray-200">Chưa có câu trả lời nào từ crush</h3>
                <p class="text-xs text-neutral-400 max-w-sm">
                    Gửi link website <code class="text-orange-400 font-mono">http://localhost/whatDoUThink</code> (hoặc qua ngrok/laragon) cho em ấy và chờ bất ngờ nhé!
                </p>
                <a href="index.php" target="_blank" class="mt-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-orange-500 text-neutral-950 font-black text-xs uppercase tracking-wider">
                    Thử làm trắc nghiệm ngay 🔥
                </a>
            </div>
        <?php else: ?>

            <!-- Answer Cards Grouped by Session / User -->
            <div class="space-y-6">
                <?php foreach ($grouped as $uid => $userGroup): ?>
                    <div class="manga-panel p-6 space-y-5">
                        
                        <!-- User Header Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-orange-500/20 pb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-lg bg-orange-600/20 border border-orange-500/50 flex items-center justify-center text-sm font-black text-amber-400 font-mono">
                                    #<?= $uid ?>
                                </span>
                                <div>
                                    <h2 class="text-base font-bold text-gray-100 flex items-center gap-2">
                                        <span>Người trả lời (Session #<?= $uid ?>)</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-950 border border-red-500/40 text-red-300 font-semibold">
                                            <?= count($userGroup['answers']) ?> / 6 câu
                                        </span>
                                    </h2>
                                    <span class="text-[11px] text-neutral-400 font-mono">
                                        Thời gian: <?= $userGroup['last_active'] ?>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button onclick="copyAnswers(<?= $uid ?>)" class="px-3 py-1.5 rounded-lg bg-neutral-900 border border-orange-500/30 hover:border-orange-500 text-xs text-amber-400 font-semibold transition-all flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                    <span>Copy tóm tắt</span>
                                </button>
                                <form method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa câu trả lời của session này?');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="target_user_id" value="<?= $uid ?>">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-950/60 border border-red-500/30 hover:border-red-500 text-xs text-red-400 font-semibold transition-all">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Answers List -->
                        <div id="answers-<?= $uid ?>" class="space-y-3.5">
                            <?php foreach ($userGroup['answers'] as $ans): ?>
                                <div class="p-3.5 rounded-xl bg-neutral-900/60 border border-orange-500/20 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-amber-400">
                                            <?= htmlspecialchars($ans['question_title']) ?>
                                        </span>
                                        <span class="text-[11px] text-neutral-500 font-mono">
                                            <?= date('H:i d/m/Y', strtotime($ans['answered_at'])) ?>
                                        </span>
                                    </div>
                                    <p class="text-xs text-neutral-400 italic">
                                        "<?= htmlspecialchars($ans['question_content']) ?>"
                                    </p>
                                    <div class="pt-1.5 border-t border-orange-500/10">
                                        <div class="text-sm font-semibold text-gray-100 bg-neutral-950/70 p-2.5 rounded-lg border border-orange-500/30 font-sans leading-relaxed">
                                            👉 <span class="text-amber-300"><?= nl2br(htmlspecialchars($ans['answer_content'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Clear All Button -->
            <div class="text-right pt-4">
                <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa TOÀN BỘ câu trả lời trong database?');">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="text-xs text-red-500/80 hover:text-red-400 hover:underline">
                        ⚠️ Xóa toàn bộ dữ liệu câu trả lời (Clear Database)
                    </button>
                </form>
            </div>

        <?php endif; ?>

    </div>

    <!-- Script to copy answers formatted -->
    <script>
        function copyAnswers(uid) {
            const container = document.getElementById('answers-' + uid);
            if (!container) return;

            let text = "🔥 CÂU TRẢ LỜI CỦA CRUSH (Session #" + uid + ") 🔥\n\n";
            const items = container.querySelectorAll('.space-y-1\\.5, div.p-3\\.5');
            items.forEach((item, idx) => {
                const title = item.querySelector('.font-bold')?.innerText || '';
                const ans = item.querySelector('.text-amber-300')?.innerText || '';
                text += `[${title}]\n👉 ${ans}\n\n`;
            });

            navigator.clipboard.writeText(text).then(() => {
                alert("Đã copy toàn bộ câu trả lời của session #" + uid + " vào clipboard! Bạn có thể dán để nhắn tin với em ấy ❤️");
            }).catch(err => {
                console.error("Lỗi copy:", err);
            });
        }
    </script>
</body>
</html>
