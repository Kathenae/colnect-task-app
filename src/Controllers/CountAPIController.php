<?php

namespace App\Controllers;

use DateTime;
use App\Components\APIResponse;
use App\Components\Database;
use App\Components\ErrorBag;
use App\Components\HtmlInspector;
use App\Components\HttpClient\HttpClient;
use App\Models;
use App\Models\Request;
use Exception;

class CountAPIController
{
   const FIELD_URL = "targetUrl";
   const FIELD_ELEMENT = "targetElement";

   /**
    * Handles the request for counting elements in a web page.
    */
   public function index()
   {
      try {
         $inputs = $this->getValidatedInputs();
         $targetUrl = $inputs[self::FIELD_URL];
         $targetElement = $inputs[self::FIELD_ELEMENT];

         // Find request in the last 5 minute
         $request = Request::findOneByTime($targetUrl, $targetElement, new DateTime("-5 minutes"));

         if (!isset($request)) {
            // Fetch URL and handle errors.
            $httpClient = new HttpClient($targetUrl);
            $response = $httpClient->request($targetUrl);

            if ($response->statusIsOkay() == false) {
               APIResponse::emitErrorMessage();
            }

            $request = Models\Request::create(
               elementName: $targetElement,
               urlName: $targetUrl,
               domainName: $response->getDomainName(),
               durationMs: $response->getConnectDurationMs(),
               elementCount: HtmlInspector::countElement($targetElement, $response->getBody()),
            );
            Database::manager()->persist($request);
            Database::manager()->flush();
         }

         $results = $request->results();
         APIResponse::emitSuccessData($results);
      } catch (Exception $e) {
         throw $e;
         APIResponse::emitErrorMessage();
      }
   }

   /**
    * Validates and retrieves the inputs from the request.
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
