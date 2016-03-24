<?php

namespace htmlgen\test;
use function htmlgen\capture;
use function htmlgen\html;

function captureFake($x="can't", $y="hold", $z="us") {
  printf("<strong>%s %s %s !</strong>", $x, $y, $z);
}

class CaptureTest extends \PHPUnit_Framework_TestCase {

  public function test_capture_without_a_callable_throws () {
    $this->expectException('TypeError');
    capture();
  }

  public function test_capture_callable () {
    $expected = "<strong>can't hold us !</strong>";
    $actual = (string) capture('\htmlgen\test\captureFake');
    $this->assertSame($expected, $actual);
  }

  public function test_capture_callable_with_arguments () {
    $expected = "<strong>x y z !</strong>";
    $actual = (string) capture('\htmlgen\test\captureFake', 'x', 'y', 'z');
    $this->assertSame($expected, $actual);
  }

  public function test_capture_with_lambda () {
    $expected = "i <3 honey bees";
    $actual = (string) capture(function() { print('i <3 honey bees'); });
    $this->assertSame($expected, $actual);
  }

  public function test_capture_with_lambda_and_arguments () {
    $expected = "i <3 honey bees";
    $actual = (string) capture(function($x) { printf('i <3 %s', $x); }, 'honey bees');
    $this->assertSame($expected, $actual);
  }

  public function test_html_expects_raw_output_from_capture () {
    $expected = "<p>i <3 honey bees</p>";
    $actual = (string) html('p', capture(function() { print('i <3 honey bees'); }));
    $this->assertSame($expected, $actual);
  }

}
