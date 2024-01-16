<?php

namespace Elemizer\App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Element
{
   #[ORM\Id]
   #[ORM\Column(type: 'integer')]
   #[ORM\GeneratedValue]
   private int|null $id;


   #[ORM\Column(type: 'string')]
   private string $name;

   public function setName(string $name)
   {
      $this->name = $name;
   }

   public function getName(): string
   {
      return $this->name;
   }
}
