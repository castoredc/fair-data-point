<?php
declare(strict_types=1);

namespace App\MessageHandler\Country;

use App\Api\Resource\Country\CountriesApiResource;
use App\Entity\FAIRData\Country;
use App\Message\Country\GetCountriesCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCountriesCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(GetCountriesCommand $message): CountriesApiResource
    {
        $countries = $this->em->getRepository(Country::class)->findAll();

        return new CountriesApiResource($countries);
    }
}
