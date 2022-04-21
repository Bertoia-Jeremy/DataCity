<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220121071938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE property_value (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, mutation_id VARCHAR(100) NOT NULL, mutation_date DATETIME NOT NULL, price INT NOT NULL, postal_code VARCHAR(10) NOT NULL, city_name VARCHAR(255) NOT NULL, sale_type VARCHAR(80) NOT NULL, parcelle_id VARCHAR(100) DEFAULT NULL, building_type VARCHAR(100) NOT NULL, surface_building INT NOT NULL, surface_field INT NOT NULL, piece_number INT DEFAULT NULL, longitude VARCHAR(100) NOT NULL, latitude VARCHAR(100) NOT NULL, number_lot INT DEFAULT NULL, nature_code VARCHAR(50) DEFAULT NULL, INDEX IDX_DB6499398BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE property_value ADD CONSTRAINT FK_DB6499398BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE property_value');
    }
}
