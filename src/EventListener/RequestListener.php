<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestValidatorBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use GepurIt\RequestValidatorBundle\Annotations\RequestValidation;
use GepurIt\RequestValidatorBundle\Exception\InvalidValidatorException;
use GepurIt\RequestValidatorBundle\Exception\RequestValidationException;
use GepurIt\RequestValidatorBundle\RequestValidator\RequestValidator;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class RequestListener
 * @package GepurIt\RequestValidatorBundle\EventListener
 */
class RequestListener
{
    /**
     * @var RequestValidator
     */
    private $requestValidator;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * RequestListener constructor.
     *
     * @param RequestValidator $requestValidator
     * @param Reader           $annotationReader
     */
    public function __construct(RequestValidator $requestValidator, Reader $annotationReader)
    {
        $this->requestValidator = $requestValidator;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param ControllerEvent $event
     * @throws \ReflectionException
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        list($controllerObject, $methodName) = $controller;

        $controllerReflection = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflection->getMethod($methodName);
        /** @var RequestValidation|null $methodAnnotation */
        $methodAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, RequestValidation::class);
        if (null === $methodAnnotation || empty($validatorName = $methodAnnotation->model)) {
            return;
        }

        $request    = $event->getRequest();

        $validatorModel = null;
        if ($this->requestValidator->exists($validatorName)) {
            $validatorModel = $this->requestValidator->get($validatorName);
        } elseif (class_exists($validatorName)) {
            $validatorModel = new $validatorName(...$methodAnnotation->arguments);
        }
        if (null === $validatorModel) {
            throw new InvalidValidatorException("validator {$validatorName} not found");
        }

        $violations = $this->requestValidator->validate($request, $validatorModel);
        if ($violations->count() > 0) {
            throw new RequestValidationException($violations);
        }
    }
}
