<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250622212721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE farm ADD postal_code VARCHAR(5) DEFAULT NULL');

        $this->addSql("UPDATE farm SET postal_code = '00000' WHERE postal_code IS NULL");

        $this->addSql('ALTER TABLE farm ALTER COLUMN postal_code SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE farm DROP postal_code');
    }
}
