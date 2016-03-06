htmlgen
=======

A lightweight DSL for HTML generation in PHP

What's new in 2.0 ?
-------------------

A list of bullet points for people that like bulleted lists:

* Real name, no gimmicks
* All the magic is gone
* It's just pure PHP with no funny business.
* Templating via native PHP `require` (see the provided example)
* Syntax is roughly the same, but much nicer
* Code base is 60% smaller
* Unit tests
* No more auto-echoing. Use `h` to send html into other functions
* Output buffering is gone
* View helpers like `h::cycle` are gone
* Data store is no longer necessary. `set_variable` and `get_variable` are gone
* Pretty printing is out (at least for now)

Closures are out. And oh how sweet, too.

```php
// version 1.0
// barf. cough. spew !
h::html(function(){
  h::head(function(){
    h::meta(array("charset"=>"UTF-8"));
    h::link(array("rel"=>"stylesheet", "type"=>"text/css", "href"=>"global.css"));
  });
  h::body(function(){ ...
```

So much better now...

```php
// version 2.0
// cuddle. swoon.
h('html',
  h('head',
    h('meta', ['charset'=>'UTF-8']),
    h('link', ['rel'=>'stylesheet', 'type'=>'text/css', 'href'=>'global.css'])
  })
  h('body',  ...
```

Remember how hard it was to get data into the view too?

```php
// version 1.0
// GAG. BLECH. RETCH.
h::table(function(){
  # sadly, i'm not sure how to get around this at the moment :(  help me make this awesome
  global $table_data;

  h::tr(array("class"=>"header"), function(){
    h::th("key");
    h::th("value");
  });
  foreach($table_data as $k => $v){
    h::tr(array("class"=>h::cycle(array("odd", "even"))), function() use($k,$v){
      h::td($k);
      h::td($v);
    });
  }
});
```

So much better now...

```php
// version 2.0
// embrace. kiss.
h('table',
  h('tr', ['class'=>'header'],
    h('th', 'key'),
    h('th', 'value')
  ),
  map($table_data, function ($v,$k) { return
    h('tr',
      h('td', $k),
      h('td', $v)
    )
  })
);
```

Requirements
------------

Sorry, but it requires `PHP >= 7` right now. The only real thing causing this is
the type hinting right now. I'm going to make a `PHP 5.4` version too.

API
---

**h** **(** `string` <_tag_> [, `assoc` <_$attributes_>], `mixed` <..._$children_> **)** : `RawString` &mdash;
main constructor. more docs on this coming soon.

**map** **(** `array` _$xs_ **,** `callable` {(_$v_, _$k_): `string`} **):** `array` &mdash;
this exists because `array_map` doesn't pass in array keys by default. this is very helpful.

**render** **(** `mixed` <..._$children_> **)** : `void` &mdash;
this is probably not even a necessary function. it's just nicer to use this in
the root template instead of having to `echo` once for the doctype and once for
the `<html>` root node.

**raw** **(** `string` <_$html_>**):** `RawString` &mdash;
all child strings passed to `h` will automatically have html entities encoded
using `htmlentities($str, ENT_HTML5)`. If you would like to bypass encoding, you
can wrap a string using this function.

WARNINGS
--------

* The default behavior will be display **html entities only**. A special string
  wrapper is required to output raw HTML strings. Guess what that helper is
  called? It's called `raw`. I already said that above. C'mon.

Code example
------------

**example/index.js**

```php
require '../htmlgen.php';

use function htmlgen\html as h;
use function htmlgen\render;

render(
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
```

**example/navigation.php**

```php
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
```

**example/body.php**

```php
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
```

**example/footer.php**

```php
use function htmlgen\html as h;

// notice "&" will automatically be converted to "&amp;"
// this behavior protects you from malicious user input
return h('p', 'Thanks for your interest in cats & donuts and stuff !');
```

Output
------

```html
<!DOCTYPE html><html><head><meta charset="UTF-8"><link rel="stylesheet"
type="text/css" href="/main.css"></head><body><header><nav><ul><li><a
href="/">home</a></li><li><a href="/cats">cats</a></li><li><a
href="/milk">milk</a></li><li><a href="/honey">honey</a></li><li><a
href="/donuts">donuts</a></li><li><a
href="/bees">bees</a></li></ul></nav></header><main><h1>Hello from
HtmlGgen</h1><!-- really cool and thought-provoking article --><article><h2>All
about honey</h2><img src="/busybeehive.png" alt="bees like to keep busy!"
width="300" height="100"><p>Did you know that bees are responsible for making
honey &quest;</p><p>It's a wonder more people don't like bees &excl;</p><p>Bees
are &gt; htmlentities</p><p>Raw honey is the
<strong>best</strong></p><table><thead><tr><td>item</td><td>do bees like it&ques
t;</td></tr></thead><tbody><tr><td>pop</td><td>yup</td></tr><tr><td>candy</td><t
d>sometimes</td></tr><tr><td>flowers</td><td>so
much</td></tr><tr><td>water</td><td>not really</td></tr><tr><td>sand</td><td>ind
ifferent</td></tr><tr><td>donuts</td><td>most
definitely</td></tr></tbody></table><aside>Did you know that queen bees come
from larvae that are overfed with royal jelly &quest;</aside></article><!--
newsletter signup form --><form action="#subscribe"><input name="email"
autofocus><input type="button" value="Get Bee News
!"></form></main><footer><p>Thanks for your interest in cats &amp; donuts and
stuff &excl;</p></footer><script src="/main.js"></script></body></html>
```

Try it and see!
---------------

```sh
$ cd htmlgen/example
$ php index.php
```

License
-------

[BSD 3-clause](https://github.com/naomik/htmlgen/blob/master/LICENSE)
