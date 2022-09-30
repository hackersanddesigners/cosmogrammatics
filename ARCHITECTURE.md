# Architecture

WIP; listing out major blocks / ideas for the website setup.

## Comments

Commenting is a probably the, or one of the most, crucial part of how the website works. 

A user can make a request for commenting on an article, and will receive an invite link to confirm their presence (?).

After that, they can comment on any part of the article:

- texts
- images
- audios
- videos
- comments
- ...

This action can either be on an entire block, or on a portion of it: eg, the user can select a part of a text, a clip of an audio file, etc.

A user can also comment on an existing comment. As of <2022-08-22>, after discussing comment threading, we first want to go with a more simple flat thread. Eg, A user can comment below the first comment, and any subsequent comment will be displayed as if responding to the opening post (the first comment). More variations of it might happen once the basics are in place and we can iterate more concretely on it.

On a software level, we plan to implement comments in Kirby by using an external SQL database. Kirby provides adapter and ways to extend its own logic to read from an external source that is not the file system (which is its default). See for instance:

- https://getkirby.com/docs/guide/virtual-pages/content-from-database

The way to bind a comment to a specific "block" in the article is by using Kirby's blocks. Each block comes with an ID (in the form on an hash it seems). 

The most straightforward way to go with this would be to ouput Kirby block's ID to the HTML as a `data-attribute`, then when adding a commenting (which happens via Javascript), we can read the block's ID via the `data-attribute` and store the correct block ID to the SQL databaase in this way.

We probably need a second ID (eg a sequence number) to order and count the comments added to a certain block, so that we can save and display them in the correct order.

A full spec of the SQL table will be added soon once more work is done.

## SQLite structure

We need several tables to store comments from a specific article, roughly following this schema:

+ table comment
  - id INT
  - user VARCHAR (32) // set max length based on UUID or hash max size?
  - timestamp INTEGER (unix time)
  - text TEXT
  - slug VARCHAR (185) // set max lenght
  - title VARCHAR (185)
  - status VARCHAR (8)
  - anchor_id (table) INT,
    FOREIGN KEY (anchor_id) REFERENCES anchor (anchor_id)

+ table anchor
  - id INT ?
  - cosmogram id (= comment.slug?) INT / VARCHAR
  - block id INT / VARCHAR
  - block_type VARCHAR (?)
  - selection_text (table) INT,
    FOREIGN KEY (selection_text) REFERENCES text (selection_text)
  - selection_image (table) INT,
    FOREIGN KEY (selection_image) REFERENCES image (selection_image)
  - selection_audio (table) INT,
    FOREIGN KEY (selection_audio) REFERENCES audio (selection_audio)
  - selection_video (table) INT,
    FOREIGN KEY (selection_video) REFERENCES video (selection_video)
    
+ table text 
  - id INT ?
  - x1 INT
  - x2 INT

+ table image
  - id INT ?
  - x1 INT
  - x2 INT
  - y1 INT
  - y2 INT

+ table audio
  - id INT ?
  - t1 INT
  - t2 INT

+ table video
  - id INT ?
  - x1 INT
  - y1 INT
  - t1 INT
  - y2 INT
  - y2 INT
  - t2 INT
