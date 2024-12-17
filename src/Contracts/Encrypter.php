<?php

declare(strict_types=1);

namespace SwooleTW\Hyperf\Encryption\Contracts;

interface Encrypter
{
    /**
     * Encrypt the given value.
     *
     * @throws \SwooleTW\Hyperf\Encryption\Exceptions\EncryptException
     */
    public function encrypt(mixed $value, bool $serialize = true): string;

    /**
     * Decrypt the given value.
     *
     * @throws \SwooleTW\Hyperf\Encryption\Exceptions\DecryptException
     */
    public function decrypt(string $payload, bool $unserialize = true): mixed;

    /**
     * Get the encryption key that the encrypter is currently using.
     */
    public function getKey(): string;

    /**
     * Get the current encryption key and all previous encryption keys.
     */
    public function getAllKeys(): array;

    /**
     * Get the previous encryption keys.
     */
    public function getPreviousKeys(): array;
}
