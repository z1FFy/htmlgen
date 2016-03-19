<?php

require '../htmlgen.php';
require './htmlgen.extensions.php';

use function htmlgen\html as h;
use function htmlgen\render;

render(STDOUT,
  h('doctype'),
  h('html', ['lang'=>'en'],
    h('head',
      h('meta', ['charset'=>'utf-8']),
      h('meta', ['http-equiv'=>'X-UA-Compatible', 'content'=>'IE=edge']),
      h('meta', ['name'=>'viewport', 'content'=>'width=device-width, initial-scale=1']),
      h('link', ['rel'=>'stylesheet', 'type'=>'text/css', 'href'=>'/main.css']),
      h('condition', 'lt IE 9',
        h('script', ['src'=>'ie.js'])
      )
    ),
    h('body',
      h('header',
        require './navigation.php'
      ),
      h('main',
        require './body.php'
      ),
      h('footer',
        require './footer.php'
      ),
      h('script', ['src'=>'/main.js'])
    )
  )
);
