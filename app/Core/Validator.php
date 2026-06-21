<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, string $label): self
    {
        if (trim((string) ($this->data[$field] ?? '')) === '') {
            $this->errors[$field] = $label . ' is required.';
        }

        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $label . ' must be a valid email address.';
        }

        return $this;
    }

    public function min(string $field, int $length, string $label): self
    {
        if (!empty($this->data[$field]) && mb_strlen((string) $this->data[$field]) < $length) {
            $this->errors[$field] = $label . ' must be at least ' . $length . ' characters.';
        }

        return $this;
    }

    public function max(string $field, int $length, string $label): self
    {
        if (!empty($this->data[$field]) && mb_strlen((string) $this->data[$field]) > $length) {
            $this->errors[$field] = $label . ' must not exceed ' . $length . ' characters.';
        }

        return $this;
    }

    public function matches(string $field, string $otherField, string $label): self
    {
        if (($this->data[$field] ?? null) !== ($this->data[$otherField] ?? null)) {
            $this->errors[$field] = $label . ' does not match.';
        }

        return $this;
    }

    public function unique(string $field, string $modelClass, string $label, ?int $ignoreId = null): self
    {
        if (empty($this->data[$field])) {
            return $this;
        }

        $row = $modelClass::findBy($field, $this->data[$field]);

        if ($row && (!$ignoreId || (int) $row['id'] !== $ignoreId)) {
            $this->errors[$field] = $label . ' is already taken.';
        }

        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(): ?string
    {
        $values = array_values($this->errors);

        return $values[0] ?? null;
    }
}
