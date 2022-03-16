<?php
namespace Spaceboy\Datex;


use Nette\Database\Explorer;


class DatexModel {

    private Explorer $explorer;

    private $structure;

    private array $config;

    public function __construct($config, Explorer $explorer)
    {
        $this->config = $config;
        $this->explorer = $explorer;
    }

    public function getColumns(string $table)
    {
        return $this->getStructure()->getColumns($table);
    }

    public function getTables()
    {
        return $this->getStructure()->getTables();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function getStructure()
    {
        if ($this->structure === null) {
            $this->structure = $this->explorer->getStructure();
        }
        return $this->structure;
    }
}