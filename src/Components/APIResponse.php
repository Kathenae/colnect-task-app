<?php

namespace App\Components;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class APIResponse
{
   const HTTP_STATUS_OK = 200;
   const HTTP_STATUS_VALIDATION_ERROR = 422;
   const API_STATUS_SUCCESS = 'success';
   const API_STATUS_ERROR = 'error';
   const API_STATUS_VALIDATION_ERROR = 'validation-error';
   const API_STATUS_ERROR_MESSAGE = 'error-message';
   const DEFAULT_ERROR_MESSAGE = 'Something went wrong. please contact the developer!';

   /**
    * Create and send successful API response.
    *
    * @param array $data The data to be included in the response.
    * @return void
    */
   public static function okResponse(array $data = null)
   {
      response()->httpCode(APIResponse::HTTP_STATUS_OK);
      response()->json([
         'status' => APIResponse::API_STATUS_SUCCESS,
         'data' => $data
      ]);
   }

   /**
    * Create and send an error message API response.
    *
    * @param string $errorMessage The error message to be included in the response.
    * @return void
    */
   public static function errorResponse(string $errorMessage = self::DEFAULT_ERROR_MESSAGE)
   {
      response()->httpCode(400);
      response()->json([
         'status' => APIResponse::API_STATUS_ERROR_MESSAGE,
         'message' => $errorMessage,
      ]);
   }

   /**
    * Create and send a validation error API response.
    *
    * @param ErrorBag $errorBag The error bag containing validation errors.
    * @return void
    */
   public static function validationResponse(ErrorBag $errorBag)
   {
      response()->httpCode(APIResponse::HTTP_STATUS_VALIDATION_ERROR);
      response()->json([
         'status' => APIResponse::API_STATUS_VALIDATION_ERROR,
         'errors' => $errorBag->getErrors()
      ]);
   }
}
