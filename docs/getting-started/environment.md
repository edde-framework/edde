# Environment

?> All examples here are build around Docker, Docker Composer and [Docker Swarm](https://docs.docker.com/engine/swarm/). Even
there are much more cool orchestrators like [Kubernets](https://kubernetes.io/docs/home/) or [OpenShift](https://www.openshift.com/),
if you are not heavily experienced in [DevOps](https://en.wikipedia.org/wiki/DevOps), Swarm is for humans, natively supports
`docker-compose.yml` and it simple to setup at all (what about `docker swarm init` ?). Thus this tutorial follows production
environment build on Docker Swarm. 

> Usually it's necessary to use some proxy for production deployment, for example [Docker Flow](https://proxy.dockerflow.com/) which
will be used in this tutorial; there is also a [tool](http://proxy-letsencrypt.dockerflow.com/) which is able to **automagically**
generate certificates from [Let's Encrypt](https://letsencrypt.org/).

!> Be careful about Let's Encrypt as you can get certificates only for domains you own, thus it **deos not work** for a local
development!

**Previous**: [Index](/getting-started/index) | **Next**: [Dockerfile](/getting-started/dockerfile)
