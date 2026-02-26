FROM php:8.2-cli

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Railway usa la variable $PORT
ENV PORT=8080

# Exponer puerto
EXPOSE $PORT

# Servir desde la carpeta public usando el puerto dinámico
CMD php -S 0.0.0.0:${PORT} -t public/
