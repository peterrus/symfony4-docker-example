<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity  \User;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // admin user
        $admin = new User();
        $admin->setEmail('admin@admin.nl');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $admin->setPassword($this->passwordEncoder->encodePassword(
             $admin,
             'admin@admin.nl'
        ));

        $manager->persist($admin);

        // regular users
        $user = new User();
        $user->setEmail('user@user.nl');
        $user->setRoles(['ROLE_USER']);

        $user->setPassword($this->passwordEncoder->encodePassword(
             $user,
             'user@user.nl'
        ));

        $manager->persist($user);

        $manager->flush();
    }
}
