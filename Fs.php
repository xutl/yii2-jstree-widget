<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\jstree;

use Yii;
use yii\base\Object;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

/**
 * Class Fs
 * @package xutl\jstree\helpers
 */
class Fs extends Object
{
    /**
     * @var null|string 基础文件路径
     */
    public $base = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->base = FileHelper::normalizePath($this->base);
        if (!$this->base) {
            throw new \Exception('Base directory does not exist');
        }
    }

    /**
     * 格式化路径
     * @param string $id
     * @return string
     * @throws \Exception
     */
    protected function path($id)
    {
        return FileHelper::normalizePath($this->base . DIRECTORY_SEPARATOR . $id);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function id($path)
    {
        $path = FileHelper::normalizePath($path);
        //替换成相对路径
        $path = substr($path, strlen($this->base));
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        return strlen($path) ? trim($path, '/') : '/';
    }

    /**
     * 获取字符串编码
     * @param string $string 待检查的字符串。
     * @return mixed
     */
    protected function detectEncoding($string)
    {
        return mb_detect_encoding($string, mb_detect_order(['GB18030', 'CP936', 'BIG-5', 'ISO-8859-1', 'UTF-8']), true);
    }

    /**
     * 转换到UTF-8
     * @param string $string
     * @return mixed
     */
    protected function toUtf8($string)
    {
        return mb_convert_encoding($string, 'UTF-8', $this->detectEncoding($string));
    }

    /**
     * 使用RecursiveDirectoryIterator遍历文件
     * @param string $directory 指定了目录的RecursiveDirectoryIterator实例
     * @param boolean $withRoot 是否是跟目录
     * @return array $files 文件列表
     */
    public function getList($directory, $withRoot = false)
    {
        if (!$directory instanceof \RecursiveDirectoryIterator) {
            $directory = new \RecursiveDirectoryIterator($directory);
        }
        $files = [];
        for (; $directory->valid(); $directory->next()) {
            if ($directory->isDir() && !$directory->isDot()) {
                if ($directory->haschildren()) {
                    $files[] = [
                        'text' => $this->toUtf8($directory->getFilename()),
                        'children' => true,
                        'id' => $this->toUtf8($this->id($directory->getPathName())),
                        'icon' => 'folder',
                    ];
                };
            } else if ($directory->isFile()) {
                $files[] = [
                    'text' => $this->toUtf8($directory->getFilename()),
                    'children' => false,
                    'id' => $this->toUtf8($this->id($directory->getPathName())),
                    'type' => 'file',
                    'icon' => 'file file-' . $directory->getExtension(),
                ];
            }
        }
        if ($withRoot && $node === '/') {
            $files = [
                ['text' => basename($this->base), 'children' => $files, 'id' => '/', 'icon' => 'folder',
                    'state' => ['opened' => true, 'disabled' => true]]
            ];
        }
        return $files;
    }

    /**
     * 获取文件内容
     * @param string $id
     * @return array
     * @throws Exception
     */
    public function getContent($id)
    {
        if (strpos($id, ":")) {
            $id = array_map([$this, 'id'], explode(':', $id));
            return ['type' => 'multiple', 'content' => 'Multiple selected: ' . implode(' ', $id)];
        }
        $dir = $this->path($id);
        if (is_dir($dir)) {
            return ['type' => 'folder', 'content' => $id];
        }
        if (is_file($dir)) {
            $ext = strpos($dir, '.') !== false ? substr($dir, strrpos($dir, '.') + 1) : '';
            $dat = ['type' => $ext, 'content' => ''];
            switch ($ext) {
                case 'txt':
                case 'text':
                case 'md':
                case 'js':
                case 'json':
                case 'css':
                case 'html':
                case 'htm':
                case 'xml':
                case 'c':
                case 'cpp':
                case 'h':
                case 'sql':
                case 'log':
                case 'py':
                case 'rb':
                case 'htaccess':
                case 'php':
                    $dat['content'] = file_get_contents($dir);
                    break;
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                case 'bmp':
                    $dat['content'] = 'data:' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir) . ';base64,' . base64_encode(file_get_contents($dir));
                    break;
                default:
                    $dat['content'] = 'File not recognized: ' . $this->id($dir);
                    break;
            }
            return $dat;
        }
        throw new Exception('Not a valid selection: ' . $dir);
    }
}