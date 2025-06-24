<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624211434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE consumption ADD farm_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consumption ADD CONSTRAINT FK_2CFF2DF965FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2CFF2DF965FCFA0D ON consumption (farm_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD farm_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B65FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6117D13B65FCFA0D ON purchase (farm_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consumption DROP CONSTRAINT FK_2CFF2DF965FCFA0D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2CFF2DF965FCFA0D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consumption DROP farm_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP CONSTRAINT FK_6117D13B65FCFA0D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6117D13B65FCFA0D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP farm_id
        SQL);
    }
}
