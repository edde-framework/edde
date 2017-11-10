FROM phpdockerio/php71-fpm:latest
RUN apt-get update && apt-get upgrade -y
RUN apt-get -y --no-install-recommends install php-redis php-pgsql php-mysql php-bcmath php-xdebug php-mbstring php-intl php-igbinary composer git
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data
