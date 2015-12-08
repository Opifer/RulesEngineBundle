<?php

namespace Opifer\RulesEngineBundle\Tests\Form\DataTransformer;

use Opifer\RulesEngine\Condition\Condition;
use Opifer\RulesEngine\Condition\ConditionSet;
use Opifer\RulesEngine\Operator\Doctrine\Equals;
use Opifer\RulesEngine\RulesEngine;
use Opifer\RulesEngineBundle\Form\DataTransformer\SerializedConditionSetTransformer;

class SerializedConditionSetTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $condition = new Condition();
        $condition->setLeft('a.name');
        $condition->setOperator(new Equals());
        $condition->setRight('somename');

        $expected = new ConditionSet();
        $expected->addCondition($condition);

        $json = '{"operator":"AND","conditions":[{"left":"a.name","operator":{"type":"doctrine_equals"},"right":"somename"}]}';
        $transformer = new SerializedConditionSetTransformer();
        $actual = $transformer->transform($json);

        $this->assertEquals($expected, $actual);
    }

    public function testReverseTransform()
    {
        $expected = '{"operator":"AND","conditions":[{"left":"a.name","operator":{"type":"doctrine_equals"},"right":"somename"}]}';

        $condition = new Condition();
        $condition->setLeft('a.name');
        $condition->setOperator(new Equals());
        $condition->setRight('somename');

        $set = new ConditionSet();
        $set->addCondition($condition);

        $transformer = new SerializedConditionSetTransformer();
        $actual = $transformer->reverseTransform($set);

        $this->assertEquals($expected, $actual);
    }
}
