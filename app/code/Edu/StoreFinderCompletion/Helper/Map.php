<?php
/**
 * @category    Edu
 * @package     Edu/StoreFinderCompletion
 * @author      Maxim Dzyuba <maxim.d@tdo.kz>

 */

namespace Edu\StoreFinderCompletion\Helper;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Edu\StoreFinderCompletion\Model\Store as StoreModel;

class Map
{
    const EARTH_RADIUS = 6371000;
    const KEY_LATITUDE = 'latitude';
    const KEY_LONGITUDE = 'longitude';
    const KEY_DISTANCE = 'distance';

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * Map constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnectionByName(ResourceConnection::DEFAULT_CONNECTION);
    }

    /**
     * @param $latitudeFrom
     * @param $longitudeFrom
     * @param $latitudeTo
     * @param $longitudeTo
     * @return float|int
     */
    public function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
        $angle = atan2(sqrt($a), $b);

        return $angle * self::EARTH_RADIUS;
    }

    /**
     * @return array
     */
    public function getStoreLocations()
    {
        $select = $this->connection->select();
        $storeTableName = $this->connection->getTableName(Data::TABLE_STORES);
        $select->from(
            $storeTableName,
            [StoreModel::COLUMN_STORE_ID, StoreModel::COLUMN_LATITUDE, StoreModel::COLUMN_LONGITUDE]
        );

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @param double $centerLat
     * @param double $centerLon
     * @param double $maxDistance
     * @return array
     */
    public function getCloseStoreLocations($centerLat, $centerLon, $maxDistance)
    {
        $stores = $this->getStoreLocations();
        $validStores = [];
        foreach ($stores as $key => $store) {
            if (!$this->isValidPoint($store)) {
                unset($stores[$key]);
                continue;
            }
            $distance = $this->getDistance(
                $centerLat,
                $centerLon,
                $store[StoreModel::COLUMN_LATITUDE],
                $store[StoreModel::COLUMN_LONGITUDE]
            );
            if ($distance <= $maxDistance) {
                $store[self::KEY_DISTANCE] = $distance;
                $validStores[$key] = $store;
            }
        }

        uasort($validStores, function ($a, $b) {
            if ($a[self::KEY_DISTANCE] == $b[self::KEY_DISTANCE]) {
                return 0;
            }

            return ($a[self::KEY_DISTANCE] < $b[self::KEY_DISTANCE]) ? -1 : 1;
        });

        return $validStores;
    }

    /**
     * @param float $maxLat
     * @param float $maxLng
     * @param float $minLat
     * @param float $minLng
     * @param float $centerLat
     * @param float $centerLon
     * @return array
     */
    public function getStoresWithinBounds($maxLat, $maxLng, $minLat, $minLng, $centerLat, $centerLon)
    {
        $stores = $this->getStoreLocations();
        $validStores = [];
        foreach ($stores as $key => $store) {
            if (!$this->isValidPoint($store)) {
                unset($stores[$key]);
                continue;
            }
            $storeLat = $store[StoreModel::COLUMN_LATITUDE];
            $storeLng = $store[StoreModel::COLUMN_LONGITUDE];

            if ($minLng > $maxLng) {
                $inside = ($storeLat >= $maxLat
                    && $storeLat <= $minLat
                    && $storeLng <= $maxLng
                    && $storeLng >= $minLng);
            } else {
                $inside = ($storeLat <= $maxLat
                    && $storeLat >= $minLat
                    && $storeLng <= $maxLng
                    && $storeLng >= $minLng);
            }

            if ($inside) {
                $store[self::KEY_DISTANCE] = $this->getDistance(
                    $centerLat,
                    $centerLon,
                    $storeLat,
                    $storeLng
                );
                $validStores[$key] = $store;
            }
        }

        uasort($validStores, function ($a, $b) {
            if ($a[self::KEY_DISTANCE] == $b[self::KEY_DISTANCE]) {
                return 0;
            }

            return ($a[self::KEY_DISTANCE] < $b[self::KEY_DISTANCE]) ? -1 : 1;
        });

        return $validStores;
    }

    /**
     * @param array $point
     * @return bool
     */
    protected function isValidPoint($point)
    {
        return (is_array($point)
            && isset($point[self::KEY_LATITUDE], $point[self::KEY_LONGITUDE])
            && is_numeric($point[self::KEY_LATITUDE])
            && is_numeric($point[self::KEY_LONGITUDE]));
    }
}
