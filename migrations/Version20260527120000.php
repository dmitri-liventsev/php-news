<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Mudimind massage-salon booking: masseur, client and book tables.
 */
final class Version20260527120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Mudimind masseur, client and book tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE masseur (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_MUDIMIND_CLIENT_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, masseur_id INT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_MUDIMIND_BOOK_CLIENT (client_id), INDEX IDX_MUDIMIND_BOOK_MASSEUR (masseur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_MUDIMIND_BOOK_CLIENT FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_MUDIMIND_BOOK_MASSEUR FOREIGN KEY (masseur_id) REFERENCES masseur (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_MUDIMIND_BOOK_CLIENT');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_MUDIMIND_BOOK_MASSEUR');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE masseur');
    }
}