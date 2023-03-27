# Deliverist\Builder

[![Build Status](https://github.com/deliverist/builder/workflows/Build/badge.svg)](https://github.com/deliverist/builder/actions)
[![Downloads this Month](https://img.shields.io/packagist/dm/deliverist/builder.svg)](https://packagist.org/packages/deliverist/builder)
[![Latest Stable Version](https://poser.pugx.org/deliverist/builder/v/stable)](https://github.com/deliverist/builder/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/deliverist/builder/blob/master/license.md)

<a href="https://www.janpecha.cz/donate/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>

## Installation

[Download a latest package](https://github.com/deliverist/builder/releases) or use [Composer](http://getcomposer.org/):

```
composer require deliverist/builder
```

`Deliverist\Builder` requires PHP 5.6.0 or later.

## Usage

``` php
<?php

use Deliverist\Builder\Builder;
use Deliverist\Builder\Commands;
use Deliverist\Builder\Loggers;

$builder = new Builder('/path/to/source/code', [
	'composer-install' => new Commands\ComposerInstall,
	'rename' => new Commands\Rename,
	'remove' => new Commands\Remove,
], new Loggers\TextLogger);

$builder->make('composer-install')
	->make('rename', ['from' => 'index.php', 'to' => 'www/index.php'])
	->make('remove', ['file' => 'composer.lock'])
	->make('composer-install');

```


## Commands

### ApacheImports

Expands clause `<!--#include file="file.txt" -->` in specified files.

``` php
$builder->make('apache-imports', ['file' => 'file-to-expand.txt']);
$builder->make('apache-imports', ['files' => [
	'admin.js',
	'front.js',
]]);
```


### ComposerInstall

Runs `composer install` in `composer.json` directory.

``` php
$builder->make('composer-install');
$builder->make('composer-install', ['composerFile' => 'path/to/composer.json']);
```


### Copy

Copies specified files.

``` php
$builder->make('copy', ['from' => 'old.txt', 'to' => 'new.txt']);
$builder->make('copy', ['files' => [
	'old.txt' => 'new.txt',
]]);
$builder->make('copy', ['paths' => [
	'old/dir' => 'new/dir',
]]);
```


### CreateDirectory

Creates specified directories.

``` php
$builder->make('create-directory', ['directory' => 'new-directory']);
$builder->make('create-directory', ['directories' => [
	'new-directory',
	'new-directory-2',
]]);
```


### CssExpandImports

Expands clause `@import 'file.css'` in specified files.

``` php
$builder->make('css-expand-imports', ['file' => 'file-to-expand.txt']);
$builder->make('css-expand-imports', ['files' => [
	'admin.css',
	'front.css',
]]);
```


### GoogleAnalytics

Replaces placeholder with Google Analytics script in file.

``` php
$builder->make('google-analytics', [
	'file' => 'path/to/file.php',
	'code' => 'UA-9876-5',
	'placeholder' => '%% GA %%',
]); // replaces placeholder '%% GA %%' in file
$builder->make('google-analytics', ['file' => 'path/to/file.html', 'code' => 'UA-9876-5']); // uses placeholder '<!-- GA -->' in file
$builder->make('google-analytics', ['file' => 'path/to/file.latte', 'code' => 'UA-9876-5']); // uses placeholder {* GA *} in file
```


### GoogleClosureCompiler

Minifies files in online Google Closure Compiler.

``` php
$builder->make('google-closure-compiler', ['file' => 'script.js']);
$builder->make('google-closure-compiler', ['files' => [
	'script-1.js',
	'script-2.js',
]]);
```


### LessCompile

Runs `lessc` for compiling of LESS files.

``` php
$builder->make('less-compile', ['file' => 'styles.less']);
$builder->make('less-compile', ['files' => [
	'style-1.less',
	'style-2.less',
]]);
```


### MinifyContent

Removes empty lines & whitespaces on start & end of lines.

``` php
$builder->make('minify-content', ['file' => 'file.txt']);
$builder->make('minify-content', ['files' => [
	'file-1.txt',
	'file-2.txt',
]]);
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
$builder->make('ping-url', ['url' => 'https://example.com/migrations.php']);
$builder->make('ping-url', ['url' => 'https://example.com/migrations.php', 'validateSsl' => FALSE]); // disable SSL validation
```


### Remove

Removes file or directory.

``` php
$builder->make('remove', ['file' => 'path/to/file.txt']);
$builder->make('remove', ['path' => 'path/to/directory']);
$builder->make('remove', ['files' => [
	'path/to/file.txt',
	'path/to/directory',
]]);
$builder->make('remove', ['paths' => [
	'path/to/file.txt',
	'path/to/directory',
]]);
```


### Rename

Renames file or directory.

``` php
$builder->make('rename', ['from' => 'old.txt', 'to' => 'new.txt']);
$builder->make('rename', ['files' => [
	'old.txt' => 'new.txt',
]]);
```


### ReplaceContent

Replaces content in file.

``` php
$builder->make('replace-content', [
	'file' => 'file.txt',
	'replacements' => [
		'from' => 'to',
		'old string' => 'new string',
	],
]);
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
