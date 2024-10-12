<?php

//TODO Mond

class ASTROGEN{

    // Julian Date Functions

    /**
     * Computes the Julian Date for the current time.
     * @return float Current Julian Date
     */
    public static function oldJulianDay():float{
       
        
        $date = new DateTime();
        return ASTROGEN::oldJulianDayFromTimestamp($date->getTimestamp());
    }

    /**
     * Computes the Julian Date for the given timestamp.
     * @param int $timestamp Timestamp the Julian Date is to compute for
     * @return float Julian Date for the given timestamp
     */
    public static function oldJulianDayFromTimestamp(int $timestamp){

        $dy = 0;
        $dm = 0;
        if (idate('m', $timestamp) <= 2) {
            $dy = -1;
            $dm = +12;
        }
        $jd = floor(365.25 * (idate('Y', $timestamp) + $dy + 4716)) +
            floor(30.6001 * (idate('m', $timestamp) + $dm + 1)) +
            idate('d', $timestamp) +
            $timestamp / 86400 - floor($timestamp / 86400) -
            1524.5;

        if ($jd >= 2299160) {
            $jd += (2 - floor(idate('Y', $timestamp) / 100) + floor(floor(idate('Y', $timestamp) / 100) / 4));
        }

        return $jd;

    }

    /**
     * Summary of JulianDayFromDateTime
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return float
     */
    public static function oldJulianDayFromDateTime(int $year, int $month, int $day, int $hour = 0, int $minute = 0, int $second = 0){
       $date = mktime($hour, $minute, $second , $month, $day, $year);
       return ASTROGEN::oldJulianDayFromTimestamp($date);
    }

    public static function JulianDay(int $year, int $month, int $day, int $hour = 0, int $minute = 0, float $second = 0.0, float $dut1 = 0.0, float $tz = 0.0)
    {
        $day_decimal = $day + ($hour - $tz + ($minute + ($second + $dut1) / 60.0) / 60.0) / 24.0;

        if ($month < 3) {
            $month += 12;
            $year--;
        }

        $julian_day = intval(365.25 * ($year + 4716.0)) + intval(30.6001 * ($month + 1)) + $day_decimal - 1524.5;

        if ($julian_day > 2299160.0) {
            $a = intval($year / 100);
            $julian_day += (2 - $a + intval($a / 4));
        }

        return $julian_day;
    }
    
    public static function JulianDayFromTimestamp(int $timestamp, float $dut1 = 0.0, float $tz = 0.0){
        return JulianDay(idate('Y', $timestamp), idate('m', $timestamp), idate('d', $timestamp), idate('H', $timestamp), idate('i', $timestamp), idate('s', $timestamp), $dut1, $tz);
    }

    /**
     * Summary of JulianCentury
     * @param float $julianDay
     * @return float
     */
    public static function JulianCentury(float $julianDay){
        return ($julianDay - 2451545.0) / 36525.0;
    }

    /**
     * Summary of JulianMillennium
     * @param float $julianCentury
     * @return float
     */
    public static function JulianMillennium(float $julianCentury)
    {
        return ($julianCentury) / 10.0;
    }

    /**
     * Summary of JDE
     * @param float $julianDay
     * @param mixed $deltaT
     * @return float
     */
    public static function JDE(float $julianDay, $deltaT){
        return $julianDay + $deltaT / 86400;
    }

}

class JulianDay{
    private float $jd = 0;
    private float $dut1 = 0;
    private float $deltaT = 0;
    
    function __construct($deltaT = 0, float $dut1 = 0, int $timestamp = null)
    {
        if(is_null($timestamp)){
            $date = new DateTime();
            $timestamp = $date->getTimestamp();
        }
        $this->jd = JulianDay(idate('Y', $timestamp), idate('m', $timestamp), idate('d', $timestamp), idate('H', $timestamp), idate('i', $timestamp), idate('s', $timestamp));
    }

    public function get_JD(): float
    {
        return $this->jd;
    }

    public function get_JC(): float
    {
        return $this->DayToCent($this->get_JD());
    }

    public function get_JM(): float
    {
        return $this->CentToMill($this->get_JC());
    }

    public function get_JDE(): float
    {
        return $this->get_JD() + $this->deltaT / 86400.0;
    }

    public function get_JCE(): float
    {
        return $this->DayToCent($this->get_JDE());
    }

    public function get_JME(): float
    {
        return $this->CentToMill($this->get_JCE());
    }

    private function DayToCent(float $d):float{
        return ($d - 2451545.0) / 36525.0;
    }

    private function CentToMill(float $c):float
    {
        return $c / 10.0;
    }

    private function JulianDay(int $year, int $month, int $day, int $hour = 0, int $minute = 0, float $second = 0.0):float
    {
        $day_decimal = $day + ($hour + ($minute + ($second + $this->dut1) / 60.0) / 60.0) / 24.0;

        if ($month < 3) {
            $month += 12;
            $year--;
        }

        $julian_day = intval(365.25 * ($year + 4716.0)) + intval(30.6001 * ($month + 1)) + $day_decimal - 1524.5;

        if ($julian_day > 2299160.0) {
            $a = intval($year / 100);
            $julian_day += (2 - $a + intval($a / 4));
        }

        return $julian_day;
    }
}

class Sun{

    /**
     * Astronomical unit (mean distance earth - sun) in m
     */
    const AU = 149597870700;
    const radius = 0.26667;

    function __construct($name)
    {
        $this->name = $name;
    }



}

class ASTROSUN{
    /**
     * Astronomical unit (mean distance earth - sun) in m
     */
    const AU = 149597870700;
    const sun_radius = 0.26667;
    const l_count = 6;
    const b_count = 2;
    const r_count = 5;
    const y_count = 63;
    const l_subcount = array(64,34,20,7,3,1);
    const b_subcount = array(5,2);
    const r_subcount = array(40,10,6,2,1);

    //
    public static function SunriseSunsetTransit(int $year, int $month, int $day, int $deltaT, int $lat, int $long, int $angleOfSun):array
    {
        $timestamp_zero_ut = gmmktime(0, 0, 0, $month, $day, $year);

        $JD_ZERO_UT = ASTROGEN::oldJulianDayFromTimestamp($timestamp_zero_ut);
        $JC_ZERO_UT = ASTROGEN::JulianCentury($JD_ZERO_UT);
        $JM_ZERO_UT = ASTROGEN::JulianMillennium($JC_ZERO_UT);

        $JD_ZERO_TT = ASTROGEN::oldJulianDayFromTimestamp($timestamp_zero_ut + $deltaT);
        $JC_ZERO_TT = ASTROGEN::oldJulianDay($JD_ZERO_TT);
        $JM_ZERO_TT = ASTROGEN::JulianMillennium($JC_ZERO_TT);

        $vu = ASTROSUN::v($JD_ZERO_UT);
        $a = array();
        $d = array();
        $m = array();
        $v = array();
        $n = array();

        for($i = -1; $i <= 1; $i++){
            $a[$i] = ASTROSUN::a($JD_ZERO_TT + $i);
            $d[$i] = ASTROSUN::DeclinationOfSun($JD_ZERO_TT + $i);
        }

        $m[0] = ($a[0] - $long - $vu) / 360;

        $arg = (sin(deg2rad($angleOfSun)) - sin(deg2rad($lat)) * sin(deg2rad($d[0]))) / (cos(deg2rad($lat)) * cos(deg2rad($d[0])));
        $H0 = -99999;
        if (abs($arg) <= 1) {
            $H0 = ASTROMISC::LimitToDesigDeg(rad2deg(acos($arg)), 180);
        }
        

        $m[1] = $m[0] - $H0 / 360;
        $m[2] = $m[0] + $H0 / 360;

        $m[0] = ASTROMISC::LimitZeroToOne($m[0]);
        $m[1] = ASTROMISC::LimitZeroToOne($m[1]);
        $m[2] = ASTROMISC::LimitZeroToOne($m[2]);

        for ($i = 0; $i <= 2; $i++) {
            $v[$i] = $vu + 360.985647 * $m[$i];
        }

        for ($i = 0; $i <= 2; $i++) {
            $n[$i] = $m[$i] + $deltaT / 86400;
        }

        $aa = $a[0] - $a[-1];
        if (abs($aa) > 2 ) {
            $aa = ASTROMISC::LimitZeroToOne($aa);
        }
        $bb = $a[1] - $a[0];
        if (abs($bb) > 2) {
            $bb = ASTROMISC::LimitZeroToOne($bb);
        }
        $cc = $bb - $aa;

        $alphasi = array();
        for ($i = 0; $i <= 2; $i++) {
            $alphasi[$i] = $a[0] + $n[$i] * ($aa + $bb + $cc * $n[$i]) / 2;
        }

        $as = $d[0] - $d[-1];
        if ($as > 2) {
            $as = ASTROMISC::LimitZeroToOne($as);
        }
        $bs = $d[1] - $d[0];
        if ($bs > 2) {
            $bs = ASTROMISC::LimitZeroToOne($bs);
        }
        $cs = $bs - $as;
        $deltasi = array();
        for ($i = 0; $i <= 2; $i++) {
            $deltasi[$i] = $d[0] + $n[$i] * ($as + $bs + $cs * $n[$i]) / 2;
        }

        $Hs = array();
        for ($i = 0; $i <= 2; $i++) {
            $Hs[$i] = $v[$i] + $long - $alphasi[$i];

            $Hs[$i] = $Hs[$i] / abs($Hs[$i]) * ASTROMISC::LimitToDesigDeg(abs($Hs[$i]), 360);
            if (abs($Hs[$i]) > 180) {
                $Hs[$i] = $Hs[$i] - $Hs[$i] / abs($Hs[$i]) * 360;
            }
        }

        $hh = array();
        for ($i = 0; $i <= 2; $i++) {
            $hh[$i] = rad2deg(asin(
                sin(deg2rad($lat)) * sin(deg2rad($deltasi[$i])) +
                cos(deg2rad($lat)) * cos(deg2rad($deltasi[$i])) * cos(deg2rad($Hs[$i]))
                )
            );
        }

        $t = $m[0] - $Hs[0] / 360;

        $R = $m[1] + ($hh[1] - ($angleOfSun)) / (360 * cos(deg2rad($deltasi[1])) * cos(deg2rad($lat)) * sin(deg2rad($Hs[1])));
        $S = $m[2] + ($hh[2] - ($angleOfSun)) / (360 * cos(deg2rad($deltasi[2])) * cos(deg2rad($lat)) * sin(deg2rad($Hs[2])));

        $values = array(
            "R" => $timestamp_zero_ut + floor($R * 24 * 60 * 60),
            "T" => $timestamp_zero_ut + floor($t * 24 * 60 * 60),
            "S" => $timestamp_zero_ut + floor($S * 24 * 60 * 60)
            );

        return $values;
    }

