<?php

namespace Drewlabs\Passwords;

use Drewlabs\Passwords\Contracts\HashedTokenInterface;
use Drewlabs\Passwords\Contracts\TokenHasher;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;
use PDO;

class PdoTokenRepository implements TokenRepositoryInterface
{
    /**
     * @var PDO
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
     * @param PDO $connection 
     * @param TokenHasher $hasher 
     * @param string $table 
     * @param string $key 
     * @param int $expiresTtl 
     * @param int $throttleTtl 
     * @return void 
     */
    public function __construct(PDO $connection, TokenHasher $hasher, string $table, int $expiresTtl = 60)
    {
        $this->connection = $connection;
        $this->hasher = $hasher;
        $this->table = $table;
        $this->expiresTtl = $expiresTtl * 60;
    }

    public function addToken(TokenInterface $token)
    {
        $this->transaction(function (\PDOStatement $stmt) use ($token) {
            $hashedToken = $this->hasher->make($token);
            // Execute the PDO statement
            $stmt->execute([(string)$hashedToken->getSubject(), $hashedToken->getToken(), (null !== ($createdAt = $token->getCreatedAt()) ? $createdAt->format('Y-m-d H:i:s') : null)]);
            // Prepare PDO statement
        }, $this->preparePDOStatement(sprintf("INSERT INTO %s(sub, token, created_at) VALUES (?, ?, ?)", $this->table)));
    }

    public function getToken(string $sub): ?HashedTokenInterface
    {
        return $this->transaction(function (\PDOStatement $stmt) use ($sub) {
            // Bind PDO param
            $stmt->bindParam(1, $sub, \PDo::PARAM_STR);
            // Execute the PDO statement
            $stmt->execute();

            // Select the first matching row
            $result = $stmt->fetch(\PDO::FETCH_OBJ);

            if (false === $result) {
                return null;
            }

            $createdAt = null !== ($created_at = $result->created_at) ? (new \DateTimeImmutable)->setTimestamp(strtotime($created_at)) : null;
            $expiresAt = null !== $createdAt ? $createdAt->modify(sprintf("+%d seconds", $this->expiresTtl)) : null;

            // Returne a hashed password token instance
            return new HashedPasswordResetToken($result->sub, $result->token, $createdAt, $expiresAt);

            // Prepare PDO statement
        }, $this->preparePDOStatement(sprintf("SELECT * FROM %s WHERE sub = ?", $this->table)));
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
        return $this->transaction(function (\PDOStatement $stmt) use ($sub) {
            // Bind PDO param
            $stmt->bindParam(1, $sub, \PDo::PARAM_STR);
            // Execute the PDO statement
            return $stmt->execute();
            // Prepare PDO statement
        }, $this->preparePDOStatement(sprintf("DELETE * FROM %s WHERE sub = ?", $this->table)));
    }

    private function preparePDOStatement(string $sql)
    {
        $stmt = $this->connection->prepare($sql);
        if (false === $stmt) {
            list($err, $_, $message) = $this->connection->errorInfo();
            throw new \PDOException($message ?? sprintf("SQL ERROR: %s", $sql, $err ? intval($err) : 500));
        }
        return $stmt;
    }

    private function transaction(callable $callback, ...$args)
    {
        try {
            $this->connection->beginTransaction();
            $result = call_user_func($callback, ...$args);
            $this->connection->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->connection->rollback();
            throw $e;
        }
    }
}
