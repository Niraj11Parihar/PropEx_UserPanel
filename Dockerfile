# Use official PHP with Apache
FROM php:8.2-apache

# Install required PHP extensions (add more if your project needs them)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (important for frameworks like Laravel, CodeIgniter, etc.)
RUN a2enmod rewrite

# Copy project files into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose Render's port
EXPOSE 10000

# Start Apache on Render's required port
CMD ["apache2-foreground"]
