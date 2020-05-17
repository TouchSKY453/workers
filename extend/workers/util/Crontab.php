<?php
namespace workers\util;

class Crontab {
    public static $error;
    private $preg_second = "/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i";
    private $preg_minute = "/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i";
    /**
     * 解析crontab的定时格式
     * @param string $crontab :
     *
     *    0    1    2    3    4    5
     *    *    *    *    *    *    *
     *    -    -    -    -    -    -
     *    |    |    |    |    |    |
     *    |    |    |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *    |    |    |    |    +----- month (1 - 12)
     *    |    |    |    +------- day of month (1 - 31)
     *    |    |    +--------- hour (0 - 23)
     *    |    +----------- min (0 - 59)
     *    +------------- sec (0-59)
     * @param int $time 时间戳
     * @return bool
     */
    public static function check($crontab, $time = null) {
        if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',
            trim($crontab))
        ) {
            if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i',
                trim($crontab))
            ) {
                self::$error = "Invalid cron string: " . $crontab;
                return false;
            }
        }
        $crontab = preg_split("/[\s]+/i", trim($crontab));
        $time    = $time ?: time();

        if (count($crontab) == 6) {
            $date = array(
                'second'  => (empty($crontab[0])) ? array(1 => 1) : self::parse_crontab_number($crontab[0], 1, 59),
                'minutes' => self::parse_crontab_number($crontab[1], 0, 59),
                'hours'   => self::parse_crontab_number($crontab[2], 0, 23),
                'day'     => self::parse_crontab_number($crontab[3], 1, 31),
                'month'   => self::parse_crontab_number($crontab[4], 1, 12),
                'week'    => self::parse_crontab_number($crontab[5], 0, 6),
            );
        } elseif (count($crontab) == 5) {
            $date = array(
                'second'  => array(1 => 1),
                'minutes' => self::parse_crontab_number($crontab[0], 0, 59),
                'hours'   => self::parse_crontab_number($crontab[1], 0, 23),
                'day'     => self::parse_crontab_number($crontab[2], 1, 31),
                'month'   => self::parse_crontab_number($crontab[3], 1, 12),
                'week'    => self::parse_crontab_number($crontab[4], 0, 6),
            );
        }

        if (
            in_array(intval(date('s', $time)), $date['second']) &&
            in_array(intval(date('i', $time)), $date['minutes']) &&
            in_array(intval(date('G', $time)), $date['hours']) &&
            in_array(intval(date('j', $time)), $date['day']) &&
            in_array(intval(date('w', $time)), $date['week']) &&
            in_array(intval(date('n', $time)), $date['month'])
        ) {
            return true;
        }
        return false;
    }
    /**
     * 解析单个配置的含义
     * @param $value
     * @param $min
     * @param $max
     * @return array
     */
    protected static function parse_crontab_number($value, $min, $max) {
        $result = array();
        $v1 = explode(",", $value);
        foreach ($v1 as $v2) {
            $v3 = explode("/", $v2);
            $step = empty($v3[1]) ? 1 : $v3[1];
            $v4 = explode("-", $v3[0]);
            $_min = count($v4) == 2 ? $v4[0] : ($v3[0] == "*" ? $min : $v3[0]);
            $_max = count($v4) == 2 ? $v4[1] : ($v3[0] == "*" ? $max : $v3[0]);
            for ($i = $_min; $i <= $_max; $i += $step) {
                if (intval($i) < $min) {
                    $result[$min] = $min;
                } elseif (intval($i) > $max) {
                    $result[$max] = $max;
                } else {
                    $result[$i] = intval($i);
                }
            }
        }
        ksort($result);
        return $result;
    }
}
?>