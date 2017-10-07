FROM phpdockerio/php71-fpm:latest
RUN usermod -u 1000 www-data
RUN apt-get update
RUN apt-get -y --no-install-recommends install php-redis php-bcmath php-xdebug php-mbstring php-intl iproute2 composer git
RUN apt-get clean
