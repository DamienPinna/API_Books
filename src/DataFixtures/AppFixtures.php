<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture {
  public function load(ObjectManager $manager): void {

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
