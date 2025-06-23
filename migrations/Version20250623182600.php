<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250623182600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX idx_8d93d64965fcfa0d
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER farm_id SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D64965FCFA0D ON "user" (farm_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D64965FCFA0D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER farm_id DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_8d93d64965fcfa0d ON "user" (farm_id)
        SQL);
    }
}
