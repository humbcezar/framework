<?php

use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\Database\Console\Migrations\RollbackCommand;

class DatabaseMigrationRollbackCommandTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRollbackCommandCallsMigratorWithProperArguments()
    {
        $command = new RollbackCommand($migrator = m::mock('Illuminate\Database\Migrations\Migrator'));
        $app = new ApplicationDatabaseRollbackStub(['path.database' => __DIR__]);
        $app->useDatabasePath(__DIR__);
        $command->setLaravel($app);
        $migrator->shouldReceive('paths')->once()->andReturn([]);
        $migrator->shouldReceive('setConnection')->once()->with(null);
        $migrator->shouldReceive('rollback')->once()->with([__DIR__.'/migrations'], false);
        $migrator->shouldReceive('getNotes')->andReturn([]);

        $this->runCommand($command);
    }

    public function testRollbackCommandCanBePretended()
    {
        $command = new RollbackCommand($migrator = m::mock('Illuminate\Database\Migrations\Migrator'));
        $app = new ApplicationDatabaseRollbackStub(['path.database' => __DIR__]);
        $app->useDatabasePath(__DIR__);
        $command->setLaravel($app);
        $migrator->shouldReceive('paths')->once()->andReturn([]);
        $migrator->shouldReceive('setConnection')->once()->with('foo');
        $migrator->shouldReceive('rollback')->once()->with([__DIR__.'/migrations'], true);
        $migrator->shouldReceive('getNotes')->andReturn([]);

        $this->runCommand($command, ['--pretend' => true, '--database' => 'foo']);
    }

    protected function runCommand($command, $input = [])
    {
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), new Symfony\Component\Console\Output\NullOutput);
    }
}

class ApplicationDatabaseRollbackStub extends Application
{
    public function __construct(array $data = [])
    {
        foreach ($data as $abstract => $instance) {
            $this->instance($abstract, $instance);
        }
    }

    public function environment()
    {
        return 'development';
    }
}
