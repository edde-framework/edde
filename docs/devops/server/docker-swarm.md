# Docker Swarm {docsify-ignore-all}

Let's take your freshly installed server and do some heavy magic to make Docker working.

!> For this tutorial we'll use one server as a one node; in general it's enough and it's not necessary to scale things
up on thousands of servers as your application will never grow to this scale; when do, there will be a lot of teammates
who will help you to maintain this kind of stuff; this guide aims for "low-scale" deployment on a few machines at
most (even it's possible to run huge farm of servers). **Do backups of your data, that's enough for a long time!**

> This tutorial is general way how to prepare server to host not just your application, but you can also host
[GitLab](/devops/gitlab/index) instance and other services (ex. [Sentry](/devops/sentry/index)).

## Docker Swarm

Swarm initialization is quite simple process as issuing one command: the server where the command is run became 
node manager also able run as a worker.

```bash
# initialize Docker swarm; things it will print out you can ignore for now
$ docker swarm init
```

## Proxy network

Because we'll connect a lot of services to one network where Proxy will be listening, we'll need network for it. This
network will be eventually available on all nodes - thus doesn't matter where a service is running as it's on the
same virtual network.

```bash
# external network is necessary to keep all services connected
$ docker network create --driver overlay --attachable proxy
```
