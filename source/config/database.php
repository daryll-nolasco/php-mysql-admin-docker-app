<?php

    class Database {
        private $dbhost = '172.19.0.3';
        private $dbuser = 'web';
        private $dbpass = 'xdanolasco';
        private $dbname = 'docker_app';
        private $dbport = '3306';
        
        public function connect() {
            $mysql_connect_str = "mysql:host=$this->dbhost;port=$this->dbport;dbname=$this->dbname";
            $db_connection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db_connection;
        }
    }