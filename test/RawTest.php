<?php

namespace htmlgen\test;
use \htmlgen\RawString;
use function \htmlgen\raw;

class RawTest extends \PHPUnit_Framework_TestCase {

  public function test_raw_returns_a_raw_string () {
    $this->assertTrue(raw('honey') instanceof RawString);
  }

  public function test_raw_string_returns_boxed_value_when_cast_to_string () {
    $expected = 'candybox 2.0';
    $actual = (string) raw('candybox 2.0');
    $this->assertSame($expected, $actual);
  }

  public function test_raw_string_is_automatically_coerced_when_joined () {
    $expected = 'candybox 2.0';
    $actual = join(' ', [raw('candybox'), raw('2.0')]);
    $this->assertSame($expected, $actual);
  }

}
