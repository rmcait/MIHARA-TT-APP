<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>卓球台管理</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    /* Apple風トグルボタンのスタイル */
    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 28px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 22px;
      width: 22px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #4cd964; /* Appleの緑 */
    }

    input:checked + .slider:before {
      transform: translateX(22px);
    }
  </style>
</head>
<body>
  <h1>卓球台ステータス管理</h1>
  <p>現在のスロット：<strong>{{ $currentSlot }}</strong></p>

  <div>
    @foreach ($tables as $table)
      <div style="margin-bottom: 1.5em;">
        <p>卓球台番号：{{ $table->number }}</p>
        <label class="switch">
          <input type="checkbox"
                 class="status-toggle"
                 data-id="{{ $table->id }}"
                 {{ $table->status === 'in_use' ? 'checked' : '' }}>
          <span class="slider"></span>
        </label>
        <span id="status-text-{{ $table->id }}">
          {{ $table->status === 'available' ? '空き' : '利用中' }}
        </span>
      </div>
    @endforeach
  </div>

  <script>
    document.querySelectorAll('.status-toggle').forEach(toggle => {
      toggle.addEventListener('change', function () {
        const tableId = this.dataset.id;
        const newStatus = this.checked ? 'in_use' : 'available';

        fetch(`/admin/tables/${tableId}/status`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ status: newStatus })
        })
        .then(response => response.json())
        .then(data => {
          const statusText = document.getElementById(`status-text-${tableId}`);
          statusText.textContent = data.status === 'available' ? '空き' : '利用中';
        })
        .catch(err => {
          alert('更新に失敗しました');
          console.error(err);
        });
      });
    });
  </script>
</body>
</html>