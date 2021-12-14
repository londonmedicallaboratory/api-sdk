FROM jitesoft/phpunit:8.1

COPY composer* .
COPY tests .
COPY phpunit* .
COPY scripts/docker.run.sh run.sh

RUN composer install --ignore-platform-reqs \
        && chmod u+x run.sh

ENTRYPOINT [ "./run.sh" ]
