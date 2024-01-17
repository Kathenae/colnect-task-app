<?php

namespace App\Controllers;

use App\Components\Template;

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
