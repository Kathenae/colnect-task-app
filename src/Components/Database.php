<?php

namespace Elemizer\App\Components;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Elemizer\App\Components\Env;

class Database
{
   private static $entityManager;

   /**
    * Initializes the database connection and entity manager.
    *
    * @param Configuration|null $config The Doctrine ORM configuration (optional).
    * @param Connection|null $connection The Doctrine DBAL connection (optional).
    * @return void
    */
   static function init(Configuration $config = null, Connection $connection = null)
   {
      if (!isset($config)) {
         $paths = ['src/models/'];
         $isDevMode = Env::get(Env::ENV_MODE, 'local') == 'local';
         $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
      }

      if (!isset($connection)) {
         $dbParams = [
            'driver' => Env::get(Env::DB_DRIVER, 'pdo_mysql'),
            'host' => Env::get(Env::DB_HOST, 'localhost'),
            'port' => Env::get(Env::DB_PORT, '3306'),
            'user' => ENV::get(Env::DB_USERNAME, 'root'),
            'password' => Env::get(Env::DB_PASSWORD, ''),
            'dbname' => Env::get(Env::DB_NAME)
         ];
         $connection = DriverManager::getConnection($dbParams, $config);
      }

      self::$entityManager = new EntityManager($connection, $config);
   }

   /**
    * Returns the entity manager.
    *
    * @return EntityManager The Doctrine entity manager.
    */
   static function manager()
   {
      return self::$entityManager;
   }
}
