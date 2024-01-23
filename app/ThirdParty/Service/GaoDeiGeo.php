<?php

namespace App\ThirdParty\Service;

use Illuminate\Support\Arr;

/**
 * 高德地图Api
 * [参考文档] https://lbs.amap.com/api/webservice/guide/api/search
 */
class GaoDeiGeo
{
    private $appKey;

    private static $geoUrl = 'https://restapi.amap.com/v3/geocode/geo?';

    private static $reGeoUrl = 'https://restapi.amap.com/v3/geocode/regeo?';

    private static $directionUrl = 'https://restapi.amap.com/v3/direction/walking?';

    private static $directionUrlV2 = 'https://restapi.amap.com/v5/direction/driving?';

    private static $ipUrl = 'https://restapi.amap.com/v3/ip?';

    private static $coordinateConvertUrl = 'https://restapi.amap.com/v3/assistant/coordinate/convert?';

    private static $weatherUrl = 'https://restapi.amap.com/v3/weather/weatherInfo?';

    private static $inputTipsUrl = 'https://restapi.amap.com/v3/assistant/inputtips?';

    private static $staticMapUrl = 'https://restapi.amap.com/v3/staticmap?';

    public static function getInstance() : self
    {
        return new self();
    }

    public function __construct()
    {
        $this->appKey = config('third.gaodei.app_key');
    }

    /**
     * 地理编码，地址转经纬度
     * @param string $address
     * @return false|mixed
     */
    public function getGeo(string $address)
    {
        try {
            $query = http_build_query([
                'key'     => $this->appKey,
                'address' => $address,
            ]);
            $url = self::$geoUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getGeo: fail to get address', ['address' => $address, 'result' => $result]);
                return false;
            } else{
                return $result['geocodes'][0]['location'] ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('getGeo: fail to get address', ['address' => $address, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * 逆地理编码，经纬度转地址
     * @param string $location (经度在前，纬度在后，经纬度间以分割，经纬度小数点后不要超过 6位) # 110.348820,20.019057
     * @return array|false
     */
    public function getLocation(string $location)
    {
        try {
            $query = http_build_query([
                'key'      => $this->appKey,
                'location' => $location,
            ]);
            $url = self::$reGeoUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getLocation: fail to get location', ['location' => $location, 'result' => $result]);
                return false;
            } else{
                return $result['regeocode']['addressComponent']
                    ? Arr::only($result['regeocode']['addressComponent'], ['province', 'city', 'district', 'adcode'])
                    : false;
            }
        } catch (\Exception $e) {
            logger()->info('getLocation: fail to get location', ['location' => $location, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * 路径规划
     * @param string $origin 110.348820,20.019057
     * @param string $destination 110.349820,20.019157
     * @return false|mixed
     */
    public function getDirection(string $origin, string $destination)
    {
        try {
            $query = http_build_query([
                'key'         => $this->appKey,
                'origin'      => $origin,
                'destination' => $destination,
            ]);
            $url = self::$directionUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getDirection: fail to get direction', ['origin' => $origin, 'destination' => $destination, 'result' => $result]);
                return false;
            } else{
                return $result['route'] ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('getDirection: fail to get direction', ['origin' => $origin, 'destination' => $destination, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * 路径规划V2
     * @param string $origin 110.348820,20.019057
     * @param string $destination 110.349820,20.019157
     * @return false|mixed
     */
    public function getDirectionV2(string $origin, string $destination)
    {
        try {
            $query = http_build_query([
                'key'         => $this->appKey,
                'origin'      => $origin,
                'destination' => $destination,
            ]);
            $url = self::$directionUrlV2 . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getDirectionV2: fail to get direction', ['origin' => $origin, 'destination' => $destination, 'result' => $result]);
                return false;
            } else{
                return $result['route'] ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('getDirectionV2: fail to get direction', ['origin' => $origin, 'destination' => $destination, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * @param string $ip
     * @return false|mixed
     */
    public function getIp(string $ip)
    {
        try {
            $query = http_build_query([
                'key'     => $this->appKey,
                'ip'      => $ip,
            ]);
            $url = self::$ipUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getIp: fail to get ip', ['ip' => $ip, 'result' => $result]);
                return false;
            } else{
                return $result ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('getIp: fail to get ip', ['ip' => $ip, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * 坐标转换
     * $coordsys 可选值：gps;mapbar;baidu;autonavi(不进行转换)
     * @param string $locations
     * @param string $coordsys
     * @return false|mixed
     */
    public function coordinateConvert(string $locations, string $coordsys = 'gps')
    {
        try {
            $query = http_build_query([
                'key'         => $this->appKey,
                'locations'   => $locations,
                'coordsys'    => $coordsys,
            ]);
            $url = self::$coordinateConvertUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('coordinateConvert: fail to get coordinateConvert', ['locations' => $locations, 'result' => $result]);
                return false;
            } else{
                return $result['locations'] ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('coordinateConvert: fail to get coordinateConvert', ['locations' => $locations, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * 获取天气
     * base/all  => 实况天气/预报天气
     * @param string $cityCode
     * @param string $extensions
     * @return false|mixed
     */
    public function getWeather(string $cityCode, string $extensions = 'base')
    {
        try {
            $query = http_build_query([
                'key'         => $this->appKey,
                'city'        => $cityCode,
                'extensions'  => 'all',
            ]);
            $url = self::$weatherUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getWeather: fail to get weather', ['cityCode' => $cityCode, 'result' => $result]);
                return false;
            } else{
                $extensions == 'base' && $res = $result['lives'];
                $extensions == 'all' && $res = $result['forecasts'];
                return $res ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('getWeather: fail to get weather', ['cityCode' => $cityCode, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * @param string $keywords [搜索关键字]
     * @param string $cityCode [城市编码]
     * @return false|mixed
     */
    public function getInputTips(string $keywords, string $cityCode)
    {
        try {
            $query = http_build_query([
                'key'         => $this->appKey,
                'keywords'    => $keywords,
                'city'        => $cityCode,
            ]);
            $url = self::$inputTipsUrl . $query;
            $content = file_get_contents($url, false);
            $result = json_decode($content, true);
            if($result['status'] == 0){
                logger()->info('getInputTips: fail to get inputTips', ['keywords' => $keywords, 'cityCode' => $cityCode, 'result' => $result]);
                return false;
            } else{
                return $result['tips'] ?? false;
            }
        } catch (\Exception $e) {
            logger()->info('getInputTips: fail to get inputTips', ['keywords' => $keywords, 'cityCode' => $cityCode, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }

    /**
     * 获取静态地图
     * @param string $location
     * @param string $zoom
     * @param string $size
     * @param string $markers
     * @return false|string
     */
    public function getStaticMap(string $location, string $zoom = '10', string $size = '750*300', string $markers = 'mid,0xFF0000,A:110.348820,20.019057')
    {
        try {
            $query = http_build_query([
                'key'         => $this->appKey,
                'location'    => $location,
                'zoom'        => $zoom,
                'size'        => $size,
                'markers'     => $markers,
            ]);
            $url = self::$staticMapUrl . $query;
            $result = file_get_contents($url, false);
            $path = 'map/' . md5($location . $zoom . $size . $markers) . '.png';
            file_put_contents(public_path($path), $result);
            return $path;
        } catch (\Exception $e) {
            logger()->info('getStaticMap: fail to get staticMap', ['location' => $location, 'zoom' => $zoom, 'size' => $size, 'markers' => $markers, 'message' => $e->getMessage(), 'code' => $e->getCode()]);
            return false;
        }
    }
}