    public static function nextEl($timestamp, $deltaT, $lat, $long, $angleOfSun, $elem){
        $sr = -1;
        $nsr = -1;
        $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $timestamp), idate('m', $timestamp), idate('d', $timestamp), $deltaT, $lat, $long, $angleOfSun)[$elem];

        if ($sr > $timestamp) {
            $nsr = $sr;
        } elseif ($sr > 0) {
            for ($i = 1; $i < 366; $i++) {
                $t = $timestamp + $i * 86400;
                $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $t), idate('m', $t), idate('d', $t), $deltaT, $lat, $long, $angleOfSun)[$elem];
                if ($sr > 0) {
                    $nsr = $sr;
                    $i = 400;
                }
            }
        } elseif (is_nan($sr)) {
            for ($i = 1; $i < 366; $i++) {
                $t = $timestamp + $i * 86400;
                $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $t), idate('m', $t), idate('d', $t), $deltaT, $lat, $long, $angleOfSun)[$elem];
                if (!is_nan($sr)) {
                    $nsr = $sr;
                    $i = 400;
                }
            }
        }
        return $nsr;
    }

    public static function lastEl($timestamp, $deltaT, $lat, $long, $angleOfSun, $elem)
    {
        $sr = -1;
        $lsr = -1;
        $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $timestamp), idate('m', $timestamp), idate('d', $timestamp), $deltaT, $lat, $long, $angleOfSun)[$elem];
        if ($sr > $timestamp) {
            for ($i = 1; $i < 366; $i++) {
                $t = $timestamp - $i * 86400;
                $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $t), idate('m', $t), idate('d', $t), $deltaT, $lat, $long, $angleOfSun)[$elem];
                if ($sr > 0) {
                    $lsr = $sr;
                    $i = 400;
                }
            }
        } elseif ($sr > 0) {
            $lsr = $sr;
        } elseif (is_nan($sr)) {
            for ($i = 1; $i < 366; $i++) {
                $t = $timestamp - $i * 86400;
                $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $t), idate('m', $t), idate('d', $t), $deltaT, $lat, $long, $angleOfSun)[$elem];

                if (!is_nan($sr)) {
                    $lsr = $sr;
                    $i = 400;
                }
            }
        }
        return $lsr;
    }

    private static function v($julianDay){
        $jc = ASTROGEN::JulianCentury($julianDay);
        $jm = ASTROGEN::JulianMillennium($jc);
        $nutLong = ASTROSUN::NutationInLongitude($jc);
        $nutObl = ASTROSUN::NutationInObliquity($jc);
        $meanObl = ASTROSUN::MeanObliquityOfTheEcliptic($jm);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($meanObl, $nutObl);
        return ASTROSUN::ApparentSiderealTimeAtGreenwich($julianDay, $jc, $nutLong, $trueOblEcl);
    }

    private static function a($julianDay){
        $jc = ASTROGEN::JulianCentury($julianDay);
        $jm = ASTROGEN::JulianMillennium($jc);

        $nutLong = ASTROSUN::NutationInLongitude($jc);
        $HeliocentricLongitude = ASTROSUN::EarthHeliocentricLongitude($jm);
        $HeliocentricLatitude = ASTROSUN::EarthHeliocentricLatitude($jm);
        $earthRadVec = ASTROSUN::EarthRadiusVector($jm);
        $geoCentLong = ASTROSUN::GeocentricLongitude($HeliocentricLongitude);
        $geoCentLat = ASTROSUN::GeocentricLatitude($HeliocentricLatitude);
        $abCorr = ASTROSUN::AberrationCorrection($earthRadVec);
        $appSunLong = ASTROSUN::ApparentSunLongitude($geoCentLong, $nutLong, $abCorr);
        $nutObl = ASTROSUN::NutationInObliquity($jc);
        $meanObl = ASTROSUN::MeanObliquityOfTheEcliptic($jm);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($meanObl, $nutObl);
        return ASTROSUN::GeocentricSunRightAscension($appSunLong, $trueOblEcl, $geoCentLat);
    }

    public static function DeclinationOfSun($julianDay){
        $jc = ASTROGEN::JulianCentury($julianDay);
        $jm = ASTROGEN::JulianMillennium($jc);

        $HeliocentricLongitude = ASTROSUN::EarthHeliocentricLongitude($jm);
        $earthRadVec = ASTROSUN::EarthRadiusVector($jm);
        $HeliocentricLatitude = ASTROSUN::EarthHeliocentricLatitude($jm);
        $meanObl = ASTROSUN::MeanObliquityOfTheEcliptic($jm);
        $geoCentLong = ASTROSUN::GeocentricLongitude($HeliocentricLongitude);
        $nutObl = ASTROSUN::NutationInObliquity($jc);
        $nutLong = ASTROSUN::NutationInLongitude($jc);
        $abCorr = ASTROSUN::AberrationCorrection($earthRadVec);
        $geoCentLat = ASTROSUN::GeocentricLatitude($HeliocentricLatitude);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($meanObl, $nutObl);
        $appSunLong = ASTROSUN::ApparentSunLongitude($geoCentLong, $nutLong, $abCorr);
        return ASTROSUN::GeocentricSunDeclination($geoCentLat, $trueOblEcl, $appSunLong);
    }

    private static function SummationOfPeriodicTermsOfTheEarth(array $terms, int $count, float $jme):float
    {
        $sum = 0.0;
        for($i = 0; $i < $count; $i++){
            $sum += $terms[$i][0] * cos($terms[$i][1]+$terms[$i][2] * $jme);
        }
        return $sum;
    }

    private static function ValuesOfTheEarth(array $term_sum, int $count, float $jme):float
    {
        
        $sum = 0.0;

        for ($i = 0; $i < $count; $i++)
            $sum += $term_sum[$i] * pow($jme, $i);

        $sum /= pow(10, 8);

        return $sum;
    }
    
    public static function EarthHeliocentricLongitude(float $jce):float
    {
        $jme = ASTROGEN::JulianMillennium($jce);
        $sum = array();
    
        for ($i = 0; $i < ASTROSUN::l_count; $i++){
            $sum[$i] = ASTROSUN::SummationOfPeriodicTermsOfTheEarth(ASTROTERMS::l_terms[$i], ASTROSUN::l_subcount[$i], $jme);
        }

        return ASTROMISC::LimitTo360Deg(rad2deg(ASTROSUN::ValuesOfTheEarth($sum, ASTROSUN::l_count, $jme)));
    }

    public static function EarthHeliocentricLatitude(float $jce):float
    {
        $jme = ASTROGEN::JulianMillennium($jce);
        $sum = array();
        for ($i = 0; $i < ASTROSUN::b_count; $i++) {
            $sum[$i] = ASTROSUN::SummationOfPeriodicTermsOfTheEarth(ASTROTERMS::b_terms[$i], ASTROSUN::b_subcount[$i], $jme);
        }

        return rad2deg(ASTROSUN::ValuesOfTheEarth($sum, ASTROSUN::b_count, $jme));
    }

    //
    /**
     * Summary of HeliocentricLongitudeRAD
     * @param mixed $julianMillenium
     * @return float
     */
    /*public static function HeliocentricLongitudeRAD($julianMillenium){
        return (ASTROSUN::L0($julianMillenium)
            + ASTROSUN::L1($julianMillenium) * pow($julianMillenium, 1)
            + ASTROSUN::L2($julianMillenium) * pow($julianMillenium, 2)
            + ASTROSUN::L3($julianMillenium) * pow($julianMillenium, 3)
            + ASTROSUN::L4($julianMillenium) * pow($julianMillenium, 4)
            + ASTROSUN::L5($julianMillenium) * pow($julianMillenium, 5)) / pow(10, 8);
    }

    public static function HeliocentricLongitudeDEG($julianMillenium)
    {
        $l = ASTROSUN::HeliocentricLongitudeRAD($julianMillenium)*180/pi();
        return ASTROMISC::LimitTo360Deg($l);
    }

    public static function HeliocentricLatitude($julianMillenium)
    {
        return rad2deg((ASTROSUN::B0($julianMillenium)
            + ASTROSUN::B1($julianMillenium) * pow($julianMillenium, 1)) / pow(10, 8));
    }
    */
    public static function EarthRadiusVector(float $jce) :float
    {
        $jme = ASTROGEN::JulianMillennium($jce);
        $sum = array();
        for ($i = 0; $i < ASTROSUN::r_count; $i++) {
            $sum[$i] = ASTROSUN::SummationOfPeriodicTermsOfTheEarth(ASTROTERMS::r_terms[$i], ASTROSUN::r_subcount[$i], $jme);
        }

        return ASTROSUN::ValuesOfTheEarth($sum, ASTROSUN::r_count, $jme);
        /*
        return (ASTROSUN::R0($jme)
            + ASTROSUN::R1($jme) * pow($jme, 1)
            + ASTROSUN::R2($jme) * pow($jme, 2)
            + ASTROSUN::R3($jme) * pow($jme, 3)
            + ASTROSUN::R4($jme) * pow($jme, 4)) / pow(10, 8);*/
    }

    public static function GeocentricLongitude(float $jce):float
    {
        $glong = ASTROSUN::EarthHeliocentricLongitude($jce) + 180;
        return ASTROMISC::LimitTo360Deg($glong);
    }

    public static function GeocentricLatitude(float $jce):float
    {
        return -1 * ASTROSUN::EarthHeliocentricLatitude($jce);
    }

    //Nutuation
    public static function MeanAnomalyOfTheSun(float $jce):float
    {
        return ASTROMISC::ThirdOrderPolynomial(-1.0 / 24490000.0, -0.0001536, 35999.0502909, 357.52772, $jce);
        //return 357.5291092 + 35999.0502909 * $jce - 0.0001536 * pow($jce, 2) + pow($jce, 3)/ 24490000;
    }

    public static function NutationInLongitude(float $jce):float
    {
        $psi = 0.0;
        $y_terms = ASTROTERMS::y_terms;
        $pe_terms = ASTROTERMS::pe_terms;
        for($i = 0; $i < count($y_terms); $i++){
            $sumterm = 0;
            for($j = 0; $j < 5; $j++){
                $sumterm += ASTROSUN::X($j, $jce) * $y_terms[$i][$j];
            }
            $psi += ($pe_terms[$i][0]+$pe_terms[$i][1]*$jce)*sin($sumterm);
        }
        return $psi/36000000;
    }

    public static function NutationInObliquity(float $jce):float
    {
        $eps = 0.0;
        $y_terms = ASTROTERMS::y_terms;
        $pe_terms = ASTROTERMS::pe_terms;
        for($i = 0; $i < count($y_terms); $i++){
            $sumterm = 0;
            for($j = 0; $j < 5; $j++){
                $sumterm += ASTROSUN::X($j, $jce) * $y_terms[$i][$j];
            }
            $eps += ($pe_terms[$i][2]+ $pe_terms[$i][3]*$jce)*cos($sumterm);
        }
        return $eps/36000000;
    }

    //mean obliquity of the ecliptic
    public static function MeanObliquityOfTheEcliptic(float $jce):float
    {
        $jme = ASTROGEN::JulianMillennium($jce);
        $u = $jme / 10;
        return 84381.448 - 4680.93 * pow($u, 1) - 1.55 * pow($u, 2) + 1999.25 * pow($u, 3) - 51.38 * pow($u, 4) - 249.67 * pow($u, 5) - 39.05 * pow($u, 6) + 7.12 * pow($u, 7) + 27.87 * pow($u, 8) + 5.79 * pow($u, 9) + 2.45 * pow($u, 10);
    }

    public static function TrueObliquityOfTheEcliptic(float $jce):float
    {
        $meanObl = ASTROSUN::MeanObliquityOfTheEcliptic($jce);
        $nutObl = ASTROSUN::NutationInObliquity($jce);
        return $meanObl / 3600 + $nutObl;
    }

    public static function AberrationCorrection(float $jce):float
    {
        
        return -20.4898 / (3600 * ASTROSUN::EarthRadiusVector($jce));
    }

    public static function ApparentSunLongitude(float $jce):float
    {
        return ASTROSUN::GeocentricLongitude($jce) + ASTROSUN::NutationInLongitude($jce)  + ASTROSUN::AberrationCorrection($jce);
    }

    public static function MeanSiderealTimeAtGreenwich(float $jd):float
    {
        $jc = ASTROGEN::JulianCentury($jd);
        $v0 = 280.46061837 + 360.98564736629 * ($jd - 2451545) + 0.000387933 * pow($jc, 2) - pow($jc, 3) / 38710000;
        return ASTROMISC::LimitTo360Deg($v0);
    }

    public static function ApparentSiderealTimeAtGreenwich(float $jd): float
    {
        $jc = ASTROGEN::JulianCentury($jd);
        $nutationLong = ASTROSUN::NutationInLongitude($jc);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($jc);
        return ASTROSUN::MeanSiderealTimeAtGreenwich($jd) + $nutationLong * cos(deg2rad($trueOblEcl));
    }

    public static function GeocentricSunRightAscension(float $jce):float
    {
        $appSunLong = ASTROSUN::ApparentSunLongitude($jce);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($jce);
        $geoCentLat = ASTROSUN::GeocentricLatitude($jce);
        $a = rad2deg(
            atan2(
                sin(deg2rad($appSunLong)) * cos(deg2rad($trueOblEcl)) - tan(deg2rad($geoCentLat)) * sin(deg2rad($trueOblEcl)),
                cos(deg2rad($appSunLong))
            )
        );
        return ASTROMISC::LimitTo360Deg($a);
    }

    public static function GeocentricSunDeclination(float $jce): float
    {
        $appSunLong = ASTROSUN::ApparentSunLongitude($jce);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($jce);
        $geoCentLat = ASTROSUN::GeocentricLatitude($jce);
        return rad2deg(
            asin(
                sin(deg2rad($geoCentLat)) * cos(deg2rad($trueOblEcl)) + cos(deg2rad($geoCentLat)) * sin(deg2rad($trueOblEcl)) * sin(deg2rad($appSunLong))
            )
        );
    }

    public static function LocalHourAngle(float $jce, float $jd, float $longitude): float
    {
        $appSidTimeGreenwich = ASTROSUN::ApparentSiderealTimeAtGreenwich($jd);
        $geoSunRAsc = ASTROSUN::GeocentricSunRightAscension($jce);
        return ASTROMISC::LimitTo360Deg($appSidTimeGreenwich + $longitude - $geoSunRAsc);
    }

    public static function DeltaA(float $jce, float $jd, float $lat, float $long, float $elev):float 
    {
        $locHourAngle = ASTROSUN::LocalHourAngle($jd, $long, $jce);
        $geoSunDec = ASTROSUN::GeocentricSunDeclination($jce);

        $s = 8.794 / (3600 * ASTROSUN::EarthRadiusVector($jce));

        $u = atan(0.99664719 * tan(deg2rad($lat)));

        $x = cos($u) + $elev / 6378140 * cos(deg2rad($lat));

        $y = 0.99664719 * sin($u) + $elev / 6378140 * sin(deg2rad($lat));

        $da = atan2(-1 * $x * sin(deg2rad($s)) * sin(deg2rad($locHourAngle)),  cos(deg2rad($geoSunDec)) - $x * sin(deg2rad($s)) * cos(deg2rad($locHourAngle)));

        return rad2deg($da);

    }

    public static function TopocentricSunRightAscension(float $jce, float $jd, float $lat, float $long, float $elev): float
    {
        $geoSunRAsc = ASTROSUN::GeocentricSunRightAscension($jce);
        $locHourAngle = ASTROSUN::LocalHourAngle($jd, $long, $jce);
        $earthRadVec = ASTROSUN::EarthRadiusVector($jce);
        return $geoSunRAsc - ASTROSUN::DeltaA($jce, $jd, $lat, $long, $elev);
    }

    public static function TopocentricSunDeclination(float $earthRadVec, float $lat, float $elev, float $locHourAngle, float $geoSunDec, float $geoSunRAsc): float
    {
        $s = 8.794 / (3600 * $earthRadVec);

        $u = atan(0.99664719 * tan(deg2rad($lat)));

        $x = cos($u) + $elev / 6378140 * cos(deg2rad($lat));

        $y = 0.99664719 * sin($u) + $elev / 6378140 * sin(deg2rad($lat));

        return rad2deg(
            atan2(
                (sin(deg2rad($geoSunDec)) - $y * sin(deg2rad($s))) * cos(ASTROSUN::DeltaA($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec)),
                cos(deg2rad($geoSunDec)) -$x * sin(deg2rad($s)) * cos(deg2rad($locHourAngle))
            )
        );
    }

    public static function TopocentricLocalHourAngle(float $earthRadVec, float $lat, float $elev, float $locHourAngle, float $geoSunDec, float $geoSunRAsc):float 
    {
        return $locHourAngle - ASTROSUN::DeltaA($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec);
    }

    public static function TopocentricElevationAngle(float $lat, float $geoSunDec, float $topoHourAngle):float{
        return rad2deg(asin( sin(deg2rad($lat)) * sin(deg2rad($geoSunDec)) + cos(deg2rad($lat)) * cos(deg2rad($geoSunDec)) * cos(deg2rad($topoHourAngle))));

}

    public static function AtmosphericRefractionCorrection(float $lat, float $geoSunDec, float $topoHourAngle, float $press, float $temp, float $atmosRefract): float
    {
        $deltaE = 0.0;
        $e0 = ASTROSUN::TopocentricElevationAngle($lat, $geoSunDec, $topoHourAngle);
        if ($e0 >= -1 * (ASTROSUN::sun_radius + $atmosRefract)) {
            $deltaE = ($press / 1010) * (283 / (273 + $temp)) * 1.02 / (60 * tan(deg2rad($e0 + 10.3 / ($e0 + 5.11))));
        }

        return $deltaE;
    }

    public static function TopocentricElevationAngleCorrected(float $lat, float $geoSunDec, float $topoHourAngle, float $press, float $temp): float
    {
        $e0 =  ASTROSUN::TopocentricElevationAngle($lat, $geoSunDec, $topoHourAngle); 
        $deltaE = ASTROSUN::AtmosphericRefractionCorrection($lat, $geoSunDec, $topoHourAngle, $press, $temp, 0.5667);

        return $e0 + $deltaE;
    }

    public static function TopocentricZenithAngle(float $lat, float $geoSunDec, float $topoHourAngle, float $press, float $temp): float
    {
        $e =  ASTROSUN::TopocentricElevationAngleCorrected($lat, $geoSunDec, $topoHourAngle, $press, $temp); 

        return 90 - $e;

    }

    public static function TopocentricAzimuthAngle(float $lat, float $topoSunDec, float $topoHourAngle): float
    {
        $t = rad2deg(atan2(
            sin(deg2rad($topoHourAngle)),
            cos(deg2rad($topoHourAngle)) * sin(deg2rad($lat)) - tan(deg2rad($topoSunDec)) * cos(deg2rad($lat))
        ));
        $t = ASTROMISC::LimitTo360Deg($t);

        return ASTROMISC::LimitTo360Deg($t + 180);
    }

    public static function SunMeanLongitude(float $jme):float
    {
        return ASTROMISC::LimitTo360Deg(280.4664567 + $jme*(360007.6982779 + $jme*(0.03032028 +
                    $jme*(1/49931.0   + $jme*(-1/15300.0     + $jme*(-1/2000000.0))))));
    }

    public static function EqOfTime(float $jm, float $geoSunRAsc, float $nutLong, float $trueOblEcl): float
    {
        $m = ASTROSUN::SunMeanLongitude($jm);

        $E = 4*($m - 0.0057183 - $geoSunRAsc + $nutLong * cos(deg2rad($trueOblEcl)));
        return ASTROMISC::LimitTo20Minutes($E);
    }

    public static function ElevationOfTheSun($lat, $geoSunDec, $topoLocHourAngle, $press, $temp){
        return 90 - ASTROSUN::TopocentricZenithAngle($lat, $geoSunDec, $topoLocHourAngle, $press, $temp);
    }

    public static function Season(float $latitude): int
    {
        $jd= ASTROGEN::oldJulianDay();
        $declination = ASTROSUN::DeclinationOfSun($jd);
        $declinationBef = ASTROSUN::DeclinationOfSun($jd - 60/86400);
        if ($declination >= 0) {
            if ($declination > $declinationBef) {
                if ($latitude > 0) {
                    return 1;
                } else {
                    return 3;
                }
            } else {
                if ($latitude > 0) {
                    return 2;
                } else {
                    return 4;
                }
            }
        } else {
            if ($declination > $declinationBef) {
                if ($latitude > 0) {
                    return 4;
                } else {
                    return 2;
                }
            } else {
                if ($latitude > 0) {
                    return 3;
                } else {
                    return 1;
                }
            }
        }
    }

    public static function ShadowLength($sunelevation){
        $shadowlen = 1 / tan(deg2rad($sunelevation));
        if ($shadowlen > 0) {
            return $shadowlen;
        } else {

            return 0;
        }
    }

    public static function SunlightDuration($deltaT, float $lat, float $long, $geoSunDec, $topoLocHourAngle)
    {
        $now = time();
        $sr = ASTROSUN::SunriseSunsetTransit(idate('Y', $now), idate('m', $now), idate('d', $now), $deltaT, $lat, $long, -0.8333)["R"];
        $ss = ASTROSUN::SunriseSunsetTransit(idate('Y', $now), idate('m', $now), idate('d', $now), $deltaT, $lat, $long, -0.8333)["S"];
        if(!is_nan($sr) && !is_nan($ss)){
            return $ss - $sr;
        }elseif(-0.8333 < ASTROSUN::ElevationOfTheSun($lat, $geoSunDec, $topoLocHourAngle, 1013, 10)){
            return ASTROSUN::nextEl($now, $deltaT, $lat, $long, -0.8333, "S") -
                ASTROSUN::lastEl($now, $deltaT, $lat, $long, -0.8333, "R");

        }else{
            return 0;
        }

        return 0;
    }

    public static function IncidenceAngleOfSurface(float $orientation, float $slope, float $lat, float $geoSunDec, float $topoSunDec, float$topoHourAngle, float $press, float $temp):float
    {
        return rad2deg(
            acos(
                cos(deg2rad(ASTROSUN::TopocentricZenithAngle($lat, $geoSunDec, $topoHourAngle, $press, $temp))) * cos(deg2rad($slope)) +
                sin(deg2rad($slope)) * sin(deg2rad(ASTROSUN::TopocentricZenithAngle($lat, $geoSunDec, $topoHourAngle, $press, $temp))) * cos(deg2rad( ASTROSUN::TopocentricAzimuthAngle($lat, $topoSunDec, $topoHourAngle) - $orientation))
            )
        );
    }


    // Hilfsfunktionen
    private static function X(int $i, float $jce): float
    {
        switch ($i){
            case 0:
                return ASTROMOON::MeanElongationMoonSun($jce);
            case 1:
                return ASTROSUN::MeanAnomalyOfTheSun($jce);
            case 2:
                return ASTROMOON::MeanAnomalyOfTheMoon($jce);
            case 3:
                return ASTROMOON::MoonsArgumentOfLatitude($jce);
            case 4:
                return ASTROMOON::LongitudeOfTheAscendingNodeOfTheMoon($jce);
            default:
                return 0;
        }
    }
    
    /*public static function L0($julianMillenium){
        $l0 = array();
        $l0Data = ASTROSUN::L0Arr();
        for($i =0; $i < count($l0Data); $i++){
            $l0[$i] = $l0Data[$i][0] * cos($l0Data[$i][1] + $l0Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for($i = 0; $i < count($l0);$i++){
            $sum += $l0[$i];
        }
        return $sum;
    }

    public static function L1($julianMillenium)
    {
        $l1 = array();
        $l1Data = ASTROSUN::L1Arr();
        for ($i = 0; $i < count($l1Data); $i++) {
            $l1[$i] = $l1Data[$i][0] * cos($l1Data[$i][1] + $l1Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l1); $i++) {
            $sum += $l1[$i];
        }
        return $sum;
    }

    public static function L2($julianMillenium)
    {
        $l2 = array();
        $l2Data = ASTROSUN::L2Arr();
        for ($i = 0; $i < count($l2Data); $i++) {
            $l2[$i] = $l2Data[$i][0] * cos($l2Data[$i][1] + $l2Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l2); $i++) {
            $sum += $l2[$i];
        }
        return $sum;
    }

    public static function L3($julianMillenium)
    {
        $l3 = array();
        $l3Data = ASTROSUN::L3Arr();
        for ($i = 0; $i < count($l3Data); $i++) {
            $l3[$i] = $l3Data[$i][0] * cos($l3Data[$i][1] + $l3Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l3); $i++) {
            $sum += $l3[$i];
        }
        return $sum;
    }

    public static function L4($julianMillenium)
    {
        $l4 = array();
        $l4Data = ASTROSUN::L4Arr();
        for ($i = 0; $i < count($l4Data); $i++) {
            $l4[$i] = $l4Data[$i][0] * cos($l4Data[$i][1] + $l4Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l4); $i++) {
            $sum += $l4[$i];
        }
        return $sum;
    }

    public static function L5($julianMillenium)
    {
        $l5 = array();
        $l5Data = ASTROSUN::L5Arr();
        for ($i = 0; $i < count($l5Data); $i++) {
            $l5[$i] = $l5Data[$i][0] * cos($l5Data[$i][1] + $l5Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l5); $i++) {
            $sum += $l5[$i];
        }
        return $sum;
    }

    public static function B0($julianMillenium)
    {
        $b0 = array();
        $b0Data = ASTROSUN::B0Arr();
        for ($i = 0; $i < count($b0Data); $i++) {
            $b0[$i] = $b0Data[$i][0] * cos($b0Data[$i][1] + $b0Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($b0); $i++) {
            $sum += $b0[$i];
        }
        return $sum;
    }

    public static function B1($julianMillenium)
    {
        $l1 = array();
        $l1Data = ASTROSUN::B1Arr();
        for ($i = 0; $i < count($l1Data); $i++) {
            $l1[$i] = $l1Data[$i][0] * cos($l1Data[$i][1] + $l1Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l1); $i++) {
            $sum += $l1[$i];
        }
        return $sum;
    }

    public static function R0($julianMillenium)
    {
        $l0 = array();
        $l0Data = ASTROSUN::R0Arr();
        for ($i = 0; $i < count($l0Data); $i++) {
            $l0[$i] = $l0Data[$i][0] * cos($l0Data[$i][1] + $l0Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l0); $i++) {
            $sum += $l0[$i];
        }
        return $sum;
    }

    public static function R1($julianMillenium)
    {
        $l1 = array();
        $l1Data = ASTROSUN::R1Arr();
        for ($i = 0; $i < count($l1Data); $i++) {
            $l1[$i] = $l1Data[$i][0] * cos($l1Data[$i][1] + $l1Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l1); $i++) {
            $sum += $l1[$i];
        }
        return $sum;
    }

    public static function R2($julianMillenium)
    {
        $l2 = array();
        $l2Data = ASTROSUN::R2Arr();
        for ($i = 0; $i < count($l2Data); $i++) {
            $l2[$i] = $l2Data[$i][0] * cos($l2Data[$i][1] + $l2Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l2); $i++) {
            $sum += $l2[$i];
        }
        return $sum;
    }

    public static function R3($julianMillenium)
    {
        $l3 = array();
        $l3Data = ASTROSUN::R3Arr();
        for ($i = 0; $i < count($l3Data); $i++) {
            $l3[$i] = $l3Data[$i][0] * cos($l3Data[$i][1] + $l3Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l3); $i++) {
            $sum += $l3[$i];
        }
        return $sum;
    }

    public static function R4($julianMillenium)
    {
        $l4 = array();
        $l4Data = ASTROSUN::R4Arr();
        for ($i = 0; $i < count($l4Data); $i++) {
            $l4[$i] = $l4Data[$i][0] * cos($l4Data[$i][1] + $l4Data[$i][2] * $julianMillenium);
        }
        $sum = 0;
        for ($i = 0; $i < count($l4); $i++) {
            $sum += $l4[$i];
        }
        return $sum;
    }

    private static function L0Arr(){
        $l0 = array(
            array(175347046, 0, 0),
            array(3341656, 4.6692568, 6283.07585),
            array(34894, 4.6261, 12566.1517),
            array(3497, 2.7441, 5753.3849),
            array(3418, 2.8289, 3.5231),
            array(3136, 3.6277, 77713.7715),
            array(2676, 4.4181, 7860.4194),
            array(2343, 6.1352, 3930.2097),
            array(1324, 0.7425, 11506.7698),
            array(1273, 2.0371, 529.691),
            array(1199, 1.1096, 1577.3435),
            array(990, 5.233, 5884.927),
            array(902, 2.045, 26.298),
            array(857, 3.508, 398.149),
            array(780, 1.179, 5223.694),
            array(753, 2.533, 5507.553),
            array(505, 4.583, 18849.228),
            array(492, 4.205, 775.523),
            array(357, 2.92, 0.067),
            array(317, 5.849, 11790.629),
            array(284, 1.899, 796.298),
            array(271, 0.315, 10977.079),
            array(243, 0.345, 5486.778),
            array(206, 4.806, 2544.314),
            array(205, 1.869, 5573.143),
            array(202, 2.458, 6069.777),
            array(156, 0.833, 213.299),
            array(132, 3.411, 2942.463),
            array(126, 1.083, 20.775),
            array(115, 0.645, 0.98),
            array(103, 0.636, 4694.003),
            array(102, 0.976, 15720.839),
            array(102, 4.267, 7.114),
            array(99, 6.21, 2146.17),
            array(98, 0.68, 155.42),
            array(86, 5.98, 161000.69),
            array(85, 1.3, 6275.96),
            array(85, 3.67, 71430.7),
            array(80, 1.81, 17260.15),
            array(79, 3.04, 12036.46),
            array(75, 1.76, 5088.63),
            array(74, 3.5, 3154.69),
            array(74, 4.68, 801.82),
            array(70, 0.83, 9437.76),
            array(62, 3.98, 8827.39),
            array(61, 1.82, 7084.9),
            array(57, 2.78, 6286.6),
            array(56, 4.39, 14143.5),
            array(56, 3.47, 6279.55),
            array(52, 0.19, 12139.55),
            array(52, 1.33, 1748.02),
            array(51, 0.28, 5856.48),
            array(49, 0.49, 1194.45),
            array(41, 5.37, 8429.24),
            array(41, 2.4, 19651.05),
            array(39, 6.17, 10447.39),
            array(37, 6.04, 10213.29),
            array(37, 2.57, 1059.38),
            array(36, 1.71, 2352.87),
            array(36, 1.78, 6812.77),
            array(33, 0.59, 17789.85),
            array(30, 0.44, 83996.85),
            array(30, 2.74, 1349.87),
            array(25, 3.16, 4690.48)
        );
        return $l0;
    }

    private static function L1Arr()
    {
        $l1 = array(
            array(628331966747, 0, 0),
            array(206059, 2.678235, 6283.07585),
            array(4303, 2.6351, 12566.1517),
            array(425, 1.59, 3.523),
            array(119, 5.796, 26.298),
            array(109, 2.966, 1577.344),
            array(93, 2.59, 18849.23),
            array(72, 1.14, 529.69),
            array(68, 1.87, 398.15),
            array(67, 4.41, 5507.55),
            array(59, 2.89, 5223.69),
            array(56, 2.17, 155.42),
            array(45, 0.4, 796.3),
            array(36, 0.47, 775.52),
            array(29, 2.65, 7.11),
            array(21, 5.34, 0.98),
            array(19, 1.85, 5486.78),
            array(19, 4.97, 213.3),
            array(17, 2.99, 6275.96),
            array(16, 0.03, 2544.31),
            array(16, 1.43, 2146.17),
            array(15, 1.21, 10977.08),
            array(12, 2.83, 1748.02),
            array(12, 3.26, 5088.63),
            array(12, 5.27, 1194.45),
            array(12, 2.08, 4694),
            array(11, 0.77, 553.57),
            array(10, 1.3, 6286.6),
            array(10, 4.24, 1349.87),
            array(9, 2.7, 242.73),
            array(9, 5.64, 951.72),
            array(8, 5.3, 2352.87),
            array(6, 2.65, 9437.76),
            array(6, 4.67, 4690.48)
        );
        return $l1;
    }

    private static function L2Arr()
    {
        $l2 = array(
            array(52919, 0, 0),
            array(8720, 1.0721, 6283.0758),
            array(309, 0.867, 12566.152),
            array(27, 0.05, 3.52),
            array(16, 5.19, 26.3),
            array(16, 3.68, 155.42),
            array(10, 0.76, 18849.23),
            array(9, 2.06, 77713.77),
            array(7, 0.83, 775.52),
            array(5, 4.66, 1577.34),
            array(4, 1.03, 7.11),
            array(4, 3.44, 5573.14),
            array(3, 5.14, 796.3),
            array(3, 6.05, 5507.55),
            array(3, 1.19, 242.73),
            array(3, 6.12, 529.69),
            array(3, 0.31, 398.15),
            array(3, 2.28, 553.57),
            array(2, 4.38, 5223.69),
            array(2, 3.75, 0.98)
        );
        return $l2;
    }

    private static function L3Arr()
    {
        $l3 = array(
            array(289, 5.844, 6283.076),
            array(35, 0, 0),
            array(17, 5.49, 12566.15),
            array(3, 5.2, 155.42),
            array(1, 4.72, 3.52),
            array(1, 5.3, 18849.23),
            array(1, 5.97, 242.73)
        );
        return $l3;
    }

    private static function L4Arr()
    {
        $l4 = array(
            array(114, 3.142, 0),
            array(8, 4.13, 6283.08),
            array(1, 3.84, 12566.15)
        );
        return $l4;
    }

    private static function L5Arr()
    {
        $l5 = array(
            array(1, 3.14, 0),
        );
        return $l5;
    }

    private static function B0Arr()
    {
        $b0 = array(
            array(280, 3.199, 84334.662),
            array(102, 5.422, 5507.553),
            array(80, 3.88, 5223.69),
            array(44, 3.7, 2352.87),
            array(32, 4, 1577.34)
        );
        return $b0;
    }

    private static function B1Arr()
    {
        $b1 = array(
            array(9, 3.9, 5507.55),
            array(6, 1.73, 5223.69)
        );
        return $b1;
    }

    private static function R0Arr()
    {
        $r0 = array(
            array(100013989, 0, 0),
            array(1670700, 3.0984635, 6283.07585),
            array(13956, 3.05525, 12566.1517),
            array(3084, 5.1985, 77713.7715),
            array(1628, 1.1739, 5753.3849),
            array(1576, 2.8469, 7860.4194),
            array(925, 5.453, 11506.77),
            array(542, 4.564, 3930.21),
            array(472, 3.661, 5884.927),
            array(346, 0.964, 5507.553),
            array(329, 5.9, 5223.694),
            array(307, 0.299, 5573.143),
            array(243, 4.273, 11790.629),
            array(212, 5.847, 1577.344),
            array(186, 5.022, 10977.079),
            array(175, 3.012, 18849.228),
            array(110, 5.055, 5486.778),
            array(98, 0.89, 6069.78),
            array(86, 5.69, 15720.84),
            array(86, 1.27, 161000.69),
            array(65, 0.27, 17260.15),
            array(63, 0.92, 529.69),
            array(57, 2.01, 83996.85),
            array(56, 5.24, 71430.7),
            array(49, 3.25, 2544.31),
            array(47, 2.58, 775.52),
            array(45, 5.54, 9437.76),
            array(43, 6.01, 6275.96),
            array(39, 5.36, 4694),
            array(38, 2.39, 8827.39),
            array(37, 0.83, 19651.05),
            array(37, 4.9, 12139.55),
            array(36, 1.67, 12036.46),
            array(35, 1.84, 2942.46),
            array(33, 0.24, 7084.9),
            array(32, 0.18, 5088.63),
            array(32, 1.78, 398.15),
            array(28, 1.21, 6286.6),
            array(28, 1.9, 6279.55),
            array(26, 4.59, 10447.39)
        );
        return $r0;
    }

    private static function R1Arr()
    {
        $r1 = array(
            array(103019, 1.10749, 6283.07585),
            array(1721, 1.0644, 12566.1517),
            array(702, 3.142, 0),
            array(32, 1.02, 18849.23),
            array(31, 2.84, 5507.55),
            array(25, 1.32, 5223.69),
            array(18, 1.42, 1577.34),
            array(10, 5.91, 10977.08),
            array(9, 1.42, 6275.96),
            array(9, 0.27, 5486.78)
        );
        return $r1;
    }

    private static function R2Arr()
    {
        $r2 = array(
            array(4359, 5.7846, 6283.0758),
            array(124, 5.579, 12566.152),
            array(12, 3.14, 0),
            array(9, 3.63, 77713.77),
            array(6, 1.87, 5573.14),
            array(3, 5.47, 18849.23)
        );
        return $r2;
    }

    private static function R3Arr()
    {
        $r3 = array(
            array(145, 4.273, 6283.076),
            array(7, 3.92, 12566.15)
        );
        return $r3;
    }

    private static function R4Arr()
    {
        $r4 = array(
            array(4, 2.56, 6283.08)
        );
        return $r4;
    }
    
    private static function PeriodicTermsForTheNutation()
    {
        $pt = array(
            array( 'y' => array(0, 0, 0, 0, 1),
                'a' => -171996,
                'b' => -174.2,
                'c' => 92025,
                'd' => 8.9
            ),
            array( 'y' => array(-2, 0, 0, 2, 2),
                'a' => -13187,
                'b' => -1.6,
                'c' => 5736,
                'd' => -3.1
            ),
            array( 'y' => array(0, 0, 0, 2, 2),
                'a' => -2274,
                'b' => -0.2,
                'c' => 977,
                'd' => -0.5
            ),
            array( 'y' => array(0, 0, 0, 0, 2),
                'a' => 2062,
                'b' => 0.2,
                'c' => -895,
                'd' => 0.5
            ),
            array( 'y' => array(0, 1, 0, 0, 0),
                'a' => 1426,
                'b' => -3.4,
                'c' => 54,
                'd' => -0.1
            ),
            array( 'y' => array(0, 0, 1, 0, 0),
                'a' => 712,
                'b' => 0.1,
                'c' => -7,
                'd' => 0
            ),
            array( 'y' => array(-2, 1, 0, 2, 2),
                'a' => -517,
                'b' => 1.2,
                'c' => 224,
                'd' => -0.6
            ),
            array( 'y' => array(0, 0, 0, 2, 1),
                'a' => -386,
                'b' => -0.4,
                'c' => 200,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 1, 2, 2),
                'a' => -301,
                'b' => 0,
                'c' =>  129,
                'd' => -0.1
            ),
            array( 'y' => array(-2, -1, 0, 2, 2),
                'a' => 217,
                'b' => -0.5,
                'c' => -95,
                'd' => 0.3
            ),
            array( 'y' => array(-2, 0, 1, 0, 0),
                'a' => -158,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 0, 2, 1),
                'a' => 129,
                'b' => 0.1,
                'c' => -70,
                'd' => 0
            ),
            array( 'y' => array(0, 0, -1, 2, 2),
                'a' => 123,
                'b' => 0,
                'c' =>  -53,
                'd' => 0
            ),
            array( 'y' => array(2, 0, 0, 0, 0),
                'a' => 63,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 1, 0, 1),
                'a' => 63,
                'b' => 0.1,
                'c' => -33,
                'd' => 0
            ),
            array( 'y' => array(2, 0, -1, 2, 2),
                'a' => -59,
                'b' => 0,
                'c' => 26,
                'd' => 0
            ),
            array( 'y' => array(0, 0, -1, 0, 1),
                'a' => -58,
                'b' =>  -0.1,
                'c' =>  32,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 1, 2, 1),
                'a' => -51,
                'b' => 0,
                'c' => 27,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 2, 0, 0),
                'a' => 48,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, -2, 2, 1),
                'a' => 46,
                'b' => 0,
                'c' => -24,
                'd' => 0
            ),
            array( 'y' => array(2, 0, 0, 2, 2),
                'a' => -38,
                'b' => 0,
                'c' => 16,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 2, 2, 2),
                'a' => -31,
                'b' => 0,
                'c' => 13,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 2, 0, 0),
                'a' => 29,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 1, 2, 2),
                'a' => 29,
                'b' => 0,
                'c' => -12,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 0, 2, 0),
                'a' => 26,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 0, 2, 0),
                'a' => -22,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, -1, 2, 1),
                'a' => 21,
                'b' => 0,
                'c' => -10,
                'd' => 0
            ),
            array( 'y' => array(0, 2, 0, 0, 0),
                'a' => 17,
                'b' => -0.1,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(2, 0, -1, 0, 1),
                'a' => 16,
                'b' => 0,
                'c' => -8,
                'd' => 0
            ),
            array( 'y' => array(-2, 2, 0, 2, 2),
                'a' => -16,
                'b' => 0.1,
                'c' => 7,
                'd' => 0
            ),
            array( 'y' => array(0, 1, 0, 0, 1),
                'a' => -15,
                'b' => 0,
                'c' => 9,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 1, 0, 1),
                'a' => -13,
                'b' => 0,
                'c' => 7,
                'd' => 0
            ),
            array( 'y' => array(0, -1, 0, 0, 1),
                'a' => -12,
                'b' => 0,
                'c' => 6,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 2, -2, 0),
                'a' => 11,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(2, 0, -1, 2, 1),
                'a' => -10,
                'b' => 0,
                'c' => 5,
                'd' => 0
            ),
            array( 'y' => array(2, 0, 1, 2, 2),
                'a' => -8,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(0, 1, 0, 2, 2),
                'a' => 7,
                'b' => 0,
                'c' => -3,
                'd' => 0
            ),
            array( 'y' => array(-2, 1, 1, 0, 0),
                'a' => -7,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, -1, 0, 2, 2),
                'a' => -7,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(2, 0, 0, 2, 1),
                'a' => -7,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(2, 0, 1, 0, 0),
                'a' => 6,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 2, 2, 2),
                'a' => 6,
                'b' => 0,
                'c' => -3,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 1, 2, 1),
                'a' => 6,
                'b' => 0,
                'c' => -3,
                'd' => 0
            ),
            array( 'y' => array(2, 0, -2, 0, 1),
                'a' => -6,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(2, 0, 0, 0, 1),
                'a' => -6,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(0, -1, 1, 0, 0),
                'a' => 5,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, -1, 0, 2, 1),
                'a' => -5,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 0, 0, 1),
                'a' => -5,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 2, 2, 1),
                'a' => -5,
                'b' => 0,
                'c' => 3,
                'd' => 0
            ),
            array( 'y' => array(-2, 0, 2, 0, 1),
                'a' => 4,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, 1, 0, 2, 1),
                'a' => 4,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 1, -2, 0),
                'a' => 4,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-1, 0, 1, 0, 0),
                'a' => -4,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-2, 1, 0, 0, 0),
                'a' => -4,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(1, 0, 0, 0, 0),
                'a' => -4,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 1, 2, 0),
                'a' => 3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, -2, 2, 2),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(-1, -1, 1, 0, 0),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 1, 1, 0, 0),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, -1, 1, 2, 2),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(2, -1, -1, 2, 2),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(0, 0, 3, 2, 2),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            ),
            array( 'y' => array(2, -1, 0, 2, 2),
                'a' => -3,
                'b' => 0,
                'c' => 0,
                'd' => 0
            )
        );
        return $pt;
    }
*/
}

