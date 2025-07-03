<?php


namespace Tests\Unit\Generator\Definition\Entity;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use PHPUnit\Framework\TestCase;

class ResultEntityTest extends TestCase
{
    protected $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new ResultEntity();
    }

    public function testSetAndGetResultsAreSame(): void
    {
        $results = [1];

        $this->entity->setResults($results);
        $gotResults = $this->entity->getResults();
        self::assertSame($results, $gotResults);
    }

    public function testSetAndGetTableNameIsSame(): void
    {
        $tableName = uniqid('table', true);
        $this->entity->setTableName($tableName);
        $gotTableName = $this->entity->getTableName();
        self::assertSame($tableName, $gotTableName);
    }

    /**
     * @param string $tableName
     * @param bool $expectedResult
     * @testWith    ["testTable", true]
     *              ["testTable", false]
     */
    public function testHasResultForTable(string $tableName, bool $expectedResult): void
    {
        if ($expectedResult === true) {
            $value = [$tableName => []];
        } else {
            $value = [time() => []];
        }
        $this->entity->setResults($value);

        self::assertSame($expectedResult, $this->entity->hasResultForTable($tableName));
    }

    /**
     * @param string $tableName
     * @param bool $expectedResult
     * @param string $key
     *
     * @testWith    ["testTable", "key", true]
     *              ["testTable", "key", false]
     */
    public function testHasResultForTableNameAndKey(string $tableName, string $key, bool $expectedResult): void
    {
        if ($expectedResult === true) {
            $value = [$tableName => [$key => []]];
        } else {
            $value = [time() => []];
        }
        $this->entity->setResults($value);

        self::assertSame($expectedResult, $this->entity->hasResultForTableNameAndKey($tableName, $key));
    }

    public function testGetResultByTableNameAndKeyWorks(): void
    {
        $results = ['testTable' => ['testKey' => ['test']]];
        $this->entity->setResults($results);

        $result = $this->entity->getResultByTableNameAndKey('testTable', 'testKey');
        self::assertEquals(['test'], $result);
    }

    public function testGetResultByTableNameAndKeyReturnsEmptyArray(): void
    {
        $results = ['testTable' => ['testKey' => ['test']]];
        $this->entity->setResults($results);

        $result = $this->entity->getResultByTableNameAndKey('testTable2', 'testKey');
        self::assertCount(0, $result);
    }

    public function testGetResultByTableWorks(): void
    {
        $results = ['testTable' => ['testKey' => ['test']]];
        $this->entity->setResults($results);
        $result = $this->entity->getResultByTable('testTable');
        self::assertEquals($results['testTable'], $result);
    }

    public function testGetResultByTableReturnsEmptyArray(): void
    {
        $results = ['testTable' => ['testKey' => ['test']]];
        $this->entity->setResults($results);
        $result = $this->entity->getResultByTable('testTable2');
        self::assertCount(0, $result);
    }
}