<?php

namespace Bdt\Avetmiss\Frameworks\Laravel;

use Illuminate\Support\ServiceProvider;

use Bdt\Avetmiss\Exceptions\FieldNotValidException;

/**
 * Extends the Laravel Validator with AVETMISS validation rules.
 */
class ValidatorServiceProvider extends ServiceProvider
{

    /**
     * @param array
     */
    protected $natFieldsets = [];

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $validator = $this->app['Illuminate\Contracts\Validation\Factory'];

        $validator->extend(
            'avetmiss',
            function ($attribute, $value, $parameters) {
                $natName = $parameters[0];
                $fieldName = $parameters[1];
                $validateLength = isset($parameters[2]) && $parameters[2] == 'true';
                $ifNull = $parameters[3];

                if (!isset($this->natFieldsets[$natName])) {
                    $natFieldset = '\\Bdt\\Avetmiss\\Nat\\V8\\' . ucfirst($natName);
                    $this->natFieldsets[$natName] = new $natFieldset;
                }

                $field = $this->natFieldsets[$natName]->getFieldByName($fieldName);

                try {
                    $isValid = $field->validate($value);
                }
                catch(FieldNotValidException $e) {
                    $isValid = false;
                }

                if ($validateLength && $isValid) {
                    $isValid = strlen($value) <= $field->getLength();
                }

                if ($value != null && $ifNull === 'not_null') {
                    $isValid = true;
                } else if ($value === null && $ifNull != 'not_null') {
                    $isValid = true;
                } else {
                    $isValid = false;
                }

                return $isValid;
            },
            'The value for :attribute must be valid according to the :nat_name :field_name field'
        );

        $validator->replacer(
            'avetmiss',
            function ($message, $attribute, $value, $parameters) {
                $natName = ucfirst($parameters[0]);
                $fieldName = $parameters[1];

                return str_replace([':nat_name', ':field_name'], [$natName, $fieldName], $message);
            }
        );
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
    }
}