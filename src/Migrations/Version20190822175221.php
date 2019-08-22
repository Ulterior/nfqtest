<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190822175221 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE res_tables (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT DEFAULT NULL, capacity SMALLINT UNSIGNED NOT NULL, number SMALLINT UNSIGNED NOT NULL, status ENUM(\'active\', \'inactive\'), INDEX IDX_C5055597B1E7706E (restaurant_id), UNIQUE INDEX restaurant_table_number (restaurant_id, number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, status ENUM(\'active\', \'inactive\'), UNIQUE INDEX UNIQ_EB95123F2B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE res_tables ADD CONSTRAINT FK_C5055597B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE res_tables DROP FOREIGN KEY FK_C5055597B1E7706E');
        $this->addSql('DROP TABLE res_tables');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE restaurant');
    }
}
