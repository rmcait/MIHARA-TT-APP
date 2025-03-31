// resources/js/autoUpdateSlot.js

export function autoUpdateSlotStatus() {
    setInterval(() => {
        $.ajax({
            url: '/api/auto-update-slot-status',
            type: 'GET',
            success: function(response) {
                console.log('自動更新成功:', response);
            },
            error: function(error) {
                console.error('自動更新失敗:', error);
            }
        });
    }, 30000); // 1分ごとに実行
}