class ASTROMOON
{
    public static function MeanLongitude(float $jce):float
    {
        return ASTROMISC::LimitTo360Deg(
            ASTROMISC::FourthOrderPolynomial(
                -1.0 / 65194000,
                1.0 / 538841,
                -0.0015786,
                481267.88123421,
                218.3164477,
                $jce
            )
        );
        
    }

    public static function GeocentricLongitude()
    {

    }
    
    public static function MeanElongationMoon(float $jce):float
    {
        return ASTROMISC::LimitTo360Deg(
            ASTROMISC::FourthOrderPolynomial(
                -1.0 / 113065000,
                1.0 / 545868,
                -0.0018819,
                445267.1114034,
                297.8501921,
                $jce
            )
        );
    }

    public static function MeanElongationMoonSun(float $jce):float
    {
        return ASTROMISC::ThirdOrderPolynomial(1.0/189474.0, -0.0019142, 445267.11148, 297.85036, $jce);
    }

    public static function MeanAnomalyOfTheMoon(float $jce):float
    {
        return ASTROMISC::FourthOrderPolynomial(-1.0 / 14712000, 1.0 / 69699.0, 0.0087414, 477198.8675055, 134.9633964, $jce);
        //return 134.9633964 + 477198.8675055 * $julianCentury + 0.0087414 * pow($julianCentury, 2) + pow($julianCentury, 3) / 56250 - pow($julianCentury, 4) / 14712000;
    }

