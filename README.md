# MY_SERVICES
#### Description:
My_Services is a simple website that allows you to offer services and products to your clients. This site was built with the purpose of being a practical example of how to work with the symfony framework from the backend. It consists of three types of users, the business owner, an editor and the customer. New customers must register to access all the options of the system.
The business owner can add and edit and consult all the information in the system. The editor can add and edit products and services, as well as have access to the list of products. He can also add quantities of products. The services are paginated and searches are possible.
The customer can search and buy products by accessing the list of services, modify products or services in his shopping cart and see the list of active orders and print it.

## Explaining the project and the database


All information about users and products are stored in warehouse.db.

I needed 8 tables for my database:

- City table: The cities with the cost of shipping are stored.

- Category table: Service categories.

- SubCategory Table: subcategories of services.

- Order Table :Stores Purchase Orders.

- Service table: stores services or products.

- ServiceHistory table: Stores each time product quantities are added.

- OrderService table: Stores the relationship between orders and services or products.

- User table: List of users.

## How to use:

As a customer, after logging in you will see the list of available products. If you want to filter the products by categories you must click on the name of the desired category in the navigation bar.
- When viewing the list of products, if you want to buy any, you must click on the add to cart text. It is possible to remove products from the shopping cart. If you want to pay for the products that appear in the cart you must click on the validate order button. Then you must fill out the form with the delivery information and click on the continue button to begin making the payment. Once completed you will receive an email with the confirmation and details of the order.

As a business owner, after logging in you will have access to all the information in the system. You will be able to add, edit and delete users, cities, categories, subcategories and services and the history of the quantities of services added.
As an editor, after logging in you will be able to add, edit and delete services and add quantities of services. You will also be able to consult the delivered and paid undelivered orders.

## How I made this:
This web application was created using the PHP framework called [Symfony](https://symfony.com/) version 7. It is a project just to demonstrate how to program with symfony in a simple way. It demonstrates how to handle images, generate pdf, perform searches and pagination, send emails and make payments using stripe.
