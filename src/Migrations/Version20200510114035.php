<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200510114035 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shopping_list (id INT AUTO_INCREMENT NOT NULL, groep_id INT NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_3DC1A4599EB44EC5 (groep_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shopping_list_item (id INT AUTO_INCREMENT NOT NULL, shopping_list_id INT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, created DATETIME NOT NULL, INDEX IDX_4FB1C22423245BF9 (shopping_list_id), INDEX IDX_4FB1C224A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A4599EB44EC5 FOREIGN KEY (groep_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE shopping_list_item ADD CONSTRAINT FK_4FB1C22423245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id)');
        $this->addSql('ALTER TABLE shopping_list_item ADD CONSTRAINT FK_4FB1C224A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shopping_list_item DROP FOREIGN KEY FK_4FB1C22423245BF9');
        $this->addSql('DROP TABLE shopping_list');
        $this->addSql('DROP TABLE shopping_list_item');
    }
}
