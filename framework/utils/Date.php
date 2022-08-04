<?php

namespace Framework\Utils;

use \Exception;
use \DateTime;
use \DateTimeZone;
use \DateInterval;
use Framework\Utils\Str;

/**
 * Contains all date manipulation methods.
 * Working with dates has not been easy from experience. 
 * This class tries to give you all the flexibilities you need to enjoy working with dates again.
 * 
 * @author Akeem Aweda | akeem@aweklin.com | +2347085287169
 */
final class Date {

    // constants
    const FORMAT_DMY_HYPHEN_SEPARATED = 'd-m-Y';
    const FORMAT_DMY_SPACE_SEPARATED = 'd m Y';
    const FORMAT_DMY_HYPHEN_SEPARATED_WITH_TIME = 'd-m-Y H:i:s';
    const FORMAT_DMY_SPACE_SEPARATED_WITH_TIME = 'd m Y H:i:s';
    const FORMAT_YMD = 'Y-m-d';
    const FORMAT_MMM_YYYY_HYPHEN_SEPARATED = 'M-Y';
    const FORMAT_MMM_YY_HYPHEN_SEPARATED = 'M-y';
    const FORMAT_MMM_YYYY = 'M Y';
    const FORMAT_MMM_YY = 'M y';
    const FORMAT_YMD_WITH_TIME = 'Y-m-d H:i:s';
    const FORMAT_DDMMMYYYY_SPACE_SEPARATED = 'd M Y';
    const FORMAT_DDMMMYYYY_SPACE_SEPARATED_WITH_TIME = 'd M Y H:i:s';

    const INTERVALS = ['day', 'week', 'month', 'year'];

    // private fields
    private static $timeZone;    

    // private constants
    const NOW = 'now';

    /**
     * Sets a new timezone for the DateTime object
     * 
     * @param string $value The actual TimeZone value. Example: UTC
     * 
     * @return void
     */
    public static function setTimeZone(string $value) {
        self::$timeZone = new DateTimeZone($value);
    }

    /**
     * Returns the current date and time on the server.
     * 
     * @param $format Specifies the expected output format. This is set to date+time by default. You can use one of the class constants FORMAT_ to specify the expected date format.
     * 
     * @return string
     */
    public static function now(string $format = self::FORMAT_YMD_WITH_TIME): string {
        if (!self::_isFormatValid($format)) throw new Exception("{$format} is not a valid date/time format.");

        $today = new DateTime(self::NOW, self::$timeZone);
        $todayFormatted = $today->format($format);
        unset($today);

        return $todayFormatted;
    }

    /**
     * Returns the first day of a given date.
     * 
     * @param string $date The date to extract the working days from its month.
     * @param string $format Specifies the expected output format. This is set to date+time by default. You can use one of the class constants FORMAT_ to specify the expected date format.
     * 
     */
    public static function getFirstDayOf(string $date = 'now', string $format = self::FORMAT_YMD) : string {
        $dateObj = new DateTime($date);
        $dateObj->modify('first day of');
        $result = $dateObj->format($format);
        unset($dateObj);
        
        return $result;
    }

    public static function format(string $value, $format = self::FORMAT_YMD_WITH_TIME): string {
        if (Str::isEmpty($value)) return '';

        if (!self::_isValid($value)) throw new Exception("{$value} is not recognized as a valid date.");

        if (!self::_isFormatValid($format)) throw new Exception("{$format} is not a valid date/time format.");
        
        $date = new DateTime($value, self::$timeZone);
        $dateFormatted = $date->format($format);
        unset($date);

        return $dateFormatted;
    }

