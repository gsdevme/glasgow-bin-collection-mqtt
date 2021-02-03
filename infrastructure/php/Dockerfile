FROM php:8.0-cli-alpine as base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mkdir -p /srv/app/var /srv/app/vendor && chown -R nobody:nogroup /srv

COPY infrastructure/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY infrastructure/php/shutdown-supervisor.sh /usr/local/bin/shutdown-supervisor.sh

WORKDIR /srv/app/

# Install dependencies
RUN buildDeps=" \
        git \
        less \
        make \
        zip \
        unzip \
        supervisor \
    "; \
    set -x && \
    apk add --no-cache $buildDeps && \
    chmod +x /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/shutdown-supervisor.sh

COPY infrastructure/php/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["bin/fetch-and-publish"]
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

FROM base as dev

USER nobody
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]

FROM base as build

COPY composer.json /srv/app/composer.json
COPY composer.lock /srv/app/composer.lock
COPY .env /srv/app/.env
COPY phpstan.neon /srv/app/phpstan.neon
COPY ruleset.xml /srv/app/ruleset.xml
COPY src/ /srv/app/src
COPY bin/ /srv/app/bin

RUN composer install -n --no-suggest -q --no-scripts

FROM build as ci

COPY Makefile /srv/app/Makefile

RUN make run-ci

FROM base as prod

ENV APP_ENV=prod

COPY --from=build /srv/app/ /srv/app/

RUN chown -R nobody:nogroup /srv/app/ && \
   composer install --no-dev -n --no-suggest --no-scripts && \
   composer dump-autoload --no-dev --classmap-authoritative -n

USER nobody
