<?php

namespace Elemizer\App\Controllers;

use DateTime;
use Elemizer\App\Components\APIResponse;
use Elemizer\App\Components\Database;
use Elemizer\App\Components\ErrorBag;
use Elemizer\App\Components\HtmlInspector;
use Elemizer\App\Components\HttpClient\HttpClient;
use Elemizer\App\Models;

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

      if ($response->statusIsOkay() == false) {
         APIResponse::emitErrorMessage('There was a problem analysing the page.');
      }

      // Parse response data
      $elementCount = HtmlInspector::countElement($targetElement, $response->getBody());
      $domainName = $response->getDomainName();
      $duration = $response->getTotalDurationMs();
      $time = new DateTime();

      // Create request info with obtained info
      $request = Models\Request::create(
         elementName: $targetElement,
         elementCount: $elementCount,
         domainName: $domainName,
         urlName: $targetUrl,
         time: $time,
         duration: $duration
      );

      Database::manager()->persist($request);
      Database::manager()->flush();

      APIResponse::emitSuccessData($request->results());
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
