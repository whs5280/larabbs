<?php

namespace App\Models\OTS;

class AliyunOTSTable
{
    static $table = array(
        'feeds' => array(        //用户动态表
            'primary_key_schema' => array(
                array('user_id', AliyunOTS::CONST_INTEGER),
                array('type_id', AliyunOTS::CONST_INTEGER),
                array('data', AliyunOTS::CONST_INTEGER),
                array('created_at', AliyunOTS::CONST_INTEGER),
            ),
            'defined_column' => array (
                array('status', AliyunOTS::CONST_INTEGER),
            )
        ),
    );

    /**
     * @param $tableName
     * @param $action
     * @param $dataRows
     * @return array[]
     */
    public static function createBatchRequest($tableName,$action,$dataRows): array
    {
        $dataRows = array_map(function($one) use($tableName) {
            $one['condition'] = $one['condition'] ?: AliyunOTS::CONST_IGNORE;
            $one['primary_key'] = self::primaryKeyFormat($tableName, $one['primary_key']);
            return $one;
        },$dataRows);
        return array (
            'tables' => array (
                array (
                    'table_name' => $tableName,
                    $action => $dataRows
                )
            )
        );
    }

    /**
     * @param $tableName
     * @param $primaryKeys
     * @return mixed
     */
    public static function primaryKeyFormat($tableName, $primaryKeys)
    {
        if(!$tableName || !$primaryKeys){
            return $primaryKeys;
        }
        $primaryKeyMap = self::$table[$tableName]['primary_key_schema'];
        foreach ($primaryKeyMap as $key => $type) {
            $primaryKeyMap[$key] = self::switchValue($primaryKeys[$key], $type);
        }
        return $primaryKeyMap;
    }

    /**
     * @param $value
     * @param $type
     * @return bool|float|int|mixed|string
     */
    public static function switchValue($value, $type)
    {
        switch ($type) {
            case AliyunOTS::CONST_INTEGER:
                return (integer)$value;
                break;
            case AliyunOTS::CONST_STRING:
                return (string)$value;
                break;
            case AliyunOTS::CONST_BOOLEAN:
                return (bool)$value;
                break;
            case AliyunOTS::CONST_DOUBLE:
                return (double)$value;
                break;
            default:
                return $value;
                break;
        }
    }
}
