<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241226172801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_history ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE service_history ADD CONSTRAINT FK_E83E22D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E83E22D7A76ED395 ON service_history (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_history DROP FOREIGN KEY FK_E83E22D7A76ED395');
        $this->addSql('DROP INDEX IDX_E83E22D7A76ED395 ON service_history');
        $this->addSql('ALTER TABLE service_history DROP user_id');
    }
}
