<?php

namespace App\Models\OTS;

use Aliyun\OTS\OTSClient as OTSClient;
use Illuminate\Http\JsonResponse;

require dirname(app_path()) . '/vendor/aliyun/aliyun-tablestore-sdk-php/vendor/autoload.php';

class AliyunOTS
{
    // 数据类型
    const CONST_STRING = 'STRING';
    const CONST_INTEGER = 'INTEGER';
    const CONST_BOOLEAN = 'BOOLEAN';
    const CONST_DOUBLE = 'DOUBLE';
    const CONST_BINARY = 'BINARY';
    const CONST_INF_MIN = 'INF_MIN';
    const CONST_INF_MAX = 'INF_MAX';

    // condition 配置
    const CONST_IGNORE = 'IGNORE';  // 忽略
    const CONST_EXPECT_EXIST = 'EXPECT_EXIST';  // 要求存在
    const CONST_EXPECT_NOT_EXIST = 'EXPECT_NOT_EXIST';  // 要求不存在

    // 排序
    const CONST_FORWARD = 'FORWARD';    // 正向排序 起始主键要小于结束主键
    const CONST_BACKWARD = 'BACKWARD';  // 反向排序，起始主键要大于结束主键

    // 操作类型
    const BATCH_PUT_ROWS = 'put_rows';  // 批量插入
    const BATCH_UPDATE_ROWS = 'update_rows'; // 批量更新
    const BATCH_DELETE_ROWS = 'delete_rows'; // 批量删除

    private $otsClient;

    /**
     * @param $instance
     */
    function __construct($instance = null)
    {
        $this->otsClient = new OTSClient([
            'EndPoint'        => config('aliyun.ots.end_point'),
            'AccessKeyID'     => config('aliyun.ots.access_key_id'),
            'AccessKeySecret' => config('aliyun.ots.access_key_secret'),
            'InstanceName'    => $instance ?: config('aliyun.ots.instance_name'),
        ]);
    }

    /**
     * @param $instance
     * @return mixed
     */
    static public function getInstance($instance = null)
    {
        $className = get_called_class();
        return new $className($instance);
    }

    /**
     * 创表
     * @param $tableName
     * @param int $read
     * @param int $write
     * @param int $retryCount
     * @return bool
     */
    function createTable($tableName, int $read = 0, int $write = 0, int $retryCount = 1): bool
    {
        try{
            $this->checkTable($tableName);
            $request = array (
                'table_meta' => array (
                    'table_name' => $tableName, // 表名为：MyTable
                    'primary_key_schema' => AliyunOTSTable::$table[$tableName]['primary_key_schema'], // 主键由一列id
                    'defined_column'     => AliyunOTSTable::$table[$tableName]['defined_column'],     // 属性列
                ),

                'reserved_throughput' => array (
                    'capacity_unit' => array (
                        'read'  => $read,   // 预留读写吞吐量设置为：0个读CU，和0个写CU
                        'write' => $write
                    )
                ),

                'table_options' => array(
                    'time_to_live' => -1,   // 数据生命周期, -1表示永久，单位秒
                    'max_versions' => 2,    // 最大数据版本
                    'deviation_cell_version_in_sec' => 86400  // 数据有效版本偏差，单位秒
                )
            );
            $this->otsClient->createTable($request);
            logger()->info(sprintf('create `table` s% success', $tableName));
            return true;

        }catch(\Throwable $e){
            if(stripos($e->getMessage(),'timed out') !== false && $retryCount) {  //超时重试
                $retryCount--;
                $this->createTable($tableName, $read, $write, $retryCount);
            }
            logger()->error('create `table` error', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
            ]);
            return false;
        }
    }

    /**
     * 单条插入, 注意 primary_key 和 defined_column 顺序跟着表结构一一致
     * @param $request
     * @param int $retryCount
     * @return bool
     */
    public function putOneRow($request, int $retryCount = 1): bool
    {
        try{
            $this->checkTable($request['table_name']);
            //$request['primary_key'] = AliyunOTSTable::primaryKeyFormat($request['table_name'], $request['primary_key']);
            $request['condition'] = $request['condition'] ?? AliyunOTS::CONST_IGNORE;
            $result = $this->otsClient->putRow($request);
            logger()->info('put one row', ['request' => $request, 'result' => $result]);
            return true;

        } catch(\Throwable $e){
            if(stripos($e->getMessage(),'timed out') !== false && $retryCount){  //超时重试
                $retryCount--;
                return $this->putOneRow($request, $retryCount);
            }
            logger()->error('put one row error', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
            ]);
            return false;
        }
    }

    /**
     * 单条更新
     * @param $request
     * @param int $retryCount
     * @return bool
     */
    public function updateOneRow($request, int $retryCount = 1): bool
    {
        try{
            $this->checkTable($request['table_name']);
            $request['condition'] = $request['condition'] ?? AliyunOTS::CONST_IGNORE;
            $result = $this->otsClient->updateRow($request);
            logger()->info('update one row', ['request' => $request, 'result' => $result]);
            return true;

        } catch(\Throwable $e){
            if(stripos($e->getMessage(),'timed out') !== false && $retryCount){  //超时重试
                $retryCount--;
                return $this->putOneRow($request, $retryCount);
            }
            logger()->error('update one row error', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
            ]);
            return false;
        }
    }

    /**
     * 单条删除
     * @param $request
     * @param int $retryCount
     * @return bool
     */
    public function deleteOneRow($request, int $retryCount = 1): bool
    {
        try{
            $this->checkTable($request['table_name']);
            $request['condition'] = $request['condition'] ?? AliyunOTS::CONST_IGNORE;
            $result = $this->otsClient->deleteRow($request);
            logger()->info('delete one row', ['request' => $request, 'result' => $result]);
            return true;

        } catch(\Throwable $e){
            if(stripos($e->getMessage(),'timed out') !== false && $retryCount){  //超时重试
                $retryCount--;
                return $this->deleteOneRow($request, $retryCount);
            }
            logger()->error('delete one row error', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
            ]);
            return false;
        }
    }

    /**
     * 区间查询
     * @param $request
     * @param int $retryCount
     * @return JsonResponse
     */
    public function getRange($request, int $retryCount = 1): JsonResponse
    {
        try{
            $this->checkTable($request['table_name']);
            $request['direction'] = $request['direction'] ?? AliyunOTS::CONST_FORWARD;
            $request['limit'] = $request['limit'] ?? 100;
            return $this->otsClient->getRange($request);

        } catch(\Throwable $e){
            if(stripos($e->getMessage(),'timed out') !== false && $retryCount){  //超时重试
                $retryCount--;
                return $this->getRange($request, $retryCount);
            }
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * 查询
     * @param $request
     * @param int $retryCount
     * @return JsonResponse
     */
    public function search($request, int $retryCount = 1): JsonResponse
    {
        try{
            $this->checkTable($request['table_name']);
            $request['direction'] = $request['direction'] ?? AliyunOTS::CONST_FORWARD;
            $request['limit'] = $request['limit'] ?? 100;
            return $this->otsClient->search($request);

        } catch(\Throwable $e){
            if(stripos($e->getMessage(),'timed out') !== false && $retryCount){  //超时重试
                $retryCount--;
                return $this->search($request, $retryCount);
            }
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $tableName
     * @return void
     */
    function checkTable($tableName)
    {
        try {
            throw_if(!in_array($tableName, array_keys(AliyunOTSTable::$table)), new \Exception('table not exist', 404));
        } catch (\Throwable $e) {
            print $e->getMessage();
        }
    }
}
