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
            text-align: center;
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

        .status-text {
            font-weight: 500;
            font-size: 1rem;
        }

        .status-text.waiting {
            color: #ff9500;
        }

        .status-text.in_use {
            color: #ff3b30;
        }

        .status-text.available {
            color: #34c759;
        }

        .waiting-status-text.waiting {
            color: #ff9500;
        }

        .waiting-status-text.available {
            color: #34c759;
        }

        .table-layout {
    margin: 0 auto;
    max-width: 960px; /* 背景の横幅を制限 */
    width: 100%;
    background: #f9f9f9;
    display: flex;
    gap: 2rem; /* 要素間の隙間 */
    justify-content: center; /* アイテムを中央に配置 */
    
}

.table-box {
    width: 13%; /* デフォルトで1列 */
    height: 200px;
    border-radius: 12px;
    text-align: center;
    margin: 50px 0;
    padding: 1rem;
    background-color: #e0f7fa; /* ソフトな水色 */
    color: #00796b; /* 深い緑 */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* ソフトな影 */
    transition: background-color 0.3s, transform 0.3s;
}

.table-box.in_use {
    background-color: #ffebee; /* ソフトな赤 */
    color: #d32f2f; /* 赤色 */
}

.table-box.available {
    background-color: #e8f5e9; /* ソフトな緑 */
    color: #388e3c; /* 深い緑 */
}

.table-box.waiting {
    background-color: #fff3e0; /* ソフトなオレンジ */
    color: #f57c00; /* オレンジ色 */
}

.table-box:hover {
    transform: scale(1.05); /* ホバー時に少し大きくする */
}

/* スマホ・タブレット用のレスポンシブデザイン */
@media (max-width: 768px) {
    .table-box {
        height: 120px;
    }
}

@media (max-width: 480px) {
    .table-layout {

    }
    .table-box {
        width: 10%;
        height: 60px;
        gap: 10px;
    }
}
    </style>
</head>

<body>
<header>
    <h1 class="facility-title">美原総合体育館 卓球室</h1>
</header>

<h2>現在の利用状況</h2>
<p id="current-slot" style="text-align: center;">
  - 
  @if($timeSlotContext['current'] === 'closed')
      営業時間外
  @else
      {{ $timeSlotContext['current'] }}
  @endif
  -
</p>

<table id="table-status-view">
    <thead>
        <tr>
            <th>テーブル番号</th>
            <th>状態</th>
            <th>待機状況<br><span id="next-slot">{{ $timeSlotContext['next'] }}</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tables as $table)
            <tr data-id="{{ $table->id }}">
                <td>{{ $table->number }}</td>
                <td>
                    <span class="status-text {{ $table->status === 'in_use' ? 'in_use' : 'available' }}">
                        {{ $table->status === 'in_use' ? '利用中' : '空き' }}
                    </span>
                </td>
                <td>
                    @foreach ($table->waitingLists as $waiting)
                        <span class="waiting-status-text {{ $waiting->status === 'waiting' ? 'waiting' : 'available' }}">
                            {{ $waiting->status === 'waiting' ? '待機中' : '空き' }}
                        </span>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<h2>卓球台の見取り図</h2>

<div style="text-align: center; max-width: 960px; /* 背景の横幅を制限 */
    width: 100%;
    background: #f9f9f9;
    margin: 0 auto;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    padding-top: 8px;">奥</div>

<div class="table-layout">
    @foreach ($tables as $table)
        <div class="table-box {{ $table->status }}" data-id="{{ $table->id }}">
            <span>{{ $table->number }}</span>
        </div>
    @endforeach
</div>

<div style="text-align: center; max-width: 960px; /* 背景の横幅を制限 */
    width: 100%;
    background: #f9f9f9;
    margin: 0 auto;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
    padding-bottom: 8px;">入り口</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    setInterval(() => {
    // テーブル情報を非同期で取得
    $.ajax({
        url: '/user/fetch-tables',  // テーブルの状態を取得するURL
        type: 'GET',
        success: function (data) {
            data.forEach(table => {
                console.log('Table ID:', table);  // デバッグ用

                // まず、テーブルの状態を更新
                const row = $('tr[data-id="' + table.id + '"]');
                const statusText = row.find('.status-text');

                // テーブルの状態を更新
                if (table.status === 'in_use') {
                    statusText.text('利用中').removeClass('available').addClass('in_use');
                } else {
                    statusText.text('空き').removeClass('in_use').addClass('available');
                }

                // 見取り図も更新
                // 見取り図の状態も更新
                const tableBox = $('.table-box[data-id="' + table.id + '"]');
                tableBox.removeClass('in_use available waiting').addClass(table.status);
            });
        },
        error: function () {
            console.error('状態の取得に失敗しました');
        }
    });

    // 待機リスト情報を非同期で取得
    $.ajax({
        url: '/user/fetch-waitinglists',  // 待機リストの状態を取得するURL
        type: 'GET',
        success: function (data) {
            data.forEach(waitingList => {
                console.log('Waiting List ID:', waitingList);  // デバッグ用

                const row = $('tr[data-id="' + waitingList.table_id + '"]'); // table_idを基に該当行を取得
                const waitingStatusText = row.find('.waiting-status-text'); // 待機状況を更新するための要素

                // waitingStatusが存在する場合に待機状況を更新
                if (waitingStatusText.length) {
                    if (waitingList.status === 'waiting') {
                        waitingStatusText.text('待機中').addClass('waiting').removeClass('available');
                    } else {
                        waitingStatusText.text('空き').removeClass('waiting').addClass('available');
                    }
                }
            });
        },
        error: function () {
            console.error('待機リストの取得に失敗しました');
        }
    });

    $.get('/user/time-slot-context', function (res) {
        // current の更新
        if (res.current === 'closed') {
            $('#current-slot').text('- 営業時間外 -');
        } else {
            $('#current-slot').text(`- ${res.current} -`);
        }

        // next の更新
        $('#next-slot').text(`- ${res.next} -`);
    });

}, 5000);  // 5秒ごとに更新
</script>

</body>
</html>