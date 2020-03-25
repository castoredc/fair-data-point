<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\CountriesApiResource;
use App\Entity\FAIRData\Country;
use App\Message\Api\Study\GetCountriesCommand;
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
