# Voila mini framework

## Description

This repository is a simple PHP MVC structure from scratch.

It uses some cool vendors/libraries such as Twig or whoops debuger.
For this one, just a simple example where users can choose one of their databases and see tables in it.

## Version

Actualy the last stable version is: voila 1.2

## Steps

1. Clone the repo from Github.
2. Run `composer install`.
3. Config *config/db.php* with your DB parameters (or not If you want to work with sqlite) and add the db.php in your .gitignore file.
4. Import `mvcvoila_items.sql` in your SQL server (if you don't use SQLite by default),
5. Run the internal PHP webserver with `php -S localhost:8000 -t public/`. The option `-t` with `public` as parameter means your localhost will target the `/public` folder.
6. Go to `localhost:8000` with your favorite browser.
7. From this starter kit, create your own web application.

### Windows Users

If you develop on Windows, you should edit you git configuration to change your end of line rules with this command :

`git config --global core.autocrlf true`

## URLs availables

* Home page at [localhost:8000/](localhost:8000/)
* Items list at [localhost:8000/item/index](localhost:8000/item)
* Item details [localhost:8000/item/index/show/:id](localhost:8000/item/show/2)
* Item edit [localhost:8000/item/index/edit/:id](localhost:8000/item/edit/2)
* Item add [localhost:8000/item/index/add](localhost:8000/item/add)
* Item deletion [localhost:8000/item/index/delete/:id](localhost:8000/item/delete/2)

## How does URL routing work ?

* url is rewrited by web server
* the first element of the url is the domain name
* the second element bears the name of the controller (without controller, by default Home)
* the third element indicates the method to use in the controller (by default, index)
* the following elements are given as method arguments.

let's take an example with localhost:8000/item/edit/2
1-localhost is the domain name (and port)
2-item will open the ItemController class
3-edit will open the edit(int id) method of ItemController
4-and 2 will be sent as an argument to the above method
