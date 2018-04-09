DIY Packagist
=========

Its a fork of original Packagist repository, slitly modified in order to handle your private Git repostiry.

**Note:** Its not a replacement of Private Packagist as you still need your own server to store this software as well as git repositories.




Packagist
=========

Package Repository Website for Composer, see the [about page](http://packagist.org/about) on [packagist.org](http://packagist.org/) for more.

This project is not meant for re-use. It is open source to make it easy to contribute but we provide no support if you want to run your own, and will do breaking changes without notice.

Requirements
------------

- MySQL for the main data store
- Redis for some functionality (favorites, download statistics)
- git/svn/hg depending on which repositories you want to support

Installation
------------

1. Clone the repository
2. Edit `app/config/parameters.yml` and change the relevant values for your setup.
3. Install dependencies: `php composer.phar install`
4. Run `app/console doctrine:schema:create` to setup the DB
5. Run `app/console assets:install web` to deploy the assets on the web dir.
6. Run `app/console cache:warmup --env=prod` and `app/console cache:warmup --env=prod` to warmup cache
7. Make a VirtualHost with DocumentRoot pointing to web/
8. Run ` app/console packagist:run-workers` in order to crawl packages

You should now be able to access the site, create a user, etc.

Day-to-Day Operation
--------------------

There are a few commands you should run periodically (ideally set up a cron job running every minute or so):

    app/console packagist:update --no-debug --env=prod
    app/console packagist:dump --no-debug --env=prod
    app/console packagist:index --no-debug --env=prod

The latter is optional and only required if you are running a solr server.
