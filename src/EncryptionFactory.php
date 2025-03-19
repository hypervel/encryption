<?php

declare(strict_types=1);

namespace Hypervel\Encryption;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Stringable\Str;
use Hypervel\Encryption\Exceptions\MissingAppKeyException;
use Laravel\SerializableClosure\SerializableClosure;
use Psr\Container\ContainerInterface;

use function Hyperf\Tappable\tap;

class EncryptionFactory
{
    public function __invoke(ContainerInterface $container): Encrypter
    {
        $config = $container->get(ConfigInterface::class);
        // Fallback to the encryption config if key is not set in app config.
        $config = ($config->has('app.cipher') && $config->has('app.key'))
            ? $config->get('app')
            : $config->get('encryption', []);

        return (new Encrypter(
            $this->parseKey($config),
            $config['cipher']
        ))->previousKeys(array_map(
            fn ($key) => $this->parseKey(['key' => $key]),
            $config['previous_keys'] ?? []
        ));
    }

    /**
     * Configure Serializable Closure signing for security.
     */
    protected function registerSerializableClosureSecurityKey(array $config): void
    {
        if (! class_exists(SerializableClosure::class) || empty($config['key'])) {
            return;
        }

        SerializableClosure::setSecretKey($this->parseKey($config));
    }

    /**
     * Parse the encryption key.
     */
    protected function parseKey(array $config): string
    {
        if (Str::startsWith($key = $this->key($config), $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }

    /**
     * Extract the encryption key from the given configuration.
     *
     * @throws \Hypervel\Encryption\Exceptions\MissingAppKeyException
     */
    protected function key(array $config): string
    {
        return tap($config['key'], function ($key) {
            if (empty($key)) {
                throw new MissingAppKeyException();
            }
        });
    }
}
