# FROM php:8.2-apache

# RUN a2enmod rewrite

# RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# EXPOSE 3000

# CMD ["apache2-foreground"]

FROM php:8.0.7-apache

COPY src /var/www/html/

RUN a2enmod rewrite
EXPOSE 3000