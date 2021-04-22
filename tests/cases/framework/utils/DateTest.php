<?php

declare(strict_types=1);

include_once realpath('.' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;
use Framework\Utils\Date;

class DateTest extends TestCase {

    public function testToday() {
        Date::setTimeZone('GMT+2'); // this may fail, based on the TimeZone of the user, you may want to change the TimeZone to pass all test in this method
        self::assertNotEquals('', Date::now(Date::FORMAT_YMD));
        self::assertEquals(date('Y-m-d'), Date::now(Date::FORMAT_YMD));
        self::assertNotEquals('2020-08-22', Date::now(Date::FORMAT_YMD));
        self::assertEquals(date('Y-m-d H:i:s'), Date::now(Date::FORMAT_YMD_WITH_TIME));
        self::assertEquals(date('d-m-Y'), Date::format(Date::NOW, Date::FORMAT_DMY_HYPHEN_SEPARATED));
    }

    public function testWorkingDays() {
        self::assertSame(['2020-08-24'], Date::getWorkingDaysDateRange('2020-08-22', '2020-08-24'));
        self::assertNotSame(['2020-08-24', '2020-08-25'], Date::getWorkingDaysDateRange('2020-08-22', '2020-08-24'));
    }

    public function testDateRange() {
        //TODO:: find a way to assert exception
        //self::expectExceptionMessage("Step must start with the + sign.", Date::generateDatesBetween('2020-08-23', '2020-08-21', '-1 day'));
        //self::assertSame(['2020-08-20', '2020-08-27'], Date::generateDatesBetween('2020-08-20', '2020-08-29', '+7day'));
        //self::assertSame(['2020-08-20', '2020-08-27'], Date::generateDatesBetween('2020-08-20', '2020-08-29', '+a day'));
        //self::assertSame(['2020-08-20', '2020-08-27'], Date::generateDatesBetween('2020-08-20', '2020-08-29', '+7 quarter'));
        self::assertSame(['2020-08-20', '2020-08-27'], Date::generateDatesBetween('2020-08-20', '2020-08-29', '+7 day'));
        self::assertSame(['2020-08-20', '2020-08-27'], Date::generateDatesBetween('2020-08-20', '2020-08-29', '+1 week'));
        self::assertSame(['2020-08-20', '2020-09-20'], Date::generateDatesBetween('2020-08-20', '2020-09-29', '+1 month'));
        self::assertNotSame(['2020-08-20', '2020-09-20'], Date::generateDatesBetween('2020-08-20', '2020-09-29', '+30 day'));
        self::assertSame(['2020-08-20', '2020-09-19'], Date::generateDatesBetween('2020-08-20', '2020-09-29', '+30 day'));
        self::assertNotSame(['2020-01-01', '2021-03-31'], Date::generateDatesBetween('2020-01-01', '2021-03-31', '+1 year'));
        self::assertSame(['2020-01-01', '2021-01-01'], Date::generateDatesBetween('2020-01-01', '2021-03-31', '+1 year'));
    }

    public function testDateDifference() {
        // day
        self::assertSame(0, Date::getDayDifference('2020-08-24', '2020-08-24'));
        self::assertNotSame(2, Date::getDayDifference('2020-08-24', '2020-08-25'));
        self::assertSame(3, Date::getDayDifference('2020-08-24', '2020-08-27'));
        self::assertSame(365, Date::getDayDifference('2020-01-01', '2020-12-31'));

        // month
        self::assertSame(1, Date::getMonthDifference('2020-07-01', '2020-07-31'));
        self::assertSame(2, Date::getMonthDifference('2020-07-01', '2020-08-31'));
        self::assertNotSame(12, Date::getMonthDifference('2020-01-01', '2020-12-31'));

        // year
        self::assertNotSame(1, Date::getYearDifference('2020-08-24', '2020-08-25'));
        self::assertNotSame(1, Date::getYearDifference('2020-01-01', '2020-12-31'));
        self::assertSame(1, Date::getYearDifference('2020-01-01', '2021-01-31'));
    }

    public function testDateAdd() {
        self::assertNotSame('2020-01-02', Date::addDay('2020-01-01', 1));
        self::assertSame('2020-01-02', Date::addDay('2020-01-01', 1, Date::FORMAT_YMD));
        self::assertSame('2020-01-01', Date::addDay('2020-01-01', 0, Date::FORMAT_YMD));
    }

}