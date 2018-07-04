# Storage {docsify-ignore-all}

**Related**: [Storage Schema](/devops/server/storage)

You have to prepare space for your installation to keep everything organized. Let's say you have domain
`john-doe.com` and GitLab instance will run on `git.john-doe.com`. Also there will be domain for Docker 
Registry on `docker.john-doe.com`. Everything must be SSL enabled as Docker Registry refuses to run on
plain http.

```bash
# jump to the root of your storage
docker-host:~ # cd /mnt/storage

# create directory tree by main domain (in this case git.john-doe.com)
 
# stack to hold any configuration/helper scripts
docker-host:/mnt/storage # mkdir -p git.john-doe.com/stack

# configuration of GitLab
docker-host:/mnt/storage # mkdir -p git.john-doe.com/storage/gitlab/config

# all the data stored by GitLab
docker-host:/mnt/storage # mkdir -p git.john-doe.com/storage/gitlab/data

# persist logs if you're curious; this could be dangerous as it can grow over time!
docker-host:/mnt/storage # mkdir -p git.john-doe.com/storage/gitlab/logs

# configuration folder for GitLab Runner
docker-host:/mnt/storage # mkdir -p git.john-doe.com/storage/gitlab-runner
```

Now it's time to prepare `docker-compose.yml` for deployment process. 
