# Sandbox

This is quite huge tutorial on how to build whole application using [Docker](https://docs.docker.com/),
[Docker Compose](https://docs.docker.com/compose/) and Edde. At  the end there will be production ready
image on which you can build you future application.

?> A lot of stuff mentioned here is not related to Edde, in general if you want high quality NGINX+PHP-FPM
image, you can use this tutorial too.

!> This is one of the ways you can build an image; you should consult best practices around the world to
check if this tutorial does not contain some anti-patterns despite it was build with big care. 

!> Please follow same convention as mentioned here, use **Sandbox** as a default name; when everything will
be working, you can do whatever you want, but as the process is quite complex, it's simple to make mistake.

?> Any port used by this application is prefixed with 26, for example SSH port is published as `2622`, http as
`2680` and so on.

* [Dockerfile](/sandbox/dockerfile): Let's start with Dockerfile, the base for all the magic around there.
* [Root Filesystem](/sandbox/rootfs): We need a lot of files to build base filesystem.
* [Local Filesystem](/sandbox/localfs): Part of filesystem needed for local development.
* [Docker Compose](/sandbox/docker-compose): Set of parts making you life easier; define an application stack for
various environments.
* [Bin](/sandbox/bin): Bin is cool in general :wink: why not to have one too; it contains some useful scripts used
to startup the application.
* [Backend](/sandbox/backend): Some files required to make backend stuff working. 
* [Startup](/sandbox/startup): After all, we are able to run the thing!
