<?php


namespace App\Tests\Entity;


use App\Tests\DatabasePrimer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class EntityTest extends KernelTestCase
{
    protected EntityManagerInterface | null $entityManager;
    protected ValidatorInterface $validator;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->validator = Validation::createValidatorBuilder()->setConstraintValidatorFactory(new ContainerConstraintValidatorFactory(self::$container))->enableAnnotationMapping(true)->addDefaultDoctrineAnnotationReader()->getValidator();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}