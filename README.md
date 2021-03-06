Laravel Queuing
===============

Laravel Queuing was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides queuing performance increases for [Laravel 4.2](http://laravel.com). It works by doing the actual job queuing (including the execution of sync jobs) after the response has been sent to the client (on shutdown). Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Queuing/releases), [license](LICENSE), [api docs](http://docs.grahamjcampbell.co.uk), and [contribution guidelines](CONTRIBUTING.md).

## WARNING

#### This package is no longer maintained.

![Laravel Queuing](https://cloud.githubusercontent.com/assets/2829600/4432309/c15748a4-468c-11e4-9d1f-8059185387ec.PNG)

<p align="center">
<a href="https://travis-ci.org/GrahamCampbell/Laravel-Queuing"><img src="https://img.shields.io/travis/GrahamCampbell/Laravel-Queuing/master.svg?style=flat-square" alt="Build Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing/code-structure"><img src="https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Queuing.svg?style=flat-square" alt="Coverage Status"></img></a>
<a href="https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing"><img src="https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Queuing.svg?style=flat-square" alt="Quality Score"></img></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License"></img></a>
<a href="https://github.com/GrahamCampbell/Laravel-Queuing/releases"><img src="https://img.shields.io/github/release/GrahamCampbell/Laravel-Queuing.svg?style=flat-square" alt="Latest Version"></img></a>
</p>


## Installation

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Queuing, simply add the following line to the require block of your `composer.json` file:

```
"graham-campbell/queuing": "~1.0"
```

There are some additional dependencies you will need to install for some of the features:

* The beanstalk connector requires `"pda/pheanstalk": "~2.1"` in your `composer.json`.
* The aws connector requires `"aws/aws-sdk-php": "~2.4"` in your `composer.json`.
* The iron connector requires `"iron-io/iron_mq": "~1.4"` in your `composer.json`.

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Queuing is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Queuing\QueuingServiceProvider'`

#### You MUST also remove the laravel queuing service provider!

Open up `app/config/app.php` and remove `'Illuminate\Queue\QueueServiceProvider'`.

Failure to do so will result in an infinite loop.


## Configuration

Laravel Queuing requires no configuration behond what Laravel's queuing would otherwise require, but provides a quickstart command for iron queuing where after configuring, you can simply run `php artisan queue:iron` with no arguments and it will just work.


## Usage

##### Queues\XXXQueue

All queue classes override Laravel's queue classes. When ever you call methods such as `push`, behind the scenes with package will simply queue them up in php's memory for real pushing whenever the `process` method on these classes is called. By default, this package will call this on `shutdown` through the queue manager, but you may manually call this earlier if you so which. After calling the function, the jobs will be removed from the internal queue so later calls to this function will not push same jobs twice.

##### QueueManager

This class extends Laravel's queue manager and will override it. It has one extra method `processAll`. This will call the `process` method on all active queue connections. The functionality of the `process` method on each queue is described above.

##### QueuingServiceProvider

This class contains no public methods of interest. This class should be added to the providers array in `app/config/app.php`. This class will setup ioc bindings and register queue processing. `Illuminate\Queue\QueueServiceProvider` must be removed from the service provider list before you add this class.

##### Further Information

There are other classes in this package that are not documented here. This is because they are not intended for public use and are used internally by this package.

Feel free to check out the [API Documentation](http://docs.grahamjcampbell.co.uk) for Laravel Queuing.


## License

Laravel Queuing is licensed under [The MIT License (MIT)](LICENSE).
