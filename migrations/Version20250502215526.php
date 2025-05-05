<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502215526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD quantities JSON DEFAULT \'[]\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, customer_email, created_at, products FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, customer_email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, products CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO "order" (id, customer_email, created_at, products) SELECT id, customer_email, created_at, products FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }
}
