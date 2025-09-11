// Unfollow Tool for TikTok
// Ghi đè các phương thức của console để ẩn những log không mong muốn
(function () {
    const originalLog = console.log;
    const originalInfo = console.info;
    const originalError = console.error;
    const originalWarn = console.warn;

    // Ghi đè console.log
    console.log = function (...args) {
        // Kiểm tra xem có phải log từ SDK không (dựa trên nội dung log)
        if (args.some(arg => typeof arg === "string" && arg.includes("get storage") || arg.includes("get keys with crypto"))) {
            return; // Nếu có, không in log ra console
        }
        originalLog.apply(console, args); // Nếu không phải, in log bình thường
    };

    // Ghi đè console.info
    console.info = function (...args) {
        if (args.some(arg => typeof arg === "string" && arg.includes("get storage") || arg.includes("get keys with crypto"))) {
            return;
        }
        originalInfo.apply(console, args);
    };

    // Ghi đè console.error
    console.error = function (...args) {
        if (args.some(arg => typeof arg === "string" && arg.includes("get storage") || arg.includes("get keys with crypto"))) {
            return;
        }
        originalError.apply(console, args);
    };

    // Ghi đè console.warn
    console.warn = function (...args) {
        if (args.some(arg => typeof arg === "string" && arg.includes("get storage") || arg.includes("get keys with crypto"))) {
            return;
        }
        originalWarn.apply(console, args);
    };
})();

// Auto Unfollow Script for TikTok "Đã follow" button
// - Uses excludeList to avoid reserved users
// - Auto-scrolls full follow-list-popup before unfollowing


let count = 0;
let limit = 2000;           // Số lượng muốn unfollow
let delay = 2500;           // Thời gian giữa mỗi unfollow (ms)
let scrollDelay = 1500;     // Thời gian giữa mỗi lần scroll (ms)
let stopUnfollow = false;   // Đặt true để dừng tool

// Danh sách người KHÔNG unfollow
const excludeList = [
    // { name: "𝙍𝙊𝙅𝙄̇𝙉34", id: "gecemavisi77_" },
    { name: "Phùng Ngọc Dũng", id: "pndung05" },
];

// Hàm tự động scroll tới cuối danh sách
function autoScrollToBottom(callback) {
    let lastHeight = 0;
    let sameCount = 0;
    const maxSame = 3;
    const interval = setInterval(() => {
        window.scrollTo(0, document.body.scrollHeight);
        const currHeight = document.body.scrollHeight;
        if (currHeight !== lastHeight) {
            lastHeight = currHeight;
            sameCount = 0;
        } else {
            sameCount++;
        }
        if (sameCount >= maxSame) {
            clearInterval(interval);
            console.log("✅ Đã load xong toàn bộ danh sách.");
            callback();
        }
    }, scrollDelay);
}

// Hàm thực hiện unfollow
function autoUnfollow() {
    if (stopUnfollow) {
        console.log("🛑 Tool unfollow đã dừng.");
        return;
    }

    if (count >= limit) {
        console.log(`✅ Đã unfollow đủ ${limit} người.`);
        return;
    }

    // Lấy danh sách nút "Đã follow"
    const buttons = Array.from(
        document.querySelectorAll('button[data-e2e="follow-button"]')
    ).filter(b => b.innerText.trim().toLowerCase() === "đã follow");

    let found = false;

    for (const btn of buttons) {
        const item = btn.closest('div[data-e2e="follow-list-item"]') || btn.closest('li');
        const aria = btn.getAttribute('aria-label') || '';
        const name = aria.replace(/^(Đã follow|Following)\s*/, '').trim();
        const link = item?.querySelector('a[href*="/@"]');
        const id = link ? link.getAttribute('href').split('/@')[1] : '';

        if (excludeList.some(u => u.id.toLowerCase() === id.toLowerCase())) {
            console.log(`🚫 Bỏ qua: ${name} (@${id})`);
            continue;
        }

        // Click unfollow
        btn.click();
        count++;
        // console.log(`🐸 👀 🔄 💔 👋 Unfollowed ${count}: ${name} (@${id})`);
        console.log(
            `%c🐸 👀 🔄 💔 👋 Unfollowed %c${count}%c: %c${name} (@${id})`,
            'color: #00cc66; font-weight: bold;', // style emoji + "Unfollowed" (xanh lá)
            'font-size: 20px; font-weight: bold;', // style cho count
            'color: #00aaff; font-weight: bold;', // style cho dấu :
            'color: red; font-weight: bold;' // style cho name + id
        );

        found = true;
        break; // Sau 1 lần unfollow thì break
    }

    if (!found) {
        // Nếu không tìm được ai để unfollow, scroll tiếp
        console.log("🔄 Load thêm danh sách...");
        window.scrollTo(0, document.body.scrollHeight);
        setTimeout(autoUnfollow, scrollDelay);
    } else {
        // Nếu vừa unfollow thành công thì tiếp tục sau delay
        setTimeout(autoUnfollow, delay);
    }
}

// Bắt đầu: scroll load hết danh sách rồi unfollow
autoScrollToBottom(autoUnfollow);

// Nếu muốn tạm dừng => trên console gõ: stopUnfollow = true;