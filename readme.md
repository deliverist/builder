
# Deliverist\Builder

## Installation

[Download a latest package](https://github.com/deliverist/builder/releases) or use [Composer](http://getcomposer.org/):

```
composer require deliverist/builder
```

`Deliverist\Builder` requires PHP 5.4.0 or later.

## Usage

``` php
<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;

$builder = new Builder('/path/to/source/code', array(
	'composer-install' => new Commands\ComposerInstall,
	'rename' => new Commands\Rename,
	'remove' => new Commands\Remove,
));

$builder->onLog[] = function ($message, $type) {
	echo $message, "\n";
};

$builder->make('composer-install')
	->make('rename', 'index.php', 'www/index.php')
	->make('remove', array(
		'composer.lock',
	))
	->make('composer-install');

```


## Commands

### BowerInstall

Runs `bower install` in `bower.json` directory.

``` php
$builder->make('bower-install');
$builder->make('bower-install', 'path/to/bower.json');
```


### ComposerInstall

Runs `composer install` in `composer.json` directory.

``` php
$builder->make('composer-install');
$builder->make('composer-install', 'path/to/composer.json');
```


### GoogleAnalytics

Replaces placeholder with Google Analytics script in file.

``` php
$builder->make('google-analytics', 'path/to/file.php', 'UA-9876-5', '%% GA %%'); // replaces placeholder '%% GA %%' in file
$builder->make('google-analytics', 'path/to/file.html', 'UA-9876-5'); // uses placeholder '<!-- GA -->' in file
$builder->make('google-analytics', 'path/to/file.latte', 'UA-9876-5'); // uses placeholder {* GA *} in file
```


### Remove

Removes file or directory.

``` php
$builder->make('remove', 'path/to/file.txt');
$builder->make('remove', 'path/to/directory');
$builder->make('remove', array(
	'path/to/file.txt',
	'path/to/directory',
));
```

### Rename

Renames file or directory.

``` php
$builder->make('rename', 'old.txt', 'new.txt');
$builder->make('rename', array(
	'old.txt' => 'new.txt',
));
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
