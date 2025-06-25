# Product Management System

This is a simple product management system with a PHP/MySQL backend and a Vue.js frontend.

## Technologies Used

-   **Frontend**: HTML5, CSS3, JavaScript (Vue.js)
-   **Backend**: PHP, MySQL

## Project Structure

```
assesment/
├── backend/
│   ├── api/
│   │   ├── categories.php
│   │   ├── products.php
│   │   └── upload.php
│   ├── config.php
│   ├── Database.php
│   └── database.sql
├── frontend/
│   ├── app.js
│   ├── index.html
│   └── style.css
└── README.md
```

## Setup Instructions

### 1. Backend Setup

1.  **Create a MySQL database**:
    -   Create a new database named `product_management`.

2.  **Import the database schema**:
    -   Import the `backend/database.sql` file into your `product_management` database. This will create the `products`, `categories`, and `product_categories` tables.

3.  **Configure database connection**:
    -   Open `backend/config.php` and update the database credentials (`DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`) if they are different from the default values.

    ```php
    <?php
    // Database credentials
    define('DB_HOST', '127.0.0.1');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'product_management');
    ```

### 2. Running the Application

1.  **Serve the project**:
    -   This project requires a web server with PHP and MySQL support (e.g., XAMPP, WAMP, MAMP, or Laragon).
    -   Place the entire `assesment` directory in your web server's root directory (e.g., `htdocs` in XAMPP).

2.  **Access the application**:
    -   Open your web browser and navigate to `http://localhost/assesment/frontend/`.

You should now see the product management interface and be able to create, view, update, and delete products and categories. 