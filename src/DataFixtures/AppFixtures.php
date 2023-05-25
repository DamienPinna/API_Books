<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture {

  private $userPasswordHasher;

  public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface) {
    $this->userPasswordHasher = $userPasswordHasherInterface;
  }

  public function load(ObjectManager $manager): void {

    $user = new User();
    $user->setEmail('user@bookapi.com')->setRoles(["ROLE_USER"])->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
    $manager->persist($user);

    $userAdmin = new User();
    $userAdmin->setEmail('admin@bookapi.com')->setRoles(["ROLE_ADMIN"])->setPassword($this->userPasswordHasher->hashPassword($userAdmin, 'password'));
    $manager->persist($userAdmin);

    $authors = [];
    for ($i = 0; $i < 10; $i++) {
      $author = new Author();
      $author->setFirstName('Prénom ' . $i)->setLastName('Nom ' . $i);
      $manager->persist($author);
      $authors[] = $author;
    }

    for ($i = 0; $i < 20; $i++) {
      $book = new Book();
      $book->setTitle("Titre " . $i)->setCoverText("Quatrième de couverture numéro " . $i)->setAuthor($authors[array_rand($authors)]);
      $manager->persist($book);
    }

    $manager->flush();
  }
}
