<?php
declare(strict_types=1);

namespace Location;

/**
 * Trait GetBoundsTrait
 *
 * @package Location
 *
 * @property Coordinate[] $points
 */
trait GetBoundsTrait
{
    /**
     * @return Bounds
     */
    public function getBounds(): Bounds
    {
        $latMin = 90.0;
        $latMax = -90.0;
        $lngMin = 180.0;
        $lngMax = -180.0;

        /** @var Coordinate $point */
        foreach ($this->getPoints() as $point) {
            $latMin = min($point->getLat(), $latMin);
            $lngMin = min($point->getLng(), $lngMin);
            $latMax = max($point->getLat(), $latMax);
            $lngMax = max($point->getLng(), $lngMax);
        }

        return new Bounds(
            new Coordinate($latMax, $lngMin),
            new Coordinate($latMin, $lngMax)
        );
    }
}
