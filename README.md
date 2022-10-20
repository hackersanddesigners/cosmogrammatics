# cosmogrammatics

This is repo for for the cosmogrammatics website.

Testing out if the kirby CMS can be stretched to build what we need.

## Setup

- install PHP (>=7.4.0 <8.2.0) (check `composer.json`'s `require` field)
- install [Composer](https://getcomposer.org/doc/00-intro.md)

We fetch a copy of Kirby's plainkit (via zip download or git), eventually remove the previous `.git` folder, re-init git and start tracking our own history.

To setup the REST API workflow from the frontned (javascript):

- make a new user account with role API
- copy `site/config/env.sample.php` and rename it to `site/config/env.php`
- set above user's email and password to it

### Upgrade

To upgrade Kirby, run in a terminal the following:

```
composer update getkirby/cms
```

To upgrade everything (eg kirby and any other plugin), run:

```
composer update
```

We don't track the `kirby` folder into git, but instead handle it as a normal dependency.

## Run

Before running the server, make sure you have a content folder.

To run the backend, Kirby suggests to setup a full-on server environment (eg with nginx etc). This is not stricly necessary for the most part. So that you can simply run the backend by doing (from the root of the website folder):

```
php -S localhost:8000 kirby/router.php
```

Otherwise you can use something like [valet](https://laravel.com/docs/9.x/valet) or anything else [listed here](https://getkirby.com/docs/cookbook/setup/development-environment).

Check [Kirby's own guide](https://getkirby.com/docs/guide/quickstart#requirements) as a reference.

## Authors

[Hackers & Designers](https://github.com/hackersanddesigners/cosmo.git) ([André](https://andrefincato.com/), [Anja](https://www.anjagroten.info/), [Karl](https://moubarak.eu/))

## License

(TBD)

Copyleft with a difference: This is a collective work, you are invited to copy, distribute, and modify it under the terms of the [CC4r](./LICENSE).
