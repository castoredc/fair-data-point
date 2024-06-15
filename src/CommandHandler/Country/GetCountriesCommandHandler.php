<?php
declare(strict_types=1);

namespace App\CommandHandler\Country;

use App\Api\Resource\Country\CountriesApiResource;
use App\Command\Country\GetCountriesCommand;
use App\Entity\FAIRData\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetCountriesCommandHandler
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(GetCountriesCommand $command): CountriesApiResource
    {
        /** @var Country[] $countries */
        $countries = $this->em->getRepository(Country::class)->findBy([], ['name' => 'ASC']);

        return new CountriesApiResource($countries);
    }
}
