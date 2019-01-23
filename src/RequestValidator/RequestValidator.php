<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestValidatorBundle\RequestValidator;

use GepurIt\RequestValidatorBundle\Contracts\RequestValidationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestValidator
 * @package GepurIt\RequestValidatorBundle\RequestValidator
 */
class RequestValidator
{
    /** @var RequestValidationInterface[] */
    private $validationModels = [];

    /**
     * @var ValidatorInterface
     */
    private $systemValidator;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(ValidatorInterface $systemValidator, PropertyAccessorInterface $propertyAccessor)
    {
        $this->systemValidator  = $systemValidator;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param                            $name
     * @param RequestValidationInterface $requestValidation
     */
    public function addValidator($name, RequestValidationInterface $requestValidation)
    {
        $this->validationModels[$name] = $requestValidation;
    }

    /**
     * @param Request $request
     *
     * @param mixed   $validatorName
     *
     * @return ConstraintViolationListInterface
     */
    public function validate(Request $request, RequestValidationInterface $validatorName)
    {
        $validationModel = $this->buildValidationRequest($request, $validatorName);

        return $this->systemValidator->validate($validationModel);
    }

    /**
     * @param Request                    $request
     * @param RequestValidationInterface $model
     *
     * @return RequestValidationInterface
     */
    public function buildValidationRequest(Request $request, RequestValidationInterface $model)
    {
        $model = clone $model;
        foreach ($request->request->all() as $name => $value) {
            if ($this->propertyAccessor->isWritable($model, $name)) {
                $this->propertyAccessor->setValue($model, $name, $value);
            }
        }

        return $model;
    }

    /**
     * @param $validatorName
     *
     * @return bool
     */
    public function exists($validatorName)
    {
        return array_key_exists($validatorName, $this->validationModels);
    }

    /**
     * @param $validatorName
     *
     * @return RequestValidationInterface
     */
    public function get($validatorName)
    {
        return $this->validationModels[$validatorName];
    }
}
