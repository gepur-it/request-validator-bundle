<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 20.12.18
 */

namespace GepurIt\RequestValidatorBundle\Annotations;

/**
 * @Annotation
 *
 * Class RequestValidation
 * @package GepurIt\RequestValidatorBundle\Annotations
 */
class RequestValidation
{
    /** @var string $model */
    public $model = '';

    /** @var array  */
    public $arguments = [];
}
