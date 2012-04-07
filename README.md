# MY_Model
A simple Codeigniter MY_Model library with CRUD automation, created by me (Nicola Amatucci) and developed also with the help of Isidoro Mercogliano. It's used in project Mercucci ([http://www.mercucci.it/](http://www.mercucci.it/)).

Function names are in Italian. I'll plan to translate in English in the future. Let's the code talk himself :-)

# EXAMPLE

Imagine you have a table named "user_table" with three fields: "id", "name", "password". The model will be:

    class User_model extends MY_Model {
        var $name;
        var $password;

        public function construct(){
            parent::construct('user_table');
        }
    }

Where $id is declared in MY_Model and is nedeed for CRUD. Hopefully "id" will be an "autoincrement primary key" field in the database.

You can fill the fields of a user_table instance and insert into database using "$instance->inserisci()" ("inserisci" stands for "insert").

Examine MY_Model.php code to learn about other easying function. For example you can create a new object filled with $_POST fields using

    function newFromPost()

or fill an existing object with $_POST fields

    function fillFromPost()

Have fun! :-)
