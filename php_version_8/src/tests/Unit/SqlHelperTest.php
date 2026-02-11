<?php

namespace Tests\Unit;

// use PHPUnit\Framework\TestCase;
use Tests\TestCase; // Laravelの機能(DB::beginTransaction等)をモック無しで通すためUnitでもこちらを使う
use App\Helper\SqlHelper;

class SqlHelperTest extends TestCase
{
    public function test_sql_generation()
    {
        $table = 'users';
        $datas = [
            ['region' => 'Asia', 'id' => '001', 'name' => 'Hoge[001]', 'status' => 'active'],
            ['region' => 'Europe', 'id' => '002', 'name' => 'Piyo[002]', 'status' => 'inactive']
        ];
        $keys = ['region', 'id'];
        $columns = ['name', 'status'];

        $result = SqlHelper::buildBulkUpdateQuery($table, $datas, $keys, $columns); // ★ここ！

        // 戻り値の構造チェック
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('bindings', $result);

        // SQL文のチェック
        $queryExpected = "UPDATE users AS t1 INNER JOIN (SELECT ? AS region , ? AS id , ? AS name , ? AS status UNION ALL SELECT ? AS region , ? AS id , ? AS name , ? AS status) AS t2 ON t1.region = t2.region AND t1.id = t2.id SET t1.name = t2.name , t1.status = t2.status"

    }

    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
