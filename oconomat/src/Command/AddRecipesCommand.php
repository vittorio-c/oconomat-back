<?php

namespace App\Command;

use App\Service\Slugger;
use App\Entity\Recipe;
use App\Entity\RecipeStep;
use App\Entity\Food;
use App\Entity\Ingredient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class AddRecipesCommand extends Command
{
    protected static $defaultName = 'add:recipes';

    private $container;

    private $slugger;

    private $io;

    private $unit;

    private $oldUnit;

    private $unitUser;

    private $quantity;

    private $steps;

    private $modificationUnitOfMeasure = false;

    private $differentUnitOfMeasure = false;

    public function __construct(ContainerInterface $container, Slugger $slugger)
    {
        parent::__construct();
        $this->container = $container;
        $this->slugger = $slugger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $em = $this->container->get('doctrine')->getManager();

        require 'recipes_array.php';

        foreach ($recipesToPersist as $recipe) {
            $recipeMeta = $recipe['meta'];

            $io->note('Recette parcourue : ' . $recipeMeta['title']);
            $io->note('URL : ' . $recipeMeta['url']);

            $ingredients = $recipe['ingredients'];

            $this->steps = $recipe['steps'];

            $recipe = $this->persistRecipe($recipeMeta);

            foreach ($this->steps as $key => $value) {
                $step = new RecipeStep();
                $step->setStepNumber($key);
                $step->setContent($value);
                $step->setRecipe($recipe);
                $em->persist($step);
                $em->flush();
            }

            foreach ($ingredients as $ingredient) {
                $mainName = $ingredient['name']['main'];
                $complementName = !empty($ingredient['name']['complement']) ? $ingredient['name']['complement'] : null;
                $this->unit = $ingredient['unitCalc'];
                $this->quantity = $ingredient['quantityCalc'];
                $this->unitUser = $ingredient['unitUser'];

                $food = $this->checkFoodInDB($mainName, $complementName, $input, $output);

                $newIngredient = $this->persistIngredient($recipe, $food, $complementName, $input, $output);
            }

            $io->success('Recette correctement enregistrée en bdd');
        }
        $io->success('Toutes les recettes ont été enregistrée en bdd');
    }

    public function persistRecipe($recipeMeta)
    {
        $em = $this->container->get('doctrine')->getManager();
        $title = $recipeMeta['title'];
        $slug = $this->slugger->slugify($title);
        $image = $recipeMeta['image'];

        $recipe = new Recipe();
        $recipe->setTitle($title);
        $recipe->setSlug($slug);
        $recipe->setImage($image);

        if (strpos($slug, 'petit-dejeuner') !== false) {
            $recipe->setType('petit déjeuner');
        } else {
            $rand = rand(0,1);
            if ($rand === 0) {
                $recipe->setType('déjeuner');
            } else {
                $recipe->setType('dîner');
            }
        }

        $em->persist($recipe);
        $em->flush();

        return $recipe;
    }

    public function persistIngredient($recipe, $food, $complementName, $input, $output)
    {
        $em = $this->container->get('doctrine')->getManager();
        $ingredient = new Ingredient();
        $ingredient->setAliment($food);
        $ingredient->setRecipe($recipe);

        if (!empty($complementName)) {
            $ingredient->setComplementInfo($complementName);
        }

        $this->checkUnitsOfMeasure($food->getName(), $complementName, $output, $input);

        $ingredient->setQuantity($this->quantity);

        $em->persist($ingredient);
        $em->flush();

        return $ingredient;
    }

    public function checkFoodInDB($name, $complementName, $input, $output)
    {
        $output->writeln([
            '',
            '',
            '',
            '========================================================================',
            '',
            '           Traitement de : ' . $name . ' ' . $complementName,
            '',
            '========================================================================',
            '',
        ]);
        $em = $this->container->get('doctrine')->getManager();

        $types = array_column($this->container->get('doctrine')->getRepository(Food::class)->findTypes(), 'types');

        $food = $this->container->get('doctrine')->getRepository(Food::class)
                     ->findOneBy(['name' => $name], ['createdAt' => 'DESC']);

        if (null !== $food) {
            $output->writeln($name . ' (' . $complementName . ') trouvé dans la bdd.');

            $unit = $food->getUnit();

            if ($unit !== $this->unit) {
                $this->oldUnit = $this->unit;
                $this->unit = $unit;
                //$output->writeln('unit modifié/diffèrent');
                $this->differentUnitOfMeasure = true;
            } else {
                $output->writeln('Tout semble ok. Rien à modifier. ');
            }

            return $food;
        } else {
            $food = new Food();
            $type = $this->askForTypeToUser($name, $complementName, $types, $input, $output);
            $price = $this->askForPriceToUser($name, $complementName, $input, $output);
            $food->setType($type);
            $food->setUnit($this->unit);
            $food->setName($name);
            //$food->setComplementName($complementName);
            $food->setPrice($price);
            $em->persist($food);
            $em->flush();
            return $food;
        }
    }

    public function checkUnitsOfMeasure($name, $complementName, $output, $input)
    {
        $io = new SymfonyStyle($input, $output);
        if ($this->differentUnitOfMeasure !== false) {
            $io->warning('L\'unité de mesure enregistrée en BDD et l\'unité de mesure indiquée dans la recette diffèrent.');
            $output->writeln('Il vous faut donc convertir la quantité dans l\'unité de mesure de la bdd.');
            $this->quantity = floatval($io->ask(
                'Veuillez convertir "' .
                $this->quantity .
                ' / ' . 
                $this->oldUnit . 
                '('.
                $this->unitUser.
                ')' .
                '" de "' . 
                $name . 
                ' ' . 
                $complementName . 
                '", en sa quantité équivalente en ' .
                $this->unit .
                ' (float attendu)')
            );
            $this->differentUnitOfMeasure = false;
        }

        if ($this->modificationUnitOfMeasure !== false) {
            $io->warning('Vous avez modifié l\'unité de mesure initiale. Veuillez modifier également la quantité pour la recette.');
            $this->quantity = floatval($io->ask('Veuillez convertir "' . $this->quantity . ' / ' . $this->oldUnit . '('.  $this->unitUser  .  ')' . '" de "' . $name . ' ' . $complementName . '", en sa quantité équivalente en ' . $this->unit . '(float attendu)'));
            $this->modificationUnitOfMeasure = false;
        }


    }

    public function askForTypeToUser($name, $complementName, $types, $input, $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->warning('"' . $name . ' ' . $complementName . '" n\'a pas été trouvé dans la bdd. Créons le !');
        $helper = $this->getHelper('question');
        //$output->writeln('"' . $name . ' ' . $complementName . '" n\'a pas été trouvé dans la bdd. Créons le !');
        $output->writeln(' ');
        $output->writeln('Choisissez d\'abord un type.');
        $output->writeln(' ');
        $output->writeln('Liste des types déjà disponibles (vous pouvez en créer de nouveaux) : '); 
        $output->writeln(' ');
        $io->listing($types);
        $output->writeln(' ');
        $output->writeln(' ');

        $type = $io->ask('Entrez le type de "' . $name . ' ' . $complementName . '" :', 'nc');
        $io->success('Type "' . $type . '" bien enregistré ! ');
        return $type;
    }

    public function askForPriceToUser($name, $complementName, $input, $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $this->askIfCorrectUnit($name, $complementName, $input, $output);

        $price = floatval($io->ask('Choisissez maintenant un prix au(à l\') __ ' . $this->unit . ' __ pour "' . $name . ' ' . $complementName . '". Format float attendu (ex: 2.56 ou 2) : ', 0));

        $io->success('Prix "' . $price . '" bien enregistré ! ');

        return $price;
    }

    public function askIfCorrectUnit($name, $complementName, $input, $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $result = $io->confirm(' " ' . $this->unit . ' " est-elle l\'unité de mesure correcte pour ' . $name . ' ' . $complementName . ' ? ', true);

        if (!$result) {
            $this->oldUnit = $this->unit;
            $units = array_column($this->container->get('doctrine')->getRepository(Food::class)->findUnits(), 'units');
            $io->listing($units);
            $this->unit = $io->ask('Choisissez une nouvelle unité de mesure parmi celles ci-dessus (vous pouvez en créer de nouvelles si besoin) : ', 'l');
            $this->modificationUnitOfMeasure  = true;
        } else {
            $io->note('Très bien ! ');
        }
    }
}
