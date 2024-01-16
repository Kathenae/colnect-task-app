<?php

namespace Elemizer\App\Controllers;

use DateTime;
use Elemizer\App\Components\APIResponse;
use Elemizer\App\Components\Database;
use Elemizer\App\Components\ErrorBag;
use Elemizer\App\Components\HtmlInspector;
use Elemizer\App\Components\HttpClient\HttpClient;
use Elemizer\App\Components\HttpClient\HttpClientResponse;
use Elemizer\App\Models\Domain;
use Elemizer\App\Models\Element;
use Elemizer\App\Models\Request;
use Elemizer\App\Models\Url;

class CountAPIController
{
   const FIELD_URL = "targetUrl";
   const FIELD_ELEMENT = "targetElement";

   /**
    * Handles the request for counting elements in a web page.
    *
    * @return void
    */
   public function index()
   {
      // Get inputs
      $inputs = $this->getValidatedInputs();
      $targetUrl = $inputs[self::FIELD_URL];
      $targetElement = $inputs[self::FIELD_ELEMENT];

      // Fetch URL and handle errors.
      $httpClient = new HttpClient($targetUrl);
      $response = $httpClient->request($targetUrl);
      $this->handleErrors($response);

      // Parse response data
      $elementCount = HtmlInspector::countElement($targetElement, $response->getBody());
      $domainName = $response->getDomainName();
      $duration = $response->getTotalDurationMs();
      $time = new DateTime();

      $this->saveRequestDetails($targetElement, $elementCount, $domainName, $targetUrl, $time, $duration);

      APIResponse::emitSuccessData([
         'domainName' => $domainName,
         'url' => $targetUrl,
         'element' => $targetElement,
         'count' => $elementCount,
         'fetchDurationMs' => $duration,
         'fetchedAt' => $time,
      ]);
   }

   private function saveRequestDetails(string $targetElement, int $elementCount, string $domainName, string $targetUrl, DateTime $dateTime, float $duration)
   {
      // Check if the Domain already exists
      $domain = Database::manager()->getRepository(Domain::class)->findOneBy(['name' => $domainName]);

      if (!$domain) {
         // Create a new Domain if it doesn't exist
         $domain = new Domain();
         $domain->setName($domainName);
         Database::manager()->persist($domain);
      }

      // Check if the Url already exists
      $url = Database::manager()->getRepository(Url::class)->findOneBy(['name' => $targetUrl]);

      if (!$url) {
         // Create a new Url if it doesn't exist
         $url = new Url();
         $url->setName($targetUrl);
         Database::manager()->persist($url);
      }

      // Check if the Element already exists
      $element = Database::manager()->getRepository(Element::class)->findOneBy(['name' => $targetElement]);

      if (!$element) {
         // Create a new Element if it doesn't exist
         $element = new Element();
         $element->setName($targetElement);
         Database::manager()->persist($element);
      }

      // Create a new Request
      $request = new Request();
      $request->setDuration($duration);
      $request->setElementCount($elementCount);
      $request->setTime($dateTime);
      $request->setDomain($domain);
      $request->setUrl($url);
      $request->setElement($element);

      Database::manager()->persist($request);
      Database::manager()->flush();

      return $request;
   }

   /**
    * Handles errors in the HTTP response.
    *
    * @param HttpClientResponse $response The HTTP response.
    * @return void
    */
   private function handleErrors(HttpClientResponse $response)
   {
      if ($response->statusIsOkay() == false) {
         if ($response->statusIsRedirect()) {
            // TODO: Maybe for 301, 302 and 307 redirects we should fetch the page we're redirected to instead
            APIResponse::emitErrorMessage('Remote server responded with an unhandled redirect response');
         } else {
            APIResponse::emitErrorMessage('Remote server responded with an unknown error response');
         }
      }
   }

   /**
    * Validates and retrieves the inputs from the request.
    *
    * @return array|null An associative array of validated inputs if there are no validation errors, null otherwise.
    */
   private function getValidatedInputs()
   {
      $targetUrl = input(self::FIELD_URL);
      $targetElement = input(self::FIELD_ELEMENT);
      $targetUrl = strtolower($targetUrl);
      $targetElement = strtolower($targetElement);
      $errorBag = new ErrorBag();

      if (filter_var($targetUrl, FILTER_VALIDATE_URL) == false) {
         $errorBag->addError(self::FIELD_URL, "You have entered an invalid URL");
      }

      if (!HtmlInspector::isValidElement($targetElement)) {
         $errorBag->addError(self::FIELD_ELEMENT, "This is not a valid HTML element");
      }

      if ($errorBag->hasErrors()) {
         APIResponse::emitValidationError($errorBag);
         return null;
      }

      return [
         self::FIELD_URL => $targetUrl,
         self::FIELD_ELEMENT => $targetElement,
      ];
   }
}
