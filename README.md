Laravel Queuing
===============


[![Build Status](https://img.shields.io/travis/GrahamCampbell/Laravel-Queuing/master.svg?style=flat)](https://travis-ci.org/GrahamCampbell/Laravel-Queuing)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/GrahamCampbell/Laravel-Queuing.svg?style=flat)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/GrahamCampbell/Laravel-Queuing.svg?style=flat)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Version](https://img.shields.io/github/release/GrahamCampbell/Laravel-Queuing.svg?style=flat)](https://github.com/GrahamCampbell/Laravel-Queuing/releases)


## Introduction

Laravel Queuing was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell), and provides queuing performance increases for [Laravel 4.2+](http://laravel.com). It works by doing the actual job queuing (including the execution of sync jobs) after the response has been sent to the client (on shutdown). Feel free to check out the [change log](CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Queuing/releases), [license](LICENSE.md), [api docs](http://grahamcampbell.github.io/Laravel-Queuing), and [contribution guidelines](CONTRIBUTING.md).


## Installation

[PHP](https://php.net) 5.4.7+ or [HHVM](http://hhvm.com) 3.1+, and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Queuing, simply require `"graham-campbell/queuing": "~0.4"` in your `composer.json` file.

There are some additional dependencies you will need to install for some of the features:

* The beanstalk connector for Laravel 4.2 requires `"pda/pheanstalk": "~2.1"` in your `composer.json`.
* The beanstalk connector for Laravel 4.3 requires `"pda/pheanstalk": "~3.0"` in your `composer.json`.
* The aws connector requires `"aws/aws-sdk-php": "~2.4"` in your `composer.json`.
* The iron connector requires `"iron-io/iron_mq": "~1.4"` in your `composer.json`.

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel Queuing is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

* `'GrahamCampbell\Queuing\QueuingServiceProvider'`

#### You MUST also remove the laravel queuing service provider!

Open up `app/config/app.php` and remove `'Illuminate\Queue\QueueServiceProvider'`.

Failure to do so will result in an infinite loop.


## Configuration

Laravel Queuing requires no configuration behond what Laravel's queuing would otherwise require. Just follow the simple install instructions and go!


## Usage

There is currently no usage documentation besides the [API Documentation](http://grahamcampbell.github.io/Laravel-Queuing
) for Laravel Queuing.

You may see an example of implementation in [Laravel Credentials](https://github.com/GrahamCampbell/Laravel-Credentials) and [Bootstrap CMS](https://github.com/GrahamCampbell/Bootstrap-CMS).


## License

Apache License

Copyright 2013-2014 Graham Campbell

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
