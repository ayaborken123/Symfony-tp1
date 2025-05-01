<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501194821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE order_item');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, created_at FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_at DATETIME NOT NULL, customer_email VARCHAR(255) NOT NULL, products CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('INSERT INTO "order" (id, created_at) SELECT id, created_at FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, order_command_id INTEGER NOT NULL, product_name VARCHAR(255) NOT NULL COLLATE "BINARY", price DOUBLE PRECISION NOT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_52EA1F09803B95CF FOREIGN KEY (order_command_id) REFERENCES "order" (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_52EA1F09803B95CF ON order_item (order_command_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, created_at FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, created_at DATETIME NOT NULL, total DOUBLE PRECISION NOT NULL, CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "order" (id, created_at) SELECT id, created_at FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON "order" (user_id)');
    }
}
