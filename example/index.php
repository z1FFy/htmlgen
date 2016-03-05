<?php

require '../htmlgen.php';
use htmlgen\html as h;

htmlgen\render(
  h::doctype(),
  h::html(
    h::head(
      h::meta(['charset'=>'UTF-8']),
      h::link(['rel'=>'stylesheet', 'type'=>'text/css', 'href'=>'/main.css'])
    ),
    h::body(
      h::header(
        require './navigation.php'
      ),
      h::main(
        require './body.php'
      ),
      h::footer(
        require './footer.php'
      ),
      h::script(['src'=>'/main.js'])
    )
  )
);
