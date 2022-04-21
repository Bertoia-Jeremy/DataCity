<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220125075816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_value ADD code_nature_culture VARCHAR(100) DEFAULT NULL, ADD nature_culture VARCHAR(100) DEFAULT NULL, ADD code_nature_culture_speciale VARCHAR(100) DEFAULT NULL, ADD nature_culture_speciale VARCHAR(100) DEFAULT NULL, DROP nature_code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property_value ADD nature_code VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP code_nature_culture, DROP nature_culture, DROP code_nature_culture_speciale, DROP nature_culture_speciale');
    }
}