    public static function MoonsArgumentOfLatitude(float $jce): float
    {
        return ASTROMISC::FourthOrderPolynomial(1.0 / 863310000, -1.0 / 3526000.0, -0.0036539, 483202.0175233, 93.2720950, $jce);
        //return 93.27191 + 483202.017538 * $jce - 0.0036825 * pow($jce, 2) + pow($jce, 3) / 327270;
    }

    public static function LongitudeOfTheAscendingNodeOfTheMoon(float $jce):float
    {
        return ASTROMISC::ThirdOrderPolynomial(1.0 / 450000.0, 0.0020708, -1934.136261, 125.04452, $jce);
        //return 125.04452 - 1934.136261 * $jce + 0.0020708 * pow($jce, 2) + pow($jce, 3) / 450000;
    }

    private static function SummationOfPeriodicTermsOfTheMoon(array $terms, float $jce): array
    {
        $count = count($terms);
        $e = 1.0 - $jce * (0.002516 + $jce * 0.0000074);
        $d = ASTROMOON::MeanElongationMoon($jce);
        $m = ASTROSUN::MeanAnomalyOfTheSun($jce);
        $f = ASTROMOON::MoonsArgumentOfLatitude($jce);
        $ms = ASTROMOON::MeanAnomalyOfTheMoon($jce);

        $l = 0;
        $r = 0;
        for ($i = 0; $i < $count; $i++) {
            $e_mult = pow($e, abs($terms[$i][1]));
            $trig_arg = deg2rad($terms[$i][0] * $d + $terms[$i][1] * $m +
                $terms[$i][3] * $f + $terms[$i][2] * $ms);
            $l += $e_mult * $terms[$i][4] * sin($trig_arg);
            $r += $e_mult * $terms[$i][5] * cos($trig_arg);
        }
        return array($l, $r);
    }


