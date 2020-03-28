FROM php:7.4-cli-alpine

ENV COMPOSER_VERSION 1.9.3
ENV DOCKER_HOST host.docker.internal

RUN apk add --no-cache \
  pcre-dev \
  ${PHPIZE_DEPS} \
  npm \
  git \
  g++ \
  make \
  autoconf \
  && pecl install inotify xdebug \
  && docker-php-ext-install pcntl sockets \
  && docker-php-ext-enable inotify \
  && git clone git://github.com/sensational/sassphp && cd sassphp && git submodule init && git submodule update && php install.php && make install && cd .. && rm -rf sassphp/ \
  && apk del pcre-dev ${PHPIZE_DEPS}

RUN set -eux; \
  curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/cb19f2aa3aeaa2006c0cd69a7ef011eb31463067/web/installer; \
  php -r " \
    \$signature = '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5'; \
    \$hash = hash('sha384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
      unlink('/tmp/installer.php'); \
      echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
      exit(1); \
    }"; \
  php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION}; \
  composer --ansi --version --no-interaction; \
  rm -f /tmp/installer.php; \
  find /tmp -type d -exec chmod -v 1777 {} +

# Configure xdebug
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_host=${DOCKER_HOST}" >> /usr/local/etc/php/conf.d/xdebug.ini

# Enable sass
RUN echo "extension=sass.so" > /usr/local/etc/php/conf.d/sass.ini

RUN npm i -g typescript

RUN ln -s /app/bin/pssg /usr/local/bin/pssg

CMD ["sh"]
