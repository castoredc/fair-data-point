<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 04/06/2018
 * Time: 13:10
 */

namespace App\Entity\Castor;

use App\Entity\Castor\Study;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use DateTime;

class User
{
    /**
     *
     * @var string|null
     */
    private $id;

    /**
     *
     * @var string|null
     */
    private $fullName;

    /**
     *
     * @var string|null
     */
    private $nameFirst;

    /**
     *
     * @var string|null
     */
    private $nameMiddle;

    /**
     *
     * @var string|null
     */
    private $nameLast;

    /**
     *
     * @var string|null
     */
    private $emailAddress;

    /**
     * @var Collection
     */
    private $studies;

    /**
     * User constructor.
     * @param null|string $id
     * @param null|string $userId
     * @param null|string $entityId
     * @param null|string $fullName
     * @param null|string $nameFirst
     * @param null|string $nameMiddle
     * @param null|string $nameLast
     * @param null|string $emailAddress
     */
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
        $this->studies = new ArrayCollection();
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @param null|string $userId
     */
    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return null|string
     */
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    /**
     * @param null|string $entityId
     */
    public function setEntityId(?string $entityId): void
    {
        $this->entityId = $entityId;
    }

    /**
     * @return null|string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param null|string $fullName
     */
    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return null|string
     */
    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    /**
     * @param null|string $nameFirst
     */
    public function setNameFirst(?string $nameFirst): void
    {
        $this->nameFirst = $nameFirst;
    }

    /**
     * @return null|string
     */
    public function getNameMiddle(): ?string
    {
        return $this->nameMiddle;
    }

    /**
     * @param null|string $nameMiddle
     */
    public function setNameMiddle(?string $nameMiddle): void
    {
        $this->nameMiddle = $nameMiddle;
    }

    /**
     * @return null|string
     */
    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    /**
     * @param null|string $nameLast
     */
    public function setNameLast(?string $nameLast): void
    {
        $this->nameLast = $nameLast;
    }

    /**
     * @return null|string
     */
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * @param null|string $emailAddress
     */
    public function setEmailAddress(?string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public static function fromData($data)
    {
        return new User(
             isset($data['id']) ? $data['id'] : null,
             isset($data['user_id']) ? $data['user_id'] : null,
             isset($data['entity_id']) ? $data['entity_id'] : null,
             isset($data['full_name']) ? $data['full_name'] : null,
             isset($data['name_first']) ? $data['name_first'] : null,
             isset($data['name_middle']) ? $data['name_middle'] : null,
             isset($data['name_last']) ? $data['name_last'] : null,
             isset($data['email_address']) ? $data['email_address'] : null
        );
    }
}