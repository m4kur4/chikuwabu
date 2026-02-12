<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase; // Laravelの機能(DB::beginTransaction等)をモック無しで通すためUnitでもこちらを使う
use App\Helpers\SqlHelper;

class SqlHelperTest extends TestCase
{
    public function test_build_bulk_update_query()
    {
        $table = 'users';
        $datas = [
            ['region' => 'Asia', 'id' => '001', 'name' => 'Hoge', 'status' => 'active'],
            ['region' => 'Europe', 'id' => '002', 'name' => 'Piyo', 'status' => 'inactive']
        ];
        $keys = ['region', 'id'];
        $columns = ['name', 'status'];

        $result = SqlHelper::buildBulkUpdateQuery($table, $datas, $keys, $columns);

        // 戻り値の構造チェック
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('bindings', $result);

        // SQL文のチェック
        $queryExpected = "UPDATE users AS t1 INNER JOIN (SELECT ? AS region , ? AS id , ? AS name , ? AS status UNION ALL SELECT ? AS region , ? AS id , ? AS name , ? AS status) AS t2 ON t1.region = t2.region AND t1.id = t2.id SET t1.name = t2.name , t1.status = t2.status";

        // バインド値の内容チェック
        // バインド値(expected: ["Asia", "001", "Hoge", "active", "Europe", "002", "Piyo", "inactive"])
        $bindings = $result['bindings'];
        // 項目数
        $this->assertCount(8, $bindings);
        // -- 1レコード目の内容
        $this->assertSame($bindings[0], 'Asia');
        $this->assertSame($bindings[1], '001');
        $this->assertSame($bindings[2], 'Hoge');
        $this->assertSame($bindings[3], 'active');
        // -- 2レコード目の内容
        $this->assertSame($bindings[4], 'Europe');
        $this->assertSame($bindings[5], '002');
        $this->assertSame($bindings[6], 'Piyo');
        $this->assertSame($bindings[7], 'inactive');

        // 例外処理(カラム名の不整合)チェック
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('指定したカラム「bar, foo」が更新データに存在しません');
        $keys_invalid = ['region', 'id', 'bar']; // barがおかしい
        $columns_invalid = ['name', 'status', 'foo']; // fooがおかしい
        SqlHelper::buildBulkUpdateQuery($table, $datas, $keys_invalid, $columns_invalid);
    }
}