    /**
     * Returns the working dates of a given month. Essentially, all weekdays.
     * 
     * @param string $date The date to extract the working days from its month.
     * 
     * @return array
     */
    public static function getWorkingDaysDate($date): array {
        $workingDays = [];

        $dateFormatted = date_parse_from_format(self::FORMAT_YMD, $date);
        $month  = $dateFormatted['month'];
        $year   = $dateFormatted['year'];
        $daysCount = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($i = 1; $i <= $daysCount; $i++) {
            $currentDate    = "{$year}/{$month}/{$i}";
            $fullDayName    = date('l', strtotime($currentDate));
            $shortDayName   = mb_substr($fullDayName, 0, 3);

            if (!in_array(ucfirst($shortDayName), ['Sat', 'Sun'])) {
                array_push($workingDays, $currentDate);
            }
        }

        return $workingDays;
    }

    /**
     * Returns the working dates between the given dates. Essentially, all weekdays.
     * 
     * @param string $startDate Specifies the start date.
     * @param string $endDate Specifies the end date.
     * 
     * @return array
     */
    public static function getWorkingDaysDateRange($startDate, $endDate): array {
        $workingDays = [];

        foreach(self::generateDatesBetween($startDate, $endDate) as $currentDate) {
            $fullDayName    = date('l', strtotime($currentDate));
            $shortDayName   = mb_substr($fullDayName, 0, 3);

            if (!in_array(ucfirst($shortDayName), ['Sat', 'Sun'])) {
                array_push($workingDays, $currentDate);
            }
        }

        return $workingDays;
    }

    /**
     * Generates array of dates between the start date and end date specified using the step and format specified.
     * 
     * @param string $startDate Specifies the date to start from.
     * @param string $endDate Specifies the date to stop.
     * @param string $step Specifies the incremental interval to use. Format: "+n interval". This must start with +, followed by a space character then day/week/month/year specified in the INTERVALS constant. Specifying quarter as interval will result in StackOverflowException.
     * @param string $format Indicates the output format.
     */
    public static function generateDatesBetween($startDate, $endDate, $step = '+1 day', $format = self::FORMAT_YMD): array {
        if (!self::_isValid($startDate)) throw new \Exception("{$startDate} is not recognized as a valid date.");
        if (!self::_isValid($endDate)) throw new \Exception("{$endDate} is not recognized as a valid date.");

        // confirm the step is well formed
        if (Str::isEmpty($step)) throw new \Exception("Parameter step cannot be empty.");
        if (\mb_strlen(Str::removeSpaces($step)) < 5) throw new \Exception("Parameter step is expected to have minimum of 6 characters as specified in the format given in the doc.");
        if (!Str::startsWith($step, '+')) throw new \Exception("Step must start with the + sign.");
        if (!Str::contains($step, ' ')) throw new \Exception("Step parameter must contain a space as specified in the format given in the doc.");
        $secondCharacterInStep = \mb_substr($step, 1, 1);
        if (!\is_numeric($secondCharacterInStep)) throw new \Exception("The second character of the step parameter must be a number.");
        $stepArray = \explode(' ', $step, 2);
        if (!\in_array($stepArray[1], self::INTERVALS)) throw new \Exception("Step interval must be one of the following: " . \join(', ', self::INTERVALS) . '.');

        if (!self::_isFormatValid($format)) throw new Exception("{$format} is not a valid date/time format.");

        $dates = [];
        $currentDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        while ($currentDate <= $endDate) {
            array_push($dates, date($format, $currentDate));
            $currentDate = strtotime($step, $currentDate);
        }

        return $dates;
    }

    /**
     * Calculates and returns the no of days between the specified start and end dates.
     * 
     * @param string $startDate Specifies the start date.
     * @param string $endDate Specifies the end date.
     * 
     * @return int
     */
    public static function getDayDifference($startDate, $endDate): int {
        if (!self::_isValid($startDate)) throw new Exception("{$startDate} is not recognized as a valid date.");
        if (!self::_isValid($endDate)) throw new Exception("{$endDate} is not recognized as a valid date.");

        $startDate  = new DateTime($startDate, self::$timeZone);
        $endDate    = new DateTime($endDate, self::$timeZone);
        $interval   = $endDate->diff($startDate)->days;

        return $interval;
    }

