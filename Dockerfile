FROM php:8.2-cli

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configurar sesiones PHP
RUN mkdir -p /tmp/sessions && chmod 777 /tmp/sessions
RUN echo "session.save_path=/tmp/sessions" > /usr/local/etc/php/conf.d/sessions.ini \
    && echo "session.cookie_samesite=Lax" >> /usr/local/etc/php/conf.d/sessions.ini

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Railway usa variable PORT
ENV PORT=8080
EXPOSE $PORT

# Servir desde la carpeta public
CMD php -S 0.0.0.0:${PORT} -t public/
