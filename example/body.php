<?php

use function htmlgen\html as h;
use function htmlgen\map;
use function htmlgen\raw;

$beeData = [
  'pop' => 'yup',
  'candy' => 'sometimes',
  'flowers' => 'so much',
  'water' => 'not really',
  'sand' => 'indifferent',
  'donuts' => 'most definitely'
];

return [
  h('h1', 'Hello from HtmlGgen'),
  h('comment', 'really cool and thought-provoking article'),
  h('article',
    h('h2', 'All about honey'),
    h('img', ['src'=>'/busybeehive.png', 'alt'=>'bees like to keep busy!', 'width'=>300, 'height'=>100]),
    h('p', 'Did you know that bees are responsible for making honey ?'),
    h('p', 'It\'s a wonder more people don\'t like bees !'),
    h('p', 'Bees are > htmlentities'),
    // if you really must output HTML, you can use the `raw` utility
    h('p', raw('Raw honey is the <strong>best</strong>')),
    h('table',
      h('thead',
        h('tr',
          h('td', 'item'),
          h('td', 'do bees like it?')
        )
      ),
      h('tbody',
        map($beeData, function($value, $key) { return
          h('tr',
            h('td', $key),
            h('td', $value)
          );
        })
      )
    ),
    h('aside', 'Did you know that queen bees come from larvae that are overfed with royal jelly ?')
  ),
  h('comment', 'newsletter signup form'),
  h('form', ['action'=>'#subscribe'],
    h('input', ['name'=>'email', 'autofocus']),
    h('input', ['type'=>'button', 'value'=>'Get Bee News !'])
  )
];
