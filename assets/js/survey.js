/**
 * Survey Controller & Interaction Handler
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Manga Fire Canvas Engine
    const fireEngine = new MangaFireEngine('fireCanvas');

    // Audio Engine shortcut
    const audio = window.mangaAudio;

    // Elements
    const introStep = document.getElementById('step-intro');
    const questionStep = document.getElementById('step-question');
    const outroStep = document.getElementById('step-outro');
    
    const startBtn = document.getElementById('btn-start');
    const nextBtn = document.getElementById('btn-next');
    const soundToggle = document.getElementById('sound-toggle');
    const soundIcon = document.getElementById('sound-icon');

    const progressContainer = document.getElementById('progress-container');
    const kiFill = document.getElementById('ki-fill');
    const stageBadge = document.getElementById('stage-badge');
    const progressText = document.getElementById('progress-text');

    const questionCard = document.getElementById('question-card');
    const questionBadge = document.getElementById('question-badge');
    const questionTitle = document.getElementById('question-title');
    const questionText = document.getElementById('question-text');
    const optionsContainer = document.getElementById('options-container');
    const customInputContainer = document.getElementById('custom-input-container');
    const customInput = document.getElementById('custom-input');
    const textAnswerContainer = document.getElementById('text-answer-container');
    const textAnswer = document.getElementById('text-answer');
    const feedbackToast = document.getElementById('feedback-toast');

    // Questions definition
    const questions = [
        {
            id: 1,
            badge: "STAGE 01 • THE CONFESSION",
            title: "Em muốn tôi tán em kiểu gì?",
            content: "Em hay nhỉ? Tôi thích em từ online cho tới offline. Tại sao tôi thích em á? Ban đầu là do e vui tính còn h thi vì em làm tôi bực mình, cáu bẩn, khó chịu, cục súc nhưng vậy tôi lại nghĩ về em nhiều hơn :v Thế em muốn tôi tán em kiểu gì đây dù là gì thì tôi vẫn muốn có em bằng được!?",
            type: "choice",
            options: [
                { key: "A", label: "Good boy (real)" },
                { key: "B", label: "Bad boy (50/50)" },
                { key: "C", label: "ít nói (idk maybe it's quite hard for me in present)" },
                { key: "D", label: "ngoài đời y chang lúc nhắn tin :v (khó)" },
                { key: "E", label: "answer else...", isCustom: true }
            ]
        },
        {
            id: 2,
            badge: "STAGE 02 • THE PARADOX",
            title: "Nói có là không, không là có?",
            content: "Em thuộc tuýp người nói có là không, không là có à?",
            type: "choice",
            options: [
                { key: "A", label: "Yes" },
                { key: "B", label: "No" },
                { key: "C", label: "answer else...", isCustom: true }
            ]
        },
        {
            id: 3,
            badge: "STAGE 03 • THE VIBE",
            title: "Chủ động hay lạnh lùng?",
            content: "Em thích người chủ động hay hơi lạnh lùng một chút?",
            type: "choice",
            options: [
                { key: "A", label: "Chủ động" },
                { key: "B", label: "Lạnh lùng boyy" },
                { key: "C", label: "answer else...", isCustom: true }
            ]
        },
        {
            id: 4,
            badge: "STAGE 04 • DEEP TALK",
            title: "Cảm giác được trân trọng",
            content: "Điều gì khiến em cảm thấy được trân trọng?",
            type: "text",
            placeholder: "Chia sẻ với anh điều khiến em cảm thấy được yêu thương và tôn trọng nhất nhé..."
        },
        {
            id: 5,
            badge: "STAGE 05 • HIDDEN CORNERS",
            title: "Điều ít khi nói ra",
            content: "Có điều gì em muốn người khác hiểu về mình nhưng ít khi nói ra không? (last question)",
            type: "text",
            placeholder: "Những tâm tư hay góc khuất tính cách mà em thường giữ cho riêng mình..."
        },
        {
            id: 6,
            badge: "BONUS STAGE • YOUR TURN",
            title: "Em có muốn hỏi tôi cái gì không?",
            content: "Ô quên mất tiêu!!! Em có muốn hỏi tôi cái gì không :3 ?",
            type: "text",
            placeholder: "Bất kể câu hỏi nào em tò mò về anh, cứ hỏi thoải mái nhé :3..."
        }
    ];

    let currentStepIndex = 0; // 0 to 5
    let selectedOption = null;
    let customTextValue = "";
    let isSubmitting = false;

    // Sound toggle state
    function updateSoundIcon() {
        if (audio.muted) {
            soundIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
            `;
            soundToggle.classList.add('opacity-60');
        } else {
            soundIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            `;
            soundToggle.classList.remove('opacity-60');
        }
    }
    updateSoundIcon();

    soundToggle.addEventListener('click', () => {
        audio.toggleMute();
        updateSoundIcon();
        if (!audio.muted) audio.playClick();
    });

    // Start Survey from Intro
    startBtn.addEventListener('click', () => {
        audio.playIgnite();
        fireEngine.burst();

        // Speedlines animation flash
        const speedlines = document.getElementById('speedlines');
        if (speedlines) {
            speedlines.classList.add('active');
            setTimeout(() => speedlines.classList.remove('active'), 600);
        }

        // Animate out intro, animate in questions
        introStep.classList.remove('active');
        setTimeout(() => {
            progressContainer.classList.remove('hidden');
            progressContainer.classList.add('flex');
            questionStep.classList.add('active');
            renderQuestion(0);
        }, 300);
    });

    // Show toast message for validation
    function showToast(message) {
        feedbackToast.textContent = message;
        feedbackToast.classList.remove('opacity-0', 'pointer-events-none');
        feedbackToast.classList.add('opacity-100');

        questionCard.classList.add('shake');
        setTimeout(() => questionCard.classList.remove('shake'), 450);

        setTimeout(() => {
            feedbackToast.classList.remove('opacity-100');
            feedbackToast.classList.add('opacity-0', 'pointer-events-none');
        }, 2500);
    }

    // Render Question by Index
    function renderQuestion(index) {
        currentStepIndex = index;
        const q = questions[index];
        selectedOption = null;
        customTextValue = "";

        // Update progress bar
        const progressPercent = ((index + 1) / questions.length) * 100;
        kiFill.style.width = `${progressPercent}%`;
        stageBadge.textContent = q.badge;
        progressText.textContent = `${index + 1}/${questions.length}`;

        // Update question contents
        questionBadge.textContent = `CÂU HỎI 0${index + 1}`;
        questionTitle.textContent = q.title;
        questionText.textContent = q.content;

        // Reset containers
        optionsContainer.innerHTML = '';
        customInputContainer.classList.add('hidden');
        customInput.value = '';
        textAnswerContainer.classList.add('hidden');
        textAnswer.value = '';

        if (q.type === 'choice') {
            optionsContainer.classList.remove('hidden');
            
            q.options.forEach((opt) => {
                const optEl = document.createElement('div');
                optEl.className = 'option-card flex items-center p-3.5 md:p-4 rounded-xl gap-3.5';
                optEl.setAttribute('data-key', opt.key);

                optEl.innerHTML = `
                    <div class="option-letter w-8 h-8 md:w-9 md:h-9 rounded-lg flex items-center justify-center text-sm md:text-base font-bold shrink-0">
                        ${opt.key}
                    </div>
                    <div class="option-text flex-1 text-sm md:text-base font-medium text-gray-200">
                        ${opt.label}
                    </div>
                    <div class="check-icon opacity-0 transition-opacity text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                `;

                optEl.addEventListener('click', () => {
                    audio.playClick();
                    document.querySelectorAll('.option-card').forEach(el => {
                        el.classList.remove('selected');
                        el.querySelector('.check-icon').classList.add('opacity-0');
                    });

                    optEl.classList.add('selected');
                    optEl.querySelector('.check-icon').classList.remove('opacity-0');
                    selectedOption = opt;

                    // If custom option, show input field
                    if (opt.isCustom) {
                        customInputContainer.classList.remove('hidden');
                        customInput.focus();
                    } else {
                        customInputContainer.classList.add('hidden');
                    }
                });

                optionsContainer.appendChild(optEl);
            });
        } else if (q.type === 'text') {
            optionsContainer.classList.add('hidden');
            textAnswerContainer.classList.remove('hidden');
            textAnswer.placeholder = q.placeholder || "Nhập câu trả lời của em...";
            setTimeout(() => textAnswer.focus(), 150);
        }

        // Adjust Next button label for final question
        if (index === questions.length - 1) {
            nextBtn.innerHTML = `
                <span>GỬI ĐẾN ANH ẤY ❤️</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            `;
        } else {
            nextBtn.innerHTML = `
                <span>TIẾP TỤC</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            `;
        }
    }

    // Next Question Button Click Handler
    nextBtn.addEventListener('click', async () => {
        if (isSubmitting) return;

        const q = questions[currentStepIndex];
        let answerContent = "";

        // Validate answer
        if (q.type === 'choice') {
            if (!selectedOption) {
                showToast("Em ơi, chọn một đáp án trước đã nhé! 🔥");
                return;
            }

            if (selectedOption.isCustom) {
                const val = customInput.value.trim();
                if (!val) {
                    showToast("Em chọn câu trả lời khác thì viết vào ô giúp anh nhé! ✨");
                    customInput.focus();
                    return;
                }
                answerContent = `${selectedOption.key}. ${selectedOption.label} -> ${val}`;
            } else {
                answerContent = `${selectedOption.key}. ${selectedOption.label}`;
            }
        } else if (q.type === 'text') {
            const val = textAnswer.value.trim();
            if (!val) {
                showToast("Em đừng để trống câu này nhé, viết đôi dòng tâm sự đi nào! 💬");
                textAnswer.focus();
                return;
            }
            answerContent = val;
        }

        // Play feedback sounds & fire burst
        audio.playWhoosh();
        fireEngine.burst();

        // Submit answer to server via API
        isSubmitting = true;
        nextBtn.classList.add('opacity-70', 'cursor-not-allowed');

        try {
            const response = await fetch('api/submit.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    question_id: q.id,
                    content: answerContent
                })
            });
            const result = await response.json();
            if (!result.success) {
                console.warn("Lỗi lưu dữ liệu:", result.error);
            }
        } catch (err) {
            console.error("Lỗi gửi câu trả lời:", err);
        } finally {
            isSubmitting = false;
            nextBtn.classList.remove('opacity-70', 'cursor-not-allowed');
        }

        // Transition to next question or outro
        if (currentStepIndex < questions.length - 1) {
            // Smooth card flip transition
            questionCard.style.opacity = '0';
            questionCard.style.transform = 'translateY(-15px) scale(0.97)';

            setTimeout(() => {
                renderQuestion(currentStepIndex + 1);
                questionCard.style.opacity = '1';
                questionCard.style.transform = 'translateY(0) scale(1)';
            }, 250);
        } else {
            // Final screen reached!
            finishSurvey();
        }
    });

    // Finish Survey & Show Outro
    function finishSurvey() {
        audio.playVictory();
        fireEngine.burst();

        questionStep.classList.remove('active');
        progressContainer.classList.add('hidden');
        progressContainer.classList.remove('flex');

        setTimeout(() => {
            outroStep.classList.add('active');
            createHeartSparks();
        }, 300);
    }

    // Sparkle Hearts Celebration on Final Screen
    function createHeartSparks() {
        const outroCard = document.getElementById('outro-card');
        if (!outroCard) return;

        const hearts = ['❤️', '🔥', '✨', '💖', '🥰'];
        for (let i = 0; i < 24; i++) {
            const heart = document.createElement('div');
            heart.className = 'absolute pointer-events-none select-none text-xl';
            heart.textContent = hearts[Math.floor(Math.random() * hearts.length)];
            heart.style.left = `${Math.random() * 90 + 5}%`;
            heart.style.top = `${Math.random() * 80 + 10}%`;
            heart.style.opacity = '0';
            heart.style.transform = `scale(0) translateY(0)`;
            heart.style.transition = `all ${Math.random() * 1.5 + 1.2}s cubic-bezier(0.25, 1, 0.5, 1)`;

            outroCard.appendChild(heart);

            setTimeout(() => {
                heart.style.opacity = '1';
                heart.style.transform = `scale(${Math.random() * 0.8 + 0.8}) translateY(-${Math.random() * 80 + 30}px)`;
            }, i * 70);

            setTimeout(() => {
                heart.style.opacity = '0';
                setTimeout(() => heart.remove(), 1000);
            }, 2500 + i * 50);
        }
    }
});
