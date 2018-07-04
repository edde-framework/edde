# GitLab

Tutorial about setup and hosting your own [GitLab](https://about.gitlab.com/) instance.

> If you have a project you want to maintain, it's better to run your **own** instance of GitLab as
it has **everything integrated** starting from repository, issue management to CI/CD and Docker Registry
implementation; all of this is possible in "open-source" world, but it's much harder to do. We'll 
start with one configuration and that's enough.

!> During setup you'll need some email provider for sending emails; it's enough to create a **gmail** account
for this purpose. Just keep **less secure** login options enabled. 

* [Storage](/devops/gitlab/storage): We have to prepare storage schema for GitLab to keep the stuff safe.
* [Docker Compose](/devops/gitlab/docker-compose): Configuration file of the stack.
* [Deployment](/devops/gitlab/deployment): Put things online.
