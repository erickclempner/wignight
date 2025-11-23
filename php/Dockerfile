# Start from the base image you're already using
FROM php:8.2-apache

# Use the official Docker helper script to install the mysqli extension
RUN docker-php-ext-install mysqli

# Good practice: also enable the modern PDO driver for MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache's rewrite module (useful for most web apps)
RUN a2enmod rewrite