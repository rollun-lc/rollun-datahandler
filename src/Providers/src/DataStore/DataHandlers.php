<?php


namespace rollun\datahandlers\Providers\DataStore;


use rollun\datastore\DataStore\SerializedDbTable;

class DataHandlers extends SerializedDbTable
{
    public const TABLE_NAME = 'data_handlers';

    public const FIELD_ID = 'id';
}