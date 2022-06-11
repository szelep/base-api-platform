<?php

declare(strict_types=1);

namespace App\Tests\PhpUnit;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\{
    Output\ConsoleOutput,
    Output\NullOutput,
    Input\ArrayInput
};

/**
 * Class DatabaseConfig
 */
class DatabaseConfig
{
    private Application $application;
    private ConsoleOutput $output;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $kernel = new Kernel('test', false);
        $kernel->boot();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $this->application = $application;
        $this->output = new ConsoleOutput();
    }

    /**
     * Drops old test database.
     *
     * @return void
     */
    public function dropDatabase(): void
    {
        $this->output->write('Drop existing database...');
        $input = new ArrayInput([
            'command' => 'doctrine:database:drop',
            '--force' => true,
            '--no-interaction' => true,
            '--env' => 'test',
        ]);

        $this->run($input);
    }

    /**
     * Creates fresh test database.
     *
     * @return void
     */
    public function createDatabase(): void
    {
        $this->output->write('Create database...');
        $input = new ArrayInput([
            'command' => 'doctrine:database:create',
            '--no-interaction' => true,
            '--env' => 'test',
        ]);

        $this->run($input);
    }

    /**
     * Create database schema.
     *
     * @return void
     */
    public function createSchema(): void
    {
        $this->output->write('Create database schema...');
        $input = new ArrayInput([
            'command' => 'doctrine:schema:create',
            '--no-interaction' => true,
            '--env' => 'test',
        ]);

        $this->run($input);
    }

    /**
     * Loads doctrine fixtures.
     *
     * @return void
     */
    public function loadFixtures(): void
    {
        $this->output->write('Load fixtures...');
        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true,
            '--env' => 'test',
        ]);

        $this->run($input);
    }

    /**
     * Executes command.
     *
     * @param ArrayInput $input
     *
     * @return void
     */
    private function run(ArrayInput $input): void
    {
        $result = $this
            ->application
            ->run($input, new NullOutput())
        ;

        $this->output->writeln($result === 0 ? 'ok' : 'fail');
    }
}
