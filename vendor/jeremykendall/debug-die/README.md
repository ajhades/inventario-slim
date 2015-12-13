# Debug Helper

Debug Helper is a set of convenience functions that wrap `var_dump()` and
`die(var_dump())`. Because lazy.

## Installation

Use [Composer][3] to install Debug Helper.

```
{
    "require": {
        "jeremykendall/debug-die": "*"
    }
}
```

## d() and dd()

`d()` and `dd()` wrap `var_dump()` and `die(var_dump())`, respectively. Both
functions use the same method signature as [`var_dump()`][4], meaning you can
pass more than one argument to the functions and each argument will be dumped
individually.

`void d ( mixed $expression [, mixed $... ] )`
`void dd ( mixed $expression [, mixed $... ] )`

An example usage is available in [example.php](example.php).

```php
<?php

require_once './vendor/autoload.php';

$one = array('one', 'two', 'three');
$two = 'String argument';

d('debug', $one, $two);

dd('debug and die', $one, $two);

echo "You'll never see me.";
```

The example can be executed from the command line:

```term
$ php -f example.php
string(5) "debug"
array(3) {
    [0] =>
        string(3) "one"
        [1] =>
        string(3) "two"
        [2] =>
        string(5) "three"
}
string(15) "String argument"
string(13) "debug and die"
array(3) {
    [0] =>
        string(3) "one"
        [1] =>
        string(3) "two"
        [2] =>
        string(5) "three"
}
string(15) "String argument"
```

## Recommendation

![Do you even Xdebug?][meme]

In my opinion, `var_dump()` is best used in concert with [Xdebug][1]. You
should be using Xdebug in development anyhow, so go [install it][2] now. NOW.

[1]: http://xdebug.org/
[2]: http://xdebug.org/docs/install
[3]: http://getcomposer.org
[4]: http://php.net/var_dump
[meme]: http://i.imgur.com/i7bPKxG.png
