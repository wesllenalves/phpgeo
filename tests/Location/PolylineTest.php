<?php
declare(strict_types=1);

namespace Location;

use Location\Distance\Vincenty;
use PHPUnit\Framework\TestCase;

class PolylineTest extends TestCase
{
    /**
     * @var \Location\Polyline
     */
    protected $polyline;

    public function setUp()
    {
        $this->polyline = new Polyline();
        $this->polyline->addPoint(new Coordinate(52.5, 13.5));
        $this->polyline->addPoint(new Coordinate(64.1, -21.9));
        $this->polyline->addPoint(new Coordinate(40.7, -74.0));
        $this->polyline->addPoint(new Coordinate(33.9, -118.4));
    }

    public function testCreatePolyline()
    {
        $this->assertCount(4, $this->polyline->getPoints());
    }

    public function testGetSegments()
    {
        $segments = $this->polyline->getSegments();

        $this->assertEquals(new Line(new Coordinate(52.5, 13.5), new Coordinate(64.1, -21.9)), $segments[0]);
        $this->assertEquals(new Line(new Coordinate(64.1, -21.9), new Coordinate(40.7, -74.0)), $segments[1]);
        $this->assertEquals(new Line(new Coordinate(40.7, -74.0), new Coordinate(33.9, -118.4)), $segments[2]);
    }

    public function testGetSegmentsForOnlyOnePointInLineWorksAsExpected()
    {
        $polyline = new Polyline();
        $polyline->addPoint(new Coordinate(52.5, 13.5));

        $this->assertEquals([], $polyline->getSegments());
    }

    public function testGetLength()
    {
        $this->assertEquals(10576798.9, $this->polyline->getLength(new Vincenty()), '', 0.1);
    }

    public function testGetReverseWorksAsExpected()
    {
        $reversed = $this->polyline->getReverse();

        $expected = new Polyline();
        $expected->addPoint(new Coordinate(33.9, -118.4));
        $expected->addPoint(new Coordinate(40.7, -74.0));
        $expected->addPoint(new Coordinate(64.1, -21.9));
        $expected->addPoint(new Coordinate(52.5, 13.5));

        $this->assertEquals($expected, $reversed);
    }

    public function testReverseTwiceWorksAsExpected()
    {
        $doubleReversed = $this->polyline->getReverse()->getReverse();

        $this->assertEquals($this->polyline, $doubleReversed);
    }

    public function testGetBoundsWorksAsExpected()
    {
        $expected = new Bounds(new Coordinate(64.1, -118.4), new Coordinate(33.9, 13.5));

        $this->assertEquals($expected, $this->polyline->getBounds());
    }

    public function testAddUniquePointWorksAsExpeted()
    {
        $expected = $this->polyline;
        $unique = new Polyline();

        // Pass 1
        $unique->addUniquePoint(new Coordinate(52.5, 13.5));
        $unique->addUniquePoint(new Coordinate(64.1, -21.9));
        $unique->addUniquePoint(new Coordinate(40.7, -74.0));
        $unique->addUniquePoint(new Coordinate(33.9, -118.4));

        // Pass 2
        $unique->addUniquePoint(new Coordinate(52.5, 13.5));
        $unique->addUniquePoint(new Coordinate(64.1, -21.9));
        $unique->addUniquePoint(new Coordinate(40.7, -74.0));
        $unique->addUniquePoint(new Coordinate(33.9, -118.4));

        $this->assertEquals($unique, $expected);
    }

    public function testAddUniquePointWithAllowedDistanceZero()
    {
        $expected = $this->polyline;
        $actual   = clone $expected;

        $actual->addUniquePoint(new Coordinate(33.9, -118.4), .0);

        $this->assertEquals($expected, $actual);

        $expected->addPoint(new Coordinate(33.90001, -118.40001));
        $actual->addUniquePoint(new Coordinate(33.90001, -118.40001), .0);

        $this->assertEquals($expected, $actual);
    }

    public function testAddUniquePointWithAllowedDistance()
    {
        $expected = $this->polyline;
        $actual = clone $expected;

        $actual->addUniquePoint(new Coordinate(33.90000001, -118.40000001), .001);

        $this->assertEquals($expected, $actual);

        $expected = $this->polyline;
        $actual = clone $expected;

        $actual->addUniquePoint(new Coordinate(33.900001, -118.400001), 1);

        $this->assertEquals($expected, $actual);
    }

    public function testGetMiddlePointWorksAsExpected()
    {
        $middle = $this->polyline->getMiddlePoint();

        $this->assertEquals($middle, new Coordinate(47.8, -50.2));
    }

    public function testGetMiddlePointWithNoPoints()
    {
        $polyline = new Polyline();

        $middle = $polyline->getMiddlePoint();

        $this->assertEquals($middle, null);
    }
}
