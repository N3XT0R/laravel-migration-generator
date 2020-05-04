<?php


namespace Tests\Unit\Generator\Definition;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use Tests\TestCase;

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
        $this->assertSame($results, $gotResults);
    }

    public function testSetAndGetTableNameIsSame(): void
    {
        $tableName = uniqid('table', true);
        $this->entity->setTableName($tableName);
        $gotTableName = $this->entity->getTableName();
        $this->assertSame($tableName, $gotTableName);
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

        $this->assertSame($expectedResult, $this->entity->hasResultForTable($tableName));
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

        $this->assertSame($expectedResult, $this->entity->hasResultForTableNameAndKey($tableName, $key));
    }

    public function testGetResultByTableNameAndKeyWorks(): void
    {
        $results = ['testTable' => ['testKey' => ['test']]];
        $this->entity->setResults($results);

        $result = $this->entity->getResultByTableNameAndKey('testTable', 'testKey');
        $this->assertEquals(['test'], $result);
    }
}