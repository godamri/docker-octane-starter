FROM godamri/swoole-php8.1-alpine:1.2
USER root
WORKDIR /var/www/app/

RUN echo "UTC" > /etc/timezone

COPY ./conf/supervisor/conf.d/worker.conf /etc/supervisor/conf.d/worker.conf
COPY ./conf/supervisor/conf.d/octane.conf /etc/supervisor/conf.d/octane.conf
COPY ./conf/php/php.ini /usr/local/etc/php/conf.d/999-custom.ini

COPY --chown=nobody:nobody ./appsrc /var/www/app/
RUN rm -rf ./node_modules
RUN rm -rf ./vendor

RUN composer install --no-dev
RUN chown -R nobody.nobody /var/www/app
USER nobody

RUN ln -sf /dev/stdout /var/www/app/storage/logs/out.log

EXPOSE 8080

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]