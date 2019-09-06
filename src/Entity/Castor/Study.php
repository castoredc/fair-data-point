<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 14/05/2018
 * Time: 14:06
 */

namespace App\Entity\Castor;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="study", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Study
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $mainContact;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $slug;

    /**
     * Study constructor.
     * @param string|null $id
     * @param string|null $name
     * @param string|null $mainContact
     * @param string|null $slug
     */
    public function __construct(?string $id, ?string $name, ?string $mainContact, ?string $slug)
    {
        $this->id = $id;
        $this->name = $name;
        $this->mainContact = $mainContact;
        $this->slug = $slug;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getMainContact(): ?string
    {
        return $this->mainContact;
    }

    /**
     * @param string|null $mainContact
     */
    public function setMainContact(?string $mainContact): void
    {
        $this->mainContact = $mainContact;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public static function fromData(array $data)
    {
        $study = new Study(
            isset($data['study_id']) ? $data['study_id'] : null,
            isset($data['name']) ? $data['name'] : null,
            isset($data['main_contact']) ? $data['main_contact'] : null,
            isset($data['slug']) ? $data['slug'] : null
        );

        return $study;
    }
}