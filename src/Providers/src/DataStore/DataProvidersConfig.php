<?php


namespace rollun\datahandler\Providers\DataStore;


use rollun\datastore\DataStore\SerializedDbTable;
use rollun\utils\Json\Serializer;
use Xiag\Rql\Parser\Query;

class DataProvidersConfig extends SerializedDbTable
{
    public const TABLE_NAME = 'data_providers_config';

    public const FIELD_ID = 'id';
    public const FIELD_DATA_HANDLER = 'data_handler';
    public const FIELD_CONFIG = 'config';

    public function create($itemData)
    {
        $itemData[self::FIELD_CONFIG] = Serializer::jsonSerialize($itemData['config']);
        return parent::create($itemData);
    }

    public function update($itemData)
    {
        $itemData[self::FIELD_CONFIG] = Serializer::jsonSerialize($itemData['config']);
        return parent::update($itemData);
    }

    public function query(Query $query)
    {
        $result = parent::query($query);
        return array_map(function ($item) {
            $item[self::FIELD_CONFIG] = Serializer::jsonUnserialize($item['config']);
            return $item;
        }, $result);
    }

    public function read($id)
    {
        $item = parent::read($id);
        if ($item !== null) {
            $item[self::FIELD_CONFIG] = Serializer::jsonUnserialize($item['config']);
        }
        return $item;
    }
}