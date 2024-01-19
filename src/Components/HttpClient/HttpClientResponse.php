<?php

namespace App\Components\HttpClient;

use CurlHandle;

class HttpClientResponse
{
   private $url;
   private $urlParts;
   private $statusCode;
   private $headers;
   private $body;
   private $connectDuration;
   private $totalDuration;

   /**
    * Constructs a new HttpClientResponse object.
    *
    * @param string $responseBody The response body.
    * @param CurlHandle $curlHandle The CurlHandle object representing the cURL request.
    */
   public function __construct(string $responseBody, CurlHandle $curlHandle)
   {
      $this->body = $responseBody;
      $this->statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
      $this->headers = curl_getinfo($curlHandle, CURLINFO_HEADER_OUT);
      $this->totalDuration = curl_getinfo($curlHandle, CURLINFO_TOTAL_TIME);
      $this->connectDuration = curl_getinfo($curlHandle, CURLINFO_CONNECT_TIME);
      $this->url = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
      $this->urlParts = parse_url($this->url);
   }

   /**
    * Returns true if the response status code indicates a general okay status.
    *
    * @return bool True if the response status code is between 200 and 299 (inclusive), false otherwise.
    */
   public function statusIsOkay(): bool
   {
      return $this->statusBetween(200, 299);
   }

   /**
    * Returns true if the response status code indicates a redirect.
    *
    * @return bool True if the response status code is between 300 and 399 (inclusive), false otherwise.
    */
   public function statusIsRedirect(): bool
   {
      return $this->statusBetween(300, 399);
   }

   /**
    * Returns true if the response status code indicates a client error code.
    *
    * @return bool True if the response status code is between 400 and 499 (inclusive), false otherwise.
    */
   public function statusIsClientError(): bool
   {
      return $this->statusBetween(400, 499);
   }

   /**
    * Returns true if the response status code indicates a server error code.
    *
    * @return bool True if the response status code is between 500 and 599 (inclusive), false otherwise.
    */
   public function statusIsServerError(): bool
   {
      return $this->statusBetween(500, 599);
   }

   /**
    * Returns true if the status code is between min and max (inclusive).
    *
    * @param int $min The minimum status code.
    * @param int $max The maximum status code.
    * @return bool True if the status code is between min and max (inclusive), false otherwise.
    */
   private function statusBetween(int $min, int $max): bool
   {
      return $this->statusCode >= $min && $this->statusCode <= $max;
   }

   /**
    * Returns the HTTP status code of the response.
    *
    * @return int The HTTP status code.
    */
   public function getStatusCode(): int
   {
      return $this->statusCode;
   }

   public function getDomainName()
   {
      return $this->urlParts['host'];
   }

   /**
    * Returns the headers of the response.
    *
    * @return mixed The headers of the response.
    */
   public function getHeaders()
   {
      return $this->headers;
   }

   /**
    * Returns the body of the response.
    *
    * @return string The body of the response.
    */
   public function getBody(): string
   {
      return $this->body;
   }

   /**
    * Returns the total time taken for the entire HTTP request-response cycle in milliseconds.
    *
    * @return float The total duration in milliseconds.
    */
   public function getTotalDurationMs(): float
   {
      return $this->totalDuration * 1000;
   }

   /**
    * Returns the time taken to establish the connection with the remote server in milliseconds.
    *
    * @return float The connection duration in milliseconds.
    */
   public function getConnectDurationMs(): float
   {
      return $this->connectDuration * 1000;
   }

   public function getUrl()
   {
      return $this->url;
   }
}
