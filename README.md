# Code Structure: 

Our codebase uses Angular for the front end and php for the backend. The codebase is separated into three main parts. 
The js folder contains various sections of angular controllers, directives, and modules that are used for each of the different displays.
The php folder contains all of the php files, which include all of the queries we used in order to retrieve our data.
The database folder contains files to drop and create tables, and load data into the tables (note that you will have to change the path in the load file to load the data yourself). Most of the data is contained in text files, and the python scraping scripts used to acquire this data are included.
Note that we also used Professor Yang's database, which he provided to us, in order to get a lot of our data. Both his and our data are needed for some of our queries to run.
We also have an index and css file in the root directory, which is the base for our website.
The rest of the folders and files contain dependencies for our front end.

# How to use our code base:

In order to use our system, you need to be able to control the php server which is the base of our program. In order to do this we used XAMPP (although presumably other programs will work), which provides a quick way to get a php server up and running. We placed our whole repository into the xampp/htdocs folder, and also created our database in the same folder. Then open the XAMPP control panel and start the apache module. Then navigate your web browser to localhost/316Final and the web page should be running.
In order to set up the database, create the empty database in xampp/htdocs. Then in order to load our data into it, first create the tables from 316Final/database/sql/create.sql. Then in the same folder run load.sql. Then load Professor Yang's data. At this point everything should be functional and you should be able to use the website.

# Limitations:
