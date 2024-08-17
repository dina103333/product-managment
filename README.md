# User Authentication and Product Management API

## Introduction

This is an API designed for managing users and products. The system is built using the Laravel web framework and a relational database. It supports user authentication, product management, and role-based authorization, making it suitable for a variety of use cases, including e-commerce platforms.


## Technology Used

- Laravel.
- Repository Pattern
    - Abstracts the data access layer from the rest of the application and provides a clean separation of concerns.
    - A repository is created for each model (Trip,Seat,Reservation,User) which are responsible for fetching and updating the data.
    - Allows for easy switching of data sources or updating the data access logic without affecting the rest of the application.
- Mysql Database.

## Installation and Usage

### Running the Project

1. Clone the repository to your local machine using `git clone`.

### Running the Test Cases

1. Run the test cases using `docker compose exec php php artisan test`.

# API Documentation

## Register Endpoint

### POST ```/api/users/register```

Creates a new user in the system.

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "adming6.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "Super Admin", //this value must be User or  Super Admin
    "addresses": [
        {
            "address": "123 Main St, Apt 4B",
            "is_checkpoint": true
        },
        {
            "address": "456 Oak St"
        }
    ]
}
```

## Login Endpoint

### POST ```/api/users/login```

Login a user to the system.

**Request Body:**

```json
{
    "email": "adming6.doe@example.com",
    "password": "password123"
}
```

## Reset password Endpoint

### POST ```/api/users/reset-password```

Reset password of user.

**Request Body:**

```json
{
    "email": "jocchn.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

## Users Endpoint

### GET ```/api/users/4```

Get user by id.

## Update user Endpoint

### PUT ```/api/users/4```

update the user data.

**Request Body:**

```json
{
    "email": "jocchn.doe@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "User" //this value must be User or  Super Admin
}
```

## Delete user Endpoint

### DELETE ```/api/users/4```

delete the user data.

## Products Endpoint

## Create product Endpoint

### POST ```/api/products```

crate new product.

**Request Body:**

```json
{
    "name": " ",
    "description": "This is an example product description.",
    "prices": {
        "USD": 19.99,
        "EUR": 17.99,
        "GBP": 15.99
    },
    "stock_quantity": 100
}
```

## Show the product data

### GET ```/api/products/1```

show the specific product data .


## Update product Endpoint

### PUT ```/api/products/24```

update the product data.

**Request Body:**

```json
{
    "name": "Example Product",
    "description": "This is an example product description.",
    "prices": {
        "USD": 20.99,
        "EUR": 50.99,
        "GBP": 15.99
    },
    "stock_quantity": 100
}
```

## Delete product Endpoint

### DELETE ```/api/products/4```

delete the product data.
