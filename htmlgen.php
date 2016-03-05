<?php

namespace htmlgen;

function is_assoc($xs): bool {
  return is_array($xs) && array_some('is_string', array_keys($xs));
}

function array_kmap(callable $f, $xs): array {
  return array_map(function ($k) use ($f, $xs) {
    return call_user_func($f, $k, $xs[$k]);
  }, array_keys($xs));
}

function array_some(callable $f, $xs): bool {
  foreach ($xs as $x)
    if (call_user_func($f, $x) === true)
      return true;
  return false;
}

function renderVoidElement(string $tag, array $attributes): string {
  return sprintf(
    '<%s%s>',
    $tag,
    renderAttributes($attributes)
  );
}

function renderElement(string $tag, array $attributes, array $children): string {
  return sprintf(
    '<%s%s>%s</%s>',
    $tag,
    renderAttributes($attributes),
    renderChildren($children),
    $tag
  );
}

function renderAttributes(array $attributes): string {
  if (empty($attributes)) return '';
  return sprintf(' %s', join(' ', array_kmap('\htmlgen\renderAttribute', $attributes)));
}

function renderAttribute($attribute, $value): string {
  if (is_int($attribute))
    return (string) $value;
  else
    return sprintf('%s="%s"', $attribute, $value);
}

function renderChildren($child): string {
  if (is_array($child))
    return join('', array_map('\htmlgen\renderChildren', $child));
  else
    return (string) $child;
}

function render(...$children) {
  echo renderChildren($children);
}

class HtmlElement {
  private $tag;
  private $attributes;
  private $children;

  static public $voidElements = [
    'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input',
    'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
  ];

  public function __construct(string $tag, array $attributes, array $children) {
    $this->tag = $tag;
    $this->attributes = $attributes;
    $this->children = $children;
  }

  public function __toString(): string {
    if ($this->isVoidElement($this->tag))
      return renderVoidElement($this->tag, $this->attributes);
    else
      return renderElement($this->tag, $this->attributes, $this->children);
  }

  private function isVoidElement($tag) {
    return in_array($tag, self::$voidElements);
  }
}

class html {
  static public function __callStatic($tag, $args){
    if (is_assoc($args[0]))
      return new HtmlElement($tag, $args[0], array_slice($args, 1));
    else
      return new HtmlElement($tag, [], $args);
  }

  static public function doctype() {
    return '<!DOCTYPE html>';
  }

  static public function comment($text) {
    return sprintf('<!-- %s -->', $text);
  }

  static public function _map(array $xs, callable $f): array {
    return array_kmap(function($k, $v) use ($f) {
      return call_user_func($f, $v, $k);
    }, $xs);
  }
}
