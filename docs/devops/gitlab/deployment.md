# Deployment {docsify-ignore-all}

## Stack

Let's force certificate issuance.

```bash
# jump to configuration folder
docker-host:~ # cd /mnt/storage/git.john-doe.com/stack

# deploy an instance and wait for GitLab to startup; it could take some minutes (~2-3, it depends on
# server performance)

# see stack name following same convention as directory names: when you list running stacks, you'll see 
# to which domain they belongs
docker-host:/mnt/storage/git.john-doe.com/stack # docker stack deploy -c docker-compose.yml git-john-doe-com

# wait for startup - when GitLab is running, you'll also have generated certificates so it's possible to switch
# the instance to SSL
docker-host:/mnt/storage/git.john-doe.com/stack # docker stack ps git-john-doe-com
ID                  NAME                                IMAGE                         NODE              DESIRED STATE       CURRENT STATE          ERROR                              PORTS
h7on9mirujl1        git-john-doe-com_gitlab-runner.1    gitlab/gitlab-runner:latest   docker-host       Running             Running 4 minutes ago
4njzan0dffu4        git-john-doe-com_gitlab.1           gitlab/gitlab-ce:latest       docker-host       Running             Running 4 minutes ago
```

## Configuration

Finish configuration and switch instance to SSL.

```bash
docker-host:/mnt/storage/git.john-doe.com/stack # vim docker-compose.yml
```

```yaml
	#...
	
	# find this section and update following lines
	environment:
		# http is now https (this forces GitLab internally to run on SSL)
		# registry_external_url is uncommented
        GITLAB_OMNIBUS_CONFIG: |
			external_url 'https://git.john-doe.com'
            registry_external_url 'https://docker.john-doe.com'
            
	#...
```

```bash
# update the instance with a new configuration; after this you should get https://git.john-doe.com alive
docker-host:/mnt/storage/git.john-doe.com/stack # docker stack deploy -c docker-compose.yml git-john-doe-com
```

> If you want to update your instance deploy command works in the same way; when a stack is deployed (or updated), Docker Swarm looks
into registry and checks SHA has of an image; so even there is `gitlab/gitlab-ce:latest`, it has it's hash which is changed which triggers
container update. **Remember** that updates are causing downtime even for a few minutes! 

?> Now you can enjoy your newly created GitLab instance but you have to setup an account instantly as the very first visitor of the instance
could steal it; also go through documentation of GitLab and see how to disable public registrations.

**Previous**: [Docker Compose](/devops/gitlab/docker-compose) | **Next**: [Runner](/devops/gitlab/runner)
