<?php

//connect to the database
mysql_connect('localhost', '<yourusername>', '<yourpassword>') or die("I couldn't connect to your database, please make sure your info is correct!");
mysql_select_db('confirm_new') or die("I couldn't find the database table ($table) make sure it's spelled right!");
