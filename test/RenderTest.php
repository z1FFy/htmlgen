<?php

namespace htmlgen\test;
use function \htmlgen\html;
use function \htmlgen\raw;
use function \htmlgen\render;

function capture(callable $f): array {
  ob_start();
  return [
    'return' => call_user_func($f),
    'stdout' => ob_get_clean()
  ];
}

class RenderTest extends \PHPUnit_Framework_TestCase {

  public function test_render_returns_void () {
    $actual = capture(function () { return render('bees make honey'); });
    $this->assertSame(null, $actual['return']);
  }

  public function test_render_empty_does_nothing () {
    $actual = capture(function () { render(); });
    $expected = '';
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_will_echo () {
    $actual = capture(function () { render('cats like milk'); });
    $expected = 'cats like milk';
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_multiple_children () {
    $expected = 'beeshoneycandy';
    $actual = capture(function () {
      render('bees', 'honey', 'candy');
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_automatically_encodes_html_entities () {
    $expected = 'bees &amp; honey';
    $actual = capture(function () {
      render('bees & honey');
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_will_not_double_encode_raw_strings () {
    $expected = '<strong>bees &amp; honey</strong>';
    $actual = capture(function () {
      render(raw('<strong>'), 'bees & honey', raw('</strong>'));
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_element_for_good_measure_even_though_its_just_a_raw_string_and_we_already_tested_that_lol () {
    $expected = '<a href="/bees">You won\'t believe these 10 weird facts about bees &excl;</a>';
    $actual = capture(function() {
      render(html('a', ['href'=>'/bees'], "You won't believe these 10 weird facts about bees !"));
    });
    $this->assertSame($expected, $actual['stdout']);
  }

}
