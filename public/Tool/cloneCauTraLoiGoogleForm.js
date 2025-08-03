// Truy cập https://script.google.com/, new project, dán mã này vào, lưu lại và đặt tên cho project.
// Bắt buộc lưu file sau đó mới có thể chạy được.
function submitResponses() {
  const form = FormApp.openByUrl("https://docs.google.com/forms/d/1NZgSnO4NI4XZzr0JtuaN1RRJiYQaIZVZKnopIDUNbvM/edit"); // Link Google Form thay đổi và có hậu tố /edit

  const ho = ["Nguyễn", "Trần", "Lê", "Phạm", "Hoàng", "Phan", "Vũ", "Đặng", "Bùi", "Đỗ"];
  const tenLot = ["Văn", "Thị", "Hữu", "Ngọc", "Đức", "Thanh", "Minh", "Xuân", "Gia", "Quốc"];
  const ten = ["Anh", "Bình", "Chi", "Dũng", "Hương", "Khánh", "Lan", "Minh", "Nam", "Phúc", "Quang", "Trang", "Tuấn", "Vy"];

  const genderOptions = ["Nam", "Nữ"];
  const ageGroupOptions = ["Dưới 18", "18–25", "26–35"];
  const ecoProductOptions = ["Có", "Thỉnh thoảng", "Hiếm khi", "Không bao giờ"];
  const ecoFactors = ["Giá cả", "Chất lượng", "Bao bì tái chế", "Thương hiệu", "Khuyến mãi"];
  const productInterest = ["Hộp giấy", "Đồ gia dụng thân thiện với môi trường", "Thời trang bền vững", "Mỹ phẩm thiên nhiên"];
  const productPriority = ["Hộp giấy", "Đồ gia dụng", "Thời trang", "Mỹ phẩm"];
  const devices = ["Máy tính (PC/Laptop)", "Điện thoại", "Máy tính bảng"];
  const buyingFactors = ["Giao diện dễ nhìn, thân thiện", "Tốc độ tải trang nhanh", "Dễ tìm kiếm sản phẩm", "Có đánh giá và bình luận sản phẩm", "Hình ảnh sản phẩm rõ ràng", "Quy trình đặt hàng đơn giản", "Bảo mật thông tin cá nhân", "Hỗ trợ khách hàng nhanh chóng"];
  const filters = ["Giá", "Màu sắc", "Kích cỡ", "Loại sản phẩm"];
  const paymentMethods = ["COD (Thanh toán khi nhận hàng)", "Chuyển khoản ngân hàng", "Ví điện tử (Momo, ZaloPay, VNPay,...)"];
  const exitReasons = ["Website chậm, lỗi", "Giao diện rối mắt, khó dùng", "Không tìm được sản phẩm cần", "Không tin tưởng về sản phẩm/thông tin", "Thiếu đánh giá của người dùng", "Quy trình mua hàng rườm rà", "Giá quá cao"];
  const promoNotifications = ["Email", "Tin nhắn"];

  const items = form.getItems();
  const usedNames = new Set();

  while (usedNames.size < 80) {
    const fullName =
      ho[Math.floor(Math.random() * ho.length)] + " " +
      tenLot[Math.floor(Math.random() * tenLot.length)] + " " +
      ten[Math.floor(Math.random() * ten.length)];

    if (!usedNames.has(fullName)) {
      usedNames.add(fullName);
      const res = form.createResponse();

      res.withItemResponse(items[0].asTextItem().createResponse(fullName)); // Câu 1
      res.withItemResponse(items[1].asMultipleChoiceItem().createResponse(genderOptions[Math.floor(Math.random() * genderOptions.length)])); // Câu 2
      res.withItemResponse(items[2].asMultipleChoiceItem().createResponse(ageGroupOptions[Math.floor(Math.random() * ageGroupOptions.length)])); // Câu 3

      // Sửa lỗi tại đây: Chuyển đổi tên có dấu thành không dấu trước khi tạo email
      const email = removeVietnameseAccents(fullName).replace(/\s+/g, "").toLowerCase() + "@gmail.com";
      res.withItemResponse(items[3].asTextItem().createResponse(email)); // Câu 4

      res.withItemResponse(items[4].asMultipleChoiceItem().createResponse(ecoProductOptions[Math.floor(Math.random() * ecoProductOptions.length)])); // Câu 5
      res.withItemResponse(items[5].asCheckboxItem().createResponse(ecoFactors.filter(() => Math.random() < 0.5))); // Câu 6
      res.withItemResponse(items[6].asCheckboxItem().createResponse(productInterest.filter(() => Math.random() < 0.5))); // Câu 7
      res.withItemResponse(items[7].asCheckboxItem().createResponse(productPriority.filter(() => Math.random() < 0.5))); // Câu 8
      res.withItemResponse(items[8].asCheckboxItem().createResponse(devices.filter(() => Math.random() < 0.5))); // Câu 9
      res.withItemResponse(items[9].asCheckboxItem().createResponse(shuffle(buyingFactors).slice(0, 3))); // Câu 10
      res.withItemResponse(items[10].asCheckboxItem().createResponse(filters.filter(() => Math.random() < 0.5))); // Câu 11
      res.withItemResponse(items[11].asCheckboxItem().createResponse(paymentMethods.filter(() => Math.random() < 0.5))); // Câu 12
      res.withItemResponse(items[12].asCheckboxItem().createResponse(exitReasons.filter(() => Math.random() < 0.5))); // Câu 13

      // Chọn 1 lựa chọn duy nhất cho câu 14
      res.withItemResponse(items[13].asMultipleChoiceItem().createResponse(
        promoNotifications[Math.floor(Math.random() * promoNotifications.length)]
      )); // Câu 14

      res.submit();
    }
  }
}

// Hàm hỗ trợ xáo trộn mảng
function shuffle(array) {
  const arr = array.slice();
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [arr[i], arr[j]] = [arr[j], arr[i]];
  }
  return arr;
}

// Hàm mới để loại bỏ dấu tiếng Việt
function removeVietnameseAccents(str) {
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
  str = str.replace(/đ/g, "d");
  str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
  str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
  str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
  str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
  str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
  str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
  str = str.replace(/Đ/g, "D");
  return str;
}