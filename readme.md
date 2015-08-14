# Larinterface

[![Build Status](https://travis-ci.org/bsharp/Larinterface.svg)](https://travis-ci.org/bsharp/Larinterface)
[![StyleCI](https://styleci.io/repos/39567681/shield)](https://styleci.io/repos/39567681)
[![Latest Stable Version](https://poser.pugx.org/bsharp/larinterface/v/stable)](https://packagist.org/packages/bsharp/larinterface)
[![Total Downloads](https://poser.pugx.org/bsharp/larinterface/downloads)](https://packagist.org/packages/bsharp/larinterface)
[![License](https://poser.pugx.org/bsharp/larinterface/license)](https://packagist.org/packages/bsharp/larinterface)

## Installation

Larinterface is currently in alpha version for Laravel 5.*


#### Composer

To install Larinterface using composer, run this command:

```
    composer require bsharp/larinterface
```

If your minimum project stability is to high to use Larinterface in beta, add those two lines at the end of your composer.json:

```
    "minimum-stability": "dev",
    "prefer-stable": true
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
  php artisan larinterface:generate
```

### File watcher

But executing this command each time after modifying one of your PHP Class is annoying.
You can create a gulp watcher, using elixir 3, to do that for you. Add this lines to your gulpfile:

```javascript

    var gulp = require('gulp');
    var exec = require('gulp-exec');
    var Task = elixir.Task;
    
    var files = require('./storage/app/larinterface.json');
    var locked = false;
    
    elixir.extend('larinterface', function() {
    
      new Task('larinterface_generate', function () {
    
        if(locked === true) {
          return;
        }
    
        locked = true;
    
        var task = gulp.src('').pipe(exec('php artisan larinterface:generate'));
        task.on('end', function () {
          setTimeout(function () {
            locked = false;
            files = require('./storage/app/larinterface.json');
          }, 1000);
        });
    
        return task.pipe(exec.reporter({}));
      })
      .watch(files);
    });
```

You can now use `mix.larinterface();`
