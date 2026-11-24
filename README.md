# 🔥 What Do You Want? (Rốt Cuộc Em Muốn Gì? 🤔)

Website trắc nghiệm tương tác phong cách **Manga / Anime Fire** cực chất dành riêng để hỏi tâm tư của crush, lưu dữ liệu trực tiếp vào MySQL database `questionforyou`.

---

## 🌟 Tính Năng Nổi Bật

1. **Hiệu Ứng Background Manga Fire Canvas**:
   - Sử dụng công nghệ HTML5 Canvas dựng ngọn lửa manga độc nhất vô nhị ở trung tâm/đáy màn hình (không lặp lại - *single majestic flame engine*).
   - Đồ họa đổ bóng phân lớp cel-shaded chuẩn phong cách Anime Shonen / Kimetsu no Yaiba / Demon Slayer: viền mực đen, đỏ rực, cam cháy, vàng kim và lõi trắng rực rỡ.
   - Các tàn lửa hình thoi (diamond sparks) bay lượn sinh động, tương tác phát tia lửa khi di chuột hoặc vuốt chạm trên điện thoại.
   - Hiệu ứng đường kẻ tốc độ Manga (Speedlines) và chấm hạt truyện tranh (Halftone Screentone).

2. **Giao Diện & Chuyển Động (Animations)**:
   - Màn hình mở đầu kịch tính: Tiêu đề chuyển động phát lửa **"RỐT CUỘC EM MUỐN GÌ? 🤔"**.
   - Bấm nút **"BẮT ĐẦU NÀO 🔥"** kích hoạt tiếng nổ lửa, bùng nổ ngọn lửa và chuyển cảnh anime speedline mượt mà.
   - Thanh nộ khí / Ki Gauge chuyển động theo từng câu hỏi (1/6, 2/6, ...).
   - Bộ khung câu hỏi và đáp án được thiết kế dạng Manga Panel cao cấp viền kim loại rực lửa, hòa hợp tuyệt đối với nền tối và ngọn lửa.

3. **Âm Thanh Anime Sống Động (Web Audio API)**:
   - Tự động tổng hợp âm thanh bằng Web Audio API (không cần tải thêm file mp3 ngoài, không lo lỗi mạng):
     - Tiếng nộ lửa bùng cháy (Ignite).
     - Tiếng bấm cơ học anime (Click).
     - Tiếng lướt chuyển bài (Whoosh).
     - Tiếng hợp âm chúc mừng khi hoàn thành (Victory Fanfare).
   - Nút bật/tắt âm thanh (Mute/Unmute) tiện dụng ở góc trên.

4. **Kịch Bản Câu Hỏi & Logic Bắt Buộc**:
   - **Bắt buộc phải trả lời** mới có thể sang câu tiếp theo. Nếu chưa chọn đáp án hoặc để trống, hệ thống sẽ rung lắc nhẹ khung tranh và hiện thông báo nhắc nhở dễ thương.
   - **Câu 1**: *"Em hay nhỉ? Tôi thích em từ online cho tới offline. Tại sao tôi thích em á? Ban đầu là do e vui tính còn h thi vì em làm tôi bực mình, cáu bẩn, khó chịu, cục súc nhưng vậy tôi lại nghĩ về em nhiều hơn :v Thế em muốn tôi tán em kiểu gì đây dù là gì thì tôi vẫn muốn có em bằng được!?"*
     - A. Good boy (real)
     - B. Bad boy (50/50)
     - C. ít nói (idk maybe it's quite hard for me in present)
     - D. ngoài đời y chang lúc nhắn tin :v (khó)
     - E. answer else... *(khi chọn sẽ mở rộng ô nhập liệu để crush tự viết)*
   - **Câu 2**: *"Em thuộc tuýp người nói có là không, không là có à?"*
     - A. Yes | B. No | C. answer else...
   - **Câu 3**: *"Em thích người chủ động hay hơi lạnh lùng một chút?"*
     - A. Chủ động | B. Lạnh lùng boyy | C. answer else...
   - **Câu 4**: *"Điều gì khiến em cảm thấy được trân trọng?"* [Nhập văn bản]
   - **Câu 5**: *"Có điều gì em muốn người khác hiểu về mình nhưng ít khi nói ra không? (last question)"* [Nhập văn bản]
   - **Câu 6 (Bonus)**: *"Ô quên mất tiêu!!! Em có muốn hỏi tôi cái gì không :3 ?"* [Nhập văn bản]
   - **Màn hình kết thúc**:
     - Lời nhắn: *"Cảm ơn em vì đã đi tới bước cuối này ựa a còn nghĩ là em sẽ out ngay câu đầu cơ :v câu hỏi và câu trả lời của e a sẽ nhắn tin với em sau nhé"*
     - `<text center>Dù sao a vẫn đang cố gắng để thuộc về em</text center>`
     - Mưa tim và tàn lửa lãng mạn.

5. **Cơ Sở Dữ Liệu MySQL `questionforyou`**:
   - Tự động khởi tạo và seed dữ liệu 6 câu hỏi vào bảng `question`.
   - Lưu trữ tức thì từng câu trả lời vào bảng `answer` theo session của crush (dù em ấy thoát giữa chừng cũng đã lưu lại các câu đã làm).
   - Hỗ trợ xem toàn bộ câu trả lời bí mật tại trang `admin.php` với tính năng copy nhanh nội dung để nhắn tin lại cho em ấy!

---

## 🚀 Hướng Dẫn Sử Dụng

### 1. Truy Cập Qua Laragon
- Bật Laragon (Start All).
- Truy cập vào:
  - **Trang trắc nghiệm dành cho Crush**: `http://localhost/whatDoUThink` hoặc `http://whatdouthink.test`
  - **Trang quản lý xem câu trả lời bí mật**: `http://localhost/whatDoUThink/admin.php`

### 2. Chạy Bằng Lệnh PHP Trực Tiếp (Tùy chọn)
Nếu không dùng Apache/Nginx của Laragon, bạn có thể chạy:
```bash
cd c:\laragon\www\whatDoUThink
php -S localhost:8000
```
Sau đó mở trình duyệt tại: `http://localhost:8000`