    public static function MoonLongitudeAndLatitude($jce): array
    {
        $a1 = 119.75 + 131.849 * $jce;
        $a2 = 53.09 + 479264.290 * $jce;
        $a3 = 313.45 + 481266.484 * $jce;
        $l_prime = ASTROMOON::MeanLongitude($jce);
        $f = ASTROMOON::MoonsArgumentOfLatitude($jce);
        $m_prime = ASTROMOON::MeanAnomalyOfTheMoon($jce);
        $l = ASTROMOON::SummationOfPeriodicTermsOfTheMoon(ASTROTERMS::ml_terms, $jce)[0];
        $b = ASTROMOON::SummationOfPeriodicTermsOfTheMoon(ASTROTERMS::mb_terms, $jce)[0];

        $delta_l = 3958 * sin(deg2rad($a1)) + 318 * sin(deg2rad($a2)) + 1962 * sin(deg2rad($l_prime - $f));
        $delta_b = -2235 * sin(deg2rad($l_prime)) + 175 * sin(deg2rad($a1 - $f)) + 127 * sin(deg2rad($l_prime - $m_prime))
            + 382 * sin(deg2rad($a3)) + 175 * sin(deg2rad($a1 + $f)) - 115 * sin(deg2rad($l_prime + $m_prime));

        return array(ASTROMISC::LimitTo360Deg($l_prime + ($l + $delta_l) / 1000000), ASTROMISC::LimitTo360Deg(($b + $delta_b) / 1000000));
    }

