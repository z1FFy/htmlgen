<?php

namespace htmlgen\elements;
use function htmlgen\element\renderAttributes;
use function htmlgen\element\renderChildren;

// Example 1
// You can define your own behavior for any given tag
//     h('condition', 'lt IE 9', h('script', ['src'=>'ie.js']))
// Will render as
//     <!--[if lt IE 9]><script src="ie.js"></script><![endif]-->
//
// Note: attributes and children are guaranteed to be arrays
// Note: renderChildren will take care of encoding html entities for you
// Note: your function must return a string
// Note: you do not have to wrap your output with `raw`
//
// Note: this specific custom element is provided as an example because you are
// likely to need it for legacy support. However, it's not a default as it's not
// part of html5 and deprecated in IE 10 and therefore not part of htmlgen core
function condition(array $attributes, array $children): string {
  return sprintf('<!--[if %s]>%s<![endif]-->', $children[0], renderChildren(\array_slice($children, 1)));
}

//
// The only two custom elements included in htmlgen core are
//     h('doctype')
//     h('comment', 'cats')
// Which will output
//     <!doctype html>
//     <!-- cats -->

//
// Example 2
// Here's a way you could override the defalut behavior for `a`
//     function a(array $attributes, array $children): string {
//       list($attributes['href'], $attributes['title']) = $children;
//       return sprintf('<a%s>%s</a>',
//         renderAttributes($attributes),
//         renderChildren(\array_slice($children, 1))
//       );
//     }
// Using it would look like this
//     h('a', '/cats', 'cats')
// Or You could still pass attributes
//     h('a', ['rel'=>'nofollow'], '/cats', 'cats');
// Outputs would be
//     <a href="/cats" title="cats">cats</a>
//     <a rel="nofollow" href="/cats" title="cats">cats</a>
//
//
// Example 3
// Or, in a more simple case, you could provide default attributes
//     function script(array $attributes, array $children): string {
//       $defaults = ['type'=>'text/javascript'];
//       return sprintf('<script%s>%s</script>',
//         renderAttributes(array_merge($defaults, $attributes)),
//         renderChildren($children)
//       );
//     }
// Then
//     h('script', ['src'=>'cats.js'])
// Would output
//     <script type="text/javascript" src="cats.js"></script>
//
