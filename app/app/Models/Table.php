<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    // ここで変更できるカラムの指定、
    // ここで指定されたカラムのみがcreateやupdateで変更できる
    protected $fillable = ['number', 'status'];

    const STATUS_AVAILABLE = 'available';
    const STATUS_IN_USE = 'in_use';

    // LaravelとDBを繋ぐのがModel
    // DBのテーブル構造を定義したmigrationファイルからデータの取得・登録・変換などを行なっている
    public static function statusOptions()
    {
        return [
            self::STATUS_AVAILABLE => '空き',
            self::STATUS_IN_USE => '利用中',
        ];
    }
}
