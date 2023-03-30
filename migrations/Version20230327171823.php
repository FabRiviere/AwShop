<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327171823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carriers ADD image VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE categories ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users ADD avatar VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carriers DROP image, DROP created_at');
        $this->addSql('ALTER TABLE categories DROP image');
        $this->addSql('ALTER TABLE products DROP image');
        $this->addSql('ALTER TABLE users DROP avatar');
    }
}
