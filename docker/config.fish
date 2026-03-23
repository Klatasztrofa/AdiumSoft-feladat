set --universal fish_greeting
set -x XDEBUG_MODE off
abbr --add --position command t "XDEBUG_MODE=off ./vendor/bin/phpunit --testsuite=default --display-warnings"
abbr --add --position command tf "XDEBUG_MODE=off ./vendor/bin/phpunit --testsuite=default --filter"
abbr --add --position command td "XDEBUG_MODE=debug ./vendor/bin/phpunit --testsuite=default --filter"
