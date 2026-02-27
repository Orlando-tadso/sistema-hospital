FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar Apache para servir desde /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configurar Apache para escuchar en el puerto dinámico de Railway
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Configurar permisos de sesión
RUN mkdir -p /tmp/sessions && chmod 777 /tmp/sessions
RUN echo "session.save_path=/tmp/sessions" >> /usr/local/etc/php/php.ini

# Railway usa variable PORT
ENV PORT=8080
EXPOSE $PORT

# Iniciar Apache con sustitución de PORT
CMD sed -i "s/\${PORT}/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground
