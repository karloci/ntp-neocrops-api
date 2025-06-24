<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624011920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_8d93d64965fcfa0d
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D64965FCFA0D ON "user" (farm_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D64965FCFA0D
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_8d93d64965fcfa0d ON "user" (farm_id)
        SQL);
    }
}
