#!/usr/bin/env bash

set -euo pipefail

check_mysql_connection()
{
  until nc -z -v -w30 ${1} 3306; do
    sleep 1
  done
}

read -ra MYSQL_HOSTS_TO_CHECK <<< "${MYSQL_HOST}"

for DB_HOST in "${MYSQL_HOSTS_TO_CHECK[@]}"
do
  check_mysql_connection "${DB_HOST}"
done

set -x

/usr/local/bin/php /usr/bin/composer install

# Run migrations
/usr/local/bin/php ./bin/console doctrine:migrations:migrate --no-interaction
