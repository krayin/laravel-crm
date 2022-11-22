<?php

namespace Webkul\Admin\Exceptions;

use App\Exceptions\Handler as AppExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends AppExceptionHandler
{
    /**
     * Json error messages.
     *
     * @var array
     */
    protected $jsonErrorMessages = [];

    /**
     * Create handler instance.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->jsonErrorMessages = [
            '404' => trans('admin::app.common.resource-not-found'),
            '403' => trans('admin::app.common.forbidden-error'),
            '401' => trans('admin::app.common.unauthenticated'),
            '500' => trans('admin::app.common.internal-server-error')
        ];
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable   $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if (! config('app.debug')) {
            return $this->renderCustomResponse($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->jsonErrorMessages[401]], 401);
        }

        return redirect()->guest(route('customer.session.index'));
    }

    /**
     * Render custom HTTP response.
     *
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|null
     */
    private function renderCustomResponse(Throwable $exception)
    {
        $path = request()->routeIs('admin.*') ? 'admin' : 'front';

        if ($path == 'front') {
            return redirect()->route('admin.session.create');
        }

        if ($exception instanceof HttpException) {
            $statusCode = in_array($exception->getStatusCode(), [401, 403, 404, 503])
                ? $exception->getStatusCode()
                : 500;

            return $this->response($path, $statusCode);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->response($path, 404);
        }

        if ($exception instanceof PDOException) {
            return $this->response($path, 500);
        }
    }

    /**
     * Return custom response.
     *
     * @param  string  $path
     * @param  string  $statusCode
     * @return mixed
     */
    private function response($path, $statusCode)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'message' => isset($this->jsonErrorMessages[$statusCode])
                    ? $this->jsonErrorMessages[$statusCode]
                    : trans('admin::app.common.something-went-wrong')
            ], $statusCode);
        }

        return response()->view("{$path}::errors.{$statusCode}", [], $statusCode);
    }
}
