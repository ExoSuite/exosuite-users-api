<?php declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Throwable;
use function config;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends \Illuminate\Foundation\Exceptions\Handler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];


    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     *
     * @return void
     * @throws \Exception
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render(Request $request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return $this->modelNotFound($exception);
        }

        return parent::render($request, $exception);
    }

    private function modelNotFound(ModelNotFoundException $exception): JsonResponse
    {
        $data = [
            'message' => 'Your request failed because the requested resource was not found.'
        ];

        if (config('app.debug')) {
            $url = URL::current();
            $data['hint'] = "Check if your route is correct: $url";
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
        }

        return Response::json($data)->setStatusCode(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
