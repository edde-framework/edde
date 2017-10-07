FROM phpdockerio/php71-fpm:latest
RUN apt-get update
RUN apt-get -y --no-install-recommends install php-redis php-pgsql php-mysql php-bcmath php-xdebug php-mbstring php-intl composer git
RUN apt-get clean
RUN mkdir -p /server/.assets
RUN mkdir -p /server/.storage
RUN chown www-data:www-data /server -R
