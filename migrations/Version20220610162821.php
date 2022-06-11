<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\{
    AbstractMigration,
    Exception\MigrationException,
    Exception\AbortMigration
};

/**
 * Migration Version20220610162821.
 *
 * This is auto-generated migration, please modify to your needs. Note that any
 * down migrations are prohibited! Use subsequent up migrations to fix issues.
 */
final class Version20220610162821 extends AbstractMigration
{
    /**
     * Provides optional description for migration.
     *
     * The value returned here will get outputted when you run command:
     * "bin/console doctrine:migrations:status status --show-versions"
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create users and roles tables';
    }

    /**
     * This method is called just before up().
     *
     * Avoid modify this method. If must, please add your changes at the end of the method.
     *
     * @param Schema $schema
     *
     * @return void
     *
     * @SuppressWarnings("unused")
     */
    public function preUp(Schema $schema) : void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform,
            'Migration can only be executed safely on PostgreSQL 14 or newest.'
        );
        $this->write('Executing migration ' . $this::class . '. [' . $this->getDescription() . ']');

        // Add optional changes after this comment.
    }

    /**
     * Executes up migrations.
     *
     * @param Schema $schema
     *
     * @return void
     *
     * @throws MigrationException If SQL execution fail (eg. SQL is invalid).
     *
     * @SuppressWarnings("unused")
     */
    public function up(Schema $schema): void
    {
        // This up migration is auto-generated, please modify it to your needs.
        $this->addSql('CREATE SCHEMA users');
        $this->addSql(<<<'SQL'
CREATE TABLE users.roles (
    id UUID NOT NULL, 
    name TEXT NOT NULL, 
    PRIMARY KEY(id)
                         )
SQL
        );
        $this->addSql('COMMENT ON COLUMN users.roles.id IS \'(DC2Type:uuid)\'');
        $this->addSql(<<<'SQL'
CREATE TABLE users.users (
    id UUID NOT NULL, 
    password TEXT DEFAULT NULL, 
    username TEXT NOT NULL, 
    PRIMARY KEY(id)
                         )
SQL
        );
        $this->addSql('COMMENT ON COLUMN users.users.id IS \'(DC2Type:uuid)\'');
        $this->addSql(<<<'SQL'
CREATE TABLE users.users_roles (
    user_id UUID NOT NULL, 
    role_id UUID NOT NULL, 
    PRIMARY KEY(user_id, role_id)
                               )
SQL
        );
        $this->addSql('CREATE INDEX IDX_19137BE7A76ED395 ON users.users_roles (user_id)');
        $this->addSql('CREATE INDEX IDX_19137BE7D60322AC ON users.users_roles (role_id)');
        $this->addSql('COMMENT ON COLUMN users.users_roles.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.users_roles.role_id IS \'(DC2Type:uuid)\'');
        $this->addSql(<<<'SQL'
ALTER TABLE users.users_roles ADD CONSTRAINT FK_19137BE7A76ED395 
    FOREIGN KEY (user_id) REFERENCES users.users (id) 
        ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
SQL
        );
        $this->addSql(<<<'SQL'
ALTER TABLE users.users_roles ADD CONSTRAINT FK_19137BE7D60322AC 
    FOREIGN KEY (role_id) REFERENCES users.roles (id) 
        ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
SQL
        );
    }

    /**
     * Prevents execution of down migrations.
     *
     * @param Schema $schema
     *
     * @return void
     *
     * @throws AbortMigration If migration is aborted.
     *
     * @SuppressWarnings("unused")
     */
    public function down(Schema $schema): void
    {
        // Do not modify this method.
        $this->abortIf(true, 'Down migrations are prohibited! Use subsequent up migrations to fix issues.');
    }
}
