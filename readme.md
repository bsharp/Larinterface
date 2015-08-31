# Larinterface

[![Build Status](https://travis-ci.org/bsharp/Larinterface.svg)](https://travis-ci.org/bsharp/Larinterface)
[![StyleCI](https://styleci.io/repos/39567681/shield)](https://styleci.io/repos/39567681)
[![Latest Stable Version](https://poser.pugx.org/bsharp/larinterface/v/stable)](https://packagist.org/packages/bsharp/larinterface)
[![Total Downloads](https://poser.pugx.org/bsharp/larinterface/downloads)](https://packagist.org/packages/bsharp/larinterface)
[![License](https://poser.pugx.org/bsharp/larinterface/license)](https://packagist.org/packages/bsharp/larinterface)

Larinterface help you to be more productive by generating automaticaly all the interface in your Laravel project by using PHP and black magic.

#### Composer

To install Larinterface using composer, run this command:

```
    composer require bsharp/larinterface
```
#### Setup

After running composer update, open your Laravel config file located at config/app.php and add the following in the $providers array.

```
    Bsharp\Larinterface\LarinterfaceServiceProvider::class
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

## File watcher

Executing `php artisan larinterface:generate` each time after modifying one of your PHP Class is annoying so here is some ways to automate this behavior.

### Using Laravel Elixir

You can create a gulp watcher, using elixir 3, to do that for you. Add this lines to your gulpfile:

#####If you generate the interfaces in the same directory than your classes: 
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
#####If you generate the interface in a separate directory:
```javascript

    var gulp = require('gulp');
    var exec = require('gulp-exec');
    var Task = elixir.Task;
    
    var files = require('./storage/app/larinterface.json');
    
    elixir.extend('larinterface', function() {
    
      new Task('larinterface_generate', function () {
    
        files = require('./storage/app/larinterface.json');
    
        return gulp.src('').pipe(exec('php artisan larinterface:generate')).pipe(exec.reporter({}));
      })
      .watch(files);
    });
```

You can now use `mix.larinterface();` to execute Larinterface in Elixir ! 

### Using PHPStorm or other JetBrain IDE:

First go to your settings and open `Files Watchers`
![idea-1](https://cloud.githubusercontent.com/assets/2951704/9404707/0e74d9d2-47f2-11e5-92bc-c2557595dc3e.png)

Then create a new `Larinterface` watcher and configure it as follow:
![idea-2](https://cloud.githubusercontent.com/assets/2951704/9404709/11a5af0a-47f2-11e5-9fb0-71a97015416f.png)

## Contributing

Feel free to contribute to Larinterface by sending a pull request ! You can always ask anything (related to Larinterface of course) using a GitHub issue. 

## License

Larinterface is open-sourced software licensed under the MIT license
