<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>テーブル状況管理</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        background: #f2f2f7;
        padding: 1.5rem;
        margin: 0;
    }

    header {
    width: 100%;
    text-align: center;
}

.facility-title {
    font-size: 1.6rem;
    font-weight: 600;
    color: #1c1c1e;
    margin: 0;
    padding: 1rem 0 1rem;
    line-height: 1.4;
    letter-spacing: -0.3px;
    word-break: keep-all;
    white-space: normal;
    border-bottom: 1px solid #e0e0e0;
}

h2 {
    font-size: 1.3rem;
    font-weight: 500;
    color: #333;
    margin: 1.5rem 0 0.8rem;
    letter-spacing: -0.2px;
    text-align: center; /* ← 中央寄せに */
}

    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 16px;
    }

    thead th:first-child {
    border-top-left-radius: 16px;
}
thead th:last-child {
    border-top-right-radius: 16px;
}
tbody tr:last-child td:first-child {
    border-bottom-left-radius: 16px;
}
tbody tr:last-child td:last-child {
    border-bottom-right-radius: 16px;
}

    thead th {
        background: #ADD8E6;
        font-size: 1rem;
        padding: 0.8rem;
        text-align: left;
        color: #555;
    }
    

    tbody tr {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
        display: table-row;
        transition: background-color 0.3s;
    }

    tbody tr:hover {
        background-color: #f9f9f9;
    }

    tbody td {
        padding: 1rem;
        font-size: 1.05rem;
        border-top: 1px solid #e0e0e0;
    }

    tbody tr:first-child td {
        border-top: none;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        border-radius: 34px;
        transition: 0.4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        border-radius: 50%;
        transition: 0.4s;
    }

    input:checked + .slider {
        background-color: #34c759;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    @media (max-width: 320px) {
    .facility-title {
        font-size: 1.4rem;
        padding: 0.8rem 0 1rem;
    }
}
    
    @media (max-width: 480px) {
        table {
            font-size: 0.95rem;
        }

        .switch {
            width: 52px;
            height: 30px;
        }

        .slider:before {
            height: 22px;
            width: 22px;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }
    }
</style>
</head>
<body>
<header>
    <h1 class="facility-title">美原総合体育館 卓球室</h1>
</header>
<h2>利用状況</h2>
<p id="current-slot" style="text-align: center;">
    - 
    @if($timeSlotContext['current'] === 'closed')
        営業時間外
    @else
        {{ $timeSlotContext['current'] }}
    @endif
    -
</p>
<table>
    <thead>
        <tr>
            <th>番号</th>
            <th>状態</th>
            <th>利用状況</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($tables as $table)
            <tr>
                <td>{{ $table->number }}</td>
                <td>
                    <span class="status-text" style="color: {{ $table->status === 'in_use' ? '#ff3b30' : '#34c759' }}">
                        {{ $table->status === 'in_use' ? '利用中' : '空き' }}
                    </span>
                </td>
                <td>
                    <label class="switch">
                        <<input type="checkbox"
                            class="toggle-btn"
                            data-id="{{ $table->id }}"
                            {{ $table->status === 'in_use' ? 'checked' : '' }}
                            {{ $timeSlotContext['current'] === 'closed' ? 'disabled' : '' }}>
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h2 style="margin-top: 2rem;">待ち状況</h2>
<p id="next-slot" style="text-align: center;">
    - {{ $timeSlotContext['next'] }} -
