<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    private const DEFAULT_EMAIL = 'enzode@enzode.com';
    private const DEFAULT_PASSWORD = 'enzode';
    private const DEFAULT_ROLES = ['ROLE_USER'];

    public function load(ObjectManager $manager): void
    {
        $user = $this->createUser(self::DEFAULT_EMAIL, self::DEFAULT_PASSWORD, self::DEFAULT_ROLES);

        $manager->persist($user);
        $manager->flush();
    }

    private function createUser(string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);

        return $user;
    }
}
