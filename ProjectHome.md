Leolos is a simple, easy and thin framework for web sites written in PHP5.

You need to add a few rewrite conditions to your apache configuration:
```
# mod_rewrite
RewriteEngine On
RewriteBase /

## everything except files point to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]

```

Simple example of index.php:
```

// including leolos publisher with dispatcher
require_once "leolos/publisher.php";

// our first function handler
function helloScreen($request) {

    echo "Hello world!";
    return Leolos\Status\Status::OK();
}


$publisher = new Leolos\Dispatcher();
$publisher->addHandler(new Leolos\FunctionHandler("/hello-world", "helloScreen", "GET", array(), False));

$publisher->handleRequest();
```