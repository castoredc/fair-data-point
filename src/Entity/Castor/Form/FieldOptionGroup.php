<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use Doctrine\ORM\Mapping as ORM;
use function boolval;

/**
 * @ORM\Entity
 */
class FieldOptionGroup extends CastorEntity
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /** @var bool */
    private $layout;

    /** @var FieldOption[]|null */
    private $options;

    /**
     * @param FieldOption[]|null $options
     */
    public function __construct(string $id, CastorStudy $study, string $name, ?string $description, bool $layout, ?array $options)
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

    public function getLayout(): bool
    {
        return $this->layout;
    }

    public function setLayout(bool $layout): void
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

    public function getOptionByValue(string $value): ?FieldOption
    {
        if ($this->options === null) {
            return null;
        }

        foreach ($this->options as $option) {
            if ($option->getValue() === $value) {
                return $option;
            }
        }

        return null;
    }

    public function getOptionById(string $id): ?FieldOption
    {
        if ($this->options === null) {
            return null;
        }

        foreach ($this->options as $option) {
            if ($option->getId() === $id) {
                return $option;
            }
        }

        return null;
    }

    public function hasChildren(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function getChildren(): ?array
    {
        return $this->options;
    }

    public function getChild(string $id): ?CastorEntity
    {
        return $this->getOptionById($id);
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
    public static function fromData(array $data, CastorStudy $study): FieldOptionGroup
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
            $data['layout'] !== null ? boolval($data['layout']) : false,
            $options
        );

        $group->setOptionParent();

        return $group;
    }
}