    public static function MoonEarthDistance($jce)
{
	return 385000.56 + ASTROMOON::SummationOfPeriodicTermsOfTheMoon(ASTROTERMS::ml_terms, $jce)[1]/1000;
}

    
public static function MoonEquatorialHorizParallax($jce)
{
	return rad2deg(asin(6378.14/ASTROMOON::MoonEarthDistance($jce)));
}

public static function ApparentMoonLongitude(float $jce)
{
	return ASTROMOON::MoonLongitudeAndLatitude($jce)[0] + ASTROSUN::NutationInLongitude($jce);
}

    public static function GeocentricMoonRightAscension(float $jce): float
    {
        $moonLong = ASTROMOON::MoonLongitudeAndLatitude($jce)[0];
        $moonLat = ASTROMOON::MoonLongitudeAndLatitude($jce)[1];
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($jce);
        $a = rad2deg(
            atan2(
                sin(deg2rad($moonLong)) * cos(deg2rad($trueOblEcl)) - tan(deg2rad($moonLat)) * sin(deg2rad($trueOblEcl)),
                cos(deg2rad($moonLong))
            )
        );
        return ASTROMISC::LimitTo360Deg($a);
    }

    public static function GeocentricSunDeclination(float $jce): float
    {
        $moonLong = ASTROMOON::MoonLongitudeAndLatitude($jce)[0];
        $moonLat = ASTROMOON::MoonLongitudeAndLatitude($jce)[1];
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($jce);
        return rad2deg(
            asin(
                sin(deg2rad($moonLat)) * cos(deg2rad($trueOblEcl)) + cos(deg2rad($moonLat)) * sin(deg2rad($trueOblEcl)) * sin(deg2rad($moonLong))
            )
        );
    }


    public static function Phase(){

        $syn = 29.530588;

        $phase = 1;
        $now = time();
        $year = date("Y", $now);
        if($year < 1900) {
            $year += 1900;
        }
        if($year >= 2010){
            $vm = mktime(20,12,36,11,31,2009) / 86400;
            $now = $now / 86400;
            $diff = $now - $vm;
            $anz = $diff / $syn;
            $phase = round($anz,2);
            $phase = floor(($phase - floor($phase)) * 100);
            if($phase == 0){
                $phase = 100;
            }
        }
        return $phase;
    }

    public static function PhaseStr(){
        $phase = ASTROMOON::Phase();
        $text = "";
        if($phase == 0){
            $text = "Vollmond (2. Viertel)";
        }else if($phase < 25 or  ($phase > 25 and $phase < 50)){
            $text = "Abnehmender Mond";
        }else if($phase == 25) {
            $text = "Halbmond (3. Viertel)";
        }else if($phase == 50) {
            $ext = "Neumond (4. Viertel)";
        }else if(($phase > 50 and $phase < 75) or ($phase > 75 and $phase < 100)){
            $text = "Zunehmender Mond";
        }else if($phase == 75) {
            $text = "Halbmond (1. Viertel)";
        }
        return $text;
    }

    

}


class ASTROTERMS{


    const l_count = 6;
    const b_count = 2;
    const r_count = 5;
    const y_count = 63;
    const l_subcount = array(64, 34, 20, 7, 3, 1);
    const b_subcount = array(5, 2);
    const r_subcount = array(40, 10, 6, 2, 1);
    
    ///////////////////////////////////////////////////
    ///  Earth Periodic Terms
    ///////////////////////////////////////////////////

