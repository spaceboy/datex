<?php
/**
 * CLI script
 * @author spaceboy
 */
namespace App\Bin;

require_once(__DIR__ . '/../vendor/autoload.php');

use Spaceboy\NetteCli\Cli;
use Spaceboy\NetteCli\Argument;
use Spaceboy\NetteCli\Command;

use Spaceboy\Datex\DatexHelper;

(new Cli())
    ->setName('Datex, Database Express')
    ->hideName()
    ->setDescription('Create PHP entity, form and model files on DB tables.')
    // Register parameters and options using ->registerParameter() and ->registerOption() methods.
    // Register commands using ->registerCommand() method.
    ->registerArgument(
        Argument::create('table')
            ->setDescription('Table name')
            ->setShortcut('t')
    )
    ->registerArgument(
        Argument::create('file')
            ->setDescription('Target file path/name')
            ->setShortcut('f')
    )
    ->registerOption(
        Argument::create('overwrite')
            ->setDescription('Overwrite existing target file')
            ->setShortcut('o')
    )
    ->registerOption(
        Argument::create('screen')
            ->setDescription('Show output only on screen (pipeline)')
            ->setShortcut('s')
    )
    ->registerCommand(
        Command::create('entity')
            ->setDescription('Create [table]Entity.php file.')
            ->withArgumentRequired('table')
            ->withArgumentOptional('file')
            ->withOption('overwrite')
            ->withOption('screen')
            ->setWorker([DatexHelper::class, 'commandEntity'])
    )
    ->registerCommand(
        Command::create('form')
            ->setDescription('Create [table]Form.php file.')
            ->withArgumentRequired('table')
            ->withArgumentOptional('file')
            ->withOption('overwrite')
            ->withOption('screen')
            ->setWorker([DatexHelper::class, 'commandForm'])
    )
    ->registerCommand(
        Command::create('model')
            ->setDescription('Create [table]Model.php file.')
            ->withArgumentRequired('table')
            ->withArgumentOptional('file')
            ->withOption('overwrite')
            ->withOption('screen')
            ->setWorker([DatexHelper::class, 'commandModel'])
    )
    ->registerCommand(
        Command::create('columns')
            ->setDescription('Show [table] columns.')
            ->withArgumentRequired('table')
            ->setWorker([DatexHelper::class, 'commandColumns'])
    )
    ->registerCommand(
        Command::create('tables')
            ->setDescription('Show list of tables and views in database.')
            ->setWorker([DatexHelper::class, 'commandTables'])
    )
    ->run();