<?php

namespace App\Helper;

class SqlHelper
{
    /**
     * 指定した複数レコードの連想配列を、指定したキーと紐づくようにDBへ一括反映するUPDATE文を作成する。
     * ※注：更新データの各レコードにおけるkeyがデータベース上のカラム名と異なる場合、一致させてからこの関数に渡してください
     * ※注：この関数はMySQL用です(特にVALUES文が使えないver)
     *
     * @param string $table UPDATE対象のテーブル名
     * @param array $datas 更新データの配列 (連想配列のリスト)
     * @param array $keys 更新条件のキーとするカラム名のリスト
     * @param array $columns 更新対象とするカラム名のリスト
     * @return array プリペアードステートメント(query)とバインド値(bindings)
     * - {query: string, bindings: array}
     */
    public static function buildBulkUpdateQuery(string $table, array $datas, array $keys, array $columns): array {

        // NOTE: 最終的に以下のようなUPDATE文を作る
        //       プリペアードステートメントとバインド値の配列を本関数から返却し、LaravelのDB::update()でバインド＋実行してもらう想定
        //
        // 本関数を使う具体例：
        // >>「server」というテーブルの「status」「updated_at」というカラムを「region」「id」に応じて更新
        // >> 条件：region="Asia"かつid="001"　=> 更新後の値：「status="active"」、「update_user_id="Laravel_33"」
        // >> 条件：region="Asia"かつid="002"　=> 更新後の値：「status="inactive"」、「update_user_id="Larave_44l"」
        // >> 条件：region="Asia"かつid="003"　=> 更新後の値：「status="suspended"」、「update_user_id="Larave_55l"」
        //
        // 最終的に実行されるSQL：
        // UPDATE servers AS t1
        // INNER JOIN (
        //   SELECT 'Asia' AS region, '001' AS id, 'active' AS status, 'Laravel_33' AS update_user
        //   UNION ALL
        //   SELECT 'Asia' AS region, '002' AS id, 'inactive' AS status, 'Laravel_44' AS update_user
        //   UNION ALL
        //   SELECT 'Asia' AS region, '003' AS id, 'suspended' AS status, 'Laravel_55' AS update_user
        // ) AS t2
        //   ON t1.region = t2.region
        //   AND t1.id = t2.id
        // SET t1.status = t2.status, t1.update_user_id = t2.update_user_id

        // 更新データがなければ空のひな型を返却
        if (empty($datas)) {
            return ['query' => '', 'bindings' => []];
        }

        // UPDATE文で使用(更新条件or更新対象)するカラム名のリスト
        $allTargetFields = array_merge($keys, $columns);

        // 更新データに存在しないカラム名が指定されている場合はエラー
        $firstDataKeys = array_keys($datas[0]);
        foreach ($allTargetFields as $field) {
            if (!in_array($field, $firstDataKeys)) {
                throw new \InvalidArgumentException("指定したカラム{$field}が更新データに存在しません");
            }
        }

        // クエリ内で使うテーブル名のエイリアス
        $BASE_TABLE_AS = 't1';
        $JOIN_TABLE_AS = 't2';

        // 一行のSELECT文のひな型を動的に作成(後続のバインド処理にてLaravelがプレースホルダ(?)を置換する)
        $queryPartSelectLine = 'SELECT ' . collect($allTargetFields)
            ->map(fn($field) => "? AS {$field}")
            ->implode(' , ');
        // データ件数分、雛形をコピーして配列に詰める
        $queryPartSelectLines = array_fill(0, count($datas), $queryPartSelectLine);
        // 配列に詰めたひな型のSELECT文をユニオン結合でまとめる
        $queryPartSelectUnion = '(' . implode(' UNION ALL ', $queryPartSelectLines) . ") AS {$JOIN_TABLE_AS}";

        // 結合条件(=更新条件)を動的に作成
        $queryPartWhere = collect($keys)
            ->map(fn($key) => "{$BASE_TABLE_AS}.{$key} = {$JOIN_TABLE_AS}.{$key}")
            ->implode(' AND ');

        // SETするカラム及び値を動的に作成
        $queryPartSetting = collect($columns)
            ->map(fn($column) => "{$BASE_TABLE_AS}.{$column} = {$JOIN_TABLE_AS}.{$column}")
            ->implode(' , ');

        // 実行SQLを組み立てる
        $queryParts = ["UPDATE {$table} AS {$BASE_TABLE_AS}"];
        $queryParts[] = 'INNER JOIN';
        $queryParts[] = $queryPartSelectUnion; // 更新データの仮想テーブル
        $queryParts[] = 'ON';
        $queryParts[] = $queryPartWhere; // 更新テーブルと仮想テーブルの結合条件
        $queryParts[] = 'SET';
        $queryParts[] = $queryPartSetting; // SETする更新データ
        $query = implode(' ', $queryParts);

        // バインド値の平坦化
        $bindings = [];
        foreach ($datas as $data) {
            foreach ($allTargetFields as $field) {
                $bindings[] = $data[$field] ?? null;
            }
        }
        return ['query' => $query, 'bindings' => $bindings];
    }
}