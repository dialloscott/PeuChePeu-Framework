<?php
namespace Tests\Core\Database;

use Framework\Database\Table;

class FakeTableTest extends Table {
    const TABLE = "fake";
}

class FakeEntityTest { }

class FakeTableEntity extends Table {
    const TABLE = "fake";
    const ENTITY = FakeEntityTest::class;
}

class TableTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var Mockable
     */
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(\Framework\Database\Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['query', 'fetch', 'lastInsertId', 'fetchColumn'])
            ->getMock();

        $this->table = new FakeTableTest($this->database);
    }

    public function testUpdate () {
        $this->database
            ->expects($this->once())
            ->method('query')
            ->with(
                $this->equalTo('UPDATE fake SET a = :a, b = :b WHERE id = :id'),
                $this->equalTo(['a' => 'a', 'b' => 2, 'id' => 1])
            );

        $this->table->update(1, ['a' => 'a', 'b' => 2]);
    }

    public function testDelete () {
        $this->database
            ->expects($this->once())
            ->method('query')
            ->with(
                $this->equalTo('DELETE FROM fake WHERE id = ?'),
                $this->equalTo([2])
            );

        $this->table->delete(2);
    }

    public function testFind () {
        $this->database
            ->expects($this->once())
            ->method('fetch')
            ->with(
                $this->equalTo('SELECT * FROM fake WHERE id = ?'),
                $this->equalTo([2])
            )
            ->willReturn(new \stdClass());

        $this->table->find(2);
    }

    public function testFindWithEntity () {
        $this->database
            ->expects($this->once())
            ->method('fetch')
            ->with(
                $this->equalTo('SELECT * FROM fake WHERE id = ?'),
                $this->equalTo([2]),
                $this->equalTo(FakeEntityTest::class)
            )
            ->willReturn(new \stdClass());

        $this->table = new FakeTableEntity($this->database);
        $this->table->find(2);
    }

    public function testCreate () {
        $this->database
            ->expects($this->once())
            ->method('query')
            ->with(
                $this->equalTo('INSERT INTO fake SET a = :a, b = :b'),
                $this->equalTo(['a' => 'a', 'b' => 2])
            );

        $this->database
            ->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(3);

        $id = $this->table->create(['a' => 'a', 'b' => 2]);
        $this->assertEquals(3, $id);
    }

    public function testCount () {
        $this->database
            ->expects($this->once())
            ->method('fetchColumn')
            ->with($this->equalTo('SELECT COUNT(id) FROM fake'))
            ->willReturn(10);

        $this->assertEquals(10, $this->table->count());
    }

}