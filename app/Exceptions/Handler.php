<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
  /**
   * The list of the inputs that are never flashed to the session on validation exceptions.
   *
   * @var array<int, string>
   */
  protected $dontFlash = [
  	'current_password',
  	'password',
  	'password_confirmation',
  ];
  
  /**
   * Register the exception handling callbacks for the application.
   *
   * @return void
   */

  public function render($request, Throwable $exception)
  {
      // if ($exception instanceof ModelNotFoundException) {
      //     return redirect('/404-not-found');
      // }
      // // // custom error message
      // if ($exception  instanceof \ErrorException) {
      //     return redirect('/404-not-found');
      // }
  	return parent::render($request, $exception);
  }
  /**
   * Register the exception handling callbacks for the application.
   */
  public function register(): void
  {
  	$this->reportable(function (Throwable $e) {
          //
  	});
  }
}
