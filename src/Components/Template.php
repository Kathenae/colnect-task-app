<?php

namespace Elemizer\App\Components;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Template
{
   private static Environment $twig;

   /**
    * Renders a Twig template with the given parameters.
    *
    * @param string $templateName The name of the template to render.
    * @param array $params An associative array of parameters to pass to the template.
    * @return string The rendered template content.
    */
   public static function render(string $templateName, array $params = []): string
   {
      return self::$twig->render("$templateName.twig.html", $params);
   }

   /**
    * Initializes the Twig environment and sets up the template loader.
    */
   public static function init()
   {
      $loader = new FilesystemLoader('src/templates');
      self::$twig = new Environment($loader, []);
   }
}
