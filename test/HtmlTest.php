<?php

namespace htmlgen\test;
use function \htmlgen\html;
use function \htmlgen\raw;
use const \htmlgen\VOID_ELEMENTS;

// terms used in this test suite
// `assoc array` is an associative array where at least ONE index is a string
// `numeric array` is a numerically-indexed array; NO indexes are strings

class HtmlTest extends \PHPUnit_Framework_TestCase {

  public function test_html_creates_elements_that_can_be_coerced_into_strings () {
    $expected = '<cat></cat>';
    $actual = (string) html('cat');
    $this->assertSame($expected, $actual);
  }

  public function test_html_with_empty_returns_childless_element () {
    $expected = '<cat></cat>';
    $actual = (string) html('cat', []);
    $this->assertSame($expected, $actual);
  }

  public function test_html_with_no_arguments_throws_error () {
    $this->expectException('TypeError');
    html();
  }

  public function test_html_treats_first_child_assoc_as_attributes () {
    $expected = '<bees make="honey"></bees>';
    $actual = (string) html('bees', ['make'=>'honey']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_treats_numeric_array_as_list_of_children () {
    $expected = '<bees>makehoney</bees>';
    $actual = (string) html('bees', ['make', 'honey']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_treats_string_as_single_child () {
    $expected = '<bees>honey</bees>';
    $actual = (string) html('bees', 'honey');
    $this->assertSame($expected, $actual);
  }

  public function test_html_coerces_object_children_to_strings () {
    $honey = (new class { public function __toString() { return 'honey'; }});
    $expected = '<bees>honey</bees>';
    $actual = (string) html('bees', $honey);
    $this->assertSame($expected, $actual);
  }

  public function test_html_accepts_variadic_children () {
    $expected = '<bees>honeyhoneycombpollenwaxpropolis</bees>';
    $actual = (string) html('bees', raw('honey'), ['honeycomb', 'pollen'], 'wax', ['propolis']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_accepts_first_child_assoc_as_attributes_and_children () {
    $expected = '<bees variety="honeybees">makealotofhoney</bees>';
    $actual = (string) html('bees', ['variety'=>'honeybees'], 'make', ['a', 'lot'], 'of', 'honey');
    $this->assertSame($expected, $actual);
  }

  public function test_html_skips_closing_tag_for_void_elements () {
    $expected = [
      '<area>', '<base>', '<br>', '<col>', '<embed>', '<hr>', '<img>', '<input>',
      '<link>', '<meta>', '<param>', '<source>', '<track>', '<wbr>'
    ];
    $actual = array_map(function($x) { return (string) html($x); }, VOID_ELEMENTS);
    $this->assertSame($expected, $actual);
  }

  public function test_html_accepts_attributes_for_void_elements () {
    $expected = '<input name="bees" value="1000">';
    $actual = (string) html('input', ['name'=>'bees', 'value'=>1000]);
    $this->assertSame($expected, $actual);
  }

  public function test_html_ignores_children_for_void_elements () {
    $expected = '<input>';
    $actual = (string) html('input', 'cat');
    $this->assertSame($expected, $actual);
  }

}
