<?php

namespace htmlgen;
use function htmlgen\element\renderElement;
use function htmlgen\element\renderAttributes;
use function htmlgen\element\renderChildren;
use function htmlgen\element\build;
use function htmlgen\util\array_kmap;
use function htmlgen\util\is_assoc;
use htmlgen\util\RawString;

function render(/*resource*/ $stream, ...$children): int {
  return \fwrite($stream, renderChildren($children));
}

function html(string $zen, ...$children): RawString {
  if (\count($children) > 0 && is_assoc($children[0]))
    return raw(renderElement(build($zen, $children[0], \array_slice($children, 1))));
  else
    return raw(renderElement(build($zen, [], $children)));
}

function map(array $xs, callable $f): array {
  return array_kmap(function($k, $v) use ($f) {
    return \call_user_func($f, $v, $k);
  }, $xs);
}

function raw(string $str): RawString {
  return new RawString($str);
}

function capture(callable $f, ...$xs): RawString {
  \ob_start(); \call_user_func_array($f, $xs); return raw(\ob_get_clean());
}

namespace htmlgen\util;

function is_assoc($xs): bool {
  return \is_array($xs) && array_some('is_string', \array_keys($xs));
}

function array_kmap(callable $f, array $xs): array {
  return \array_map(function ($k, $v) use ($f) {
    return \call_user_func($f, $k, $v);
  }, \array_keys($xs), \array_values($xs));
}

function array_some(callable $f, array $xs): bool {
  foreach ($xs as $x)
    if (\call_user_func($f, $x) === true)
      return true;
  return false;
}

class RawString {
  public function __construct(string $x) { $this->value = $x; }
  public function __toString(): string { return $this->value; }
}

namespace htmlgen\zen;

function parse(string $zen) {
  if (strlen($zen) === 0)
      throw new \InvalidArgumentException('htmlgen\html expects a non-empty string');
  else
    return \htmlgen\element\element(parseTag($zen), [
      'id' => parseId($zen),
      'class' => parseClass($zen)
    ]);
}

function parseTag(string $zen): string {
  return safe_preg_scan(['div'], '/^[\w-]+/', $zen)[0];
}

function parseId(string $zen): string {
  return safe_preg_scan([''], '/(?<=#)[\w:-]+/', $zen)[0];
}

function parseClass(string $zen): string {
  return \join(' ', safe_preg_scan([''], '/(?<=\.)[\w-]+/', $zen));
}

function safe_preg_scan(array $default, string $re, string $x): array {
  if (\preg_match_all($re, $x, $y) === 0)
    return $default;
  else
    return $y[0];
}

namespace htmlgen\element;
use function htmlgen\util\array_kmap;
use function htmlgen\zen\parse;

const VOID_ELEMENTS = [
  'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input',
  'link', 'meta', 'param', 'source', 'track', 'wbr'
];

function element(string $tag, array $attributes) {
  return [
    'tag' => $tag,
    'attributes' => array_merge(
      ['children' =>[]],
      $attributes
    )
  ];
}

function tag($element): string {
  return $element['tag'];
}

function attributes($element): array {
  return $element['attributes'];
}

function children($element): array {
  return attributes($element)['children'];
}

function build(string $zen, array $attributes, array $children) {
  if (\count($children) > 0)
    return updateAttributes(parse($zen), \array_merge($attributes, ['children' => $children]));
  else
    return updateAttributes(parse($zen), $attributes);
}

function hasOverride($element): bool {
  return function_exists(override($element));
}

function isVoid($element) {
  return \in_array(tag($element), VOID_ELEMENTS);
}

function override($element): string {
  return sprintf('\htmlgen\elements\%s', tag($element));
}

function renderElement($element): string {
  if (hasOverride($element))
    return call_user_func(override($element), attributes($element), children($element));
  elseif (isVoid($element))
    return toString($element, '<%s%s>');
  else
    return toString($element, '<%1$s%2$s>%3$s</%1$s>');
}

function renderAttributes(array $attributes): string {
  return \join('', array_kmap(function($attribute, $value) {
    return renderAttribute($attribute, $value);
  }, $attributes));
}

function renderAttribute($attribute, $value): string {
  if ($attribute === 'children')        // ['children'=>...], skip
    return '';
  elseif ($value === '')                // ['attribute'=>''], skip
    return '';
  elseif ($value === false)             // ['disabled'=>false], skip
    return '';
  elseif ($value === true)              // ['disabled'=>true], void attribute
    return sprintf(' %s', $attribute);
  elseif (is_int($attribute))           // ['disabled'], void attribute
    return sprintf(' %s', $value);
  else                                  // ['attribute'=>'value'], normal
    return \sprintf(' %s="%s"', $attribute, $value);
}

function renderChildren($child): string {
  if (\is_string($child))
    return htmlentities($child, ENT_HTML5);
  elseif (\is_array($child))
    return \join('', \array_map(function($node) {
      return renderChildren($node);
    }, $child));
  else
    return $child;
}

function toString($element, string $template): string {
  return \sprintf($template, tag($element), renderAttributes(attributes($element)), renderChildren(children($element)));
}

function updateAttributes($element, array $attributes) {
  return element(
    tag($element),
    array_merge(attributes($element), $attributes)
  );
}

namespace htmlgen\elements;
use function htmlgen\element\renderAttributes;
use function htmlgen\element\renderChildren;

function doctype(array $attributes, array $children): string {
  return '<!doctype html>';
}

function comment(array $attributes, array $children): string {
  return \sprintf('<!-- %s -->', renderChildren($children));
}
