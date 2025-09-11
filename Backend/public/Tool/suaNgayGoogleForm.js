// Dự án Apps Script này thuộc về Google Sheet của bạn (hoặc là một standalone script).
// Nó chịu trách nhiệm sửa đổi cột "Dấu thời gian" trong Sheet.

// Thay https://docs.google.com/forms/d/1NZgSnO4NI4XZzr0JtuaN1RRJiYQaIZVZKnopIDUNbvM/edit bằng url của Google Form của bạn. (có hậu tố /edit).
// Thay https://docs.google.com/spreadsheets/d/1uUH5BFqdF-6jsa_HKVDNOf8a_V-9uptsaYjBaqoBmw4/edit bằng url của Google Sheet của bạn. (có hậu tố /edit).

// PHẢI CHẠY HÀM updateTodayTimestamps MỚI SỬA ĐƯỢC CÁC THỜI GIAN CỦA CÁC HÀNG ĐÃ CÓ.

/**
 * Hàm này được kích hoạt tự động mỗi khi một phản hồi mới được gửi từ Google Form.
 * Nó sẽ ghi đè cột "Dấu thời gian" (Timestamp) của hàng MỚI bằng một giá trị thời gian ngẫu nhiên.
 *
 * @param {Object} e Đối tượng sự kiện chứa thông tin về phản hồi được gửi.
 */
function modifyTimestampOnFormSubmit(e) {
  // --- CẤU HÌNH CẦN THIẾT ---
  // Thay thế bằng URL Google Sheet của bạn (chỉ cần phần đến /edit)
  const sheetUrl = "https://docs.google.com/spreadsheets/d/1uUH5BFqdF-6jsa_HKVDNOf8a_V-9uptsaYjBaqoBmw4/edit"; 
  // --- KẾT THÚC CẤU HÌNH ---

  let sheet;
  let row;

  if (e && e.range) {
    // Nếu script được kích hoạt bởi một trigger onFormSubmit, đối tượng 'e' sẽ có thông tin
    sheet = e.range.getSheet();
    row = e.range.getRow();
  } else {
    // Trường hợp chạy thủ công (không khuyến khích cho hàm này)
    // Hoặc nếu trigger không truyền đối tượng e đầy đủ
    try {
      const spreadsheet = SpreadsheetApp.openByUrl(sheetUrl);
      sheet = spreadsheet.getSheets()[0]; // Lấy sheet đầu tiên (thường là sheet phản hồi Form)
      row = sheet.getLastRow(); // Lấy hàng cuối cùng có dữ liệu (có thể không phải hàng mới nhất nếu có lỗi)
      Logger.log("modifyTimestampOnFormSubmit chạy thủ công: Đã xác định sheet và hàng cuối cùng.");
    } catch (error) {
      Logger.log("Lỗi modifyTimestampOnFormSubmit: " + error.message);
      return;
    }
  }

  // Cột "Dấu thời gian" (Timestamp) mặc định của Google Form luôn là cột đầu tiên (cột A), tức là chỉ số 1.
  const timestampColumnIndex = 1;

  // Tạo một ngày giờ ngẫu nhiên trong khoảng thời gian nhất định (ví dụ: 30 ngày gần đây)
  const now = new Date();
  const randomDayOffset = Math.floor(Math.random() * 30); // Giả lập trong 30 ngày qua
  const randomHours = Math.floor(Math.random() * 24);    // Giờ ngẫu nhiên (0-23)
  const randomMinutes = Math.floor(Math.random() * 60);  // Phút ngẫu nhiên (0-59)
  const randomSeconds = Math.floor(Math.random() * 60);  // Giây ngẫu nhiên (0-59)
  
  // Đặt ngày bắt đầu cho phạm vi ngẫu nhiên (ví dụ: 30 ngày trước so với hiện tại)
  const startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 30);

  // Tạo một timestamp ngẫu nhiên trong phạm vi mong muốn
  const randomTimestamp = new Date(
    startDate.getFullYear(),
    startDate.getMonth(),
    startDate.getDate() + randomDayOffset, // Cộng thêm số ngày ngẫu nhiên vào ngày bắt đầu
    randomHours,
    randomMinutes,
    randomSeconds
  );

  // Ghi đè giá trị trong cột "Dấu thời gian" (Timestamp) của hàng mới
  sheet.getRange(row, timestampColumnIndex).setValue(randomTimestamp);
  
  Logger.log(`Đã cập nhật timestamp cho hàng ${row} thành: ${randomTimestamp}`);
}


/**
 * HÀM MỚI: Sửa đổi timestamp của các hàng hiện có chỉ khi chúng có ngày là HÔM NAY.
 * Bạn sẽ chạy hàm này THỦ CÔNG khi muốn cập nhật lại các hàng đã có.
 */
