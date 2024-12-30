<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208223517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_sub_category (service_id INT NOT NULL, sub_category_id INT NOT NULL, INDEX IDX_634F827DED5CA9E6 (service_id), INDEX IDX_634F827DF7BFE87C (sub_category_id), PRIMARY KEY(service_id, sub_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_sub_category ADD CONSTRAINT FK_634F827DED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_sub_category ADD CONSTRAINT FK_634F827DF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_sub_category DROP FOREIGN KEY FK_634F827DED5CA9E6');
        $this->addSql('ALTER TABLE service_sub_category DROP FOREIGN KEY FK_634F827DF7BFE87C');
        $this->addSql('DROP TABLE service_sub_category');
    }
}
