<?php

namespace App\Components;

class ErrorBag
{
   private array $errors = [];

   /**
    * Adds an error message to the error bag for a specific input name.
    *
    * @param string $inputName The name of the input.
    * @param string $message The error message.
    * @return void
    */
   public function addError(string $inputName, string $message)
   {
      $this->errors[$inputName][] = $message;
   }

   /**
    * Retrieves all the errors stored in the error bag.
    *
    * @return array An associative array of errors, where the keys are the input names and the values are arrays of error messages.
    */
   public function getErrors()
   {
      return $this->errors;
   }

   /**
    * Checks if the error bag contains any errors.
    *
    * @return bool True if the error bag has errors, false otherwise.
    */
   public function hasErrors()
   {
      return count($this->errors) > 0;
   }
}
