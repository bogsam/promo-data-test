<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\Exceptions;

final class InvalidValueObjectData extends DomainException
{
    public function __construct(string $valueObject, string $field, mixed $value, ?string $reason = null)
    {
        $message = sprintf(
            'Invalid data for %s::%s: %s',
            $valueObject,
            $field,
            $this->formatValue(value: $value),
        );

        if ($reason !== null && $reason !== '') {
            $message .= '. ' . $reason;
        }

        parent::__construct(message: $message);
    }

    private function formatValue(mixed $value): string
    {
        return match (true) {
            is_string(value: $value) => sprintf('"%s"', $value),
            is_int(value: $value), is_float(value: $value) => (string) $value,
            is_bool(value: $value) => $value ? 'true' : 'false',
            $value === null => 'null',
            is_array(value: $value) => sprintf('array(%d)', count(value: $value)),
            is_object(value: $value) => sprintf('object(%s)', $value::class),
            default => gettype(value: $value),
        };
    }
}
