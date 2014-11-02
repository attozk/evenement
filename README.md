# Événement-Plus

This is a fork of [Événement](https://github.com/igorw/evenement) by [Igor Wiedler](https://igor.io) with advanced options.

[![Build Status](https://secure.travis-ci.org/attozk/evenement-plus.png?branch=master)](http://travis-ci.org/attozk/evenement-plus)

## Fetch

The recommended way to install Événement-Plus is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "attozk/evenement-plus": "1.0.*"
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

Included are two types of event emitters:

* EventEmitter - the original from [Événement](https://github.com/igorw/evenement)
* EventEmitterRegex - with regex based event dispatching

### EventEmitter Usage

#### Creating an Emitter

```php
<?php
$emitter = new Evenement\EventEmitter();
```

#### Adding Listeners

```php
<?php
$emitter->on('user.created', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

#### Emitting Events

```php
<?php
$emitter->emit('user.created', array($user));
```

### EventEmitterRegex Usage

`EventEmitterRegex` uses regex to dispatching events.

#### Creating an Emitter

```php
<?php
$emitter = new Evenement\EventEmitterRegex();
```

#### Adding Listeners

Addint multiple listeners using an array:

```php
<?php
$emitter->on(['request.www.domain.com', 'request.www.example.com'], function (Request $request) use ($httpd) {
    $httpd->response(404, 'Not found.');
});
```
Above is the same as adding one listener at a time:

```php
<?php
$emitter->on('request.www.domain.com', function (Request $request) use ($httpd) {
    $httpd->response(404, 'Not found.');
});

$emitter->on('request.www.example.com', function (Request $request) use ($httpd) {
    $httpd->response(404, 'Not found.');
});
```

Adding regex listeners:

The following listeners would match `request.www.domain.\w+` and `request.example.(com|pk)` patterns

```php
<?php
$emitter->on(['request.www.domain.\w+', 'request.example.(com|pk)'], function (Request $request) use ($httpd) {
    $httpd->response(404, 'Not found.');
});
```

#### Emitting Events

```php
<?php
$emitter->emit('user.created', array($user));

// or multiple evetns at once
$emitter->emit(['user.created', 'welcome'], array($user));

// or emit using regex patterns
$emitter->emit(['request.*.pk', 'request.*.domain.pk'], array($request));
```

#### Emitting Events First Match Win

```php
<?php
$emitter->emitFirstMatch(['request.*.pk', 'request.*.domain.pk'], array($request));
```


#### Emitting Events With Default Fallback-Callback

For cases when you want to perform a default action when there are no listeners:

```php
<?php
$fallback = function() use($logger) { 
    $logger->debug(...);
};

$emitter->emit('user.created', array($user), $fallback);

// or multiple evetns at once
$emitter->emit(['user.created', 'welcome'], array($user), $fallback);

// or emit using regex patterns
$emitter->emit(['request.*.pk', 'request.*.domain.pk'], array($request), $fallback);
```


Tests
-----

    $ phpunit

License
-------
MIT, see LICENSE.
