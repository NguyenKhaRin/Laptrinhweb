/**
 * ========================================================
 * CHAT JS - FETCH API GỬI/NHẬN TIN NHẮN REAL-TIME (AJAX POLLING)
 * Phụ trách bởi: Thành viên phụ trách Module Chat
 * ========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    const messageBox = document.getElementById('messageBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');

    if (!messageBox || !chatForm) return;

    const partnerId = messageBox.dataset.partnerId;
    const productId = messageBox.dataset.productId;
    const baseUrl = messageBox.dataset.baseUrl || '';

    let isFetching = false;

    // Cuộn xuống dòng tin nhắn mới nhất
    function scrollToBottom() {
        messageBox.scrollTop = messageBox.scrollHeight;
    }

    // Tải danh sách tin nhắn từ server
    async function fetchMessages() {
        if (isFetching || !partnerId) return;
        isFetching = true;

        try {
            const url = `${baseUrl}/api/chat/messages?partner_id=${partnerId}&product_id=${productId}`;
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            if (data.success) {
                renderMessages(data.messages, data.my_id);
            }
        } catch (error) {
            console.error('Lỗi tải tin nhắn:', error);
        } finally {
            isFetching = false;
        }
    }

    // Hiển thị các tin nhắn lên giao diện
    function renderMessages(messages, myId) {
        if (!messages || messages.length === 0) {
            messageBox.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-chat-dots fs-1 mb-2 d-block opacity-50"></i>
                    <p class="small">Hãy gửi lời chào đầu tiên để bắt đầu trao đổi!</p>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMine = parseInt(msg.sender_id) === parseInt(myId);
            const timeStr = new Date(msg.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

            if (isMine) {
                html += `
                    <div class="message-bubble-mine">
                        <div>${escapeHtml(msg.message)}</div>
                        <div class="message-time">${timeStr} <i class="bi bi-check2"></i></div>
                    </div>
                `;
            } else {
                html += `
                    <div class="message-bubble-partner">
                        <div>${escapeHtml(msg.message)}</div>
                        <div class="message-time">${timeStr}</div>
                    </div>
                `;
            }
        });

        const shouldScroll = messageBox.scrollTop + messageBox.clientHeight >= messageBox.scrollHeight - 50;
        messageBox.innerHTML = html;
        if (shouldScroll) scrollToBottom();
    }

    // Thoát ký tự HTML chống XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Gửi tin nhắn mới qua API
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        const formData = new FormData(chatForm);

        try {
            messageInput.disabled = true;
            const response = await fetch(`${baseUrl}/api/chat/send`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                messageInput.value = '';
                await fetchMessages();
                scrollToBottom();
            } else {
                alert(result.error || 'Không thể gửi tin nhắn.');
            }
        } catch (error) {
            console.error('Lỗi khi gửi tin nhắn:', error);
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    });

    // Tải tin nhắn lần đầu tiên
    fetchMessages().then(scrollToBottom);

    // Long-polling: Tự động kiểm tra tin nhắn mới mỗi 3 giây
    setInterval(fetchMessages, 3000);
});

