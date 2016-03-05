<?php

use htmlgen\html as h;

$links = [
  'home' => '/',
  'cats' => '/cats',
  'milk' => '/milk',
  'honey' => '/honey',
  'donuts' => '/donuts',
  'bees' => '/bees'
];

return h::nav(
  h::ul(
    h::_map($links, function($text, $href) { return
      h::li(
        h::a(['href'=>$href], $text)
      );
    })
  )
);
