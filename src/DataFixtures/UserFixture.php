<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    /** 
     *   Undocumented variable
     *
     * @var UserPasswordEncoderInterface 
     */
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setUsername('agnes')
             ->setPassword($this->encoder->encodePassword($user, 'agnes'))
             ->setEmail('agnes@symfony.com')
             ->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
             //->setPhone('0343433434');
        $manager->persist($user);
        $manager->flush();
    }
}
