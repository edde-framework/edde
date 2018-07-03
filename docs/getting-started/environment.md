# Environment

?> All examples here are build around [Docker](https://docs.docker.com/), [Docker Compose](https://docs.docker.com/compose/) and
[Docker Swarm](https://docs.docker.com/engine/swarm/). Even there are much more cool orchestrators like
[Kubernets](https://kubernetes.io/docs/home/) or [OpenShift](https://www.openshift.com/), if you are not heavily experienced in
[DevOps](https://en.wikipedia.org/wiki/DevOps), Swarm is for humans, natively supports `docker-compose.yml` and it simple to setup
at all (what about `docker swarm init` ?). Thus this tutorial follows production environment build on Docker Swarm. 

?> You can skip to [next step](/getting-started/dockerfile) as this section is related server setup (even you can do this on localhost). 

> Usually it's necessary to use some proxy for production deployment, for example [Docker Flow](https://proxy.dockerflow.com/) which
will be used in this tutorial; there is also a [tool](http://proxy-letsencrypt.dockerflow.com/) which is able to **automagically**
generate certificates from [Let's Encrypt](https://letsencrypt.org/).

!> Be careful about **Let's Encrypt** as you can get certificates only for domains you own, thus it **deos not work** for a local
development! Also there are limitation of number of domains you can use, so issue a certificate just for production (testing, 
staging) domains, **not** for reviews.

## Docker Swarm

At first, it's necessary to run Docker in Swarm mode. Even on local machine, on server, doesn't matter. This setup is however
targeted on servers. You can use for example [RancherOS](https://rancher.com/rancher-os/) which is a bit less user friendly,
but after a while it's quite powerful distribution optimized for Docker. Even [Alpine Linux](https://alpinelinux.org/downloads/)
is quite good host - **now without grsecurity patch, so it's finally highly usable**.

> This tutorial is general way how to prepare server to host not just your application, but you can also host
[GitLab](/examples/gitlab/index) instance and other services (ex. [Sentry](/examples/sentry/index)).

```bash
# initialize Docker swarm; things it will write out you can ignore for now
$ docker swarm init
```

## Proxy network

```bash
# external network is necessary to keep all services connected
docker network create --driver overlay --attachable proxy
```

## Storage

Please read about [storage](/examples/storage/index) concept; it's necessary to keep going with the following stuff.

## Dockerflow Proxy

### docker-composer.yml

It's time to configure [Dockerflow Proxy](https://proxy.dockerflow.com/) for production deployment. This service will watch containers
and make automatic adjustments to [HAPRoxy](http://www.haproxy.org/) configuration, so you'll never make manual webserver setup again. 

**Previous**: [Index](/getting-started/index) | **Next**: [Dockerfile](/getting-started/dockerfile)
