# eCommerce Website

This is an eCommerce website built using **PHP**, **MySQL**, and **Apache**. The project is set up using **XAMPP** as the local server environment. It allows users to browse products, filter by categories, search, and add products to the cart.

## Features

- **Product Listing**: Display a list of products fetched from the MySQL database.
- **Search Functionality**: Search for products by name, description, or category.
- **Category Filtering**: Filter products by different categories.
- **Shopping Cart**: Users can add products to their shopping cart (basic functionality).
- **MySQL Database**: Stores products, user information, and orders.

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Server**: Apache (via XAMPP)
- **Version Control**: Git

## Installation

### Prerequisites

- **XAMPP** (Apache, MySQL)
  - Download and install XAMPP from [here](https://www.apachefriends.org/index.html).
  - XAMPP includes **Apache** (for serving your PHP files) and **MySQL** (for database management).

### Steps to Run Locally

1. **Clone the repository**:

    ```bash
    git clone https://github.com/yourusername/ecommerce.git
    cd ecommerce
    ```

2. **Setup the Database**:
    - Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
    - Open **phpMyAdmin** by going to `http://localhost/phpmyadmin/` in your browser.
    - Create a new database, for example, `ecommerce_db`.
    - Import the required database schema (you can create a `db.sql` file with your tables and initial data if you haven’t already).

3. **Configure the Database Connection**:
    - In your project, find the `includes/db.php` file.
    - Modify the database credentials (hostname, username, password, and database name) to match your XAMPP setup:
    
    ```php
    <?php
    $host = 'localhost'; // Hostname
    $username = 'root';  // Default MySQL username in XAMPP
    $password = '';      // Default MySQL password in XAMPP (empty by default)
    $dbname = 'ecommerce_db'; // Your database name

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```

4. **Start Apache and MySQL**:
    - Open **XAMPP Control Panel**.
    - Start **Apache** and **MySQL** by clicking the "Start" buttons next to them.

5. **Access the Application**:
    - Open your browser and go to `http://localhost/ecommerce/` to view the project.

## Usage

- Browse the products listed on the homepage.
- Use the search bar to search for products by name or description.
- Filter products by category or price range.
- Add products to the shopping cart (this is a basic feature).





## Acknowledgments

- Inspiration from various eCommerce websites.
- PHP and MySQL documentation.
