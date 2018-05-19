<?php
declare(strict_types=1);

namespace App\Service\CastorAuth;

class RouteParameters implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $parameters;

    /**
     * RouteParameters constructor.
     * @param string    $name
     * @param array     $parameters
     */
    public function __construct(string $name, array $parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'parameters' => $this->parameters
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }




}
