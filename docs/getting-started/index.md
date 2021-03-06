# Getting Started

?> To pass this tutorial, you will need some knowledge about [Docker](https://docs.docker.com/) and
[Docker Compose](https://docs.docker.com/compose/) as development on local machine belongs to the
`good old days`.

!> This is one of the ways you can build an image; you should consult best practices around the world to
check if this tutorial does not contain some anti-patterns despite it was build with big care. 

!> Please follow same conventions as mentioned here, use **Sandbox** as a default name; when everything will
be working, you can do whatever you want, but as the process is quite complex, it's simple to make mistake.

> Any port used by this application is prefixed with 26, for example SSH port is published as `2622`, http as
`2680` and so on.

* [Dockerfile](/getting-started/dockerfile): Let's start with Dockerfile, the base for all the magic around there.
* [Root Filesystem](/getting-started/rootfs): We need a lot of files to build base filesystem.
* [Local Filesystem](/getting-started/localfs): Part of filesystem needed for local development.
* [Docker Compose](/getting-started/docker-compose): Set of parts making you life easier; define an application stack for
various environments.
* [Bin](/getting-started/bin): Bin is cool in general :wink: why not to have one too; it contains some useful scripts used
to startup the application.
* [Backend](/getting-started/backend): Some files required to make backend stuff working. 
* [Startup](/getting-started/startup): After all, we are able to run the thing!

> After all it's recommended to read some of [DevOps](/devops/index) tutorials; at least they're interesting :blush:.
