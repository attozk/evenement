# Événement++

This is a fork of [Événement](https://github.com/igorw/evenement) by [Igor Wiedler](https://igor.io) with advanced options.

[![Build Status](https://secure.travis-ci.org/attozk/evenement+.png?branch=master)](http://travis-ci.org/igorw/evenement)

## Fetch

The recommended way to install Événement++ is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "attozk/evenement": "1.0.*"
    }
}
```

And run these two commands to install it:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

Now you can add the autoloader, and you will have access to the library:

```php
<?php
require 'vendor/autoload.php';
```

## Usage

### Creating an Emitter

```php
<?php
$emitter = new Evenement\EventEmitter();
```

### Adding Listeners

```php
<?php
$emitter->on('user.created', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

### Emitting Events

```php
<?php
$emitter->emit('user.created', array($user));
```

Tests
-----

    $ phpunit

License
-------
MIT, see LICENSE.