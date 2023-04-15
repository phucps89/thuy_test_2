<?php

namespace App\Utils;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Throwable;

class ValidationUtil
{
    /**
     * @param ValidationException $validationException
     *
     * @param string $prefixCode
     *
     * @return Collection
     */
    public static function convertToListMessageCode(
        ValidationException $validationException,
        string $prefixCode = 'validation'
    ): Collection {
        $result = collect();
        $validator = $validationException->validator;
        $errors = $validator->errors()->getMessages();
        $fails = $validator->failed();
        foreach ($fails as $field => $rules) {
            $i = 0;
            foreach ($rules as $rule => $ruleInfo) {
                if (class_exists($rule)) {
                    try {
                        $ruleName = $rule::alias();
                    } catch (Throwable) {
                        $ruleName = strtolower($rule);
                    }
                } else {
                    $ruleName = strtolower($rule);
                }
                $result->push([
                    'message_code' => implode('_', [
                        $prefixCode,
                        $field,
                        $ruleName,
                    ]),
                    'message' => $errors[$field][$i],
                ]);
                $i++;
            }
        }

        return $result;
    }
}
