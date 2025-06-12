### Multi stages Dockerfile

### BASE ###

FROM dunglas/frankenphp:php8.4 AS php_base

ARG USER=appuser

ENV COMPOSER_HOME=/usr/local/share/composer

# install composer & extensions
RUN set -eux; install-php-extensions \
	@composer \
	apcu \
    gd \
    intl \
	opcache \
	pdo_pgsql \
    sockets

# add composer bin to PATH (to use framework cli installer commands)
ENV PATH="/usr/local/share/composer/vendor/bin:${PATH}"

### DEV ###
FROM php_base AS php_dev

# install xdebug
RUN set -eux; install-php-extensions xdebug

RUN apt-get update \
    && apt-get install -y \
        bash \
        git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# create a non-root user + assign new shell
RUN useradd -m ${USER} \
    && echo "${USER} ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers \
    && chsh -s /bin/bash ${USER}

# force creation of composer directory
RUN mkdir -p $COMPOSER_HOME && chown -R ${USER}:${USER} $COMPOSER_HOME

# add additional capability to bind to port 80 and 443
RUN	setcap -r /usr/local/bin/frankenphp; \
    # setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    # give write access to /data/caddy and /config/caddy
    chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy

USER ${USER}

### PROD ###
FROM php_base AS php_prod

COPY . /app
