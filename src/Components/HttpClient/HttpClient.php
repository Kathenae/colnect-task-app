<?php

namespace Elemizer\App\Components\HttpClient;

use Elemizer\App\Components\HttpClient\HttpClientResponse;

class HttpClient
{
   const DEFAULT_USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36';
   const DEFAULT_HEADERS = [
      'Content-Type: text/html',
   ];

   /**
    * Sends an HTTP request to the specified URL.
    *
    * @param string $url The URL to send the request to.
    * @param string $method The HTTP method to use (default: 'GET').
    * @param array $headers Additional headers to include in the request (optional).
    * @param string $data The data to send with the request (optional).
    * @return HttpClientResponse The response object containing the response body and other information.
    */
   public function request($url, $method = 'GET', $headers = [], $data = ''): HttpClientResponse
   {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_USERAGENT, self::DEFAULT_USER_AGENT);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(self::DEFAULT_HEADERS, $headers));

      if ($method === 'POST') {
         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      }

      $responseBody = curl_exec($ch);
      $response = new HttpClientResponse($responseBody, $ch);
      curl_close($ch);

      return $response;
   }
}
