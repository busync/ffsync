<?php
namespace App\Controllers;

class FieldsValidator
{
    private array $rules;
    private array $fields;
    private array $errors = [];

    public function __construct(array $rules, array $fields)
    {
        $this->rules = $rules;
        $this->fields = $fields;
    }

    public function validate(): array
    {
        $this->errors = [];
        $filledRequired = 0;
        $totalRequired = 0;

        foreach ($this->rules as $fieldName => $rule) {
            if (!empty($rule['required'])) {
                $totalRequired++;
            }
        }

        foreach ($this->rules as $fieldName => $rule) {
            if (!isset($this->fields[$fieldName]) && empty($rule['required'])) {
                continue;
            }

            if (!empty($rule['required'])) {
                if (!isset($this->fields[$fieldName]) || $this->fields[$fieldName] === '' || $this->fields[$fieldName] === null) {
                    $this->addError($fieldName, 'required', 'Field is required');
                } else {
                    $filledRequired++;
                }
            }

            $value = $this->fields[$fieldName] ?? null;

            if (empty($rule['required']) && (empty($value) && $value !== '0' && $value !== 0)) {
                continue;
            }

            if (isset($this->fields[$fieldName]) || !empty($rule['required'])) {
                $this->validateByType($fieldName, $value, $rule);
            }
        }

        if ($filledRequired !== $totalRequired) {
            $this->errors['_form'] = [
                'required_fields' => "Required fields count mismatch: $filledRequired of $totalRequired filled"
            ];
        }

        return [
            'success' => empty($this->errors),
            'errors' => $this->errors,
            'stats' => [
                'total_required' => $totalRequired,
                'filled_required' => $filledRequired,
                'total_fields' => count($this->rules),
                'filled_fields' => count(array_intersect_key($this->fields, $this->rules))
            ]
        ];
    }

    private function validateByType(string $fieldName, $value, array $rule): void
    {
        $type = $rule['type'] ?? 'string';

        switch ($type) {
            case 'string':
                $this->validateString($fieldName, $value, $rule);
                break;
            case 'integer':
                $this->validateInteger($fieldName, $value, $rule);
                break;
            case 'decimal':
                $this->validateDecimal($fieldName, $value, $rule);
                break;
            case 'email':
                $this->validateEmail($fieldName, $value, $rule);
                break;
            default:
                $this->validateString($fieldName, $value, $rule);
        }
    }

    private function validateString(string $fieldName, $value, array $rule): void
    {
        $value = (string)$value;

        if (isset($rule['minLength']) && mb_strlen($value) < $rule['minLength']) {
            $this->addError($fieldName, 'minLength', "Minimum length is {$rule['minLength']} characters");
            return;
        }

        if (isset($rule['maxLength']) && mb_strlen($value) > $rule['maxLength']) {
            $this->addError($fieldName, 'maxLength', "Maximum length is {$rule['maxLength']} characters");
            return;
        }

        if (isset($rule['regex']) && !preg_match($rule['regex'], $value)) {
            $this->addError($fieldName, 'regex', 'Field contains invalid characters');
            return;
        }
    }

    private function validateInteger(string $fieldName, $value, array $rule): void
    {
        if (!is_numeric($value) || !ctype_digit((string)$value)) {
            $this->addError($fieldName, 'type', 'Must be an integer');
            return;
        }

        $value = (int)$value;

        if (isset($rule['min']) && $value < $rule['min']) {
            $this->addError($fieldName, 'min', "Minimum value is {$rule['min']}");
            return;
        }

        if (isset($rule['max']) && $value > $rule['max']) {
            $this->addError($fieldName, 'max', "Maximum value is {$rule['max']}");
            return;
        }
    }

    private function validateDecimal(string $fieldName, $value, array $rule): void
    {
        if (!is_numeric($value)) {
            $this->addError($fieldName, 'type', 'Must be a number');
            return;
        }

        $value = (float)$value;

        if (isset($rule['decimal'])) {
            $decimalPlaces = strlen(substr(strrchr((string)$value, "."), 1));
            if ($decimalPlaces > $rule['decimal']) {
                $this->addError($fieldName, 'decimal', "Maximum {$rule['decimal']} decimal places allowed");
                return;
            }
        }

        if (isset($rule['min']) && $value < $rule['min']) {
            $this->addError($fieldName, 'min', "Minimum value is {$rule['min']}");
            return;
        }

        if (isset($rule['max']) && $value > $rule['max']) {
            $this->addError($fieldName, 'max', "Maximum value is {$rule['max']}");
            return;
        }
    }

    private function validateEmail(string $fieldName, $value, array $rule): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($fieldName, 'email', 'Invalid email format');
            return;
        }

        $this->validateString($fieldName, $value, $rule);
    }

    private function addError(string $field, string $rule, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][$rule] = $message;
    }

    public function getValidatedData(): array
    {
        $validated = [];

        foreach ($this->rules as $fieldName => $rule) {
            if (isset($this->fields[$fieldName])) {
                $value = $this->fields[$fieldName];

                $type = $rule['type'] ?? 'string';
                switch ($type) {
                    case 'integer':
                        $validated[$fieldName] = (int)$value;
                        break;
                    case 'decimal':
                        $validated[$fieldName] = (float)$value;
                        break;
                    default:
                        $validated[$fieldName] = (string)$value;
                }
            } else if ($rule['required']) {
                $validated[$fieldName] = null;
            }
        }

        return $validated;
    }
}