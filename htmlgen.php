<?php

namespace htmlgen;

const VOID_ELEMENTS = [
  'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input',
  'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
];

// PUBLIC API
function render(...$children) {
  echo renderChildren($children);
}

function html(string $tag, ...$children): RawString {
  if (\count($children) > 0 && is_assoc($children[0]))
    return raw(_renderElement($tag, $children[0], \array_slice($children, 1)));
  else
    return raw(_renderElement($tag, [], $children));
}

function map(array $xs, callable $f): array {
  return array_kmap(function($k, $v) use ($f) {
    return \call_user_func($f, $v, $k);
  }, $xs);
}

function raw(string $str): RawString {
  return new RawString($str);
}

function renderAttributes(array $attributes): string {
  if (\count($attributes) === 0) return '';
  return \sprintf(' %s', \join(' ', array_kmap('\htmlgen\_renderAttribute', $attributes)));
}

function renderChildren($child): string {
  if (\is_string($child))
    return htmlentities($child, ENT_HTML5);
  elseif (\is_array($child))
    return raw(\join('', \array_map('\htmlgen\renderChildren', $child)));
  else
    return raw((string) $child);
}

// UTILITIES
function isVoidElement(string $tag): bool {
  return \in_array($tag, VOID_ELEMENTS);
}

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

// PRIVATE API
class RawString {
  public function __construct(string $x) { $this->value = $x; }
  public function __toString(): string { return $this->value; }
}

function _renderElement(string $tag, array $attributes, array $children): string {
  if (function_exists("\\htmlgen\\elements\\{$tag}"))
    return \call_user_func("\\htmlgen\\elements\\{$tag}", $attributes, $children);
  elseif (isVoidElement($tag))
    return _renderVoidElement($tag, renderAttributes($attributes));
  else
    return _renderFullElement($tag, renderAttributes($attributes), renderChildren($children));
}

function _renderFullElement(string $tag, string $attributes, string $children): string {
  return \sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $attributes, $children);
}

function _renderVoidElement(string $tag, string $attributes): string {
  return \sprintf('<%s%s>', $tag, $attributes);
}

function _renderAttribute($attribute, $value): string {
  if (is_int($attribute))
    return (string) $value;
  else
    return \sprintf('%s="%s"', $attribute, $value);
}

namespace htmlgen\elements;

function doctype(array $attributes, array $children): string {
  return '<!DOCTYPE html>';
}

function comment(array $attributes, array $children): string {
  return \sprintf('<!-- %s -->', \htmlgen\renderChildren($children));
}
