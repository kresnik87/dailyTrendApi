<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210116094258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feed ADD publisher_id INT DEFAULT NULL, ADD updated DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE feed ADD CONSTRAINT FK_234044AB40C86FCE FOREIGN KEY (publisher_id) REFERENCES publisher (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_234044AB40C86FCE ON feed (publisher_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE feed DROP FOREIGN KEY FK_234044AB40C86FCE');
        $this->addSql('DROP INDEX UNIQ_234044AB40C86FCE ON feed');
        $this->addSql('ALTER TABLE feed DROP publisher_id, DROP updated');
    }
}
