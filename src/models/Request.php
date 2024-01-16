<?php

namespace Elemizer\App\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity]
class Request
{
   #[ORM\Id]
   #[ORM\Column(type: 'integer')]
   #[ORM\GeneratedValue]
   private int|null $id;

   #[ORM\Column(type: 'float')]
   private float $duration;

   #[ORM\Column(type: 'integer')]
   private int $elementCount;

   #[ORM\Column(type: 'datetime')]
   private DateTime $time;

   #[ManyToOne(targetEntity: Domain::class)]
   private Domain $domain;

   #[ManyToOne(targetEntity: Url::class)]
   private Url $url;

   #[ManyToOne(targetEntity: Element::class)]
   private Element $element;

   public function setDomain(Domain $domain)
   {
      $this->domain = $domain;
   }

   public function getDomain(): Domain
   {
      return $this->domain;
   }

   public function setUrl(Url $url)
   {
      $this->url = $url;
   }

   public function getUrl(): Url
   {
      return $this->url;
   }

   public function setElement(Element $element)
   {
      $this->element = $element;
   }

   public function getElement(): Element
   {
      return $this->element;
   }

   public function setTime(DateTime $time)
   {
      $this->time = $time;
   }

   public function getTime(): DateTime
   {
      return $this->time;
   }

   public function setDuration(float $duration)
   {
      $this->duration = $duration;
   }

   public function getDuration(): float
   {
      return $this->duration;
   }

   public function setElementCount(int $elementCount)
   {
      $this->elementCount = $elementCount;
   }

   public function getElementCount(): float
   {
      return $this->elementCount;
   }
}
