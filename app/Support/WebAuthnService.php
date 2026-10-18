<?php

namespace App\Support;

use LogicException;
use Symfony\Component\Serializer\SerializerInterface;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\Denormalizer\WebauthnSerializerFactory;

class WebAuthnService
{
    public function rpId(): string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            throw new LogicException('APP_URL must include a valid host to enable passkeys.');
        }

        return strtolower($host);
    }

    public function origin(): string
    {
        $appUrl = (string) config('app.url');
        $scheme = parse_url($appUrl, PHP_URL_SCHEME);
        $host = parse_url($appUrl, PHP_URL_HOST);
        $port = parse_url($appUrl, PHP_URL_PORT);

        if (! is_string($scheme) || ! is_string($host) || $host === '') {
            throw new LogicException('APP_URL must be a valid absolute URL to enable passkeys.');
        }

        return strtolower($scheme).'://'.strtolower($host).($port === null ? '' : ':'.$port);
    }

    public function assertSecureContext(): void
    {
        $scheme = parse_url($this->origin(), PHP_URL_SCHEME);
        $host = $this->rpId();
        $isLocalhost = $host === 'localhost'
            || str_ends_with($host, '.localhost')
            || $host === '127.0.0.1'
            || $host === '[::1]';

        if ($scheme !== 'https' && ! $isLocalhost) {
            abort(400, 'Passkeys require HTTPS. Use localhost only for local development.');
        }
    }

    public function serializer(): SerializerInterface
    {
        return (new WebauthnSerializerFactory(new AttestationStatementSupportManager()))->create();
    }

    public function ceremonyStepManagerFactory(): CeremonyStepManagerFactory
    {
        $factory = new CeremonyStepManagerFactory();
        $factory->setSecuredRelyingPartyId([$this->rpId()]);

        return $factory;
    }

    public function encodeBase64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    public function decodeBase64Url(string $value): string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        if (! is_string($decoded)) {
            throw new \InvalidArgumentException('Invalid base64url data.');
        }

        return $decoded;
    }
}
