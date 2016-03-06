<?php

namespace htmlgen\test;
use function \htmlgen\map;

class MapTest extends \PHPUnit_Framework_TestCase {

  public function test_map_empty_returns_empty () {
    $input = [];
    $expected = [];
    $actual = map($input, function($v, $k) { return [$k, $v]; });
    $this->assertSame($expected, $actual);
  }

  public function test_map_array_returns_array () {
    $input = ['x', 'y', 'z'];
    $expected = [[0,'x'], [1,'y'], [2,'z']];
    $actual = map($input, function($v, $k) { return [$k, $v]; });
    $this->assertSame($expected, $actual);
  }

  public function test_map_assoc_returns_array () {
    $input = ['x'=>1, 'y'=>2, 'z'=>3];
    $expected = [['x',1], ['y',2], ['z',3]];
    $actual = map($input, function($v, $k) { return [$k, $v]; });
    $this->assertSame($expected, $actual);
  }

}
