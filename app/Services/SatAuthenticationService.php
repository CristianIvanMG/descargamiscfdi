<?php

namespace App\Services;

use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\Fiel;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\FielRequestBuilder;
use PhpCfdi\SatWsDescargaMasiva\Service;
use PhpCfdi\SatWsDescargaMasiva\WebClient\GuzzleWebClient;
use RuntimeException;

class SatAuthenticationService
{
    public function authenticate(string $cerPath, string $keyPath, string $password): string
    {
        foreach ([Fiel::class, FielRequestBuilder::class, GuzzleWebClient::class, Service::class] as $class) {
            if (! class_exists($class)) {
                throw new RuntimeException('Librería SAT no instalada o autoload pendiente.');
            }
        }

        $fiel = Fiel::create(
            (string) file_get_contents($cerPath),
            (string) file_get_contents($keyPath),
            $password,
        );

        if (! $fiel->isValid()) {
            throw new RuntimeException('La e.firma no es válida, no corresponde a FIEL o está vencida.');
        }

        $service = new Service(
            new FielRequestBuilder($fiel),
            new GuzzleWebClient(),
        );

        $token = $service->authenticate();

        if (! $token->isValid()) {
            throw new RuntimeException('SAT no devolvió un token válido.');
        }

        return $token->getValue();
    }
}
