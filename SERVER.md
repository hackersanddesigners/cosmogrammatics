# server

## deployment with git

- `sudo apt-get install git`
- `git init --bare repo.git`
- add git post-hook to `.git/hooks/post-receive` ([ref](https://coderwall.com/p/oj5smw/git-post-receive-hook-to-checkout-a-specific-branch)) and `chmod+x` it:

```
#!/bin/bash

while read oldrev newrev ref
do
    BRANCH=`echo $ref | cut -d/ -f3`
    GIT_WORK_TREE=/var/www/<repo> git checkout -f $BRANCH
done
```

- `sudo mkdir -p /var/www/<repo>`
- make new user group `cosmo`: `sudo groupadd cosmo`
- add group to each user: `sudo usermod -a -G cosmo <user>`
- install ACL: `sudo apt install acl`
- add ACL rules:
  - `sudo setfacl -Rdm g:cosmo:rwx /var/www/<repo>`
  - `sudo setfacl -Rm g:cosmo:rwx /var/www/<repo>`
- test git post-hook by hand:
  - `GIT_WORK_TREE=/var/www/<repo> git checkout -f main`

Lastly, add a new remote to the repo:

```
git remote add <name> <user>:dev.hackersanddesigners.nl:<repo.git>
```

Then do `git push <name>` to send a copy of your repo to the WIP dev version of the website.
