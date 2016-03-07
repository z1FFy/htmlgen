<?php

require '../htmlgen.php';

use function htmlgen\html as h;
use function htmlgen\render;

render(STDOUT,
  h('doctype'),
  h('html',
    h('head',
      h('meta', ['charset'=>'UTF-8']),
      h('link', ['rel'=>'stylesheet', 'type'=>'text/css', 'href'=>'/main.css'])
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
