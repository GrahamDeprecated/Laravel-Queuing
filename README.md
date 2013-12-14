Laravel Queuing
===============


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/GrahamCampbell/Laravel-Queuing/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
[![Build Status](https://travis-ci.org/GrahamCampbell/Laravel-Queuing.png?branch=master)](https://travis-ci.org/GrahamCampbell/Laravel-Queuing)
[![Latest Version](https://poser.pugx.org/graham-campbell/queuing/v/stable.png)](https://packagist.org/packages/graham-campbell/queuing)
[![Total Downloads](https://poser.pugx.org/graham-campbell/queuing/downloads.png)](https://packagist.org/packages/graham-campbell/queuing)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing/badges/quality-score.png?s=8aa8514610dfe89cd32922515c7ed35d0901bdd9)](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing)
[![Still Maintained](http://stillmaintained.com/GrahamCampbell/Laravel-Queuing.png)](http://stillmaintained.com/GrahamCampbell/Laravel-Queuing)


## What Is Laravel Queuing?

Laravel Queuing is a cool way to queue in [Laravel 4.1](http://laravel.com).  

* Laravel Queuing was created by, and is maintained by [Graham Campbell](https://github.com/GrahamCampbell).  
* Laravel Queuing relies on my [Laravel Core](https://github.com/GrahamCampbell/Laravel-Core) package.  
* Laravel Queuing uses [Travis CI](https://travis-ci.org/GrahamCampbell/Laravel-Queuing) to run tests to check if it's working as it should.  
* Laravel Queuing uses [Scrutinizer CI](https://scrutinizer-ci.com/g/GrahamCampbell/Laravel-Queuing) to run additional tests and checks.  
* Laravel Queuing uses [Composer](https://getcomposer.org) to load and manage dependencies.  
* Laravel Queuing provides a [change log](https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/CHANGELOG.md), [releases](https://github.com/GrahamCampbell/Laravel-Queuing/releases), and a [wiki](https://github.com/GrahamCampbell/Laravel-Queuing/wiki).  
* Laravel Queuing is licensed under the Apache License, available [here](https://github.com/GrahamCampbell/Laravel-Queuing/blob/master/LICENSE.md).  


## System Requirements

* PHP 5.4.7+ or PHP 5.5+ is required.
* You will need [Laravel 4.1](http://laravel.com) because this package is designed for it.  
* You will need [Composer](https://getcomposer.org) installed to load the dependencies of Laravel Queuing.  


## Installation

Please check the system requirements before installing Laravel Queuing.  

To get the latest version of Laravel Queuing, simply require it in your `composer.json` file.

`"graham-campbell/queuing": "dev-master"`

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

You will need to register the [Laravel Core](https://github.com/GrahamCampbell/Laravel-Core) service provider before you attempt to load the Laravel Queuing service provider. Open up `app/config/app.php` and add the following to the `providers` key.

`'GrahamCampbell\Core\CoreServiceProvider'`

Once Laravel Queuing is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

`'GrahamCampbell\Queuing\QueuingServiceProvider'`

You can register the three facades in the `aliases` key of your `app/config/app.php` file if you like.

`'JobProvider' => 'GrahamCampbell\Queuing\Facades\JobProvider'`
`'Queuing' => 'GrahamCampbell\Queuing\Facades\Queuing'`
`'Cron' => 'GrahamCampbell\Queuing\Facades\Cron'`

You will additionally need to replace `app/config/queue.php` with the `queue.php` provided in the root folder of this repo. This config allows us to specify different queues for special jobs.


## Updating Your Fork

The latest and greatest source can be found on [GitHub](https://github.com/GrahamCampbell/Laravel-Queuing).  
Before submitting a pull request, you should ensure that your fork is up to date.  

You may fork Laravel Queuing:  

    git remote add upstream git://github.com/GrahamCampbell/Laravel-Queuing.git

The first command is only necessary the first time. If you have issues merging, you will need to get a merge tool such as [P4Merge](http://perforce.com/product/components/perforce_visual_merge_and_diff_tools).  

You can then update the branch:  

    git pull --rebase upstream develop
    git push --force origin <branch_name>

Once it is set up, run `git mergetool`. Once all conflicts are fixed, run `git rebase --continue`, and `git push --force origin <branch_name>`.  


## Pull Requests

Please submit pull requests against the develop branch.  

* Any pull requests made against the master branch will be closed immediately.  
* If you plan to fix a bug, please create a branch called `fix-`, followed by an appropriate name.  
* If you plan to add a feature, please create a branch called `feature-`, followed by an appropriate name.  
* Please indent with 4 spaces rather than tabs, and make sure your code is commented.  


## License

Apache License  

Copyright 2013 Graham Campbell  

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at  

 http://www.apache.org/licenses/LICENSE-2.0  

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.  
