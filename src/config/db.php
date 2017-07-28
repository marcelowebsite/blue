<?php
    class db{
        //Proprieties
        public $connection;
        private $dbhost = 'localhost';
        private $dbuser = 'root';
        private $dbpass = '';
        private $dbname = 'blueprintsprograms';

        //Connection
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname;";
            $connection = new PDO($mysql_connect_str,$this->dbuser,$this->dbpass);
            $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $connection;
        }

        private function confirm_query($result){


            if(!$result) {

                die("Query Failed" . $this->connection->error);

            }

        }

        public function escape_string($string) {


            $escaped_string = $this->connection->real_escape_string($string);

            return $escaped_string;


        }



        public function the_insert_id() {

            return $this->connection->insert_id;

        }





    }  // End of Class Database


$db = new db();


