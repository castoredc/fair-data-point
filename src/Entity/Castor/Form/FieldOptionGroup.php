<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Study;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FieldOptionGroup extends CastorEntity
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $layout;

    /** @var FieldOption[]|null */
    private $options;

    /**
     * @param FieldOption[]|null $options
     */
    public function __construct(string $id, Study $study, string $name, ?string $description, ?string $layout, ?array $options)
    {
        parent::__construct($id, $name, $study, null);

        $this->name = $name;
        $this->description = $description;
        $this->layout = $layout;
        $this->options = $options;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setLayout(?string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return FieldOption[]|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param FieldOption[]|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    public function setOption(int $pos, FieldOption $option): void
    {
        $this->options[$pos] = $option;
    }

    public function setOptionParent(): void
    {
        foreach ($this->options as $option) {
            $option->setOptionGroup($this);
        }
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, Study $study): FieldOptionGroup
    {
        $options = [];
        if (isset($data['options'])) {
            foreach ($data['options'] as $option) {
                $options[] = FieldOption::fromData($option, $study);
            }
        }
        $group = new FieldOptionGroup(
            $data['id'],
            $study,
            $data['name'],
            $data['description'] ?? null,
            $data['layout'] ?? null,
            $options
        );

        $group->setOptionParent();

        return $group;
    }
}
