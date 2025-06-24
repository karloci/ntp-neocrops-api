<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624194106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE consumption (id SERIAL NOT NULL, supply_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, transaction_date DATE NOT NULL, comment TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2CFF2DF9FF28C0D8 ON consumption (supply_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN consumption.transaction_date IS '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consumption ADD CONSTRAINT FK_2CFF2DF9FF28C0D8 FOREIGN KEY (supply_id) REFERENCES supply (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD comment TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase RENAME COLUMN billing_date TO transaction_date
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consumption DROP CONSTRAINT FK_2CFF2DF9FF28C0D8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE consumption
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP comment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase RENAME COLUMN transaction_date TO billing_date
        SQL);
    }
}