    /**
     * Calculates and returns the no of months between the specified start and end dates.
     * 
     * @param string $startDate Specifies the start date.
     * @param string $endDate Specifies the end date.
     * 
     * @return int
     */
    public static function getMonthDifference($startDate, $endDate): int {
        if (!self::_isValid($startDate)) throw new Exception("{$startDate} is not recognized as a valid date.");
        if (!self::_isValid($endDate)) throw new Exception("{$endDate} is not recognized as a valid date.");

        $startDate  = new DateTime($startDate);
        $endDate    = new DateTime($endDate);
        $interval   = $endDate->diff($startDate)->format('%m');

        return $interval;
    }

    /**
     * Calculates and returns the no of years between the specified start and end dates.
     * 
     * @param string $startDate Specifies the start date.
     * @param string $endDate Specifies the end date.
     * 
     * @return int
     */
    public static function getYearDifference($startDate, $endDate): int {
        if (!self::_isValid($startDate)) throw new Exception("{$startDate} is not recognized as a valid date.");
        if (!self::_isValid($endDate)) throw new Exception("{$endDate} is not recognized as a valid date.");

        $startDate  = new DateTime($startDate);
        $endDate    = new DateTime($endDate);
        $interval   = $endDate->diff($startDate)->format('%y');

        return $interval;
    }

    public static function addDay($date = self::NOW, $days = 1, $format = self::FORMAT_YMD_WITH_TIME) {
        return self::_addDate($date, 'D', $days, $format);
    }

    public static function addMonth($date = self::NOW, $months = 1, $format = self::FORMAT_YMD_WITH_TIME) {
        return self::_addDate($date, 'M', $months, $format);
    }

    public static function addYear($date = self::NOW, $years = 1, $format = self::FORMAT_YMD_WITH_TIME) {
        return self::_addDate($date, 'Y', $years, $format);
    }

    private function _addDate($value = self::NOW, $datePart = 'D', $interval = 1, $format = self::FORMAT_YMD_WITH_TIME) {
        if (Str::isEmpty($value)) return '';

        if (!self::_isValid($value)) throw new Exception("{$value} is not recognized as a valid date.");

        if (!self::_isFormatValid($format)) throw new Exception("{$format} is not a valid date/time format.");
        
        $date = new DateTime($value, self::$timeZone);
        $date->add(new DateInterval("P{$interval}{$datePart}"));    //TODO:: fix bad format exception thrown when negative value is supplied for the interval parameter.
        $dateFormatted = $date->format($format);
        unset($date);

        return $dateFormatted;
    }

    /**
     * Checks if the format specified is valid for datetime.
     * 
     * @param string $format Expects to be one of the constands FORMAT_...
     * 
     * @return boolean
     */
    private static function _isFormatValid($format): bool {
        return (in_array($format, [
            self::FORMAT_DMY_HYPHEN_SEPARATED,
            self::FORMAT_DMY_SPACE_SEPARATED,
            self::FORMAT_DMY_HYPHEN_SEPARATED_WITH_TIME,
            self::FORMAT_DMY_SPACE_SEPARATED_WITH_TIME,
            self::FORMAT_YMD,
            self::FORMAT_MMM_YYYY,
            self::FORMAT_MMM_YY,
            self::FORMAT_MMM_YYYY_HYPHEN_SEPARATED,
            self::FORMAT_MMM_YY_HYPHEN_SEPARATED,
            self::FORMAT_YMD_WITH_TIME,
            self::FORMAT_DDMMMYYYY_SPACE_SEPARATED,
            self::FORMAT_DDMMMYYYY_SPACE_SEPARATED_WITH_TIME
        ]));
    }

    /**
     * Checks if the specified value is valid datetime.
     * 
     * @param string $date A date/time value.
     * 
     * @return boolean
     */
    private static function _isValid($date): bool {
        try {
            if (Str::isEmpty($date)) return false;
            new DateTime($date, self::$timeZone);   //TODO:: refine this to ensure an actual invalid date is recognized as invalid
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}