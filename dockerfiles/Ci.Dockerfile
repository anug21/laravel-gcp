FROM devopsfnl/image:php-8.2.11-np

ARG PORT
ENV PORT=${PORT}

WORKDIR /var/www/html

COPY . /var/www/html
COPY php.ini /usr/bin
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

ENTRYPOINT ["/var/www/html/dockerfiles/api-runner"]
#RUN composer install --no-progress
#RUN npm install
#RUN npm run build
