FROM phpdockerio/php71-fpm:latest
RUN apt-get update
RUN apt-get -y --no-install-recommends install php-redis php-pgsql php-mysql php-bcmath php-xdebug php-mbstring php-intl composer git
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
