# DevOps

As some of the other sections of this site, this does not belongs to Edde itself, but
it's needed to run the whole stuff; general knowledge, how to build an application
server from A-Z, using Docker is quite missing, so here you'll find everything on one
place.

!> Note that all the stuff mentioned here are based on some experience gained by maintaining
pipelines in technological companies, but still you can use it as a base for you own
experimenting. Tutorials here are just guides, how the stuff **could** be done.  

?> All examples here are build around [Docker](https://docs.docker.com/), [Docker Compose](https://docs.docker.com/compose/) and
[Docker Swarm](https://docs.docker.com/engine/swarm/). Even there are much more cool orchestrators like
[Kubernets](https://kubernetes.io/docs/home/) or [OpenShift](https://www.openshift.com/), if you are not heavily experienced in
[DevOps](https://en.wikipedia.org/wiki/DevOps) Docker Swarm is enough humans, natively supports `docker-compose.yml` and it simple
to setup at all (what about `docker swarm init` ?). Thus this tutorial follows production environment build on Docker Swarm.

* [Server](/devops/server/index): Choose distribution, install server and solve some other
basic stuff needed to have foundation for all the stuff you want to run.
* [GitLab](/devops/gitlab/index): GitLab is a good product how to run and maintain whole
pipeline of you work - from commit, to merge-request to a deployment and monitoring.
* [Sentry](/devops/sentry/index): Nice tool for exception monitoring to help you get
exceptions before your users.  
