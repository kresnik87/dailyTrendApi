FROM  php:7.4-fpm-alpine3.11

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it

RUN apk --update --no-cache add git

RUN docker-php-ext-install pdo_mysql
RUN apk add --no-cache zip libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# Chromium and ChromeDriver
ENV PANTHER_NO_SANDBOX 1
# Not mandatory, but recommended
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'
RUN apk add --no-cache \
        unzip \
        chromium \
        chromium-chromedriver


COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY crontab/crontab /crontab
RUN mkdir /var/log/cron
RUN touch /var/log/cron/cron.log
RUN chown -R www-data:www-data /var/log/cron

RUN /usr/bin/crontab -u www-data /crontab

CMD ["crond", "-f", "-l", "8"]

CMD composer install ; -- bin/console doctrine:migrations:migrate ;  php-fpm

EXPOSE 9000
