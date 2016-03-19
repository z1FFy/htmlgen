<?php

namespace htmlgen\test;
use function htmlgen\raw;

class RawTest extends \PHPUnit_Framework_TestCase {

  public function test_raw_is_idempotent () {
    $expected = 'honey';
    $actual = (string) raw(raw('honey'));
    $this->assertSame($expected, $actual);
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
