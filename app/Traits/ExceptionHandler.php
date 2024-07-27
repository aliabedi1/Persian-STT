<?php

namespace App\Traits;


use App\Enums\SystemMessage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundation;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): \Illuminate\Http\Response
    {
        // Check if the request is for an API endpoint
        // If not, delegate the exception rendering to the parent class
        if (!$this->isApi($request))
            return parent::render($request, $e);

        /**
         * Exception mapping for handling different types of exceptions.
         *
         * The array maps exception classes to their corresponding response settings.
         * Each entry in the array consists of the following fields:
         * - code: The error code to be used in the response.
         * - message: The error message to be displayed.
         * - errors (optional): Additional errors or validation messages (if applicable).
         * - http_status: The HTTP status code for the response.
         *
         */
        $exceptionMap = [
            NotFoundHttpException::class => [
                'code' => SystemMessage::PAGE_NOT_FOUND,
                'message' => __('Not Found'),
                'http_status' => HttpFoundation::HTTP_NOT_FOUND
            ],
            AuthorizationException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => $e->getMessage(),
                'http_status' => HttpFoundation::HTTP_FORBIDDEN
            ],
            ModelNotFoundException::class => [
                'code' => SystemMessage::DATA_NOT_FOUND,
                'message' => __('Not Found'),
                'http_status' => HttpFoundation::HTTP_NOT_FOUND
            ],
            PostTooLargeException::class => [
                'code' => SystemMessage::FAIL,
                'message' => __('Request size exceeds limit.'),
                'http_status' => HttpFoundation::HTTP_REQUEST_ENTITY_TOO_LARGE
            ],
            ValidationException::class => [
                'code' => SystemMessage::BAD_DATA,
                'message' => __("The data(s) is invalid."),
                'errors' => fn ($e) => $e->errors(),
                'http_status' => HttpFoundation::HTTP_UNPROCESSABLE_ENTITY
            ],
            AuthenticationException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => __($e->getMessage()),
                'http_status' => HttpFoundation::HTTP_UNAUTHORIZED
            ],
            MethodNotAllowedHttpException::class => [
                'code' => SystemMessage::DATA_NOT_FOUND,
                'message' => __('Not Found'),
                'http_status' => HttpFoundation::HTTP_NOT_FOUND
            ],
            ThrottleRequestsException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => __($e->getMessage()),
                'http_status' => HttpFoundation::HTTP_TOO_MANY_REQUESTS
            ],
            UnauthorizedException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => __($e->getMessage()),
                'http_status' => HttpFoundation::HTTP_UNAUTHORIZED,
            ],
            TokenMismatchException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => __($e->getMessage()),
                'http_status' => HttpFoundation::HTTP_BAD_REQUEST,
            ],
            InvalidSignatureException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => __($e->getMessage()),
                'http_status' => HttpFoundation::HTTP_BAD_REQUEST,
            ],
            FileNotFoundException::class => [
                'code' => SystemMessage::FAIL,
                'message' => fn ($e) => __("File not found"),
                'http_status' => HttpFoundation::HTTP_NOT_FOUND,
            ],
        ];

        // Check if the current exception class exists in the exception map
        if(isset($exceptionMap[$e::class]))
        {
            // Prepare the error message and errors (if applicable) based on the settings
            $message = is_callable($exceptionMap[$e::class]['message']) ? $exceptionMap[$e::class]['message']($e) : $exceptionMap[$e::class]['message'];
            $errors = isset($exceptionMap[$e::class]['errors']) && is_callable($exceptionMap[$e::class]['errors']) ? $exceptionMap[$e::class]['errors']($e) : null;

            // Return a response with the error details
            return Response::error(
                code: $exceptionMap[$e::class]['code'],
                message: $message,
                errors: $errors,
                http_status: $exceptionMap[$e::class]['http_status']
            );
        }

        // If the exception is an HttpResponseException with HTTP status code 429 (Too Many Requests),
        if ($e instanceof HttpResponseException && $e->getResponse()->getStatusCode() == HttpFoundation::HTTP_TOO_MANY_REQUESTS) {
            // Return a response with the error details
            return Response::error(
                code: SystemMessage::FAIL,
                message: __($e->getMessage()),
                http_status: HttpFoundation::HTTP_TOO_MANY_REQUESTS
            );
        }

        // Log the exception as an error
        Log::error($e);

        // Check if the application is not in debug mode
        if (!config('app.debug'))
            // If not in debug mode, set the error message to a generic "Server Error"
            $message = __('Server Error');
        else
            // If in debug mode, set the error message to the message of the exception
            $message = __($e->getMessage());

        // Return a response with the error details
        return Response::error(
            code: SystemMessage::INTERNAL_ERROR,
            message: $message,
            http_status: HttpFoundation::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    /**
     * Check if the request is an API request.
     *
     * This method examines the URL and host of the request to determine if it is an API request.
     * If the URL contains '/api' or the host contains 'api', it is considered an API request.
     *
     * @param  Request  $request  The request object to check.
     * @return bool  Returns true if the request is an API request, false otherwise.
     */
    private function isApi(Request $request): bool
    {
        // Get the URL and host from the request
        $url = $request->url();
        $host = $request->getHost();

        // Check if the URL contains '/api' or the host contains 'api'
        return str_contains($url, '/api') || str_contains($host, 'api');
    }
}
