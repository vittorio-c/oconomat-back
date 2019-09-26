<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190926145342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recipe_menu (recipe_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_21E604C59D8A214 (recipe_id), INDEX IDX_21E604CCCD7E912 (menu_id), PRIMARY KEY(recipe_id, menu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_label (food_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_FFC69502BA8E87C4 (food_id), INDEX IDX_FFC6950233B92F39 (label_id), PRIMARY KEY(food_id, label_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE recipe_menu ADD CONSTRAINT FK_21E604C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_menu ADD CONSTRAINT FK_21E604CCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE food_label ADD CONSTRAINT FK_FFC69502BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE food_label ADD CONSTRAINT FK_FFC6950233B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objectif ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE objectif ADD CONSTRAINT FK_E2F86851A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E2F86851A76ED395 ON objectif (user_id)');
        $this->addSql('ALTER TABLE menu ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7D053A93A76ED395 ON menu (user_id)');
        $this->addSql('ALTER TABLE recipe_step ADD recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipe_step ADD CONSTRAINT FK_3CA2A4E359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_3CA2A4E359D8A214 ON recipe_step (recipe_id)');
        $this->addSql('ALTER TABLE ingredient ADD recipe_id INT NOT NULL, ADD aliment_id INT NOT NULL');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF787059D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870415B9F11 FOREIGN KEY (aliment_id) REFERENCES food (id)');
        $this->addSql('CREATE INDEX IDX_6BAF787059D8A214 ON ingredient (recipe_id)');
        $this->addSql('CREATE INDEX IDX_6BAF7870415B9F11 ON ingredient (aliment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE recipe_menu');
        $this->addSql('DROP TABLE food_label');
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF787059D8A214');
        $this->addSql('ALTER TABLE ingredient DROP FOREIGN KEY FK_6BAF7870415B9F11');
        $this->addSql('DROP INDEX IDX_6BAF787059D8A214 ON ingredient');
        $this->addSql('DROP INDEX IDX_6BAF7870415B9F11 ON ingredient');
        $this->addSql('ALTER TABLE ingredient DROP recipe_id, DROP aliment_id');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A93A76ED395');
        $this->addSql('DROP INDEX IDX_7D053A93A76ED395 ON menu');
        $this->addSql('ALTER TABLE menu DROP user_id');
        $this->addSql('ALTER TABLE objectif DROP FOREIGN KEY FK_E2F86851A76ED395');
        $this->addSql('DROP INDEX IDX_E2F86851A76ED395 ON objectif');
        $this->addSql('ALTER TABLE objectif DROP user_id');
        $this->addSql('ALTER TABLE recipe_step DROP FOREIGN KEY FK_3CA2A4E359D8A214');
        $this->addSql('DROP INDEX IDX_3CA2A4E359D8A214 ON recipe_step');
        $this->addSql('ALTER TABLE recipe_step DROP recipe_id');
    }
}
