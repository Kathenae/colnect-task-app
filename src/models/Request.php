<?php

namespace App\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use App\Components\Database;

#[ORM\Entity]
class Request
{
   #[ORM\Id]
   #[ORM\Column(type: 'integer')]
   #[ORM\GeneratedValue]
   private int|null $id;

   public function __construct(
      #[ORM\Column(type: 'float')]
      private float $durationMs,

      #[ORM\Column(type: 'integer')]
      private int $elementCount,

      #[ORM\Column(type: 'datetime')]
      private DateTime $fetchedAt,

      #[ManyToOne(targetEntity: Domain::class)]
      private Domain $domain,

      #[ManyToOne(targetEntity: Url::class)]
      private Url $url,

      #[ManyToOne(targetEntity: Element::class)]
      private Element $element
   ) {
   }

   public function results()
   {
      $domainTotalUrls = $this->domainTotalUrls();
      $averageFetchTime = $this->getAvgResponseTime(new DateTime('-24 hours'));
      $elementCountDomain = $this->countElementsForDomain();
      $elementCountAll = $this->countElementsForAllRequest();

      return [
         'elementCount' => $this->elementCount,
         'elementName' => $this->element->getName(),
         'domainName' => $this->domain->getName(),
         'urlName' => $this->url->getName(),
         'fetchedAt' => $this->fetchedAt,
         'fetchDurationMs' => $this->durationMs,
         'stats' => [
            'domainTotalUrls' => $domainTotalUrls,
            'domainAvgResponseTime' => $averageFetchTime,
            'elementsCountOnDomain' => $elementCountDomain,
            'elementCountOnAllRequests' => $elementCountAll
         ]
      ];
   }

   public function domainTotalUrls()
   {
      $entityManager = Database::manager();

      $query = $entityManager->createQuery(<<<EOD
      SELECT 
         COUNT(DISTINCT(r.url)) 
      FROM 
         App\Models\Request r 
      JOIN 
         r.domain d where d = :domain
      EOD);

      $query->setParameter('domain', $this->domain);
      $count = $query->getSingleScalarResult();
      return $count;
   }

   public function getAvgResponseTime(DateTime $timespan)
   {
      $query = Database::manager()->createQuery(<<<EOD
      SELECT
          AVG(r.durationMs) 
      FROM App\Models\Request r 
      JOIN 
         r.domain d WHERE d = :domain AND r.fetchedAt >= :timespan         
      EOD);
      $query->setParameter('domain', $this->domain);
      $query->setParameter('timespan', $timespan);
      $avg = $query->getSingleScalarResult();
      return $avg;
   }

   public function countElementsForDomain()
   {
      $query = Database::manager()->createQuery(<<<EOD
      SELECT 
         SUM(r.elementCount) 
      FROM App\Models\Request r 
      JOIN 
         r.domain d 
      JOIN 
         r.element e 
      WHERE 
         d = :domain AND e = :element
      AND NOT EXISTS (
         SELECT 
            other
         FROM 
            App\Models\Request other 
         WHERE 
            other.url = r.url 
            AND other.fetchedAt > r.fetchedAt
      )
      EOD);
      $query->setParameter('domain', $this->domain);
      $query->setParameter('element', $this->element);
      $sum = $query->getSingleScalarResult();
      return $sum;
   }

   public function countElementsForAllRequest()
   {
      $query = Database::manager()->createQuery(<<<EOD
      SELECT 
         sum(r.elementCount)
      FROM 
         App\Models\Request r 
      JOIN 
         r.element e 
      WHERE e = :element
      AND NOT EXISTS (
         SELECT 
            other
         FROM 
            App\Models\Request other 
         WHERE 
            other.url = r.url 
            AND other.fetchedAt > r.fetchedAt
      )
      EOD);
      $query->setParameter('element', $this->element);
      $sum = $query->getSingleScalarResult();
      return $sum;
   }

   public static function findOneByTime(string $url, string $elementName, DateTime $timespan): Request | null
   {
      $query = Database::manager()->createQuery(<<<EOD
      SELECT
          r
      FROM App\Models\Request r 
      JOIN 
         r.url u 
      JOIN
         r.element e
      WHERE 
         u.name = :urlName AND e.name = :elementName AND r.fetchedAt >= :timespan   
      ORDER BY
         r.fetchedAt DESC   
      EOD);
      $query->setParameter('urlName', $url);
      $query->setParameter('elementName', $elementName);
      $query->setParameter('timespan', $timespan);
      $query->setMaxResults(1);
      $result = $query->getOneOrNullResult();
      return $result;
   }

   public static function create(string $elementName, int $elementCount, string $domainName, string $urlName, float $durationMs, DateTime $fetchedAt = new DateTime())
   {
      // find domain or create new
      $domain = Database::manager()->getRepository(Domain::class)->findOneBy(['name' => $domainName]);
      if (!$domain) {
         $domain = new Domain();
         $domain->setName($domainName);
         Database::manager()->persist($domain);
      }

      $url = Database::manager()->getRepository(Url::class)->findOneBy(['name' => $urlName]);
      if (!$url) {
         $url = new Url();
         $url->setName($urlName);
         Database::manager()->persist($url);
      }

      $element = Database::manager()->getRepository(Element::class)->findOneBy(['name' => $elementName]);
      if (!$element) {
         $element = new Element();
         $element->setName($elementName);
         Database::manager()->persist($element);
      }

      // Create a new Request
      $request = new Request(
         domain: $domain,
         url: $url,
         element: $element,
         elementCount: $elementCount,
         durationMs: $durationMs,
         fetchedAt: $fetchedAt,
      );

      return $request;
   }
}
