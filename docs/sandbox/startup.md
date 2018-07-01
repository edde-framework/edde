# Startup

Now everything is prepared for execution, it's time to say hello to you new and shiny container.

## Startup

Use `./bin/local.sh` to startup your container; it will take some time when everything is done, you will be in your
container.

```
# Starting point where you can start to do any magic you want: 
/sandbox/backend #
```

## Prepare composer

Composer is available from Docker image, so you can start your project by `composer.json` creation followed by dependencies. 

```
# init composer, follow instructions on screen 
/sandbox/backend # composer init
```

> You should have a look into `composer.json` as it will probably need some adjustments. 

## Edde Time

Because all of this you are doing because you want to use Edde, it's good to install it :wink: 

```
# init composer, follow instructions on screen 
/sandbox/backend # composer require edde-framework/edde
```

!> Be careful as this will install `master` version of Edde, thus latest version; in general it's quite safe to use it, but it's better
to stick to a particular version.

## Check your running application

Go to browser on `http://{localhost | docker-ip}:2680/hello.world/cheers` and you'll get `cheers!`.

Also you can test CLI controller:

```
/sandbox/backend # ./cli hello.world/cheers
yep!
```

> You can simply `exit`, it will stop running stack.

**Previous**: [Backend](/sandbox/backend) | **Next**: [Examples](/examples/index)
