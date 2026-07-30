<?php

namespace App\Exceptions;

use Exception;

class SicoobException extends Exception
{
    public function __construct(string $message, protected int $httpStatus, protected string|array|null $response = null) {
        parent::__construct($message);
    }

    public function getHttpStatus(): int{
        return $this->httpStatus;
    }

    public function getResponse(): string|array|null{
        return $this->response;
    }

    public function friendlyMessage(): string{
        return match ($this->httpStatus) {
            400, 406 => $this->extractBusinessMessage(),
            401, 403 => 'Não foi possível autenticar com o Sicoob.',
            404 => 'Serviço do Sicoob indisponível no momento.',
            500 => 'O Sicoob apresentou uma instabilidade. Tente novamente em alguns minutos.',
            default => 'Erro ao comunicar com o Sicoob.'
        };
    }

    protected function extractBusinessMessage(): string{
        $body = is_array($this->response) ? $this->response : json_decode($this->response, true);

        if (!is_array($body)) {
            return 'A solicitação foi rejeitada pelo Sicoob.';
        }

        if (!isset($body['mensagens'])) {
            return 'A solicitação foi rejeitada pelo Sicoob.';
        }

        return collect($body['mensagens'])
            ->pluck('mensagem')
            ->filter()
            ->implode("\n");
    }
    
    public function context(): array{
        return [
            'http_status' => $this->httpStatus,
            'response_body' => $this->responseBody,
        ];
    }

}