    const l_terms = array(
        array(
           array(175347046.0,0,0),
            array(3341656.0,4.6692568,6283.07585),
            array(34894.0,4.6261,12566.1517),
            array(3497.0,2.7441,5753.3849),
            array(3418.0,2.8289,3.5231),
            array(3136.0,3.6277,77713.7715),
            array(2676.0,4.4181,7860.4194),
            array(2343.0,6.1352,3930.2097),
            array(1324.0,0.7425,11506.7698),
            array(1273.0,2.0371,529.691),
            array(1199.0,1.1096,1577.3435),
            array(990,5.233,5884.927),
            array(902,2.045,26.298),
            array(857,3.508,398.149),
            array(780,1.179,5223.694),
            array(753,2.533,5507.553),
            array(505,4.583,18849.228),
            array(492,4.205,775.523),
            array(357,2.92,0.067),
            array(317,5.849,11790.629),
            array(284,1.899,796.298),
            array(271,0.315,10977.079),
            array(243,0.345,5486.778),
            array(206,4.806,2544.314),
            array(205,1.869,5573.143),
            array(202,2.458,6069.777),
            array(156,0.833,213.299),
            array(132,3.411,2942.463),
            array(126,1.083,20.775),
            array(115,0.645,0.98),
            array(103,0.636,4694.003),
            array(102,0.976,15720.839),
            array(102,4.267,7.114),
            array(99,6.21,2146.17),
            array(98,0.68,155.42),
            array(86,5.98,161000.69),
            array(85,1.3,6275.96),
            array(85,3.67,71430.7),
            array(80,1.81,17260.15),
            array(79,3.04,12036.46),
            array(75,1.76,5088.63),
            array(74,3.5,3154.69),
            array(74,4.68,801.82),
            array(70,0.83,9437.76),
            array(62,3.98,8827.39),
            array(61,1.82,7084.9),
            array(57,2.78,6286.6),
            array(56,4.39,14143.5),
            array(56,3.47,6279.55),
            array(52,0.19,12139.55),
            array(52,1.33,1748.02),
            array(51,0.28,5856.48),
            array(49,0.49,1194.45),
            array(41,5.37,8429.24),
            array(41,2.4,19651.05),
            array(39,6.17,10447.39),
            array(37,6.04,10213.29),
            array(37,2.57,1059.38),
            array(36,1.71,2352.87),
            array(36,1.78,6812.77),
            array(33,0.59,17789.85),
            array(30,0.44,83996.85),
            array(30,2.74,1349.87),
            array(25,3.16,4690.48)
        ),
        array(
            array(628331966747.0,0,0),
            array(206059.0,2.678235,6283.07585),
            array(4303.0,2.6351,12566.1517),
            array(425.0,1.59,3.523),
            array(119.0,5.796,26.298),
            array(109.0,2.966,1577.344),
            array(93,2.59,18849.23),
            array(72,1.14,529.69),
            array(68,1.87,398.15),
            array(67,4.41,5507.55),
            array(59,2.89,5223.69),
            array(56,2.17,155.42),
            array(45,0.4,796.3),
            array(36,0.47,775.52),
            array(29,2.65,7.11),
            array(21,5.34,0.98),
            array(19,1.85,5486.78),
            array(19,4.97,213.3),
            array(17,2.99,6275.96),
            array(16,0.03,2544.31),
            array(16,1.43,2146.17),
            array(15,1.21,10977.08),
            array(12,2.83,1748.02),
            array(12,3.26,5088.63),
            array(12,5.27,1194.45),
            array(12,2.08,4694),
            array(11,0.77,553.57),
            array(10,1.3,6286.6),
            array(10,4.24,1349.87),
            array(9,2.7,242.73),
            array(9,5.64,951.72),
            array(8,5.3,2352.87),
            array(6,2.65,9437.76),
            array(6,4.67,4690.48)
        ),
        array(
            array(52919.0,0,0),
            array(8720.0,1.0721,6283.0758),
            array(309.0,0.867,12566.152),
            array(27,0.05,3.52),
            array(16,5.19,26.3),
            array(16,3.68,155.42),
            array(10,0.76,18849.23),
            array(9,2.06,77713.77),
            array(7,0.83,775.52),
            array(5,4.66,1577.34),
            array(4,1.03,7.11),
            array(4,3.44,5573.14),
            array(3,5.14,796.3),
            array(3,6.05,5507.55),
            array(3,1.19,242.73),
            array(3,6.12,529.69),
            array(3,0.31,398.15),
            array(3,2.28,553.57),
            array(2,4.38,5223.69),
            array(2,3.75,0.98)
        ),
        array(
            array(289.0,5.844,6283.076),
            array(35,0,0),
            array(17,5.49,12566.15),
            array(3,5.2,155.42),
            array(1,4.72,3.52),
            array(1,5.3,18849.23),
            array(1,5.97,242.73)
        ),
        array(
            array(114.0,3.142,0),
            array(8,4.13,6283.08),
            array(1,3.84,12566.15)
        ),
        array(
            array(1,3.14,0)
        )
    );

    const b_terms = array(
        array(
            array(280.0,3.199,84334.662),
            array(102.0,5.422,5507.553),
            array(80,3.88,5223.69),
            array(44,3.7,2352.87),
            array(32,4,1577.34)
        ),
        array(
            array(9,3.9,5507.55),
            array(6,1.73,5223.69)
        )
    );

    const r_terms = array(
        array(
            array(100013989.0,0,0),
            array(1670700.0,3.0984635,6283.07585),
            array(13956.0,3.05525,12566.1517),
            array(3084.0,5.1985,77713.7715),
            array(1628.0,1.1739,5753.3849),
            array(1576.0,2.8469,7860.4194),
            array(925.0,5.453,11506.77),
            array(542.0,4.564,3930.21),
            array(472.0,3.661,5884.927),
            array(346.0,0.964,5507.553),
            array(329.0,5.9,5223.694),
            array(307.0,0.299,5573.143),
            array(243.0,4.273,11790.629),
            array(212.0,5.847,1577.344),
            array(186.0,5.022,10977.079),
            array(175.0,3.012,18849.228),
            array(110.0,5.055,5486.778),
            array(98,0.89,6069.78),
            array(86,5.69,15720.84),
            array(86,1.27,161000.69),
            array(65,0.27,17260.15),
            array(63,0.92,529.69),
            array(57,2.01,83996.85),
            array(56,5.24,71430.7),
            array(49,3.25,2544.31),
            array(47,2.58,775.52),
            array(45,5.54,9437.76),
            array(43,6.01,6275.96),
            array(39,5.36,4694),
            array(38,2.39,8827.39),
            array(37,0.83,19651.05),
            array(37,4.9,12139.55),
            array(36,1.67,12036.46),
            array(35,1.84,2942.46),
            array(33,0.24,7084.9),
            array(32,0.18,5088.63),
            array(32,1.78,398.15),
            array(28,1.21,6286.6),
            array(28,1.9,6279.55),
            array(26,4.59,10447.39)
        ),
        array(
            array(103019.0,1.10749,6283.07585),
            array(1721.0,1.0644,12566.1517),
            array(702.0,3.142,0),
            array(32,1.02,18849.23),
            array(31,2.84,5507.55),
            array(25,1.32,5223.69),
            array(18,1.42,1577.34),
            array(10,5.91,10977.08),
            array(9,1.42,6275.96),
            array(9,0.27,5486.78)
        ),
        array(
            array(4359.0,5.7846,6283.0758),
            array(124.0,5.579,12566.152),
            array(12,3.14,0),
            array(9,3.63,77713.77),
            array(6,1.87,5573.14),
            array(3,5.47,18849.23)
        ),
        array(
            array(145.0,4.273,6283.076),
            array(7,3.92,12566.15)
        ),
        array(
            array(4,2.56,6283.08)
        )
    );


    ////////////////////////////////////////////////////////////////
    ///  Periodic Terms for the nutation in longitude and obliquity
    ////////////////////////////////////////////////////////////////

    const y_terms = array(
        array(0, 0, 0, 0, 1),
        array(-2, 0, 0, 2, 2),
        array(0, 0, 0, 2, 2),
        array(0, 0, 0, 0, 2),
        array(0, 1, 0, 0, 0),
        array(0, 0, 1, 0, 0),
        array(-2, 1, 0, 2, 2),
        array(0, 0, 0, 2, 1),
        array(0, 0, 1, 2, 2),
        array(-2, -1, 0, 2, 2),
        array(-2, 0, 1, 0, 0),
        array(-2, 0, 0, 2, 1),
        array(0, 0, -1, 2, 2),
        array(2, 0, 0, 0, 0),
        array(0, 0, 1, 0, 1),
        array(2, 0, -1, 2, 2),
        array(0, 0, -1, 0, 1),
        array(0, 0, 1, 2, 1),
        array(-2, 0, 2, 0, 0),
        array(0, 0, -2, 2, 1),
        array(2, 0, 0, 2, 2),
        array(0, 0, 2, 2, 2),
        array(0, 0, 2, 0, 0),
        array(-2, 0, 1, 2, 2),
        array(0, 0, 0, 2, 0),
        array(-2, 0, 0, 2, 0),
        array(0, 0, -1, 2, 1),
        array(0, 2, 0, 0, 0),
        array(2, 0, -1, 0, 1),
        array(-2, 2, 0, 2, 2),
        array(0, 1, 0, 0, 1),
        array(-2, 0, 1, 0, 1),
        array(0, -1, 0, 0, 1),
        array(0, 0, 2, -2, 0),
        array(2, 0, -1, 2, 1),
        array(2, 0, 1, 2, 2),
        array(0, 1, 0, 2, 2),
        array(-2, 1, 1, 0, 0),
        array(0, -1, 0, 2, 2),
        array(2, 0, 0, 2, 1),
        array(2, 0, 1, 0, 0),
        array(-2, 0, 2, 2, 2),
        array(-2, 0, 1, 2, 1),
        array(2, 0, -2, 0, 1),
        array(2, 0, 0, 0, 1),
        array(0, -1, 1, 0, 0),
        array(-2, -1, 0, 2, 1),
        array(-2, 0, 0, 0, 1),
        array(0, 0, 2, 2, 1),
        array(-2, 0, 2, 0, 1),
        array(-2, 1, 0, 2, 1),
        array(0, 0, 1, -2, 0),
        array(-1, 0, 1, 0, 0),
        array(-2, 1, 0, 0, 0),
        array(1, 0, 0, 0, 0),
        array(0, 0, 1, 2, 0),
        array(0, 0, -2, 2, 2),
        array(-1, -1, 1, 0, 0),
        array(0, 1, 1, 0, 0),
        array(0, -1, 1, 2, 2),
        array(2, -1, -1, 2, 2),
        array(0, 0, 3, 2, 2),
        array(2, -1, 0, 2, 2),
    );

