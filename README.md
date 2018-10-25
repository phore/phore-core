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

