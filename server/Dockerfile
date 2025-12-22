FROM php:8.0.7-apache

COPY src /var/www/html/
RUN mkdir -p /var/www/html/app/file-persistence \
    && touch /var/www/html/app/file-persistence/chat_members.json \
    && touch /var/www/html/app/file-persistence/chat_messages.json \
    && chown -R www-data:www-data /var/www/html/app/file-persistence \
    && chmod 755 /var/www/html/app/file-persistence \
    && chmod 644 /var/www/html/app/file-persistence/chat_members.json \
    && chmod 644 /var/www/html/app/file-persistence/chat_messages.json

RUN a2enmod rewrite
EXPOSE 3000