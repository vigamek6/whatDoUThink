-- Database: questionforyou
CREATE DATABASE IF NOT EXISTS `questionforyou` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `questionforyou`;

-- Table structure for table `question`
CREATE TABLE IF NOT EXISTS `question` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `user_id` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `answer`
CREATE TABLE IF NOT EXISTS `answer` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`question_id`) REFERENCES `question`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial data for table `question`
INSERT INTO `question` (`id`, `title`, `content`, `user_id`) VALUES
(1, 'Câu 1: Em muốn tôi tán em kiểu gì?', 'Em hay nhỉ? Tôi thích em từ online cho tới offline. Tại sao tôi thích em á? Ban đầu là do e vui tính còn h thi vì em làm tôi bực mình, cáu bẩn, khó chịu, cục súc nhưng vậy tôi lại nghĩ về em nhiều hơn :v Thế em muốn tôi tán em kiểu gì đây dù là gì thì tôi vẫn muốn có em bằng được!?', 1),
(2, 'Câu 2: Nói có là không, không là có?', 'Em thuộc tuýp người nói có là không, không là có à?', 1),
(3, 'Câu 3: Chủ động hay lạnh lùng?', 'Em thích người chủ động hay hơi lạnh lùng một chút?', 1),
(4, 'Câu 4: Cảm giác được trân trọng', 'Điều gì khiến em cảm thấy được trân trọng?', 1),
(5, 'Câu 5: Điều ít khi nói ra', 'Có điều gì em muốn người khác hiểu về mình nhưng ít khi nói ra không? (last question)', 1),
(6, 'Bonus: Em có muốn hỏi tôi cái gì không?', 'Ô quên mất tiêu!!! Em có muốn hỏi tôi cái gì không :3 ?', 1)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `content` = VALUES(`content`);
