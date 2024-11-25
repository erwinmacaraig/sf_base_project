<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241125073907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forecast (id INT AUTO_INCREMENT NOT NULL, location_id INT NOT NULL, date DATE NOT NULL, celsius INT NOT NULL, INDEX IDX_2A9C784464D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forecast ADD CONSTRAINT FK_2A9C784464D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql("INSERT INTO forecast (location_id, `date`, celsius) VALUES (2, '2024-01-01', 21)");
        $this->addSql("INSERT INTO forecast (location_id, `date`, celsius) VALUES (2, '2024-01-02', 22)");
        $this->addSql("INSERT INTO forecast (location_id, `date`, celsius) VALUES (2, '2024-01-03', 23)");
        $this->addSql("INSERT INTO forecast (location_id, `date`, celsius) VALUES (2, '2024-01-04', 24)");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forecast DROP FOREIGN KEY FK_2A9C784464D218E');
        $this->addSql('DROP TABLE forecast');
    }
}
