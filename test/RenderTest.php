<?php

namespace htmlgen\test;
use function htmlgen\html;
use function htmlgen\raw;
use function htmlgen\render;

function capture(callable $f): array {
  $stream = fopen('php://memory', 'r+');
  $return =  call_user_func($f, $stream);
  rewind($stream);
  $stdout = stream_get_contents($stream);
  fclose($stream);
  return [
    'return' => $return,
    'stdout' => $stdout
  ];
}

class RenderTest extends \PHPUnit_Framework_TestCase {

  public function test_render_returns_fwrites_int () {
    $actual = capture(function ($stream) { return render($stream, 'bees make honey'); });
    $this->assertSame(15, $actual['return']);
  }

  public function test_render_empty_does_nothing () {
    $actual = capture(function ($stream) { render($stream); });
    $expected = '';
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_will_echo () {
    $actual = capture(function ($stream) { render($stream, 'cats like milk'); });
    $expected = 'cats like milk';
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_multiple_children () {
    $expected = 'beeshoneycandy';
    $actual = capture(function ($stream) {
      render($stream, 'bees', 'honey', 'candy');
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_automatically_encodes_html_entities () {
    $expected = 'bees &amp; honey';
    $actual = capture(function ($stream) {
      render($stream, 'bees & honey');
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_will_not_double_encode_raw_strings () {
    $expected = '<strong>bees &amp; honey</strong>';
    $actual = capture(function ($stream) {
      render($stream, raw('<strong>'), 'bees & honey', raw('</strong>'));
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_element_for_good_measure_even_though_its_just_a_raw_string_and_we_already_tested_that_lol () {
    $expected = '<a href="/bees">You won\'t believe these 10 weird facts about bees &excl;</a>';
    $actual = capture(function ($stream) {
      render($stream, html('a', ['href'=>'/bees'], "You won't believe these 10 weird facts about bees !"));
    });
    $this->assertSame($expected, $actual['stdout']);
  }

  public function test_render_deeply_nested_arrays_of_children () {
    $expected = '<nonsense>catmousewolfsheepchickenrooster</nonsense>';
    $actual = capture(function ($stream) {
      render($stream, html('nonsense', ['cat', 'mouse', ['wolf', 'sheep', ['chicken', 'rooster']]]));
    });
    $this->assertSame($expected, $actual['stdout']);
  }

}
