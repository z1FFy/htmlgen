htmlgen
=======

A lightweight DSL for HTML generation in PHP

What's new in 2.0 ?
-------------------

* All the magic is gone
* Version 1 API is completely obsolete
* No more auto-echoing
* No more output buffering
* No more closures for child elements
* No more explicit data stores/fetches
* No more pretty printing (for now, at least)
* Code base is 25% smaller
* Unit tests
* Namespaces
* Safely encodes all text nodes with `htmlentities`
* Achieve "templating" via native PHP `require` (see the provided example)
* User-defined elements
* Lite support for _Zen_-like elements

Remember all of the closures ?

```php
// version 1.0 required closures for all children ...
h::html(function(){
  h::head(function(){
    h::meta(array("charset"=>"UTF-8"));
    h::link(array("rel"=>"stylesheet", "type"=>"text/css", "href"=>"global.css"));
  });
  h::body(function(){ ...

// version 2.0 variadic interface allows passing as many children as you want
h('html',
  h('head',
    h('meta', ['charset'=>'UTF-8']),
    h('link', ['rel'=>'stylesheet', 'type'=>'text/css', 'href'=>'global.css'])
  })
  h('body',  ...
```

Remember how hard it was to thread data to the children ?

```php
// version 1.0 required `global` unless `use` was specified on every closure
h::table(function(){
  // icky global state
  global $table_data;

  h::tr(array("class"=>"header"), function(){
    h::th("key");
    h::th("value");
  });
  foreach($table_data as $k => $v){
    // verbose function use($k,$v) ...
    h::tr(array("class"=>h::cycle(array("odd", "even"))), function() use($k,$v){
      h::td($k);
      h::td($v);
    });
  }
});

// version 2.0 has no problem accessing data in child nodes
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

Remember how specifying `id` and `class` for everything was so annoying?

```php
h('#sidebar.column',
  h('.section',
    h('h2.section-title', 'Cat Links'),
    h('button.btn.btn-large', 'Adopt a cat now !')
  )
);
```

Will now render

```html
<div id="sidebar" class="column">
  <div class="section">
    <h2 class="section-title">Cat Links</h2>
    <button class="btn btn-large">Adopt a cat now &excl;</button>
  </div>
</div>
```

Requirements
------------

Sorry, but it requires `PHP >= 7` right now. The only real thing causing this is
the type hinting right now. I'll eventually get around to making a `PHP 5.x`
version. Eventually. Probably.

API
---

**html** **(** `string` _tag_ [**,** `assoc` _attributes_]**,** `mixed` **...**_children_ **)** : `RawString`

This is your bread and butter. See the exaples for more help. Reminder:
`htmlgen\RawString` is an implementation detail and should be ignored. Never
write tests against this type or check `$html instanceof RawString`. Just know
that `RawString` can be coerced to a `string` so just treat it as such.

---

**map** **(** `array` _xs_ **,** `callable` **λ(**`mixed` _v_ **,** `mixed` _k_ **):** `string` **):** `array`

This function is very helpful for building lists of nodes from existing data.
This exists because `array_map` doesn't pass in array keys by default. Also,
note the order of arguments in this function compared to native `array_map`.
See the code examples below for more details.

---

**raw** **(** `string` _html_ **):** `RawString`

All child strings passed to `html` will automatically have html entities encoded
using `htmlentities($str, ENT_HTML5)`. If you would like to bypass encoding, you
can wrap a string using this function.

---

**render** **(** `resource` _writableStream_ **,** `mixed` **...**_children_ **)** : `int`

Most people will probably just use `echo html(...)` which is fine, but `render`
is a bit more flexible as it allows you to render to any writable stream. That
means `render(STDOUT, html(...))` is effectively the same as `echo html(...)`.
Use this for writing html to files `render($fd, ...)` or to memory
`$mem = fopen('php://memory'); render($mem, ...)`, or skip `render` altogether
and just `$html = html(...); doSomething($html);` It's PHP, you can figure it
out.

**capture** **(** `callable` _f_[**,** **...**_xs_] **)** : `RawString`

Use this when you have an unmaneuverable, unwieldy, clumsy, impure function such
as 100% of the functions found within WordPress. This will conveniently hijack
any function that otherwise writes to `STDOUT` and instead bottles it up in
`string`. `capture('the_title')` will return a string instead of echoing. If you
need to pass arguments, you can `capture('the_title', $postId)` or you can even
use a lambda, `capture(function($id) { the_title($id); }, $postId)` or
`capture(function() use($postId) { the_title($postId); })`. Remember, how I said
it's just PHP ? It's just PHP. And yes I know `get_the_title` exists. This is
an example.

Oh yeah, it should go without saying that `capture` is not a magician or a
mind-reader. It wil **not** automatically encode HTML entities returned from the
callable. It can (and does) promise not to double encode HTML entities, tho.

WARNINGS
--------

* htmlgen displays **html entities only**. A special string wrapper is required
  to output raw HTML strings. Guess what that helper is called? It's called
  `raw`. I already said that above. C'mon.

Code example
------------

**example/index.js**

```php
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

Line wrapping (actual output does not contain line breaks)

```html
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta
http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport"
content="width=device-width, initial-scale=1"><link rel="stylesheet"
type="text/css" href="/main.css"><!--[if lt IE 9]><script src="ie.js"></script>
<![endif]--></head><body><header><nav><ul><li><a href="/">home</a></li><li>
<a href="/cats">cats</a></li><li><a href="/milk">milk</a></li><li>
<a href="/honey">honey</a></li><li><a href="/donuts">donuts</a></li><li>
<a href="/bees">bees</a></li></ul></nav></header><main><h1>Hello from HtmlGgen
</h1><!-- really cool and thought-provoking article --><article><h2>
All about honey</h2><img src="/busybeehive.png" alt="bees like to keep busy!"
width="300" height="100"><p>Did you know that bees are responsible for making
honey &quest;</p><p>It's a wonder more people don't like bees &excl;</p><p>Bees
are &gt; htmlentities</p><p>Raw honey is the <strong>best</strong></p><table>
<thead><tr><td>item</td><td>do bees like it&quest;</td></tr></thead><tbody><tr>
<td>pop</td><td>yup</td></tr><tr><td>candy</td><td>sometimes</td></tr><tr><td>
flowers</td><td>so much</td></tr><tr><td>water</td><td>not really</td></tr><tr>
<td>sand</td><td>indifferent</td></tr><tr><td>donuts</td><td>most definitely
</td></tr></tbody></table><aside>Did you know that queen bees come from larvae
that are overfed with royal jelly &quest;</aside></article><!-- newsletter
signup form --><form action="#subscribe"><input name="email" autofocus><input
type="button" value="Get Bee News !"></form></main><footer><p>Thanks for your
interest in cats &amp; donuts and stuff &excl;</p></footer><script
src="/main.js"></script></body></html>
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
