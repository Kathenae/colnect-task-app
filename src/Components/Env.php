<?php

namespace App\Components;

use Dotenv\Dotenv;

class Env
{
   const ENV_MODE = "ENV";
   const DB_DRIVER = 'DATABASE_DRIVER';
   const DB_HOST = 'DATABASE_HOST';
   const DB_PORT = 'DATABASE_PORT';
   const DB_NAME = 'DATABASE_NAME';
   const DB_USERNAME = 'DATABASE_USER';
   const DB_PASSWORD = 'DATABASE_PASSWORD';

   private static DotEnv $dotenv;

   /**
    * Retrieves the value of an environment variable.
    *
    * @param string $name The name of the environment variable.
    * @param string|null $default The default value to return if the environment variable is not set.
    * @return string|null The value of the environment variable, or the default value if not set.
    */
   public static function get(string $name, string $default = null)
   {
      if (!isset($_ENV[$name])) {
         return $default;
      }

      return $_ENV[$name];
   }

   /**
    * Initializes the Dotenv library to load environment variables from a .env file.
    *
    * @return void
    */
   public static function init()
   {
      self::$dotenv = Dotenv::createImmutable('./');
      self::$dotenv->load();
   }
}
