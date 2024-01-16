<?php

namespace Elemizer\App\Components;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class APIResponse
{
   /**
    * HTTP status code for a successful response.
    */
   const HTTP_STATUS_OK = 200;

   /**
    * HTTP status code for a validation error response.
    */
   const HTTP_STATUS_VALIDATION_ERROR = 422;

   /**
    * API status for a successful response.
    */
   const API_STATUS_SUCCESS = 'success';

   /**
    * API status for an error response.
    */
   const API_STATUS_ERROR = 'error';

   /**
    * API status for a validation error response.
    */
   const API_STATUS_VALIDATION_ERROR = 'validation-error';

   /**
    * API status for an error message response.
    */
   const API_STATUS_ERROR_MESSAGE = 'error-message';

   /**
    * Create and send successful API response.
    *
    * @param array $data The data to be included in the response.
    * @return void
    */
   public static function emitSuccessData(array $data)
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
   public static function emitErrorMessage(string $errorMessage)
   {
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
   public static function emitValidationError(ErrorBag $errorBag)
   {
      response()->httpCode(APIResponse::HTTP_STATUS_VALIDATION_ERROR);
      response()->json([
         'status' => APIResponse::API_STATUS_VALIDATION_ERROR,
         'errors' => $errorBag->getErrors()
      ]);
   }
}