</p>
<table>
    <thead>
        <tr>
            <th>番号</th>
            <th>状態</th>
            <th>切り替え</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($waitingLists as $waiting)
        <tr>
            <td>{{ $waiting->table->number }}</td>
            <td>
                <span class="waiting-status-text" style="color: {{ $waiting->status === 'waiting' ? '#ff9500' : '#34c759' }}">
                    {{ $waiting->status === 'waiting' ? '待機中' : '空き' }}
                </span>
            </td>
            <td>
                <label class="switch">
                <input type="checkbox"
                    class="toggle-waiting-btn"
                    data-id="{{ $waiting->id }}"
                    {{ $waiting->status === 'waiting' ? 'checked' : '' }}
                    {{ $timeSlotContext['current'] === 'closed' ? 'disabled' : '' }}> 
                    <span class="slider"></span>
                </label>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    $('.toggle-btn').on('change', function () {
        const checkbox = $(this);
        const tableId = checkbox.data('id');
        const statusSpan = checkbox.closest('tr').find('.status-text');

        $.ajax({
            url: `/employee/tables/${tableId}/toggle`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                // 状態をテキストで反映
                if (res.status === 'in_use') {
                    statusSpan.text('利用中').css('color', '#ff3b30');
                } else {
                    statusSpan.text('空き').css('color', '#34c759');
                    // tableが空きになったらtoggleボタンをOFFにする
                    checkbox.prop('checked', false); // checkboxをOFFに設定
                }
                console.log('状態更新: ', res.status);
            },
            error: function () {
                alert('更新に失敗しました');
            }
        });
    });


    // 待ち状況切り替え
$('.toggle-waiting-btn').on('change', function () {
    const checkbox = $(this);
    const waitingId = checkbox.data('id');
    const statusSpan = checkbox.closest('tr').find('.waiting-status-text');

    $.ajax({
        url: `/employee/waiting-lists/${waitingId}/toggle`,
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if (res.status === 'waiting') {
                statusSpan.text('待機中').css('color', '#ff9500');
            } else {
                statusSpan.text('空き').css('color', '#34c759');
                       // tableが空きになったらtoggleボタンをOFFにする
                       checkbox.prop('checked', false); // checkboxをOFFに設定
            }
            // table_idをコンソールに出力
            console.log('待ち状態更新: ', res.status);
            console.log('対応するテーブルID: ', res.table_id); // table_idを出力
        },
        error: function () {
            alert('更新に失敗しました（待ち状況）');
        }
    });
});

// 1分ごとに自動更新をトリガーする
setInterval(() => {
        axios.post('/user/trigger-auto-update')
            .then(response => {
                console.log('自動更新処理:', response.data.message);
            })
            .catch(error => {
                console.error('エラー:', error);
            });
    }, 30000);  // 1分ごとにリクエストを送信
// 

// 5秒ごとにテーブルと待ち状況のステータスを取得してUIを更新
setInterval(() => {
    // テーブルのステータスを取得して更新
    $.get('/employee/tables/statuses', function (tables) {
        tables.forEach(table => {
            const row = $(`.toggle-btn[data-id="${table.id}"]`).closest('tr');
            const statusSpan = row.find('.status-text');
            const checkbox = row.find('.toggle-btn');

            if (table.status === 'in_use') {
                statusSpan.text('利用中').css('color', '#ff3b30');
                checkbox.prop('checked', true);
            } else {
                statusSpan.text('空き').css('color', '#34c759');
                checkbox.prop('checked', false);
            }
        });
    });

    // 待ち状況のステータスを取得して更新
    $.get('/employee/waiting-lists/statuses', function (waitingLists) {
        waitingLists.forEach(item => {
            const row = $(`.toggle-waiting-btn[data-id="${item.id}"]`).closest('tr');
            const statusSpan = row.find('.waiting-status-text');
            const checkbox = row.find('.toggle-waiting-btn');

            if (item.status === 'waiting') {
                statusSpan.text('待機中').css('color', '#ff9500');
                checkbox.prop('checked', true);
            } else {
                statusSpan.text('空き').css('color', '#34c759');
                checkbox.prop('checked', false);
            }
        });
    });

    $.get('/employee/timeslot-context', function (slotContext) {
        const currentText = slotContext.current === 'closed' ? '営業時間外' : slotContext.current;
        $('#current-slot').text(`- ${currentText} -`);
        $('#next-slot').text(`- ${slotContext.next} -`);
    });
}, 5000); // 5秒ごとに監視して更新
</script>

</body>
</html>