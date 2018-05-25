
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

### ApacheImports

Expands clause `<!--#include file="file.txt" -->` in specified files.

``` php
$builder->make('apache-imports', 'file-to-expand.txt');
$builder->make('apache-imports', array(
	'admin.js',
	'front.js',
));
```


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


### Copy

Copies specified files.

``` php
$builder->make('copy', 'old.txt', 'new.txt');
$builder->make('copy', array(
	'old.txt' => 'new.txt',
));
```


### CreateDirectory

Creates specified directories.

``` php
$builder->make('create-directory', 'new-directory');
$builder->make('create-directory', array(
	'new-directory',
	'new-directory-2',
));
```


### GoogleAnalytics

Replaces placeholder with Google Analytics script in file.

``` php
$builder->make('google-analytics', 'path/to/file.php', 'UA-9876-5', '%% GA %%'); // replaces placeholder '%% GA %%' in file
$builder->make('google-analytics', 'path/to/file.html', 'UA-9876-5'); // uses placeholder '<!-- GA -->' in file
$builder->make('google-analytics', 'path/to/file.latte', 'UA-9876-5'); // uses placeholder {* GA *} in file
```


### GoogleClosureCompiler

Minifies files in online Google Closure Compiler.

``` php
$builder->make('google-closure-compiler', 'script.js');
$builder->make('google-closure-compiler', array(
	'script-1.js',
	'script-2.js',
));
```


### LessCompile

Runs `lessc` for compiling of LESS files.

``` php
$builder->make('less-compile', 'styles.less');
$builder->make('less-compile', array(
	'style-1.less',
	'style-2.less',
));
```


### MinifyContent

Removes empty lines & whitespaces on start & end of lines.

``` php
$builder->make('minify-content', 'file.txt');
$builder->make('minify-content', array(
	'file-1.txt',
	'file-2.txt',
));
```

**Example:**

Input:

```
{block content}
	<h1>Homepage</h1>

	<p>
		Lorem ipsum dolor sit amet.
	</p>
{/block}
```

Output:

```
{block content}
<h1>Homepage</h1>
<p>
Lorem ipsum dolor sit amet.
</p>
{/block}
```


### PingUrl

Opens URL and shows content.

``` php
$builder->make('ping-url', 'https://example.com/migrations.php');
$builder->make('ping-url', 'https://example.com/migrations.php', FALSE); // disable SSL validation
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


### ReplaceContent

Replaces content in file.

``` php
$builder->make('rename', 'file.txt', array(
	'from' => 'to',
	'old string' => 'new string',
));
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
