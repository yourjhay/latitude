<?php

namespace Latitude\QueryBuilder;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var EngineInterface */
    protected $engine;

    /** @var QueryFactory */
    protected $factory;

    public function setUp()
    {
        $this->engine = $this->getEngine();
        $this->factory = new QueryFactory($this->engine);
    }

    protected function getEngine(): EngineInterface
    {
        return new Engine\BasicEngine();
    }

    public function assertSql(string $sql, StatementInterface $statement)
    {
        $this->assertSame($sql, $statement->sql($this->engine));
    }

    public function assertParams(array $params, StatementInterface $statement)
    {
        $this->assertSame($params, $statement->params($this->engine));
    }
}
