<?php

namespace App\Exceptions;

use App\Traits\SlackNotify;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use SlackNotify;
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
        if (config('app.debug') and !in_array(get_class($exception), $this->dontReport, true)) {
            $message = $exception->getMessage();
            $reportable = config('exception.report.enabled', true);
            $this->slackNotify('{emoji}{emoji}{emoji}{br}{uri} {br}*{exception}* in *{filename}* line: *{line}*{br}{message} _{file}_', [
                '{emoji}' => ':sob:',
                '{exception}' => class_basename($exception),
                '{filename}' => basename($exception->getFile()),
                '{line}' => $exception->getLine(),
                '{message}' => !empty($message) ? strtr("`{message}` \n", [
                    '{message}' => $message,
                ]) : '',
                '{file}' => $exception->getFile(),
                '{uri}' => preg_replace('/^http[s]?:/', '', request()->getUri()),
            ]);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($this->isHttpException($exception)) {
            return redirect()->route('admin-login');
        }
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $ip = $request->getClientIp();
            $messages = [
                'reason' => $exception->validator->getMessageBag(),
            ];
            $reportable = config('exception.report.enabled', true);
            if ($reportable and $ip !== '127.0.0.1') {
                $this->slackNotify('IP: {ip}{br}{message}', [
                    '{ip}' => $ip,
                    '{message}' => json_encode($messages),
                ]);
            }

            return response()->json($messages, 422);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(
            route('admin-login')
        );
    }
}
