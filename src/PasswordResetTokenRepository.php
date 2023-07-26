<?php

namespace Drewlabs\Passwords;

use Drewlabs\Passwords\Contracts\ConnectionInterface;
use Drewlabs\Passwords\Contracts\HashedTokenInterface;
use Drewlabs\Passwords\Contracts\TokenHasher;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;

class PasswordResetTokenRepository implements TokenRepositoryInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * Token hasher instance
     * 
     * @var TokenHasher
     */
    private $hasher;

    /**
     * @var string
     */
    private $table;

    /**
     * Number of seconds after which token expires
     * 
     * @var int
     */
    private $expiresTtl;

    /**
     * Creates tokens repository instances
     * 
     * @param ConnectionInterface $connection 
     * @param TokenHasher $hasher 
     * @param int $expiresTtl 
     * @return void 
     */
    public function __construct(ConnectionInterface $connection, TokenHasher $hasher, int $expiresTtl = 60)
    {
        $this->connection = $connection;
        $this->hasher = $hasher;
        $this->expiresTtl = $expiresTtl * 60;
    }

    public function addToken(TokenInterface $token)
    {
        $this->connection->transaction(function (TokenInterface $token) {
            $hashedToken = $this->hasher->make($token);
            // Execute the PDO statement
            $this->connection->create([
                'sub' => (string)$hashedToken->getSubject(),
                'token' => $hashedToken->getToken(),
                'created_at' => $token->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
            // Prepare PDO statement
        }, $token);
    }

    public function getToken(string $sub): ?HashedTokenInterface
    {
        return $this->connection->transaction(function (array $attributes) {
            // Bind PDO param

            $result = $this->connection->selectOne($attributes);

            if (false === $result) {
                return null;
            }
            $createdAt = null !== $result->created_at ? \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string)$result->created_at) : null;
            $expiresAt = null !== $createdAt ? $createdAt->modify(sprintf("+%d seconds", $this->expiresTtl)) : null;


            // Return a hashed password token instance
            return new HashedPasswordResetToken($result->sub, $result->token, $createdAt, $expiresAt);
        }, ['sub' => $sub]);
    }

    public function hasToken(string $sub, string $token): bool
    {
        if (null === ($hashedToken = $this->getToken($sub))) {
            return false;
        }
        return !$hashedToken->hasExpired() && $this->hasher->check($hashedToken, $token);
    }

    public function deleteToken(string $sub): bool
    {
        return $this->connection->transaction(function (array $conditions) use ($sub) {
            return $this->connection->delete($conditions);
        }, ['sub' => $sub]);
    }
}
