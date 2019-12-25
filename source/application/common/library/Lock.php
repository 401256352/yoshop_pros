<?php

namespace app\common\library;

/**
 * 文件阻塞锁
 * Class Lock
 * @package app\common\library
 */
class Lock
{
    // resource
    static $resource = [];

    /**
     * 加锁
     * @param $uniqueId
     * @return bool
     */
    public static function lockUp($uniqueId)
    {
        static::$resource[$uniqueId] = fopen(static::getFilePath($uniqueId), 'w+');
        return flock(static::$resource[$uniqueId], LOCK_EX);
    }

    /**
     * 解锁
     * @param $uniqueId
     * @return bool
     */
    public static function unLock($uniqueId)
    {
        flock(static::$resource[$uniqueId], LOCK_UN);
        fclose(static::$resource[$uniqueId]);
        return static::deleteFile($uniqueId);
    }

    /**
     * 获取锁文件的路径
     * @param $uniqueId
     * @return string
     */
    private static function getFilePath($uniqueId)
    {
        $dirPath = RUNTIME_PATH . 'lock/';
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        return $dirPath . md5($uniqueId);
    }

    /**
     * 删除锁文件
     * @param $uniqueId
     * @return bool
     */
    private static function deleteFile($uniqueId)
    {
        $filePath = RUNTIME_PATH . 'lock/' . md5($uniqueId);
        return file_exists($filePath) && unlink($filePath);
    }
}