<?php

use function htmlgen\html as h;

// notice "&" will automatically be converted to "&amp;"
// this behavior protects you from malicious user input
return h('p', 'Thanks for your interest in cats & donuts and stuff !');
