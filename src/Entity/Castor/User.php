<?php
declare(strict_types=1);

namespace App\Entity\Castor;

class User
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $fullName;

    /** @var string|null */
    private $nameFirst;

    /** @var string|null */
    private $nameMiddle;

    /** @var string|null */
    private $nameLast;

    /** @var string|null */
    private $emailAddress;

    /** @var string|null */
    private $entityId;

    /** @var string|null */
    private $userId;

    public function __construct(?string $id, ?string $userId, ?string $entityId, ?string $fullName, ?string $nameFirst, ?string $nameMiddle, ?string $nameLast, ?string $emailAddress)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->entityId = $entityId;
        $this->fullName = $fullName;
        $this->nameFirst = $nameFirst;
        $this->nameMiddle = $nameMiddle;
        $this->nameLast = $nameLast;
        $this->emailAddress = $emailAddress;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(?string $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(?string $nameFirst): void
    {
        $this->nameFirst = $nameFirst;
    }

    public function getNameMiddle(): ?string
    {
        return $this->nameMiddle;
    }

    public function setNameMiddle(?string $nameMiddle): void
    {
        $this->nameMiddle = $nameMiddle;
    }

    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    public function setNameLast(?string $nameLast): void
    {
        $this->nameLast = $nameLast;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): User
    {
        return new User(
            $data['id'] ?? null,
            $data['user_id'] ?? null,
            $data['entity_id'] ?? null,
            $data['full_name'] ?? null,
            $data['name_first'] ?? null,
            $data['name_middle'] ?? null,
            $data['name_last'] ?? null,
            $data['email_address'] ?? null
        );
    }
}
