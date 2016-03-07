2.0.0
-----

* removed api: `HtmlGen::__callStatic`
* removed api: `HtmlGen::a`
* removed api: `HtmlGen::comment`
* removed api: `HtmlGen::set_indent_level`
* removed api: `HtmlGen::set_indent_pattern`
* removed api: `HtmlGen::cycle`
* removed api: `HtmlGen::reset_cycle`
* removed api: `HtmlGen::set_variable`
* removed api: `HtmlGen::get_variable`
* removed api: `Utility::array_kmap`
* removed api: `Utility::reverse_merge`
* removed api: `Utility::capture`
* removed api: `class alias h`
* added namespace: `htmlgen`
* added namespace: `htmlgen\elements`
* added namespace: `htmlgen\test`
* added api: `html(string[, array<string => string>], ...<string | array | object>): RawString`
* added api: `map(array, λ(mixed, int | string): array<string | array | object>): array<string | array | object>`
* added api: `raw(string): RawString`
* added api: `render(resource, string | array<string | object> | object): void`
