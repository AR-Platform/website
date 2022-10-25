FROM php:apache
RUN apt-get update && apt-get install -y libpq-dev python3 python3-pip && docker-php-ext-install pdo pdo_pgsql
RUN pip3 install numpy
WORKDIR /var/www
COPY . .
COPY ./public ./html
RUN mv -f ./docker/config-docker.php ./config/config.php
RUN rm -r ./public/
WORKDIR /var/www/resources
RUN mkdir -p ./download ./upload
RUN chown -R www-data:www-data ./download ./upload
RUN chmod -R og-r ./download ./upload
ENTRYPOINT ["bash", "/var/www/docker/custom-start.sh"]