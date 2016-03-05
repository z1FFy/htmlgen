<?php

use htmlgen\html as h;

$beeData = [
  'pop' => 'yup',
  'candy' => 'sometimes',
  'flowers' => 'so much',
  'water' => 'not really',
  'sand' => 'indifferent',
  'donuts' => 'most definitely'
];

return [
  h::h1('Hello from HtmlGgen'),
  h::comment('really cool and thought-provoking article'),
  h::article(
    h::h2('All about honey'),
    h::p('Did you know that bees are responsible for making honey ?'),
    h::p('It\'s a wonder more people don\'t like bees !'),
    h::table(
      h::thead(
        h::tr(
          h::td('item'),
          h::td('do bees like it?')
        )
      ),
      h::tbody(
        h::_map($beeData, function($value, $key) { return
          h::tr(
            h::td($key),
            h::td($value)
          );
        })
      )
    ),
    h::aside('Did you know that queen bees come from larvae that are overfed with royal jelly ?')
  ),
  h::comment('newsletter signup form'),
  h::form(['action'=>'#subscribe'],
    h::input(['name'=>'email', 'autofocus']),
    h::input(['type'=>'button', 'value'=>'Get Bee News !'])
  )
];
