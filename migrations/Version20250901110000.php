<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250901110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du champ is_read à la table notification';
    }

    public function up(Schema $schema): void
    {
        // Cette migration est spécifique à MySQL
        $this->addSql('ALTER TABLE notification ADD is_read TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP COLUMN is_read');
    }
}
