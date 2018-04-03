<?php

namespace Latitude\QueryBuilder\Partial;

use Latitude\QueryBuilder\TestCase;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\group;

class CriteriaTest extends TestCase
{
    public function testBetween()
    {
        $expr = field('created_at')->between('2018-01-01', '2018-02-01');

        $this->assertSql('created_at BETWEEN ? AND ?', $expr);
        $this->assertParams(['2018-01-01', '2018-02-01'], $expr);

        $expr = field('created_at')->notBetween('2018-02-01', '2018-03-01');

        $this->assertSql('created_at NOT BETWEEN ? AND ?', $expr);
        $this->assertParams(['2018-02-01', '2018-03-01'], $expr);
    }

    public function testIn()
    {
        $expr = field('country')->in('CN', 'JP');

        $this->assertSql('country IN (?, ?)', $expr);
        $this->assertParams(['CN', 'JP'], $expr);

        $expr = field('country')->notIn('CA', 'US', 'MX');

        $this->assertSql('country NOT IN (?, ?, ?)', $expr);
        $this->assertParams(['CA', 'US', 'MX'], $expr);
    }

    public function testInQuery()
    {
        $expr = field('country')->in(
            $this->factory->selectDistinct('country')->from('users')
        );

        $this->assertSql('country IN (SELECT DISTINCT country FROM users)', $expr);
        $this->assertParams([], $expr);
    }

    public function testEquals()
    {
        $expr = field('id')->eq(11);

        $this->assertSql('id = ?', $expr);
        $this->assertParams([11], $expr);

        $expr = field('id')->notEq(42);

        $this->assertSql('id != ?', $expr);
        $this->assertParams([42], $expr);
    }

    public function testGreaterThan()
    {
        $expr = field('age')->gt(65);

        $this->assertSql('age > ?', $expr);
        $this->assertParams([65], $expr);

        $expr = field('age')->gte(18);

        $this->assertSql('age >= ?', $expr);
        $this->assertParams([18], $expr);
    }

    public function testLessThan()
    {
        $expr = field('age')->lt(21);

        $this->assertSql('age < ?', $expr);
        $this->assertParams([21], $expr);

        $expr = field('age')->lte(30);

        $this->assertSql('age <= ?', $expr);
        $this->assertParams([30], $expr);
    }

    public function testNull()
    {
        $expr = field('deleted_at')->isNull();

        $this->assertSql('deleted_at IS NULL', $expr);
        $this->assertParams([], $expr);

        $expr = field('deleted_at')->isNotNull();

        $this->assertSql('deleted_at IS NOT NULL', $expr);
        $this->assertParams([], $expr);
    }

    public function testAnd()
    {
        $expr = field('id')->eq(5);
        $expr = $expr->and(field('is_active')->eq(true));

        $this->assertSql('id = ? AND is_active = ?', $expr);
    }

    public function testOr()
    {
        $expr = field('is_deleted')->eq(true);
        $expr = $expr->or(field('is_inactive')->eq(true));

        $this->assertSql('is_deleted = ? OR is_inactive = ?', $expr);
    }

    public function testGroup()
    {
        $expr = group(
            field('username')->eq('jane')
                ->or(field('first_name')->eq('Jane'))
        )->and(field('last_login')->isNotNull());

        $this->assertSql('(username = ? OR first_name = ?) AND last_login IS NOT NULL', $expr);
    }
}