function updateTodayTimestamps() {
  // --- CẤU HÌNH CẦN THIẾT ---
  // Thay thế bằng URL Google Sheet của bạn (chỉ cần phần đến /edit)
  const sheetUrl = "https://docs.google.com/spreadsheets/d/1uUH5BFqdF-6jsa_HKVDNOf8a_V-9uptsaYjBaqoBmw4/edit"; 
  // --- KẾT THÚC CẤU HÌNH ---

  try {
    const spreadsheet = SpreadsheetApp.openByUrl(sheetUrl);
    const sheet = spreadsheet.getSheets()[0]; // Lấy sheet đầu tiên (thường là sheet phản hồi Form)

    // Giả định hàng đầu tiên là tiêu đề, bắt đầu từ hàng thứ 2
    const startRow = 2; 
    const lastRow = sheet.getLastRow();
    const timestampColumnIndex = 1; // Cột A

    if (lastRow < startRow) {
      Logger.log("Không có dữ liệu để cập nhật (hoặc chỉ có hàng tiêu đề).");
      return;
    }

    const today = new Date();
    // Đặt giờ, phút, giây, mili giây về 0 để so sánh chỉ ngày
    today.setHours(0, 0, 0, 0); 
    
    let updatedCount = 0;

    // Lặp qua từng hàng từ hàng bắt đầu đến hàng cuối cùng
    for (let row = startRow; row <= lastRow; row++) {
      const timestampCell = sheet.getRange(row, timestampColumnIndex);
      const timestampValue = timestampCell.getValue();

      // Đảm bảo giá trị là Date object và có cùng ngày với hôm nay
      if (timestampValue instanceof Date) {
        const timestampDate = new Date(timestampValue);
        // Đặt giờ, phút, giây, mili giây về 0 để so sánh chỉ ngày
        timestampDate.setHours(0, 0, 0, 0); 

        // Nếu ngày của timestamp bằng ngày hôm nay
        if (timestampDate.getTime() === today.getTime()) {
          // Tạo một ngày giờ ngẫu nhiên mới trong phạm vi 30 ngày qua
          const now = new Date();
          const randomDayOffset = Math.floor(Math.random() * 30);
          const randomHours = Math.floor(Math.random() * 24);
          const randomMinutes = Math.floor(Math.random() * 60);
          const randomSeconds = Math.floor(Math.random() * 60);
          
          const startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 30); // 30 ngày trước

          const newRandomTimestamp = new Date(
            startDate.getFullYear(),
            startDate.getMonth(),
            startDate.getDate() + randomDayOffset,
            randomHours,
            randomMinutes,
            randomSeconds
          );

          timestampCell.setValue(newRandomTimestamp);
          updatedCount++;
          // Có thể thêm một độ trễ nhỏ nếu có rất nhiều hàng và gặp lỗi quá tải
          // Utilities.sleep(50); 
        }
      }
    }
    Logger.log(`Đã cập nhật timestamp cho ${updatedCount} hàng có ngày hôm nay.`);

  } catch (error) {
    Logger.log("Lỗi khi cập nhật timestamps có ngày hôm nay: " + error.message);
  }
}

/**
 * Hàm này dùng để tạo trigger cho hàm `modifyTimestampOnFormSubmit`.
 * Bạn chỉ cần chạy hàm này MỘT LẦN để thiết lập trigger.
 * Sau đó, mỗi khi có phản hồi mới từ Form, `modifyTimestampOnFormSubmit` sẽ tự động chạy.
 */
function createFormSubmitTriggerForTimestampOverride() {
  // --- CẤU HÌNH CẦN THIẾT ---
  // Thay thế bằng URL Google Form của bạn
  const formUrl = "https://docs.google.com/forms/d/1NZgSnO4NI4XZzr0JtuaN1RRJiYQaIZVZKnopIDUNbvM/edit";
  // Thay thế bằng URL Google Sheet của bạn (chỉ cần phần đến /edit)
  const sheetUrl = "https://docs.google.com/spreadsheets/d/1uUH5BFqdF-6jsa_HKVDNOf8a_V-9uptsaYjBaqoBmw4/edit";
  // --- KẾT THÚC CẤU HÌNH ---

  const form = FormApp.openByUrl(formUrl);
  const spreadsheet = SpreadsheetApp.openByUrl(sheetUrl); // Cần mở spreadsheet để liên kết trigger nếu là standalone

  // Xóa các trigger cũ nếu có để tránh trùng lặp
  const triggers = ScriptApp.getProjectTriggers();
  for (let i = 0; i < triggers.length; i++) {
    if (triggers[i].getHandlerFunction() === 'modifyTimestampOnFormSubmit') {
      ScriptApp.deleteTrigger(triggers[i]);
      Logger.log('Đã xóa trigger cũ cho modifyTimestampOnFormSubmit.');
    }
  }

  // Tạo trigger mới
  ScriptApp.newTrigger('modifyTimestampOnFormSubmit')
    .forForm(form)
    .onFormSubmit()
    .create();
  
  Logger.log('Trigger onFormSubmit để ghi đè timestamp đã được tạo thành công.');
  Logger.log('Đảm bảo rằng Google Sheet liên kết với Form này là: ' + sheetUrl);
}