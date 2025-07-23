# Massdata Laravel Assignment

This project follows the standard Laravel MVC structure, as agreed during the interview.  
While I generally prefer a modular architecture (Nwidart modules) for better separation of concerns and scalability,  
I implemented the traditional approach here for consistency with our initial interview.

---

**A few things to know:**

- I placed test import files in the root IMPORT_FILES folder to make testing easier.
- AdminLTE theme is imported using npm. The current theme version according to the documentation is 3.2, so I used that.
- I left the ability to delete your own permissions for easier testing.
- As an example, I created a relation between Price and Discount, one Price can have multiple Discounts (depending on quantity), and a Discount can be assigned to one Price.

- Used `composer require laravel/breeze` for authentication, only login and logout are enabled.
- Used `composer require spatie/laravel-permission` for permissions management.
- Used `composer require maatwebsite/excel` for reading xls/xlsx/csv files.

**Note:** This implementation assumes that the necessary tables, models, and configuration already exist and are properly defined.  
It is expected that new models and related structures will be created **before** adjusting the configuration.  
Please refer to the comments in `ImportService::getDataTableModel` for more details.

### Test Credentials

To log in to the application for testing, use the following credentials:

- **Email:** `admin@massdata.rs`
- **Password:** `massdata`

---

## Installation

### Using Docker (RECOMMENDED)

**Note:** Traefik is included in the Docker Compose and started together with the project. Usually, Traefik is run separately to manage multiple projects at once, but here it is started and stopped with the project.  
Using Docker is the recommended way to run the application because it includes additional services like **phpMyAdmin** and **MailHog** out of the box, which simplify database management and email testing during development.

1. Make sure required ports are available (especially MySQL port 3306):

    ```bash
    sudo systemctl stop mysql
    ```

2. Copy the .env file:
    ```bash
    cp .env.example .env
    ```

3. Install npm dependencies (note: AdminLTE theme uses an older Bootstrap version, which can be ignored):

    ```bash
    npm install
    ```

4. Build frontend assets (CSS/JS):

    ```bash
    npm run build
    ```

5. Install dependencies:

    ```bash
    composer install
    ```

   **Note:** If you encounter issues related to missing PHP extensions, you have two options:

   - Install the required PHP extension (example for PHP 8.3 on Ubuntu):

       ```bash
       sudo apt-get install php8.3-gd
       ```

   - Or run Composer ignoring the missing extension requirement:

       ```bash
       composer install --ignore-platform-req=ext-gd
       ```

6. Start the containers:

    ```bash
    ./vendor/bin/sail up -d
    ```

7. Run migrations:

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

8. Run seeders:

    ```bash
    ./vendor/bin/sail artisan db:seed
    ```

9. Run queue worker:

    ```bash
    ./vendor/bin/sail artisan queue:work
    ```

10. Access the app at:

     ```
     http://massdata.docker.localhost/
     ```

---

### Using Laravel Artisan Locally

1. Ensure PHP and MySQL are installed on your system.

2. Copy the .env file (the .env.example is configured for Docker environment):
   
    **Note:** The `.env` file is currently configured for Docker environment, so if you run the app locally without Docker, adjust the database and other settings accordingly.

    ```bash
    cp .env.example .env
    ```
3. Install npm dependencies (note: AdminLTE theme uses an older Bootstrap version, which can be ignored):

    ```bash
    npm install
    ```

4. Build frontend assets (CSS/JS):

    ```bash
    npm run build
    ```

5. Install dependencies:

    ```bash
    composer install
    ```

   **Note:** If you encounter issues related to missing PHP extensions, you have two options:

   - Install the required PHP extension (example for PHP 8.3 on Ubuntu):

       ```bash
       sudo apt-get install php8.3-gd
       ```

   - Or run Composer ignoring the missing extension requirement:

       ```bash
       composer install --ignore-platform-req=ext-gd
       ```

6. Run migrations:

    ```bash
    php artisan migrate
    ```

7. Run seeders:

    ```bash
    php artisan db:seed
    ```

8. Start the Laravel development server:

    ```bash
    php artisan serve
    ```

9. Run queue worker:

    ```bash
    php artisan queue:work
    ```

10. Access the app at:

     ```
     http://127.0.0.1:8000
     ```

---

Feel free to reach out if you encounter any issues during setup.
