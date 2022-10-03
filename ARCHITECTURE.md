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

As of <2022-08-22>, after discussing comment threading, we first want to go with a more simple flat thread. Eg, A user can comment below the first comment, and any subsequent comment will be displayed as if responding to the opening post (the first comment). More variations of it might happen once the basics are in place and we can iterate more concretely on it.

<2022-10-03> After a first attempt to connect Kirby CMS to a SQLite database, we are realised more pieces are needed to comfortably manage the db (eg, a plugin to programmatically create, update, delete and migrate tables), so we are leaning towards do a first iteration with simply using Kirby

The way to bind a comment to a specific "block" in the article is by using Kirby's blocks. Each block comes with an ID (in the form on an hash it seems). 

The most straightforward way to go with this would be to ouput Kirby block's ID to the HTML as a `data-attribute`, then when adding a commenting (which happens via Javascript), we can read the block's ID via the `data-attribute` and store the correct block ID to the SQL databaase in this way.

We probably need a second ID (eg a sequence number) to order and count the comments added to a certain block, so that we can save and display them in the correct order.
