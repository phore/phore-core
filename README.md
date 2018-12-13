# Phore core library

## Install

```
composer requre phore/core
```

## Basic usage


### Pluck

```php
$data = ["some"=>["path"=>"data"]];

assert( "data" === phore_pluck("some.path", $data) );

assert( "data" === phore_pluck(["some", "path"], $data) );

assert( "fail" === phore_pluck("unknown", $data, "fail") );
phore_pluck("unknown", $data, new InvalidArgumentException("path missing"));
```


### Transform Array

```php

$input = ["a", "remove"];


$out = phore_array_transform($input, function ($key, $value) {
    if ($key == "remove")
        return null;
    return ["x"=>"y"];
});

assert([["x"=>"y"]] == $out);


```


### Text functions

- `phore_text_unindent(string $input)`
