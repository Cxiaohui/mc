<?php
/**
 *
 * User: XiaoHui
 * Date: 2016/10/10 16:03
 */
namespace extend;

class Pack{
    /**
     * 将数组按格式打包成二进制
     * @param mixed $dataArray
     * @param mixed $formatArray
     * @param mixed $outData
     * @return int
     */
    static public function arr_pack2bin($dataArray, $formatArray, &$outData) {
        $len = count($dataArray);
        if ($len != count($formatArray)) {
            return 1;
        }
        $outData = '';
        for ($i = 0; $i < $len; $i++) {
            $outData .= pack($formatArray[$i], $dataArray[$i]);
        }
        return 0;
    }

    /**
     * 将二进制数据包按格式解成数组
     * @param mixed $binData
     * @param mixed $dataFormat
     * @param mixed $outArray
     * @return int
     */
    static public function bin_pack2arr($binData, $dataFormat, &$outArray) {
        //php 5.5使用Z替换a来解包，解决多余\0的问题
        if (version_compare(PHP_VERSION, '5.5.0-dev', '>=')) {
            $format_arr = explode("/", $dataFormat);

            $dataFormat = "";
            foreach ($format_arr as $value) {
                if (!empty($value)) {
                    $value = preg_replace("/^a(\d+.*)$/", "Z\\1", $value);
                    $dataFormat .= $value.'/';
                }
            }
            $dataFormat = rtrim($dataFormat, "/");
        }

        $outArray = unpack($dataFormat, $binData);
        $formats = explode('/', $dataFormat);
        if (count($formats) != count($outArray)) {
            return 1;
        }
        return 0;
    }
}