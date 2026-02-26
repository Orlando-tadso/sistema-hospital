FROM php:8.2-cli

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Exponer puerto
EXPOSE 8080

# Servir desde la carpeta public
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public/"]
