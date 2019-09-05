FROM php:7.2-cli

LABEL io.kuzzle.vendor="Kuzzle <support@kuzzle.io>"
LABEL description="Runner for php documentation snippets"
LABEL name="kuzzleio/doc-runner:php"

RUN apt-get update && \
  apt-get install -y --no-install-recommends git zip

RUN curl --silent --show-error https://getcomposer.org/installer | php

WORKDIR /app