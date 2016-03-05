<?php

use function htmlgen\html as h;
use function htmlgen\map as map;

$links = [
  'home' => '/',
  'cats' => '/cats',
  'milk' => '/milk',
  'honey' => '/honey',
  'donuts' => '/donuts',
  'bees' => '/bees'
];

return h('nav',
  h('ul',
    map($links, function($href, $text) { return
      h('li',
        h('a', ['href'=>$href], $text)
      );
    })
  )
);