    const pe_terms = array(
        array(-171996, -174.2, 92025, 8.9),
        array(-13187, -1.6, 5736, -3.1),
        array(-2274, -0.2, 977, -0.5),
        array(2062, 0.2, -895, 0.5),
        array(1426, -3.4, 54, -0.1),
        array(712, 0.1, -7, 0),
        array(-517, 1.2, 224, -0.6),
        array(-386, -0.4, 200, 0),
        array(-301, 0, 129, -0.1),
        array(217, -0.5, -95, 0.3),
        array(-158, 0, 0, 0),
        array(129, 0.1, -70, 0),
        array(123, 0, -53, 0),
        array(63, 0, 0, 0),
        array(63, 0.1, -33, 0),
        array(-59, 0, 26, 0),
        array(-58, -0.1, 32, 0),
        array(-51, 0, 27, 0),
        array(48, 0, 0, 0),
        array(46, 0, -24, 0),
        array(-38, 0, 16, 0),
        array(-31, 0, 13, 0),
        array(29, 0, 0, 0),
        array(29, 0, -12, 0),
        array(26, 0, 0, 0),
        array(-22, 0, 0, 0),
        array(21, 0, -10, 0),
        array(17, -0.1, 0, 0),
        array(16, 0, -8, 0),
        array(-16, 0.1, 7, 0),
        array(-15, 0, 9, 0),
        array(-13, 0, 7, 0),
        array(-12, 0, 6, 0),
        array(11, 0, 0, 0),
        array(-10, 0, 5, 0),
        array(-8, 0, 3, 0),
        array(7, 0, -3, 0),
        array(-7, 0, 0, 0),
        array(-7, 0, 3, 0),
        array(-7, 0, 3, 0),
        array(6, 0, 0, 0),
        array(6, 0, -3, 0),
        array(6, 0, -3, 0),
        array(-6, 0, 3, 0),
        array(-6, 0, 3, 0),
        array(5, 0, 0, 0),
        array(-5, 0, 3, 0),
        array(-5, 0, 3, 0),
        array(-5, 0, 3, 0),
        array(4, 0, 0, 0),
        array(4, 0, 0, 0),
        array(4, 0, 0, 0),
        array(-4, 0, 0, 0),
        array(-4, 0, 0, 0),
        array(-4, 0, 0, 0),
        array(3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
        array(-3, 0, 0, 0),
    );

    ///////////////////////////////////////////////////
    ///  Moon Periodic Terms
    ///////////////////////////////////////////////////
    const ml_terms = array(
        array(0, 0, 1, 0, 6288774, -20905355),
        array(2, 0, -1, 0, 1274027, -3699111),
        array(2, 0, 0, 0, 658314, -2955968),
        array(0, 0, 2, 0, 213618, -569925),
        array(0, 1, 0, 0, -185116, 48888),
        array(0, 0, 0, 2, -114332, -3149),
        array(2, 0, -2, 0, 58793, 246158),
        array(2, -1, -1, 0, 57066, -152138),
        array(2, 0, 1, 0, 53322, -170733),
        array(2, -1, 0, 0, 45758, -204586),
        array(0, 1, -1, 0, -40923, -129620),
        array(1, 0, 0, 0, -34720, 108743),
        array(0, 1, 1, 0, -30383, 104755),
        array(2, 0, 0, -2, 15327, 10321),
        array(0, 0, 1, 2, -12528, 0),
        array(0, 0, 1, -2, 10980, 79661),
        array(4, 0, -1, 0, 10675, -34782),
        array(0, 0, 3, 0, 10034, -23210),
        array(4, 0, -2, 0, 8548, -21636),
        array(2, 1, -1, 0, -7888, 24208),
        array(2, 1, 0, 0, -6766, 30824),
        array(1, 0, -1, 0, -5163, -8379),
        array(1, 1, 0, 0, 4987, -16675),
        array(2, -1, 1, 0, 4036, -12831),
        array(2, 0, 2, 0, 3994, -10445),
        array(4, 0, 0, 0, 3861, -11650),
        array(2, 0, -3, 0, 3665, 14403),
        array(0, 1, -2, 0, -2689, -7003),
        array(2, 0, -1, 2, -2602, 0),
        array(2, -1, -2, 0, 2390, 10056),
        array(1, 0, 1, 0, -2348, 6322),
        array(2, -2, 0, 0, 2236, -9884),
        array(0, 1, 2, 0, -2120, 5751),
        array(0, 2, 0, 0, -2069, 0),
        array(2, -2, -1, 0, 2048, -4950),
        array(2, 0, 1, -2, -1773, 4130),
        array(2, 0, 0, 2, -1595, 0),
        array(4, -1, -1, 0, 1215, -3958),
        array(0, 0, 2, 2, -1110, 0),
        array(3, 0, -1, 0, -892, 3258),
        array(2, 1, 1, 0, -810, 2616),
        array(4, -1, -2, 0, 759, -1897),
        array(0, 2, -1, 0, -713, -2117),
        array(2, 2, -1, 0, -700, 2354),
        array(2, 1, -2, 0, 691, 0),
        array(2, -1, 0, -2, 596, 0),
        array(4, 0, 1, 0, 549, -1423),
        array(0, 0, 4, 0, 537, -1117),
        array(4, -1, 0, 0, 520, -1571),
        array(1, 0, -2, 0, -487, -1739),
        array(2, 1, 0, -2, -399, 0),
        array(0, 0, 2, -2, -381, -4421),
        array(1, 1, 1, 0, 351, 0),
        array(3, 0, -2, 0, -340, 0),
        array(4, 0, -3, 0, 330, 0),
        array(2, -1, 2, 0, 327, 0),
        array(0, 2, 1, 0, -323, 1165),
        array(1, 1, -1, 0, 299, 0),
        array(2, 0, 3, 0, 294, 0),
        array(2, 0, -1, -2, 0, 8752)
    );

    const mb_terms = array(
        array(0, 0, 0, 1, 5128122, 0),
        array(0, 0, 1, 1, 280602, 0),
        array(0, 0, 1, -1, 277693, 0),
        array(2, 0, 0, -1, 173237, 0),
        array(2, 0, -1, 1, 55413, 0),
        array(2, 0, -1, -1, 46271, 0),
        array(2, 0, 0, 1, 32573, 0),
        array(0, 0, 2, 1, 17198, 0),
        array(2, 0, 1, -1, 9266, 0),
        array(0, 0, 2, -1, 8822, 0),
        array(2, -1, 0, -1, 8216, 0),
        array(2, 0, -2, -1, 4324, 0),
        array(2, 0, 1, 1, 4200, 0),
        array(2, 1, 0, -1, -3359, 0),
        array(2, -1, -1, 1, 2463, 0),
        array(2, -1, 0, 1, 2211, 0),
        array(2, -1, -1, -1, 2065, 0),
        array(0, 1, -1, -1, -1870, 0),
        array(4, 0, -1, -1, 1828, 0),
        array(0, 1, 0, 1, -1794, 0),
        array(0, 0, 0, 3, -1749, 0),
        array(0, 1, -1, 1, -1565, 0),
        array(1, 0, 0, 1, -1491, 0),
        array(0, 1, 1, 1, -1475, 0),
        array(0, 1, 1, -1, -1410, 0),
        array(0, 1, 0, -1, -1344, 0),
        array(1, 0, 0, -1, -1335, 0),
        array(0, 0, 3, 1, 1107, 0),
        array(4, 0, 0, -1, 1021, 0),
        array(4, 0, -1, 1, 833, 0),
        array(0, 0, 1, -3, 777, 0),
        array(4, 0, -2, 1, 671, 0),
        array(2, 0, 0, -3, 607, 0),
        array(2, 0, 2, -1, 596, 0),
        array(2, -1, 1, -1, 491, 0),
        array(2, 0, -2, 1, -451, 0),
        array(0, 0, 3, -1, 439, 0),
        array(2, 0, 2, 1, 422, 0),
        array(2, 0, -3, -1, 421, 0),
        array(2, 1, -1, 1, -366, 0),
        array(2, 1, 0, 1, -351, 0),
        array(4, 0, 0, 1, 331, 0),
        array(2, -1, 1, 1, 315, 0),
        array(2, -2, 0, -1, 302, 0),
        array(0, 0, 1, 3, -283, 0),
        array(2, 1, 1, -1, -229, 0),
        array(1, 1, 0, -1, 223, 0),
        array(1, 1, 0, 1, 223, 0),
        array(0, 1, -2, -1, -220, 0),
        array(2, 1, -1, -1, -220, 0),
        array(1, 0, 1, 1, -185, 0),
        array(2, -1, -2, -1, 181, 0),
        array(0, 1, 2, 1, -177, 0),
        array(4, 0, -2, -1, 176, 0),
        array(4, -1, -1, -1, 166, 0),
        array(1, 0, 1, -1, -164, 0),
        array(4, 0, 1, -1, 132, 0),
        array(1, 0, -1, -1, -119, 0),
        array(4, -1, 0, -1, 115, 0),
        array(2, -2, 0, 1, 107, 0)
    );


}

class ASTROMISC{

    public static function LimitTo360Deg(float|int $angle):float{
        $limited = 0.0;
        $angle /= 360.0;
        $limited = 360.0 * ($angle - floor($angle));
        if ($limited < 0) $limited += 360.0;

        return $limited;
    }

    public static function LimitTo180Deg(float|int $angle):float
    {
        $limited = 0.0;
        $angle /= 180.0;
        $limited = 180.0 * ($angle - floor($angle));
        if ($limited < 0)
            $limited += 180.0;

        return $limited;
    }

    public static function LimitTo180DegPM(float|int $angle): float
    {
        $limited = 0.0;

        $angle /= 360.0;
        $limited = 360.0 * ($angle - floor($angle));
        if ($limited < -180.0) {
            $limited += 360.0;
        } else if ($limited > 180.0) {
            $limited -= 360.0;
        }
        return $limited;
    }

    public static function LimitToDesigDeg(float|int $angle, float|int $limit):float
    {
        $limited = 0.0;
        $angle /= $limit;
        $limited = $limit * ($angle - floor($angle));
        if ($limited < 0)
            $limited += $limit;

        return $limited;
    }

    public static function LimitZeroToOne(float $value):float
    {
        $limited = $value - floor($value);
        if ($limited < 0)
            $limited += 1.0;

        return $limited;
    }

    public static function ThirdOrderPolynomial(float $a, float $b, float $c, float $d, float $x): float
    {
        return (($a * $x + $b) * $x + $c) * $x + $d;
    }

    public static function FourthOrderPolynomial(float $a, float $b, float $c, float $d, float $e, float $x): float
    {
        return ((($a * $x + $b) * $x + $c) * $x + $d) * $x + $e;
    }

    public static function LimitTo20Minutes(float $minutes):float
    {
        $limited = $minutes;

        if ($limited < -20.0) {
            $limited += 1440.0;
        } elseif ($limited > 20.0) {
            $limited -= 1440.0;
        }

    return $limited;
    }

    public static function DayFracToHr(float $dayFrac)
    {
        return 24.0 * ASTROMISC::LimitZeroToOne($dayFrac);
    }
}

?>