<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109142434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD created DATETIME NOT NULL');
        $this->addSql('ALTER TABLE task ADD company_id INT DEFAULT NULL, ADD text VARCHAR(255) DEFAULT NULL, ADD accessibility VARCHAR(10) NOT NULL, ADD status TINYINT(1) NOT NULL, ADD created DATETIME NOT NULL, ADD deadline DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB25979B1AD6 ON task (company_id)');
        $this->addSql('ALTER TABLE user ADD company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649979B1AD6 ON user (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25979B1AD6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE comment DROP created');
        $this->addSql('DROP INDEX UNIQ_8D93D649979B1AD6 ON user');
        $this->addSql('ALTER TABLE user DROP company_id');
        $this->addSql('DROP INDEX UNIQ_527EDB25979B1AD6 ON task');
        $this->addSql('ALTER TABLE task DROP company_id, DROP text, DROP accessibility, DROP status, DROP created, DROP deadline');
    }
}
