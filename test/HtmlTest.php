<?php

namespace htmlgen\elements;
use function htmlgen\element\renderAttributes;
use function htmlgen\element\renderChildren;

function fake(array $attributes, $children): string {
  return sprintf('<fake%s>%s</fake>', renderAttributes($attributes), renderChildren($children));
}

namespace htmlgen\test;
use function htmlgen\html;
use function htmlgen\raw;
use const htmlgen\element\VOID_ELEMENTS;

// terms used in this test suite
// `assoc array` is an associative array where at least ONE index is a string
// `numeric array` is a numerically-indexed array; NO indexes are strings

class HtmlTest extends \PHPUnit_Framework_TestCase {

  public function test_html_with_no_arguments_throws_error () {
    $this->expectException('TypeError');
    html();
  }

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

  public function test_html_accepts_children_attribute_as_children () {
    $expected = '<bees variety="honeybees">workerdronequeen</bees>';
    $actual = (string) html('bees', ['variety'=>'honeybees', 'children'=>['worker','drone','queen']]);
    $this->assertSame($expected, $actual);
  }

  public function test_html_children_override_children_attribute () {
    $expected = '<bees>overridden</bees>';
    $actual = (string) html('bees', ['children'=>['worker','drone','queen']], 'overridden');
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

  public function test_html_accepts_void_attributes () {
    $expected = '<input name="bees" disabled>';
    $actual = (string) html('input', ['name'=>'bees', 'disabled']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_accepts_true_attribute_value_as_void_attribute () {
    $expected = '<input name="bees" disabled>';
    $actual = (string) html('input', ['name'=>'bees', 'disabled'=>true]);
    $this->assertSame($expected, $actual);
  }

  public function test_html_accepts_false_attribute_value_as_void_attribute () {
    $expected = '<input name="bees">';
    $actual = (string) html('input', ['name'=>'bees', 'disabled'=>false]);
    $this->assertSame($expected, $actual);
  }

  public function test_html_ignores_empty_attributes () {
    $expected = '<input name="bees">';
    $actual = (string) html('input', ['name'=>'bees', 'style'=>'']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_ignores_children_for_void_elements () {
    $expected = '<input>';
    $actual = (string) html('input', 'cat');
    $this->assertSame($expected, $actual);
  }

  public function test_html_checks_for_override_elements () {
    $expected = '<fake omg>veryhoneywow&excl;</fake>';
    $actual = (string) html('fake', ['omg'=>true], 'very', 'honey', 'wow!');
    $this->assertSame($expected, $actual);
  }

  public function test_html_doctype_element () {
    $expected = '<!doctype html>';
    $actual = (string) html('doctype', 'ignored', 'children');
    $this->assertSame($expected, $actual);
  }

  public function test_html_comment_element () {
    $expected = '<!-- honeybees -->';
    $actual = (string) html('comment', 'honey', 'bees');
    $this->assertSame($expected, $actual);
  }

  public function test_html_empty_zen_element_throw_exception () {
    $this->expectException('InvalidArgumentException');
    html('');
  }

  public function test_html_empty_zen_element_with_id_defaults_to_div () {
    $expected = '<div id="main"></div>';
    $actual = (string) html('#main');
    $this->assertSame($expected, $actual);
  }

  public function test_html_empty_zen_element_with_class_defaults_to_div () {
    $expected = '<div class="cat"></div>';
    $actual = (string) html('.cat');
    $this->assertSame($expected, $actual);
  }

  public function test_html_zen_element_with_id () {
    $expected = '<div id="main"></div>';
    $actual = (string) html('div#main');
    $this->assertSame($expected, $actual);
  }

  public function test_html_zen_element_with_id_and_id_attribute () {
    $expected = '<div id="override"></div>';
    $actual = (string) html('div#main', ['id'=>'override']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_zen_element_with_classes () {
    $expected = '<div class="alert alert-warning"></div>';
    $actual = (string) html('div.alert.alert-warning');
    $this->assertSame($expected, $actual);
  }

  public function test_html_zen_element_with_classesand_class_attribute () {
    $expected = '<div class="override"></div>';
    $actual = (string) html('div.alert.alert-warning', ['class'=>'override']);
    $this->assertSame($expected, $actual);
  }

  public function test_html_zen_element_with_id_and_classes () {
    $expected = '<div id="modal" class="alert alert-warning"></div>';
    $actual = (string) html('div#modal.alert.alert-warning');
    $this->assertSame($expected, $actual);
  }

  public function test_html_zen_element_with_id_and_classes_and_children () {
    $expected = '<div id="modal" class="alert alert-warning">giftofhoney</div>';
    $actual = (string) html('div#modal.alert.alert-warning', 'gift', 'of', 'honey');
    $this->assertSame($expected, $actual);
  }

}
