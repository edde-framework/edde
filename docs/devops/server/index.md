# Server

Every product needs to be run somewhere; purpose of this guide is to prepare production
ready server to run all of related products needed to have full and simple
[workflow](/examples/workflow/index). 

!> A lot of things are possible to run on localhost, but this section requires some cheap
VPS to run the stuff on; yes, it will costs something, but it's a bit price for the
knowledge.

!> VPS (or other) server with **public** IP address (with access to
virtual CD drive to install **any** operating system) and **own** domain name is **required**!
`example.com` will be used in subsequent documentation, you should replace it with your
own, for example `your-name.me`.

?> For this section is in general required knowledge about [Docker](https://docs.docker.com/),
[Docker Compose](https://docs.docker.com/compose/) and [Docker Swarm](https://docs.docker.com/engine/swarm/).
You can continue without deeper knowledge, but you could be a bit blind to the things you are doing.

* [Distribution](/devops/server/distribution): This is neverending question, here are some tips
which is good to use for Dockerized environment.
* [Docker Swarm](/devops/server/docker-swarm): One way how run containers on multiple servers (even
on just one node).
* [Storage](/devops/server/storage): Yes, you'll need to store data in some way.
* [Proxy](/devops/server/proxy): Messing up with nginx/apache virtual hosts belongs to some ancient
ages we already don't remember!
