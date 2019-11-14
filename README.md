# Phore core library

[![Actions Status](https://github.com/phore/phore-core/workflows/tests/badge.svg)](https://github.com/phore/phore-core/actions)


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


### phore_random_str()

Wrapper around libsodium and 
