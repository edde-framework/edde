# Storage {docsify-ignore-all}

This topic itself it very complex, but a little important piece will be discussed to help you maintain
**simple** schema: running system (aka logic) and storage (aka data) should be separated. When system dies
for whatever reason, you can take your data and setup all services in incredibly short time. This is
also useful when you want experiment with different [distributions](/devops/server/distribution).

?> It's good to have some knowledge about filesystems, ways how to share drives/filesystems and other
interesting stuff; basically if you ever run NAS at home, you'll probably know something about topic
being discussed.

## Basic Idea

When you setup any kind of server (VPS, physical server, virtual machine, ...), at least good practice is
to have extra drive attached just as a storage. 

> In common it's useful to attach another drive to `/mnt/storage` as the very beginning; we'll make some
magic later. 

If you know about some magic called [iSCSI](https://en.wikipedia.org/wiki/ISCSI), you should attach
this drive as `/mnt/storage`; with this you can startup another machine in minutes if the `logic` (system)
dies for whatever reason. Also it's good to have this drive on some kind of [RAID](https://en.wikipedia.org/wiki/RAID).
In general this setup could save a lot of pain if something went wrong.

> Let's note that backups are another standalone story! Good on Docker is that you can shut it down, make
filesystem backup and you know everything is `safe and sound`. It has drawback of downtime (for a few
minutes depending on services you're using).  

## Storage Schema

There are plenty schemas, how to save data on the storage, but here is the concept targeting two basic
requirements:

* **configuration**: this is probably most valuable thing right behind data itself!
* **data**: almost every service needs some persistence

This simple schema means you have one root of everything important: `/mnt/storage`; it's enough to harden
this drive and you're happy. Basically.

Schema itself could be based on domain name of used service, for example common services on the server (thus
occupying some well known port like `80` or `443`) could live in `common` leading to:

* `/mnt/storage/common/stack`: for configuration of common services globally available on the Swarm (node)
* `/mnt/storage/common/storage`: for data needed to be persisted by common service (like certificates issued
by Let's Encrypt)

When you deploy your application it's good practice to use it's (primary) domain as a folder name:
* `/mnt/storage/my-app.john-doe.me/stack`: you'll probably don't use this folder as the application should be
maintained by some CI server like [GitLab](/devops/gitlab/index). 
* `/mnt/storage/my-app.john-doe.me/storage`: storage for your application if needed 

> Do not create `stack` folder for your applications if you want to follow workflow described [here](/examples/workflow/index).

!> Domain name is a nice way how to organize things on storage so you can simply see what's deployed and where
and how much data the service is taking. Also you can simply take service's data and moved it to another node
if necessary.

## Drawbacks

There is also a drawback of this schema as when you're working on physical filesystem of the server, you need
to create exactly **same** structure on every node where an application may run; for example if you have GitLab,
it could be deployed on any of 100 nodes you have, thus everywhere must be same structure or label could be 
set to restrict service on a particular node.

!> This is not done in a nice way, but it's enough for a lot of applications; do not argue when you'll be Facebook
as in that time you won't need guide.

**Previous**: [Docker Swarm](/devops/server/docker-swarm) | **Next**: [Proxy](/devops/server/proxy)
