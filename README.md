# cosmogrammatics

This is repo for for the cosmogrammatics website.

Testing out if the kirby CMS can be stretched to build what we need.

## Setup

- install PHP (>=7.4.0 <8.2.0) (check `composer.json`'s `require` field)
- install [Composer](https://getcomposer.org/doc/00-intro.md)

We fetch a copy of Kirby's plainkit (via zip download or git), eventually remove the previous `.git` folder, re-init git and start tracking our own history.

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

To run the backend, Kirby suggests to setup a full-on server environment (eg with nginx etc). This is not stricly necessary for the most part. So that you can simply run the backend by doing (from the root of the website folder):

```
php -S localhost:8000 kirby/router.php
```

Otherwise you can use something like [valet](https://laravel.com/docs/9.x/valet) or anything else [listed here](https://getkirby.com/docs/cookbook/setup/development-environment).

Check [Kirby's own guide](https://getkirby.com/docs/guide/quickstart#requirements) as a reference.

## Setup SQLite

SQLite comes pre-installed with PHP.

We're going to first create our database, setup tables and schemas using the SQLite command line program, and then setup Kirby to read from and write to this file.

Check if foreign keys (to create a relationship between tables) is enabled

```
PRAGMA foreign_keys;

# if 1 enabled, 0 disabled

# to enable it run

PRAGMA foreign_keys = ON;
```

We make the following tables:

- comment
- anchor (where the comment points to in an article block)
- text (text selection offset)
- image (image selection "crop")
- audio (audio selection clip)
- video (video selection clip, potentially "cropped")

With the following commands:

```
create table comment(
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  user VARCHAR(32) NOT NULL,
  timestamp INTEGER NOT NULL,
  text TEXT NOT NULL,
  slug VARCHAR(185) NOT NULL,
  status VARCHAR(8) NOT NULL,
  anchor_id INTEGER NOT NULL, 
  FOREIGN KEY (anchor_id) REFERENCES anchor (anchor_id)
);
```

```
create table anchor(
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  anchor_id INTEGER,
  block_id VARCHAR(36),
  block_type VARCHAR(5),
  selection_text_id INTEGER,
  selection_image_id INTEGER, 
  selection_audio_id INTEGER,
  selection_video_id INTEGER,
  FOREIGN KEY (selection_text_id) REFERENCES selection_text (selection_text_id),
  FOREIGN KEY (selection_image_id) REFERENCES selection_image (selection_image_id),
  FOREIGN KEY (selection_audio_id) REFERENCES selection_audio (selection_audio_id),
  FOREIGN KEY (selection_video_id) REFERENCES selection_video (selection_video_id)
);
```


```
create table selection_text(
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  selection_text_id INTEGER NOT NULL,
  x1 INT NOT NULL,
  x2 INT NOT NULL
);
```

```
create table selection_image(
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  selection_image_id INTEGER NOT NULL,
  x1 INTEGER NOT NULL,
  x2 INTEGER NOT NULL,
  y1 INTEGER NOT NULL,
  y2 INTEGER NOT NULL
);
```

```
create table selection_audio(
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  selection_audio_id INTEGER NOT NULL,
  t1 INTEGER NOT NULL,
  t2 INTEGER NOT NULL
);
```

```
create table selection_video(
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  selection_video_id INTEGER NOT NULL,
  x1 INTEGER NOT NULL,
  y1 INTEGER NOT NULL,
  t1 INTEGER NOT NULL,
  x2 INTEGER NOT NULL,
  y2 INTEGER NOT NULL,
  t2 INTEGER NOT NULL
);
```

to display all tables:

```
.tables
```

to show table and schema has been created correctly, type for example:

```
.schema comment
```

to display the comment schema. Check if it matches the inserted schema as shown above.


## Authors

[Hackers & Designers](https://github.com/hackersanddesigners/cosmo.git) ([André](https://andrefincato.com/), [Anja](https://www.anjagroten.info/), [Karl](https://moubarak.eu/))

## License

(TBD)

Copyleft with a difference: This is a collective work, you are invited to copy, distribute, and modify it under the terms of the [CC4r](./LICENSE).
