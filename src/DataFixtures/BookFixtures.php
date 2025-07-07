<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $book = new Book();
            $book->setTitle($faker->sentence(3));
            $book->setAuthor($faker->name);
            $book->setSummary($faker->paragraph);
            $book->setGenre($faker->randomElement(['Science-fiction', 'Fantasy', 'Histoire', 'Romance', 'Thriller']));
            $book->setPublishedDate($faker->dateTimeBetween('-20 years', 'now'));
            $book->setCoverImage($faker->imageUrl(200, 300, 'books', true, 'Book'));
            $book->setAvailable($faker->boolean(80)); // 80% de chances que le livre soit disponible

            $manager->persist($book);
        }

        $manager->flush();
    }
}
