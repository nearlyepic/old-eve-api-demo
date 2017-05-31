# old-eve-api-demo
A collection of my old scripts (2015 or so) that I created when learning the EVE Online API, and PHP in general.

Nothing is pretty here, but it's good to reflect on your old code and identify ways in which you've improved.

`facwar.php` is a standalone script, it queries the EVE Online API and searches for low-activity systems. These systems are ideal for farming Loyalty Points by taking control of complexes throughout the system.

There is a rudimentary login and registration system implemented. You can get an API key from the EVE Online website if you have an account. (They're free to create!)

`charsheet.php` is another standalone script, It uses a Key ID and vCode to get the account balance, name, and picture of all characters on an account.

This is very much an example of the "hard way" to do PHP (no framework, no templating), and not necessarily reflective of my current skill. If I were to rebuild this, I would absolutely use a framework such as Laravel.

NOTE: This requires you to set up a database with the EVE Online Static Data Export. You can find the SQL for this at [Fuzzwork](https://www.fuzzwork.co.uk/dump/).
