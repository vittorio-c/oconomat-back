<?php

namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Nelmio\Alice\Loader\NativeLoader;


class NelmioAliceFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Malgré la config dans config/packages/dev/nelmio_alice.yaml
        // On est obligé d'ajouter cette ligne (et son use) pour avoir des résultats en français :
        $faker = Factory::create('fr_FR');
        $loader = new NativeLoader($faker);

        // Importe le fichier de fixtures et récupère les entités générés
        $entities = $loader->loadFile(__DIR__.'/fixtures.yaml')->getObjects();

        // Persiste chacun des objets à enregistrer en BDD
        foreach ($entities as $entity) {
            $manager->persist($entity);
        };

        // Flush pour exécuter les requêtes SQL
        $manager->flush();
        /*
        Au lieu de coder nos entités comme avec la librairie Faker,
        on utiliser un fichier YAML qui, lui, définir le type de données qu'on veut.
        Le code ici, reste nécessaire pour DoctrineFixturesBundle
        Lorsqu'on appel ma comamnde doctrine:fixtures:load (d:f:l), on lance la méthode load() d'ici
        Toutes les modificiations et les précisions se passeront dans l'autre fichier
         */
    }
}
