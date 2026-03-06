<?php

namespace SmartDato\GlsShopReturnsCustomer\Exceptions;

use Exception;
use Saloon\Http\Response;
use SmartDato\GlsShopReturnsCustomer\Data\ErrorData;

class GlsApiException extends Exception
{
    /** @var ErrorData[] */
    public readonly array $errors;

    /**
     * @param  ErrorData[]  $errors
     */
    public function __construct(
        string $message,
        int $code,
        array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public static function fromResponse(Response $response): self
    {
        $status = $response->status();
        $body = $response->json();

        $errors = [];
        if (isset($body['errors']) && is_array($body['errors'])) {
            $errors = array_map(
                fn (array $error) => ErrorData::from($error),
                $body['errors'],
            );
        }

        $message = match (true) {
            ! empty($errors) => $errors[0]->message ?? "GLS API error ({$status})",
            isset($body['message']) => $body['message'],
            default => "GLS API error ({$status})",
        };

        return new self(
            message: $message,
            code: $status,
            errors: $errors,
        );
    }
}
