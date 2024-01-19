<?php

namespace App\Controllers;

use DateTime;
use App\Components\APIResponse;
use App\Components\Database;
use App\Components\ErrorBag;
use App\Components\HtmlInspector;
use App\Components\HttpClient\HttpClient;
use App\Components\HttpClient\HttpClientResponse;
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
         $inputs = $this->validateInputs();
         $targetUrl = $inputs[self::FIELD_URL];
         $targetElement = $inputs[self::FIELD_ELEMENT];

         // Find request in the last 5 minutes
         $request = Request::findOneByTime($targetUrl, $targetElement, new DateTime("-5 minutes"));

         // If no request sent for the same url and elemen in the last 5 minutes
         if (!isset($request)) {

            // Fetch url and handle status
            $httpClient = new HttpClient($targetUrl);
            $response = $httpClient->request(url: $targetUrl, followRedirects: false);
            $errorMessage = $this->checkForErrors($response);

            if (isset($errorMessage)) {
               APIResponse::errorResponse($errorMessage);
            }

            // Save request details
            $request = Request::create(
               elementName: $targetElement,
               urlName: $targetUrl,
               domainName: $response->getDomainName(),
               durationMs: $response->getTotalDurationMs(),
               elementCount: HtmlInspector::countElement($targetElement, $response->getBody()),
            );
            Database::manager()->persist($request);
            Database::manager()->flush();
         }

         // finally get request results and send them to the browser
         $results = $request->results();
         APIResponse::okResponse($results);
      } catch (Exception $e) {
         error_log($e->getMessage());
         APIResponse::errorResponse();
      }
   }

   /**
    * Validates and retrieves the inputs from the request.
    */
   private function validateInputs()
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
         APIResponse::validationResponse($errorBag);
      }

      return [
         self::FIELD_URL => $targetUrl,
         self::FIELD_ELEMENT => $targetElement,
      ];
   }

   private function checkForErrors(HttpClientResponse $response)
   {
      if ($response->statusIsOkay() == false) {
         $url = $response->getUrl();
         $domain  = $response->getDomainName();
         $statusCode = $response->getStatusCode();
         $errorMessage = null;

         switch ($statusCode) {
            case 400:
               $errorMessage = "Bad Request: The external server could not understand the request.";
               break;
            case 401:
               $errorMessage = "Unauthorized: we can only analyse publicly accessible pages";
               break;
            case 403:
               $errorMessage = "Access Denied: The external server at $url denied access to this page.";
               break;
            case 404:
               $errorMessage = "Page Not Found: The page at $url was not found. Please make sure you entered the correct URL.";
               break;
            case 405:
               $errorMessage = "Method Not Allowed: we can only send GET requests to remote servers";
               break;
            case 408:
               $errorMessage = "Request Timeout: The connection to the external at $domain server timed out.";
               break;
            case 409:
               $errorMessage = "Conflict: There is a conflict with the current state of the external resource at $url.";
               break;
            case 429:
               $errorMessage = "Too Many Requests: You have exceeded the rate limit for accessing the external resource at $domain.";
               break;
            default:
               if ($response->statusIsRedirect()) {
                  $errorMessage = "Redirected: server at $domain redirected to another location";
               }
               $errorMessage = "Unable to fetch data from the external server at $url.";
               break;
         }

         return $errorMessage;
      }
   }
}
