#!/usr/bin/env sh

set -e

export COMPOSER_ALLOW_SUPERUSER=1 && docker-compose-wait && composer run tests

exit $?
