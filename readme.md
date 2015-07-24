# Larinterface

[![Build Status](https://travis-ci.org/bsharp/Larinterface.svg)](https://travis-ci.org/bsharp/Larinterface)
[![Latest Stable Version](https://poser.pugx.org/bsharp/larinterface/v/stable)](https://packagist.org/packages/bsharp/larinterface)
[![Total Downloads](https://poser.pugx.org/bsharp/larinterface/downloads)](https://packagist.org/packages/bsharp/larinterface)
[![License](https://poser.pugx.org/bsharp/larinterface/license)](https://packagist.org/packages/bsharp/larinterface)

## Installation

#### Composer

To install Larinterface as a Composer package to be used with Laravel 5.*, simply add this line to your composer.json:

```
  "Bsharp/larinterface": "dev-master"
```

### Publish

To add larinterface config file to your app, use the `vendor:publish` artisan command

```
  php artisan vendor:publish
```

### Configuration

Open the generated `config/larinterface.php` file and change the configuration to fits your needs.

### Execution

Now Larinterface should work using the command:

```
  php artisan larinterface:make
```

But executing this command each time after modifying one of your PHP Class is annoying.
You can create a gulp watcher to do that for you, add this lines to your gulpfile:

```javascript

    var gulp = require('gulp');
    var exec = require('gulp-exec');

    var files = [
        'app/Models/Repositories/*.php'
    ];

    var locked = false;

    gulp.watch(files, ['larinterface:start']);

    gulp.task('larinterface:start', function () {

    if (locked === true) {
        return;
    }

    locked = true;

    return gulp.src('').pipe(exec('php artisan larinterface:make'))
        .pipe(exec.reporter({}))
        .on('end', function () {
            setTimeout(function () {
                locked = false;
            }, 1000);
        });
    });
```
