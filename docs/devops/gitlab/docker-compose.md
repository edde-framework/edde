# Docker Compose {docsify-ignore-all}

GitLab has one little trick to do when it has to be deployed over SSL; **this configuration will be
updated during deployment**. 

```yaml
# version is important to keep on 3+ as lower version is not supported by Docker Swarm
version: '3.0'
services:
	# GitLab has one nice standalone image, so it's quite easy to set things up 
    gitlab:
        image: gitlab/gitlab-ce
        # to keep nice name inside the container (instead of container hash)
        hostname: git.john-doe.com
        # connect GitLab to proxy network to deploy it on Docker Flow
        networks:
            - proxy
        # tricky part: host port 22 is already taken by SSH daemon, you can choose whatever
        # port you want, just remember to update it in config below
        ports:
            - "4722:22"
        environment:
            # see the first line where is just http:// not https://
            # also registry_external_url is commented out to let GitLab start; if it does not
            # start, certificates won't be issued, thus no SSL and no Docker Registry  
            GITLAB_OMNIBUS_CONFIG: |
                external_url 'http://git.john-doe.com'
                #registry_external_url 'https://docker.john-doe.com'
                gitlab_rails['gitlab_shell_ssh_port'] = 4722
                gitlab_rails['rack_attack_git_basic_auth'] = { 'enabled' => false }
                gitlab_rails['smtp_enable'] = true
                gitlab_rails['smtp_address'] = 'smtp.gmail.com'
                gitlab_rails['smtp_port'] = 587
                gitlab_rails['smtp_user_name'] = '<your-user>@gmail.com'
                gitlab_rails['smtp_password'] = '<your gmail password>'
                gitlab_rails['smtp_domain'] = 'smtp.gmail.com'
                gitlab_rails['smtp_authentication'] = 'plain'
                gitlab_rails['smtp_enable_starttls_auto'] = true
                gitlab_rails['smtp_tls'] = false
                gitlab_rails['smtp_openssl_verify_mode'] = 'peer'
                gitlab_rails['gitlab_email_from'] = 'git@git.john-doe.com'
                gitlab_rails['gitlab_email_reply_to'] = 'noreply@git.john-doe.com'

        # persistence of GitLab stuff
        volumes:
            # this one is tricky as it points to the path where Let's Encrypt service saves issued certificate
            # for GitLab instance; proxy and GitLab talks on SSL, because it's almost impossible to run properly
            # GitLab non-ssl and route requests inside from ssl
            - /mnt/storage/common/storage/certbot:/etc/gitlab/ssl
            - /mnt/storage/git.john-doe.com/storage/gitlab/config:/etc/gitlab
            # you can hide logs inside container if you're not so much interested in them
            - /mnt/storage/git.john-doe.com/storage/gitlab/logs:/var/log/gitlab
            - /mnt/storage/git.john-doe.com/storage/gitlab/data:/var/opt/gitlab
        # this is Docker Flow proxy configuration to let it deploy GitLab instance
        deploy:
            labels:
                - com.df.notify=true
                - com.df.distribute=true
                # enable ssl: this asks for certificates for following domains (yes, there could be more on one line)
                - com.df.letsencrypt.host=git.john-doe.com,docker.john-doe.com
                # this email will be eventually spammed about expiring certificates
                - com.df.letsencrypt.email=ssl@john-doe.com
                # proxy will listen in the given domains (again, there could be more domains at once)
                - com.df.serviceDomain=git.john-doe.com,docker.john-doe.com
                - com.df.servicePath=/
                # incoming port from the world
                - com.df.srcPort=443
                # target port of a service - GitLab is running internally on SSL too, thus 443
                - com.df.port=443
                - com.df.sslVerifyNone=true

	# CI is useful thing, thus we're setup GitLab runner
    gitlab-runner:
        image: gitlab/gitlab-runner:latest
        # access to global proxy network
        networks:
            - proxy
        volumes:
            # bind config to runner; it's enough just to change config file, runner will reload automagically
            - /mnt/storage/git.john-doe.com/storage/gitlab-runner:/etc/gitlab-runner
            # GitLab will be responsible for deployments on Docker Swarm, thus it needs access to host instance
            - /var/run/docker.sock:/var/run/docker.sock

networks:
    proxy:
        external: true
```

**Previous**: [Index](/devops/gitlab/index) | **Next**: [Deployment](/devops/gitlab/deployment)
