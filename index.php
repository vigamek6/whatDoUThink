<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rốt Cuộc Em Muốn Gì? 🤔</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        fire: {
                            crimson: '#d90429',
                            scarlet: '#ef233c',
                            orange: '#ff5722',
                            gold: '#ffb703',
                            yellow: '#ffea00',
                            dark: '#0b0709',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Favicon (Fiery heart emoji) -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔥</text></svg>">
</head>
<body class="bg-[#0b0709] text-gray-100 flex flex-col justify-between min-h-screen relative select-none">

    <!-- Canvas for Manga Fire Engine -->
    <canvas id="fireCanvas"></canvas>

    <!-- Manga Speedlines Slash Effect -->
    <div id="speedlines" class="speedlines-overlay"></div>

    <!-- Header Navigation -->
    <header class="relative z-10 w-full px-5 py-4 flex items-center justify-between max-w-4xl mx-auto">
        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-950/80 border border-orange-500/60 shadow-lg text-lg animate-pulse-glow">
                🔥
            </span>
            <span class="font-manga text-xl tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-orange-500 to-red-500 font-extrabold uppercase">
                What Do You Want?
            </span>
        </div>

        <div class="flex items-center gap-3">
            <!-- Sound Toggle Button -->
            <button id="sound-toggle" type="button" title="Bật/Tắt âm thanh" class="w-10 h-10 rounded-xl bg-neutral-900/80 border border-orange-500/40 hover:border-amber-400 flex items-center justify-center text-amber-400 transition-all shadow-md active:scale-95">
                <svg id="sound-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-3 w-full max-w-2xl mx-auto">

        <!-- Progress Ki / Energy Bar (Hidden in Intro) -->
        <div id="progress-container" class="hidden w-full flex-col gap-1.5 mb-4 max-w-xl transition-all">
            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-gray-300">
                <span id="stage-badge" class="text-amber-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
                    STAGE 01 • THE CONFESSION
                </span>
                <span id="progress-text" class="text-orange-400 font-manga tracking-widest text-sm">1/6</span>
            </div>
            <div class="ki-bar-bg w-full h-3 rounded-full overflow-hidden p-0.5">
                <div id="ki-fill" class="ki-bar-fill h-full rounded-full w-1/6"></div>
            </div>
        </div>

        <!-- STEP 0: INTRO SCREEN -->
        <div id="step-intro" class="step-container active w-full">
            <div class="manga-panel p-6 sm:p-9 text-center flex flex-col items-center gap-6">
                
                <!-- Manga Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-950/70 border border-orange-500/50 text-xs font-semibold text-amber-300 uppercase tracking-widest shadow-inner">
                    <span>⚡ CONFESSION PROTOCOL ⚡</span>
                </div>

                <!-- Epic Animated Title -->
                <div class="space-y-3">
                    <h1 class="font-title text-3xl sm:text-5xl font-black tracking-tight leading-tight fiery-text-glow text-transparent bg-clip-text bg-gradient-to-b from-yellow-200 via-amber-400 to-orange-600">
                        RỐT CUỘC EM MUỐN GÌ? 🤔
                    </h1>
                    <p class="text-gray-300 text-sm sm:text-base font-normal max-w-md mx-auto leading-relaxed">
                        Một vài câu hỏi nghiêm túc <span class="italic text-orange-400 font-medium">(hoặc không)</span> để tìm hiểu xem rốt cuộc tâm tư em đang giấu điều gì...
                    </p>
                </div>

                <!-- Manga Decorative Illustration / Flame Core -->
                <div class="relative w-28 h-28 my-1 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-t from-red-600 via-orange-500 to-yellow-400 blur-xl opacity-50 animate-pulse"></div>
                    <div class="relative text-6xl animate-flicker">
                        🔥
                    </div>
                </div>

                <!-- Action Button -->
                <button id="btn-start" type="button" class="w-full sm:w-auto px-9 py-4 rounded-2xl bg-gradient-to-r from-red-600 via-orange-500 to-amber-500 text-neutral-950 font-black text-lg sm:text-xl uppercase tracking-wider fiery-btn-glow transition-all flex items-center justify-center gap-3 active:scale-95">
                    <span>BẮT ĐẦU NÀO 🔥</span>
                    <svg class="w-6 h-6 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>

                <div class="text-xs text-neutral-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Câu trả lời của em sẽ được gửi trực tiếp đến tôi</span>
                </div>
            </div>
        </div>

        <!-- STEP 1 -> 6: QUESTION SCREEN -->
        <div id="step-question" class="step-container w-full">
            <div id="question-card" class="manga-panel p-6 sm:p-8 flex flex-col gap-5 transition-all">
                
                <!-- Question Header -->
                <div class="flex items-center justify-between border-b border-orange-500/20 pb-3">
                    <span id="question-badge" class="px-2.5 py-0.5 rounded bg-red-900/60 border border-orange-500/50 text-[11px] font-bold text-amber-300 uppercase tracking-widest">
                        CÂU HỎI 01
                    </span>
                    <span id="question-title" class="text-xs md:text-sm font-semibold text-gray-300 truncate max-w-[200px] md:max-w-xs">
                        Em muốn tôi tán em kiểu gì?
                    </span>
                </div>

                <!-- Question Text -->
                <div class="space-y-2">
                    <p id="question-text" class="text-base sm:text-lg font-medium text-gray-100 leading-relaxed">
                        <!-- Filled by survey.js -->
                    </p>
                </div>

                <!-- Choice Options Container -->
                <div id="options-container" class="flex flex-col gap-2.5 my-1">
                    <!-- Filled dynamically -->
                </div>

                <!-- Custom Input Field for Option 'answer else...' -->
                <div id="custom-input-container" class="hidden flex flex-col gap-1.5">
                    <label class="text-xs font-semibold text-amber-400 flex items-center gap-1">
                        <span>✏️ Câu trả lời khác của em:</span>
                    </label>
                    <input id="custom-input" type="text" maxlength="300" placeholder="Viết câu trả lời của em vào đây nhé..." class="manga-input w-full p-3 rounded-xl text-sm md:text-base placeholder-neutral-500">
                </div>

                <!-- Textarea Answer Container for Open Questions -->
                <div id="text-answer-container" class="hidden flex flex-col gap-1.5">
                    <textarea id="text-answer" rows="4" maxlength="1000" placeholder="Nhập câu trả lời của em..." class="manga-input w-full p-3.5 rounded-xl text-sm md:text-base placeholder-neutral-500 resize-none"></textarea>
                    <div class="text-right text-[11px] text-neutral-400">
                        <span>Chia sẻ thoải mái, anh luôn lắng nghe ✨</span>
                    </div>
                </div>

                <!-- Validation Feedback Toast -->
                <div id="feedback-toast" class="opacity-0 pointer-events-none transition-opacity text-center text-xs font-bold text-red-400 bg-red-950/80 border border-red-500/50 py-2 px-3 rounded-lg">
                    Em ơi, trả lời câu này trước đã nhé! 🔥
                </div>

                <!-- Navigation Controls -->
                <div class="flex items-center justify-end pt-2 border-t border-orange-500/20">
                    <button id="btn-next" type="button" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-red-600 via-orange-500 to-amber-500 text-neutral-950 font-black text-sm md:text-base uppercase tracking-wider fiery-btn-glow transition-all flex items-center justify-center gap-2 active:scale-95">
                        <span>TIẾP TỤC</span>
                        <svg class="w-5 h-5 text-neutral-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- STEP OUTRO: FINAL SCREEN -->
        <div id="step-outro" class="step-container w-full">
            <div id="outro-card" class="manga-panel p-6 sm:p-10 text-center flex flex-col items-center gap-6 relative overflow-hidden">
                
                <!-- Glowing Heart Emblem -->
                <div class="relative w-24 h-24 my-2 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-t from-red-600 via-pink-500 to-amber-400 blur-2xl opacity-60 animate-pulse"></div>
                    <div class="relative text-6xl animate-bounce">
                        💖
                    </div>
                </div>

                <!-- Outro Message -->
                <div class="space-y-4 max-w-lg mx-auto">
                    <div class="inline-block px-3 py-1 rounded-full bg-amber-950/70 border border-amber-500/40 text-xs font-bold text-amber-300 uppercase tracking-widest">
                        ✨ MISSION ACCOMPLISHED ✨
                    </div>

                    <p class="text-base sm:text-lg text-gray-200 leading-relaxed font-medium">
                        Cảm ơn em vì đã đi tới bước cuối này ựa a còn nghĩ là em sẽ out ngay câu đầu cơ :v câu hỏi và câu trả lời của e a sẽ nhắn tin với em sau nhé
                    </p>

                    <!-- Exact Required Center Text -->
                    <div class="pt-2">
                        <div class="text-center font-bold text-xl sm:text-2xl text-amber-300 fiery-text-glow leading-snug tracking-wide">
                            Dù sao a vẫn đang cố gắng để thuộc về em
                        </div>
                    </div>
                </div>

                <!-- Seal of Love -->
                <div class="mt-3 pt-5 border-t border-orange-500/30 w-full flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-neutral-400">
                    <span class="flex items-center gap-1.5 text-orange-400 font-semibold">
                        <span>🔥</span>
                        <span>Đã gửi câu trả lời về cho anh</span>
                    </span>
                    <span class="font-mono text-neutral-500">
                        Session: #<?= $current_user_id ?>
                    </span>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full text-center py-3 text-xs text-neutral-400 font-medium">
        <span>Made with ❤️ & fiery passion • </span>
        <a href="admin.php" class="text-neutral-500 hover:text-orange-400 transition-colors ml-1">Secret View</a>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/audio-engine.js"></script>
    <script src="assets/js/manga-fire.js"></script>
    <script src="assets/js/survey.js"></script>
</body>
</html>
