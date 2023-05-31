Steps to make this example work
===============================

-------------------------------------
1: Edit application/config/config.php
-------------------------------------

You need to set the $config['base_url'] to the location you are saving this hmvcExample folder. 
Make sure you have the trailing '/' at the end like the example below:

$config['base_url'] = "http://localhost/hmvcExample/";


---------------------------------------
2: Edit application/config/database.php
---------------------------------------

You need to edit your database information to reflect your database.
Information you need is: Hostname, username, password.

$db['default']['hostname'] = "localhost";
$db['default']['username'] = "root";
$db['default']['password'] = "drowssap";

The database should be called ci_series. But if you must change it be sure that you edit the membership.sql.txt file to reflect the new name


------------------
3: Create Database 
------------------

Using PHPMyAdmin or whatever you wish make sure you have a database created called ci_series (unless you made a different name from step 2)


-----------------------------------------
4: Import Empty Membership database table
-----------------------------------------

Again using your DB tool of choice use the membership.sql.txt file to import/paste the SQL to create the membership table required for the "login" and "create account" functions