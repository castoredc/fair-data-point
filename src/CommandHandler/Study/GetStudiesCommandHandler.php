<?php
declare(strict_types=1);

namespace App\CommandHandler\Study;

use App\Command\Study\GetStudiesCommand;
use App\Entity\Study;
use App\Exception\UserNotACastorUser;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetStudiesCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    /**
     * @return Study[]
     *
     * @throws UserNotACastorUser
     */
    public function __invoke(GetStudiesCommand $command): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            /** @var Study[] $dbStudies */
            $dbStudies = $this->em->getRepository(Study::class)->findAll();
        } else {
            $user = $this->security->getUser();
            assert($user instanceof User);

            if (! $user->hasCastorUser()) {
                throw new UserNotACastorUser();
            }

            $userStudies = $user->getCastorUser()->getStudies();

            /** @var Study[] $dbStudies */
            $dbStudies = $this->em->getRepository(Study::class)->findBy(['id' => $userStudies]);
        }

        return $dbStudies;
    }
}
