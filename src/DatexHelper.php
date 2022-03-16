<?php
namespace Spaceboy\Datex;


use Spaceboy\NetteCli\Cli;
use Nette\DI\Container;


class DatexHelper {

    private const ERR_NO_TABLE = 'Table not found (%s).' . PHP_EOL . 'For list of all accessible tables, type [php] datex.php tables.' . PHP_EOL;

    private const ERR_DIR_NOT_EXISTS = 'Directory not found or is not writable (%s).' . PHP_EOL . 'Create it, make it writable or change settings in config file.' . PHP_EOL;

    private const ERR_FILE_EXISTS = 'Target file already exists (%s).' . PHP_EOL . 'Remove/rename it, or allow overwriting.' . PHP_EOL;

    private const MSG_FILE_CREATED = 'File created (%s).' . PHP_EOL;

    private const TYPE_ENTITY = 'entity';

    private const TYPE_FORM = 'form';

    private const TYPE_MODEL = 'model';

    private const TYPE_LIST = [
        'INT' => 'int',
        'TIMESTAMP' => 'int',
        'TINYINT' => 'int',
        'VARCHAR' => 'string',
        'TEXT' => 'string',
        'BIT' => 'bool',
        'DATETIME' => '\DateTime',
    ];

    private const INPUT_LIST = [
        'INT' => 'addText',
        'TIMESTAMP' => 'addText',
        'TINYINT' => 'addText',
        'VARCHAR' => 'addText',
        'TEXT' => 'addTextarea',
        'BIT' => 'addCheckbox',
        'DATETIME' => 'addText',
    ];

    public static function commandColumns(Cli $cli, DatexModel $model, string $table)
    {
        $cli->showName();
        if (!static::tableExists($model, $table)) {
            echo sprintf(static::ERR_NO_TABLE, $table);
            return;
        }
        echo "Columns of table '{$table}':" . PHP_EOL;
        foreach ($model->getColumns($table) as $item) {
            echo "- {$item['name']} {$item['nativetype']} "
                . ($item['nullable'] ? 'NULL ' : 'NOT NULL ')
                . ($item['autoincrement'] ? 'AI ' : '')
                . ($item['primary'] ? 'primary' : '')
                . PHP_EOL;
        }
    }

    public static function commandEntity(
        Cli $cli,
        DatexModel $model,
        string $table,
        ?string $file = null,
        bool $overwrite = false,
        bool $screen = false
    )
    {
        static::createObject(static::TYPE_ENTITY, $cli, $model, $table, $file, $overwrite, $screen);
    }

    public static function commandForm(
        Cli $cli,
        DatexModel $model,
        string $table,
        ?string $file = null,
        bool $overwrite = false,
        bool $screen = false
    )
    {
        static::createObject(static::TYPE_FORM, $cli, $model, $table, $file, $overwrite, $screen);
    }

    public static function commandModel(
        Cli $cli,
        DatexModel $model,
        string $table,
        ?string $file = null,
        bool $overwrite = false,
        bool $screen = false
    )
    {
        static::createObject(static::TYPE_MODEL, $cli, $model, $table, $file, $overwrite, $screen);
    }

    public static function commandTables(Cli $cli, DatexModel $model)
    {
        $cli->showName();
        echo 'List of tables and views:' . PHP_EOL;
        foreach ($model->getTables() as $table) {
            echo ($table['view'] ? '-  view ' : '- table ')
                . $table['name']
                . PHP_EOL;
        }
    }

    private static function createObject(
        string $type,
        Cli $cli,
        DatexModel $model,
        string $table,
        ?string $file = null,
        bool $overwrite = false,
        bool $screen = false
    )
    {
        if (!static::tableExists($model, $table)) {
            echo sprintf(static::ERR_NO_TABLE, $table);
            return;
        }
        $config = $model->getConfig();
        $data = [
            'objectName' => static::getObjectName($table, $type),
            'table' => $table,
            'namespace' => $config[$type]['namespace'],
            'columns' => $model->getColumns($table),
        ];

        // Display preview on screen:
        if ($screen) {
            include($config[$type]['template']);
            return;
        }

        // Create file:
        $cli->showName();
        if ($file === null) {
            $path = static::getProjectRoot() . DIRECTORY_SEPARATOR . $config[$type]['path'];
            if (!\is_dir($path) || !\is_writable($path)) {
                echo sprintf(static::ERR_DIR_NOT_EXISTS, $path);
                return;
            }
            $file = $path . DIRECTORY_SEPARATOR . $data['objectName'] . '.php';
        }
        if (!$overwrite && \file_exists($file)) {
            echo sprintf(static::ERR_FILE_EXISTS, $file);
            return;
        }
        \ob_start();
        include($config[$type]['template']);
        \file_put_contents($file, ob_get_clean());
        echo \sprintf(static::MSG_FILE_CREATED, $file);
    }

    private static function getProjectRoot(): string
    {
        $path = [];
        foreach (explode(DIRECTORY_SEPARATOR, realpath(__DIR__)) as $item) {
            if (in_array($item, ['app', 'vendor'])) {
                break;
            }
            $path[] = $item;
        }
        return join(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Determine whether DB table exists or not.
     * @param DatexModel $model
     * @param string $tableName
     * @return bool
     */
    private static function tableExists(DatexModel $model, string $tableName): bool
    {
        foreach ($model->getTables() as $table) {
            if ($tableName === $table['name']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build object name.
     */
    private static function getObjectName(string $table, string $type): string
    {
        return ucfirst(static::convertName2php($table)) . ucfirst($type);
    }

    /**
     * Convert database type description to PHP convention.
     * eg. VARCHAR(255) => string; INT => int etc.
     * @param string $typeDb
     * @return string|null
     */
    public static function convertType2php(string $typeDb): ?string
    {
        return (
            array_key_exists($typeDb, static::TYPE_LIST)
            ? static::TYPE_LIST[$typeDb]
            : null
        );
    }

    /**
     * Convert db names to camelized convention.
     * eg. column_name => columnName
     * @param string $nameDb
     * @return string
     */
    public static function convertName2php(string $nameDb): string
    {
        $arr = \explode('_', $nameDb);
        return lcfirst(
            join(
                '',
                array_map(
                    function ($word) {
                        return ucfirst($word);
                    },
                    $arr
                )
            )
        );
    }
}