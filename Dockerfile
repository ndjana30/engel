FROM php:8.3-fpm-alpine
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Installer les outils de gestion des utilisateurs/groupes
RUN apk --no-cache add shadow

RUN addgroup -g 1000 monprof && adduser -u 1000 -G monprof -D monprof

# Installer Supervisor
RUN apk --no-cache add supervisor

# Copier le code de l'application
COPY . /code

# Copier les configurations PHP
COPY custom-php.ini /usr/local/etc/php/conf.d/

# Copier la configuration Supervisor
  
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Définir le répertoire de travail
WORKDIR /code

# Exposer le port 9000 pour PHP-FPM
EXPOSE 9000

# Changer le propriétaire des fichiers de code
RUN chown -R monprof:monprof /code

CMD
# Démarrer PHP-FPM et Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
# CMD ["php-fpm"]