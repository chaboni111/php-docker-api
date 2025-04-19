# Use official PHP image
FROM php:8.2-cli

# Set working directory inside container
WORKDIR /var/www/html

# Copy all project files into the container
COPY . .

# Expose port for built-in PHP server
EXPOSE 8000

# Start the PHP built-in server
CMD ["php", "-S", "0.0.0.0:8000"]
