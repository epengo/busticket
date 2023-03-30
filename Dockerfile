FROM php:7.4-apache  
WORKDIR /var/www/html

COPY . .
RUN chmod -R 777 db
EXPOSE 80
