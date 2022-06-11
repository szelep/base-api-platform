<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\{
    AbstractMigration,
    Exception\MigrationException,
    Exception\AbortMigration
};

/**
 * Migration <className>.
 *
 * This is auto-generated migration, please modify to your needs. Note that any
 * down migrations are prohibited! Use subsequent up migrations to fix issues.
 */
final class <className> extends AbstractMigration
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
        return '';
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
            'Migration can only be executed safely on PostgreSQL 12 or newest.'
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
<up>
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
