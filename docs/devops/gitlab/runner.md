# Runner {docsify-ignore-all}

[GitLab Runner](https://docs.gitlab.com/runner/) is responsible for running jobs requested by CI; these
jobs are able to do any kind of magic, usually they build a container and deploy it do the stack.

To register GitLab Runner you need token which could be found in `Administration` (small key at the top of 
page, you must be admin of the instance), `Overview::Runners` on the left menu (could be collapsed).

## Registration

GitLab must know about runner, thus it's needed to register it.

```bash
# show container ID of running GitLab Runner
docker-host:~ # docker ps | grep gitlab/gitlab-runner
c08ce57e97cb        gitlab/gitlab-runner:latest                                       "/usr/bin/dumb-ini..."   31 hours ago        Up 31 hours                                         git-john-doe-com_gitlab-runner.1.14x9n7rtbtbugrinley2nj18f

# execute gitlab-runner registration; you'll need domain name for this and token found in GitLab instance
# choose `docker` executor during registration, base image should be `docker:latest`
docker-host:~ # docker exec -it c08ce57e97cb gitlab-runner register
```
## Configuration

After registration, it's necessary to make some little adjustments in configuration.

```bash
# edit runner's configuration file
docker-host:~ # vim /mnt/storage/git.john-doe.com/storage/gitlab-runner/config.toml
```

Most **important** line is

> `volumes = ["/cache"]`

change it to

> `volumes = ["/var/run/docker.sock:/var/run/docker.sock", "/cache"]`

!> Because runner will be responsible for an application deployments, it must have access to host Docker. 

**Example** file:

```text
# how many jobs could run in paralell 
concurrent = 4
check_interval = 0

[[runners]]
  name = "..."
  url = "https://git.john-doe.com"
  token = "..."
  executor = "docker"
  [runners.docker]
    tls_verify = false
    image = "docker:latest"
    privileged = false
    disable_cache = false
    volumes = ["/var/run/docker.sock:/var/run/docker.sock", "/cache"]
    shm_size = 0
  [runners.cache]
```

> Now you have running GitLab instance with runner able to catch your CI jobs! You can continue with [workflow](/examples/workflow/index)
example to see how you can take advantage from the things you've already created!  

**Previous**: [Deployment](/devops/gitlab/deployment)
