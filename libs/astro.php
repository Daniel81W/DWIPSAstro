<?php

//TODO Mond

class ASTROGEN{

    // Julian Date Functions

    /**
     * Computes the Julian Date for the current time.
     * @return float Current Julian Date
     */
    public static function JulianDay():float{
       $date = new DateTime();
       return ASTROGEN::JulianDayFromTimestamp($date->getTimestamp());
    }

    /**
     * Computes the Julian Date for the given timestamp.
     * @param int $timestamp Timestamp the Julian Date is to compute for
     * @return float Julian Date for the given timestamp
     */
    public static function JulianDayFromTimestamp(int $timestamp){

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
    public static function JulianDayFromDateTime(int $year, int $month, int $day, int $hour = 0, int $minute = 0, int $second = 0){
       $date = mktime($hour, $minute, $second , $month, $day, $year);
       return ASTROGEN::JulianDayFromTimestamp($date);
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

class ASTROSUN{
    /**
     * Astronomical unit (mean distance earth - sun) in m
     */
    const AU = 149597870700;

    //
    public static function Sunrise($year, $month, $day, $deltaT, $lat, $long){
        $timestamp_zero_ut = mktime(0, 0, 0, $month, $day, $year);

        $JD_ZERO_UT = ASTROGEN::JulianDayFromTimestamp($timestamp_zero_ut);
        $JC_ZERO_UT = ASTROGEN::JulianCentury($JD_ZERO_UT);
        $JM_ZERO_UT = ASTROGEN::JulianMillennium($JC_ZERO_UT);

        $JD_ZERO_TT = ASTROGEN::JulianDayFromTimestamp($timestamp_zero_ut + $deltaT);
        $JC_ZERO_TT = ASTROGEN::JulianDay($JD_ZERO_TT);
        $JM_ZERO_TT = ASTROGEN::JulianMillennium($JC_ZERO_TT);

        $v = ASTROSUN::v($JD_ZERO_UT);
        $am1 = ASTROSUN::a($JD_ZERO_TT - 1);
        $a0 = ASTROSUN::a($JD_ZERO_TT);
        $ap1 = ASTROSUN::a($JD_ZERO_TT + 1);
        $dm1 = ASTROSUN::d($JD_ZERO_TT - 1);
        $d0 = ASTROSUN::d($JD_ZERO_TT);
        $dp1 = ASTROSUN::d($JD_ZERO_TT + 1);

        $m0 = ($a0 - $long - $v) / 360;

        $H0 = rad2deg(
            acos(
                (sin(deg2rad(-0.8333)) - sin(deg2rad($lat)) * sin(deg2rad($d0))) / (cos(deg2rad($lat)) * cos(deg2rad($d0)))
            )
        );
        $H0 = ASTROMISC::LimitToInterval($H0, 180);

        $m1 = $m0 - $H0 / 360;
        $m2 = $m0 + $H0 / 360;

        $m0 = ASTROMISC::LimitToInterval($m0, 1);
        $m1 = ASTROMISC::LimitToInterval($m1, 1);
        $m2 = ASTROMISC::LimitToInterval($m2, 1);

        $v0 = $v + 360.985647 * $m0;
        $v1 = $v + 360.985647 * $m1;
        $v2 = $v + 360.985647 * $m2;

        $n0 = $m0 + $deltaT / 86400;
        $n1 = $m1 + $deltaT / 86400;
        $n2 = $m2 + $deltaT / 86400;

        $aa = $a0 - $am1;
        if ($aa > 2 ) {
            $aa = ASTROMISC::LimitToInterval($aa, 1);
        }
        $bb = $ap1 - $a0;
        if ($bb > 2) {
            $bb = ASTROMISC::LimitToInterval($bb, 1);
        }
        $cc = $bb - $aa;

        $a0s = $a0 + $n0 * ($aa + $bb + $cc * $n0) / 2;
        $a1s = $a0 + $n1 * ($aa + $bb + $cc * $n1) / 2;
        $a2s = $a0 + $n2 * ($aa + $bb + $cc * $n2) / 2;

        $as = $d0 - $dm1;
        if ($as > 2) {
            $as = ASTROMISC::LimitToInterval($as, 1);
        }
        $bs = $dp1 - $d0;
        if ($bs > 2) {
            $bs = ASTROMISC::LimitToInterval($bs, 1);
        }
        $cs = $bs - $as;

        $d0s = $d0 + $n0 * ($as + $bs + $cs * $n0) / 2;
        $d1s = $d0 + $n1 * ($as + $bs + $cs * $n1) / 2;
        $d1s = $d0 + $n2 * ($as + $bs + $cs * $n2) / 2;

        $H0s = $v0 + $long - $a0s;
        $H1s = $v1 + $long - $a1s;
        $H2s = $v2 + $long - $a2s;

        $H0s = $H0s / abs($H0s) * ASTROMISC::LimitToInterval(abs($H0s) ,360);
        if(abs($H0s) > 180){
            $H0s = $H0s - $H0s / abs($H0s) * 360;
        }
        $H1s = $H1s / abs($H1s) * ASTROMISC::LimitToInterval(abs($H1s), 360);
        if (abs($H1s) > 180) {
            $H1s= $H1s - $H1s / abs($H1s) * 360;
        }
        $H2s = $H2s / abs($H2s) * ASTROMISC::LimitToInterval(abs($H2s), 360);
        if (abs($H2s) > 180) {
            $H2s = $H2s - $H2s / abs($H2s) * 360;
        }

        $hh0 = rad2deg(asin(
                sin(deg2rad($lat)) * sin(deg2rad($d0s)) +
                cos(deg2rad($lat)) * cos(deg2rad($d0s)) * cos(deg2rad($H0s))
            ));
        $hh1 = rad2deg(
            asin(
                sin(deg2rad($lat)) * sin(deg2rad($d1s)) +
                cos(deg2rad($lat)) * cos(deg2rad($d1s)) * cos(deg2rad($H1s))
            )
        );
        $hh2 = rad2deg(
            asin(
                sin(deg2rad($lat)) * sin(deg2rad($d2s)) +
                cos(deg2rad($lat)) * cos(deg2rad($d2s)) * cos(deg2rad($H2s))
            )
        );

        $t = $m0 - $H0s / 360;
        return $hh0 . " - " . $hh1 . " - " . $hh2 . " - " . $t;
    }

    public static function v($julianDay){
        $jc = ASTROGEN::JulianCentury($julianDay);
        $jm = ASTROGEN::JulianMillennium($jc);
        $nutLong = ASTROSUN::NutationInLongitude($jc);
        $nutObl = ASTROSUN::NutationInObliquity($jc);
        $meanObl = ASTROSUN::MeanObliquityOfTheEcliptic($jm);
        $trueOblEcl = ASTROSUN::TrueObliquityOfTheEcliptic($meanObl, $nutObl);
        return ASTROSUN::ApparentSiderealTimeAtGreenwich($julianDay, $jc, $nutLong, $trueOblEcl);
    }

    public static function a($julianDay){
        $jc = ASTROGEN::JulianCentury($julianDay);
        $jm = ASTROGEN::JulianMillennium($jc);
        
        $nutLong = ASTROSUN::NutationInLongitude($jc);
        $HeliocentricLongitude = ASTROSUN::HeliocentricLongitudeDEG($jm);
        $HeliocentricLatitude = ASTROSUN::HeliocentricLatitude($jm);
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

    public static function d($julianDay){
        $jc = ASTROGEN::JulianCentury($julianDay);
        $jm = ASTROGEN::JulianMillennium($jc);

        $HeliocentricLongitude = ASTROSUN::HeliocentricLongitudeDEG($jm);
        $earthRadVec = ASTROSUN::EarthRadiusVector($jm);
        $HeliocentricLatitude = ASTROSUN::HeliocentricLatitude($jm);
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

    //
    /**
     * Summary of HeliocentricLongitudeRAD
     * @param mixed $julianMillenium 
     * @return float
     */
    public static function HeliocentricLongitudeRAD($julianMillenium){
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
        return ASTROMISC::LimitTo360($l);
    }

    public static function HeliocentricLatitude($julianMillenium)
    {
        return rad2deg((ASTROSUN::B0($julianMillenium)
            + ASTROSUN::B1($julianMillenium) * pow($julianMillenium, 1)) / pow(10, 8));
    }

    public static function EarthRadiusVector($julianMillenium)
    {
        return (ASTROSUN::R0($julianMillenium)
            + ASTROSUN::R1($julianMillenium) * pow($julianMillenium, 1)
            + ASTROSUN::R2($julianMillenium) * pow($julianMillenium, 2)
            + ASTROSUN::R3($julianMillenium) * pow($julianMillenium, 3)
            + ASTROSUN::R4($julianMillenium) * pow($julianMillenium, 4)) / pow(10, 8);
    }

    public static function GeocentricLongitude($HeliocentricLongitude){
        $glong = $HeliocentricLongitude + 180;
        return ASTROMISC::LimitTo360($glong);
    }

    public static function GeocentricLatitude($HeliocentricLatitude){
        return -1 * $HeliocentricLatitude;
    }
    
    //Nutuation
    public static function MeanElongationOfTheMoon($julianCentury){
        return 297.85036 + 445267.111480 * $julianCentury - 0.0019142 * pow($julianCentury, 2) + pow($julianCentury, 3)/189474;
    }
    
    public static function MeanAnomalyOfTheSun($julianCentury){
        return 357.52772 + 35999.050340 * $julianCentury - 0.0001603 * pow($julianCentury, 2) - pow($julianCentury, 3)/300000;
    }
    
    public static function MeanAnomalyOfTheMoon($julianCentury){
        return 134.96298 + 477198.867398 * $julianCentury + 0.0086972 * pow($julianCentury, 2) + pow($julianCentury, 3)/56250;
    }

    public static function MoonsArgumentOfLatitude($julianCentury){
        return 93.27191 + 483202.017538 * $julianCentury - 0.0036825 * pow($julianCentury, 2) + pow($julianCentury, 3)/327270;
    }

    public static function LongitudeOfTheAscendingNodeOfTheMoon($julianCentury){
        return  125.04452 - 1934.136261 * $julianCentury + 0.0020708 * pow($julianCentury, 2) + pow($julianCentury, 3)/450000;
    }
    
    public static function NutationInLongitude($julianCentury){
        $psi = array();
        $terms = ASTROSUN::PeriodicTermsForTheNutation();
        for($i = 0; $i < count($terms); $i++){
            $sumterm = 0;
            for($j = 0; $j < 5; $j++){
                $sumterm += ASTROSUN::X($j, $julianCentury) * $terms[$i]['y'][$j];
            }
            $psi[$i] = ($terms[$i]['a']+$terms[$i]['b']*$julianCentury)*sin($sumterm);    
        }
        $sum = 0;
        for($i = 0; $i < count($psi);$i++){
            $sum += $psi[$i];
        }
        return $sum/36000000;
    }
    
    public static function NutationInObliquity($julianCentury){
        $eps = array();
        $terms = ASTROSUN::PeriodicTermsForTheNutation();
        for($i = 0; $i < count($terms); $i++){
            $sumterm = 0;
            for($j = 0; $j < 5; $j++){
                $sumterm += ASTROSUN::X($j, $julianCentury) * $terms[$i]['y'][$j];
            }
            $eps[$i] = ($terms[$i]['c']+$terms[$i]['d']*$julianCentury)*cos($sumterm);    
        }
        $sum = 0;
        for($i = 0; $i < count($eps);$i++){
            $sum += $eps[$i];
        }
        return $sum/36000000;
    }
    
    //mean obliquity of the ecliptic
    public static function MeanObliquityOfTheEcliptic($julianMillenium){
        $u = $julianMillenium / 10;
        return 84381.448 - 4680.93 * pow($u, 1) - 1.55 * pow($u, 2) + 1999.25 * pow($u, 3) - 51.38 * pow($u, 4) - 249.67 * pow($u, 5) - 39.05 * pow($u, 6) + 7.12 * pow($u, 7) + 27.87 * pow($u, 8) + 5.79 * pow($u, 9) + 2.45 * pow($u, 10);
    }
    
    public static function TrueObliquityOfTheEcliptic($meanObl, $nutObl){
        return $meanObl / 3600 + $nutObl;
    }

    public static function AberrationCorrection($earthRadVec){
        return -20.4898 / (3600 * $earthRadVec);
    }

    public static function ApparentSunLongitude($geoCentLong, $nutLong, $abCorr){
        return $geoCentLong + $nutLong + $abCorr;
    }

    public static function ApparentSiderealTimeAtGreenwich($julianDate, $julianCentury, $nutationLong, $trueOblEcl){
        $v0 = 280.46061837 + 360.98564736629 * ($julianDate - 2451545) + 0.000387933 * pow($julianCentury, 2) - pow($julianCentury, 3) / 38710000;
        $mst = ASTROMISC::LimitTo360($v0);
        return $mst + $nutationLong * cos(deg2rad($trueOblEcl));
    }

    public static function GeocentricSunRightAscension($appSunLong, $trueOblEcl, $geoCentLat){
        $a = rad2deg(
            atan2(
                sin(deg2rad($appSunLong)) * cos(deg2rad($trueOblEcl)) - tan(deg2rad($geoCentLat)) * sin(deg2rad($trueOblEcl)),
                cos(deg2rad($appSunLong))
            )
        );
        return ASTROMISC::LimitTo360($a);
    }

    public static function GeocentricSunDeclination($geoCentLat, $trueOblEcl, $appSunLong){
        return rad2deg(
            asin(
                sin(deg2rad($geoCentLat)) * cos(deg2rad($trueOblEcl)) + cos(deg2rad($geoCentLat)) * sin(deg2rad($trueOblEcl)) * sin(deg2rad($appSunLong)),
            )
        );//TODO Genauigkeit
    }
    
    public static function LocalHourAngle($appSidTimeGreenwich, $longitude , $geoSunRAsc){
        return $appSidTimeGreenwich + $longitude - $geoSunRAsc;
    }
     
    public static function DeltaA($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec){
        $s = 8.794 / (3600 * $earthRadVec);

        $u = atan(0.99664719 * tan(deg2rad($lat)));

        $x = cos($u) + $elev / 6378140 * cos(deg2rad($lat));

        $y = 0.99664719 * sin($u) + $elev / 6378140 * sin(deg2rad($lat));

        $da = atan2(-1 * $x * sin($s) * sin(deg2rad($locHourAngle)),  cos(deg2rad($geoSunDec)) - $x * sin($s) * cos(deg2rad($locHourAngle)));

        return $da;
    }

    public static function TopocentricSunRightAscension($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec, $geoSunRAsc)
    {
       return $geoSunRAsc - ASTROSUN::DeltaA($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec);
    }

    public static function TopocentricSunDeclination($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec, $geoSunRAsc)
    {
        $s = 8.794 / (3600 * $earthRadVec);

        $u = atan(0.99664719 * tan(deg2rad($lat)));

        $x = cos($u) + $elev / 6378140 * cos(deg2rad($lat));

        $y = 0.99664719 * sin($u) + $elev / 6378140 * sin(deg2rad($lat));

        return rad2deg(
            atan2(
                (sin(deg2rad($geoSunDec)) - $y * sin($s)) * cos(ASTROSUN::DeltaA($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec)),
                cos(deg2rad($geoSunDec)) -$x * sin($s) * cos($locHourAngle)
            )
        ); 
    }

    public static function TopocentricLocalHourAngle($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec, $geoSunRAsc){
        return $locHourAngle - ASTROSUN::DeltaA($earthRadVec, $lat, $elev, $locHourAngle, $geoSunDec);
    }

    public static function TopocentricZenithAngle($lat, $geoSunDec, $topoHourAngle, $press, $temp){
        $e0 = rad2deg(asin( sin(deg2rad($lat)) * sin(deg2rad($geoSunDec)) + cos(deg2rad($lat)) * cos(deg2rad($geoSunDec)) * cos(deg2rad($topoHourAngle))));

        $de = $press / 1010 * 283 / (273 + $temp) * 1.02 / (60 * tan(deg2rad($e0+ 10.3 / ($e0 +5.11))));

        return 90 - ($e0 + $de);
        
    }

    public static function TopocentricAzimuthAngle($lat, $topoSunDec, $topoHourAngle)
    {
        $t = rad2deg(atan2(
            sin(deg2rad($topoHourAngle)),
            cos(deg2rad($topoHourAngle)) * sin(deg2rad($lat)) - tan(deg2rad($topoSunDec)) * cos(deg2rad($lat))
        ));
        $t = ASTROMISC::LimitTo360($t);

        return ASTROMISC::LimitTo360($t + 180);
    }

    public static function EqOfTime($julianMillennium, $geoSunRAsc, $nutLong, $trueOblEcl){
        $m = 280.4664567 + 360007.6982779 * $julianMillennium + 0.03032028 * pow($julianMillennium, 2) + pow($julianMillennium, 3) / 49931 - pow($julianMillennium, 4) / 15300 - pow($julianMillennium, 5) / 2000000;
        $m = ASTROMISC::LimitTo360($m);

        $E = $m - 0.0057183 - $geoSunRAsc + $nutLong * cos(deg2rad($trueOblEcl));
        $E *= 4;
        if($E)
        return $E;
        //TODO LIMIT Eq to 20 min
    }

    // Hilfsfunktionen
    public static function X($i, $julianCentury){
        switch ($i){
            case 0:
                return ASTROSUN::MeanElongationOfTheMoon($julianCentury);
            case 1:
                return ASTROSUN::MeanAnomalyOfTheSun($julianCentury);
            case 2:
                return ASTROSUN::MeanAnomalyOfTheMoon($julianCentury);
            case 3:
                return ASTROSUN::MoonsArgumentOfLatitude($julianCentury);
            case 4:
                return ASTROSUN::LongitudeOfTheAscendingNodeOfTheMoon($julianCentury);
            default:
                return 0;
        }
    }

    public static function L0($julianMillenium){
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
    
    /**
     * 
     */
    public static function MeanLongitude(float $julianCentury){
        return fmod( (280.46646 + $julianCentury * ( 36000.76983 + $julianCentury * 0.0003032 )) , 360);
    }

    /**
     * Mittlere Anomalie der Sonne
     */
    public static function MeanAnomaly(float $julianCentury){
        return 357.52911 + $julianCentury * (35999.05029 - 0.0001537 * $julianCentury);
    }

    /**
     * 
     */
    public static function EccentEarthOrbit(float $julianCentury){
        return 0.016708634 - $julianCentury * (0.000042037 + 0.0000001267 * $julianCentury);
    }
    
    /**
     * 
     */
    public static function SunEqOfCtr(float $julianCentury){
        $meanAnom = ASTROSUN::MeanAnomaly($julianCentury);
        return sin( deg2rad($meanAnom) ) * ( 1.914602 - $julianCentury * ( 0.004817 + 0.000014 * $julianCentury ) ) + sin( deg2rad( 2 * $meanAnom ) ) * ( 0.019993 - 0.000101 * $julianCentury ) + sin( deg2rad( 3 * $meanAnom ) ) * 0.000289;
    }

    /**
     * 
     */
    public static function EclipticLongitude(float $julianCentury){
        return ASTROSUN::MeanLongitude($julianCentury) + ASTROSUN::SunEqOfCtr( $julianCentury);
    }

    /**
     * 
     */
    public static function TrueAnomalySun(float $julianCentury){
        return ASTROSUN::MeanAnomaly($julianCentury) + ASTROSUN::SunEqOfCtr($julianCentury);
    }

    /**
     * 
     */
    public static function SunRadVector(float $julianCentury){
        $eeo = ASTROSUN::EccentEarthOrbit($julianCentury);
        return ( 1.000001018 * ( 1 - $eeo * $eeo ) ) / ( 1 + $eeo * cos( deg2rad( ASTROSUN::TrueAnomalySun($julianCentury) ) ) );
    }

    /**
     * 
     */
    public static function SunAppLong(float $julianCentury){
        return ASTROSUN::EclipticLongitude($julianCentury) - 0.00569 - 0.00478 * sin( deg2rad( 125.04 - 1934.136 * $julianCentury ) );
    }

    /**
     * Mittlere Schiefe der Ekliptik (Achsneigung der Erde)
     * @param float $julianCentury Das Julianische Jahrhundert
     */
    public static function MeanObliquityOfEcliptic(float $julianCentury):float{
        return 23 + ( 26 + ( ( 21.448 - $julianCentury * ( 46.815 + $julianCentury * ( 0.00059 - $julianCentury * 0.001813 ) ) ) ) / 60 ) / 60;
    }

    /**
     * 
     */
    public static function ObliqCorrected(float $julianCentury){
        return ASTROSUN::MeanObliquityOfEcliptic($julianCentury) + 0.00256 * cos( deg2rad( 125.04 - 1934.136 * $julianCentury ) );
    }

    /**
     * 
     */
    public static function RA(float $julianCentury){
        return rad2deg( atan2( cos( deg2rad( ASTROSUN::SunAppLong( $julianCentury) ) ) , cos( deg2rad( ASTROSUN::ObliqCorrected($julianCentury) ) ) * sin( deg2rad( ASTROSUN::SunAppLong( $julianCentury) ) ) ) );
    }

    /**
     * Deklination der Sonne
     */
    public static function Declination(float $julianCentury){
        return rad2deg( asin( sin( deg2rad( ASTROSUN::ObliqCorrected($julianCentury) ) ) * sin( deg2rad( ASTROSUN::SunAppLong( $julianCentury) ) ) ) );
    }

    /**
     * 
     */
    public static function VarY(float $julianCentury){
        return tan( deg2rad( ASTROSUN::ObliqCorrected($julianCentury) / 2 ) ) * tan( deg2rad( ASTROSUN::ObliqCorrected($julianCentury) / 2 ) );
    }

    /**
     * 
     */
    public static function EquationOfTime(float $julianCentury){
        return 4 * rad2deg(
            ASTROSUN::VarY( $julianCentury) * sin(
                    2*deg2rad(ASTROSUN::MeanLongitude($julianCentury))
                ) - 2 * ASTROSUN::EccentEarthOrbit($julianCentury) * sin(
                    deg2rad(ASTROSUN::MeanAnomaly($julianCentury))
                ) + 4 * ASTROSUN::EccentEarthOrbit($julianCentury) * ASTROSUN::VarY( $julianCentury) * sin(
                    deg2rad(ASTROSUN::MeanAnomaly($julianCentury))
                ) * cos(
                    2*deg2rad(ASTROSUN::MeanLongitude($julianCentury))
                ) - 0.5 * ASTROSUN::VarY( $julianCentury) * ASTROSUN::VarY( $julianCentury) * sin(
                    4*deg2rad(ASTROSUN::MeanLongitude($julianCentury))
                ) - 1.25 * ASTROSUN::EccentEarthOrbit($julianCentury) * ASTROSUN::EccentEarthOrbit($julianCentury) * sin(
                    2 * deg2rad(ASTROSUN::MeanAnomaly($julianCentury))
                )
            );
    }

    public static function HourAngleAtElevation(float $sunElevation, float $latitude, float $julianCentury){
        return rad2deg(acos(cos(deg2rad(90 - $sunElevation))/(cos(deg2rad($latitude))*cos(deg2rad(ASTROSUN::Declination($julianCentury))))-tan(deg2rad($latitude))*tan(deg2rad(ASTROSUN::Declination($julianCentury)))));
    }

    /**
     * 
     */
    public static function SolarNoon(int $timezone, float $longitude, float $julianCentury){
        if ($longitude >= -180 && $longitude <= 180) {
            return ( 720 - 4 * $longitude - ASTROSUN::EquationOfTime($julianCentury) + $timezone * 60 ) / 1440;
        }elseif ($longitude < -180) {
            return ( 720 - 4 * (360 + $longitude) - ASTROSUN::EquationOfTime($julianCentury) + $timezone * 60 ) / 1440;
        }else {
            return ( 720 - 4 * (-360 + $longitude) - ASTROSUN::EquationOfTime($julianCentury) + $timezone * 60 ) / 1440;
        }
    }
        
    /**
     * 
     */
    public static function TimeForElevation(float $sunElevation, float $latitude, float $longitude, float $timezone, float $julianCentury, bool $beforeNoon){
        if(is_nan(ASTROSUN::HourAngleAtElevation($sunElevation, $latitude, $julianCentury) / 360)){
            throw new Exception('Elevation wird zu keiner Zeit erreicht.');
        }
        else{
            if ($beforeNoon){
                return ASTROSUN::SolarNoon($timezone, $longitude, $julianCentury) - ASTROSUN::HourAngleAtElevation($sunElevation, $latitude, $julianCentury) / 360;
            }else{
                return ASTROSUN::SolarNoon($timezone, $longitude, $julianCentury) + ASTROSUN::HourAngleAtElevation($sunElevation, $latitude, $julianCentury) / 360;
            }
        }
    }

    public static function SunlightDuration(float $latitude, float $julianCentury){
        return 8 * ASTROSUN::HourAngleAtElevation(-0.833, $latitude, $julianCentury);
    }

    public static function TrueSolarTime(float $julianCentury, float $localTime, float $long, int $timezone){
        return fmod( $localTime * 1440 + ASTROSUN::EquationOfTime($julianCentury) + 4 * $long - 60 * $timezone , 1440);
    }

    /**
     * 
     */
    public static function HourAngle(float $julianCentury, float $localTime, float $long, int $timezone){
        $trueSolarTime = ASTROSUN::TrueSolarTime($localTime, $julianCentury, $long, $timezone);
        if ($trueSolarTime / 4 < 0){
            return $trueSolarTime / 4 + 180;
        }else{
            return $trueSolarTime / 4 - 180;
        }
    }

    /**
     * 
     */
    public static function SolarZenith(float $julianCentury, float $localTime, float $lat, float $long, float $timezone){
        $declination = ASTROSUN::Declination( $julianCentury);
        $hourAngle = ASTROSUN::HourAngle( $localTime,  $julianCentury,  $long,  $timezone);
        return rad2deg(
            acos(sin(deg2rad($lat))*sin(deg2rad($declination))+cos(deg2rad($lat))*cos(deg2rad($declination))*cos(deg2rad($hourAngle)))
        );
    }

    /**
     * 
     */
    public static function SolarElevation(float $julianCentury, float $localTime, float $lat, float $long, float $timezone){
        return 90 - ASTROSUN::SolarZenith($julianCentury, $localTime, $lat, $long, $timezone);
    }

    /**
     * 
     */
    public static function SolarAzimut(float $julianCentury, float $localTime, float $latitude, float $longitude, int $timezone){
        $declination = ASTROSUN::Declination($julianCentury);
        $hourAngle = ASTROSUN::HourAngle($localTime, $julianCentury, $longitude, $timezone);
        $solarZenith = ASTROSUN::SolarZenith($julianCentury, $localTime, $latitude, $longitude, $timezone);
        if ($hourAngle>0){
            return fmod(
                rad2deg(
                    acos(
                        (
                            (
                                sin(
                                    deg2rad($latitude)
                                ) * cos(
                                    deg2rad($solarZenith)
                                )
                            ) - sin(
                                deg2rad($declination)
                            )
                        ) / (
                            cos(
                                deg2rad($latitude)
                            ) * sin(
                                deg2rad($solarZenith)
                            )
                        )
                    )
                )+180,360
            );
        }else{
            return fmod(
                540 - rad2deg(
                    acos(
                        (
                            (
                                sin(
                                    deg2rad($latitude)
                                ) * cos(
                                    deg2rad($solarZenith)
                                )
                            ) - sin(
                                deg2rad($declination)
                            )
                        ) / (
                            cos(
                                deg2rad($latitude)
                            ) * sin(
                                deg2rad($solarZenith)
                            )
                        )
                    )
                ),360
            );
        }
    }
    
    public static function SolarDirection(float $solarAzimut){
        $sector = intdiv($solarAzimut, 22.5);

        switch ($sector) {
            case 0:
                return "N";
            case 1:
                return "NNE";
            case 2:
                return "NE";
            case 3:
                return "ENE";
            case 4:
                return "E";
            case 5:
                return "ESE";
            case 6:
                return "SE";
            case 7:
                return "SSE";
            case 8:
                return "S";
            case 9:
                return "SSW";
            case 10:
                return "SW";
            case 11:
                return "WSW";
            case 12:
                return "W";
            case 13:
                return "WNW";
            case 14:
                return "NW";
            case 15:
                return "NNW";
            default:
                return "";
        }
    }

    public static function Season(float $julianCentury, float $latitude) : int{
        $declination = ASTROSUN::Declination($julianCentury);
        $declinationBef = ASTROSUN::Declination($julianCentury - 0.00000002);
        if($declination>=0){
            if($declination > $declinationBef){
                if($latitude > 0){
                    return 1;
                }else{
                    return 3;
                }
            }else{
                if($latitude > 0){
                    return 2;
                }else{
                    return 4;
                }
            }
        }else{
            if($declination > $declinationBef){
                if($latitude > 0){
                    return 4;
                }else{
                    return 2;
                }
            }else{
                if($latitude > 0){
                    return 3;
                }else{
                    return 1;
                }
            }
        }
    }

    public static function DurationOfSunrise(float $latitude, float $longitude, float $julianCentury){
        return ASTROSUN::TimeForElevation(0.833, $latitude, $longitude, 1, $julianCentury, true) - ASTROSUN::TimeForElevation(-0.833, $latitude, $longitude, 1, $julianCentury, true);
    }

    public static function L0Arr(){
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

    public static function L1Arr()
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

    public static function L2Arr()
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

    public static function L3Arr()
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

    public static function L4Arr()
    {
        $l4 = array(
            array(114, 3.142, 0),
            array(8, 4.13, 6283.08),
            array(1, 3.84, 12566.15)
        );
        return $l4;
    }

    public static function L5Arr()
    {
        $l5 = array(
            array(1, 3.14, 0),
        );
        return $l5;
    }

    public static function B0Arr()
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

    public static function B1Arr()
    {
        $b1 = array(
            array(9, 3.9, 5507.55),
            array(6, 1.73, 5223.69)
        );
        return $b1;
    }

    public static function R0Arr()
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

    public static function R1Arr()
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

    public static function R2Arr()
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

    public static function R3Arr()
    {
        $r3 = array(
            array(145, 4.273, 6283.076),
            array(7, 3.92, 12566.15)
        );
        return $r3;
    }

    public static function R4Arr()
    {
        $r4 = array(
            array(4, 2.56, 6283.08)
        );
        return $r4;
    }
    
    public static function PeriodicTermsForTheNutation()
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
}

class ASTROMOON{
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

class ASTROMISC{
    
    public static function LimitTo360($angle){
        if ($angle >= 0) {
            return 360 * ($angle / 360 - floor($angle / 360));
        } else {
            return 360 + 360 * ($angle / 360 - ceil($angle / 360));
        }
    }

    public static function LimitTo180($angle)
    {
        if ($angle >= 0) {
            return 180 * ($angle / 180 - floor($angle / 180));
        } else {
            return 180 + 180 * ($angle / 180 - ceil($angle / 180));
        }
    }

    public static function LimitToInterval($numToLimit, $limit)
    {
        if ($numToLimit >= 0) {
            return $limit * ($numToLimit / $limit - floor($numToLimit / $limit));
        } else {
            return $limit + $limit * ($numToLimit / $limit - ceil($numToLimit / $limit));
        }
    }
}
    
?>