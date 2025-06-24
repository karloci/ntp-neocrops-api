<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624193743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE purchase (id SERIAL NOT NULL, supply_id INT NOT NULL, amount DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, billing_date DATE NOT NULL, invoice_no VARCHAR(45) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6117D13BFF28C0D8 ON purchase (supply_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN purchase.billing_date IS '(DC2Type:date_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BFF28C0D8 FOREIGN KEY (supply_id) REFERENCES supply (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP CONSTRAINT FK_6117D13BFF28C0D8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE purchase
        SQL);
    }
}
