<?php
require_once("config.php");
//require_once(__WEBROOT__ . "/configuration/vendor/autoload.php");

class Configuration extends PDO {

    private static $connection = null;
    private static $websiteRootDirectory = null;
    private static $view = null;

    private function __construct() {

        try {
            // Establish MySQL connection using the PDO class.
            parent::__construct(DNS, USERNAME, PASSWORD);
            //echo "Could Connect to Database";
        }
        catch (PDOException $e) {
            //echo "Could NOT Connect to Database";
        }

    }

    public static function loadModels() {
        // Used to establish references to the models, and the configuration class will be moved outside of the website's root directory.
        /**
         * Loads all of the class files in the models directory.
         */
        foreach(new DirectoryIterator(__WEBROOT__ . '/configuration/models') as $classFile) {
            // Check to see if the file is a PHP file.
            if (strpos($classFile, '.php') != false) {
                require_once(__WEBROOT__ . '/configuration/models/'. $classFile);
            }
        }
    }

    public static function loadControllers() {
        // Used to establish references to the controllers, and the configuration class will be moved outside of the website's root directory.
        /**
         * Loads all of the class files in the controllers directory.
         */
        foreach(new DirectoryIterator(__WEBROOT__ . '/configuration/controllers') as $classFile) {
            // Check to see if the file is a PHP file.
            if (strpos($classFile, '.php') != false) {
                require_once(__WEBROOT__ . '/configuration/controllers/'. $classFile);
            }
        }
    }

    public static function loadGuzzle() {
        $guzzleClient = new GuzzleHttp\Client();
        return $guzzleClient;
    }

    public static function getApi() {
        // Add a passed API key to validate
        $api_key = "<key>";

        return "http://localhost/wsfia/configuration/api.php";
    }
    /**
     * Creates a connection to the MySQL database.
     */
    public static function openConnection() {
        // Create a new instance of the Database class if connMySQL isn't set.
        if (!(self::$connection instanceof Database)) {
            self::$connection = new Configuration();
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$connection;
    }
    /**
     * Closes the connection to the MySQL database.
     */
    public static function closeConnection() {
        self::$connection = null;
        return true;
    }
    /**
     * Closes the connection to the MySQL database.
     */
    public static function rootDirectory() {
        return __WEBROOT__;
    }
    /**
     * Render the page view.
     */
    public static function renderView($view, $controllerJSON) {
        
        if (file_exists(__WEBROOT__ . '/configuration/views/'.$view.'.php')) {
            // NEED TO CREATE A WAY TO HANDLE THE JSON IN THE VIEW FILE
            echo '<script>' . $controllerJSON->response . '</script>';
            require_once(__WEBROOT__ . '/configuration/views/'.$view.'.php');
        }
        else {
            throw new Exception('View not found');
        }
        
    }

    public static function setView($view) {
        $_SESSION['view'] = $view;
    }


    /**
     * Creates the WSFIA database via a SQL file.
     */
    public function createDatabase() {
        
        $sqlFile = file_get_contents("wsfia.sql");

        try {
            self::databaseConnection()->prepare($sqlFile)->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }

    }

}

?>