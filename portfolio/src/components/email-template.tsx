import * as React from "react";

interface EmailTemplateProps {
  fullName: string;
  email: string;
  message: string;
}

// Cập nhật màu sắc hiện đại
const PRIMARY_COLOR = "#6B46C1"; // Màu Tím đậm/Xanh Tím
const ACCENT_COLOR = "#10B981"; // Màu Xanh Mint/Ngọc
const TEXT_COLOR = "#1F2937"; // Màu chữ đậm

const main = {
  backgroundColor: "#f7f7f7", // Nền nhạt
  padding: "20px 0",
};

const container = {
  maxWidth: "600px",
  margin: "0 auto",
  backgroundColor: "#ffffff",
  borderRadius: "12px", // Bo góc lớn hơn
  boxShadow: "0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)",
  fontFamily: "Poppins, Arial, sans-serif", // Font chữ hiện đại
  borderCollapse: "collapse" as const,
  overflow: "hidden",
};

const logoHeader = {
    backgroundColor: PRIMARY_COLOR,
    padding: "20px 30px",
    textAlign: "center" as const,
    borderTopLeftRadius: "12px",
    borderTopRightRadius: "12px",
};

const content = {
  padding: "30px",
  color: TEXT_COLOR,
  lineHeight: "1.7",
};

const detailsBox = {
  marginTop: "25px",
  padding: "18px",
  border: `1px solid ${PRIMARY_COLOR}20`, // Viền mờ
  backgroundColor: `${PRIMARY_COLOR}05`, // Nền siêu nhạt
  borderRadius: "8px",
};

const messageBox = {
  padding: "18px",
  backgroundColor: "#f3f4f6",
  borderRadius: "8px",
  marginTop: "20px",
  border: "1px solid #e5e7eb",
};

const footer = {
  padding: "20px 30px",
  textAlign: "center" as const,
  fontSize: "12px",
  color: "#6b7280",
  backgroundColor: "#f7f7f7",
  borderBottomLeftRadius: "12px",
  borderBottomRightRadius: "12px",
  borderTop: "1px solid #e5e7eb",
};

const button = {
  display: "inline-block",
  padding: "12px 24px",
  marginTop: "30px",
  backgroundColor: ACCENT_COLOR, // Màu Xanh Mint
  color: "#ffffff",
  textDecoration: "none",
  borderRadius: "6px",
  fontWeight: "600" as const,
  fontSize: "16px",
  textTransform: "uppercase" as const,
  letterSpacing: "0.5px",
};

export const EmailTemplate: React.FC<Readonly<EmailTemplateProps>> = ({
  fullName,
  email,
  message,
}) => (
  <div style={main}>
    <table width="100%" cellPadding="0" cellSpacing="0" style={container}>
      {/* LOGO HEADER */}
      <tr>
        <td style={logoHeader}>
          <img
            src="https://raw.githubusercontent.com/phungngocdungx/phungngocdung/refs/heads/main/portfolio/public/assets/seo/logo.png" 
            alt=""
            width="150"
            height="auto"
            style={{ display: "block", margin: "0 auto" }}
          />
        </td>
      </tr>

      {/* CONTENT BODY */}
      <tr>
        <td style={content}>
          <h2 style={{ margin: "0 0 10px 0", color: PRIMARY_COLOR, fontSize: "22px" }}>
            🚨 Yêu Cầu Liên Hệ Mới Cấp Thiết
          </h2>
          <p>Xin chào,</p>
          <p>
            Có một người dùng mới đã gửi tin nhắn liên hệ từ trang{" "}
            <strong>Portfolio/Website</strong>. Vui lòng xem xét và phản hồi ngay:
          </p>

          {/* THÔNG TIN NGƯỜI LIÊN HỆ */}
          <div style={detailsBox}>
            <h3 style={{ margin: "0 0 10px 0", color: PRIMARY_COLOR, fontSize: "18px" }}>
              Chi tiết Người dùng
            </h3>
            <p style={{ margin: "8px 0" }}>
              <strong>Họ tên:</strong> {fullName}
            </p>
            <p style={{ margin: "8px 0" }}>
              <strong>Email:</strong>{" "}
              <a
                href={`mailto:${email}`}
                style={{ color: ACCENT_COLOR, textDecoration: "none", fontWeight: "bold" }}
              >
                {email}
              </a>
            </p>
          </div>

          {/* NỘI DUNG TIN NHẮN */}
          <h3 style={{ marginTop: "30px", color: TEXT_COLOR, fontSize: "18px" }}>
            Nội dung Tin nhắn
          </h3>
          <div style={messageBox}>
            <p style={{ margin: 0, fontStyle: "italic", color: TEXT_COLOR }}>{message}</p>
          </div>

          {/* NÚT HÀNH ĐỘNG */}
          <div style={{ textAlign: "center", marginTop: "30px" }}>
            <a
              href={`mailto:${email}`}
              style={button}
            >
              Phản Hồi Ngay
            </a>
          </div>
        </td>
      </tr>

      {/* FOOTER */}
      <tr>
        <td style={footer}>
          <p style={{ margin: "5px 0" }}>
            Đây là email thông báo tự động.
          </p>
          <p style={{ margin: "5px 0" }}>
            &copy; {new Date().getFullYear()} ngocdung.id.vn. All Rights Reserved.
          </p>
        </td>
      </tr>
    </table>
  </div>
);