<?php
declare(strict_types=1);

namespace App\CommandHandler\Country;

use App\Api\Resource\Country\CountriesApiResource;
use App\Entity\FAIRData\Country;
use App\Command\Country\GetCountriesCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCountriesCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetCountriesCommand $command): CountriesApiResource
    {
        $countries = $this->em->getRepository(Country::class)->findBy([], ['name' => 'ASC']);

        return new CountriesApiResource($countries);
    }
}
