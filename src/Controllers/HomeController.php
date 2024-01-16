<?php

namespace Elemizer\App\Controllers;

use Elemizer\App\Components\Template;

class HomeController
{
   function home()
   {
      return Template::render('home');
   }

   function about()
   {
      return Template::render('about');
   }
}
