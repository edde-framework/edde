# edde

#### About

This is a successor of the original Edde Framework repository with a new history starting from the version 4.0 as
the biggest jump over the whole history; this separation is done also because the original repository was quite
heavy due to holding .runtime as an example.

#### The Stack

Edde is now using Docker to build the stack in very simple way.

##### Services

- `client` is front-end service without any server side power; it's intended to
	be used as CDN for client side content (as server should be client-less
	to achieve pure front-end / back-end implementation); this service
	should listen on common ports in production, currently there is just
	plain http on port `4080`; even there is this separation, client is
	forwarding all 404 requests to `client-server` service
	
	 `client` is not on the same network as the `server` is 
- `client-server` is front service for the `server` service, so everything on
	this service is forwarded to the server. This means only regular requests
	should go to this service (for example just on api end point)
	
	`client-server` is on the same network as `client` and also as
	the `server` is
- `server` is pure backend service which should be used only as pure backend
	service; this is also reason why Edde has dropped support for any template
	engine on server side
- other services - there is redis, neo4j, postgres and other services some of them
	just for testing ORM features, some as a part of the stack

#### Installation

```text
// put your host IP into environment variable HOST_IP; this is necessary to enable
// xdebug support (as it needs the connect back address working)
// for example
$ vim /etc/environment

// build containers 
$ sudo docker-compose build

// install dependencies for composer on the "server" service 
$ sudo docker-compose run server composer update

// because container is setting root rights on folders required for Edde,
// it's necessary to fix permissions
$ sudo chmod 777 ./assets -R

// run the containers on background 
$ sudo docker-compose up -d

```

# Enjoy :)
