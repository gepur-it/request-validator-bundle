<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestValidatorBundle\EventListener;

use GepurIt\RequestValidatorBundle\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class ValidationExceptionListener
 * @package GepurIt\RequestValidatorBundle\EventListener
 */
class ValidationExceptionListener
{

    /**
     * ValidationExceptionListener constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!($exception instanceof RequestValidationException)) {
            return;
        }
        $errors = [];
        foreach ($exception->getViolationList() as $violation) {
            $errors[] = [
                "field"   => $violation->getPropertyPath(),
                "message" => $violation->getMessage(),
            ];
        }
        $message = json_encode($errors);
        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);
        $response->setStatusCode($exception->getStatusCode(), 'Validation Error');

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
