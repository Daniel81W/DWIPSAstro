<?php

//TODO Mond

class ASTROGEN{

    /**
     * 
     */
    public static function  JulianDay(){
        $date = new DateTime();
       return ASTROGEN::JulianDayFromTimestamp($date->getTimestamp());
    }

    /**
     * 
     */
    public static function JulianDayFromTimestamp(int $timestamp){

        $dy = 0;
        $dm = 0;
        if (idate('m', $timestamp) <= 2) {
            $dy = -1;
            $dm = +12;
        }
        $jd = floor(365.25 * (idate('Y', $timestamp) + $dy + 4716)) + floor(30.6001 * (idate('m', $timestamp) + $dm + 1)) + idate('d', $timestamp) + $timestamp / 86400 - floor($timestamp / 86400) - 1524.5;

        if ($jd >= 2299160) {
            $jd += (2 - floor(idate('Y', $timestamp) / 100) + floor(floor(idate('Y', $timestamp) / 100) / 4));
        }

        return $jd;
            
    }

    /**
     * 
     */
    public static function JulianDayFromDateTime(int $year, int $month, int $day, int $hour = 0, int $minute = 0, int $second = 0){
       $date = mktime($hour, $minute, $second , $month, $day, $year);
       return ASTROGEN::JulianDayFromTimestamp($date);
    }

    /**
     * 
     */
    public static function JulianCentury(float $julianDay){
        return ($julianDay - 2451545.0) / 36525.0;
    }

    /**
     * 
     */
    public static function JulianMillenium(float $julianCentury)
    {
        return ($julianCentury) / 10.0;
    }
    
}

class ASTROSUN{

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
        $f = $l % 360;
        $f = $l / 360 - floor($l / 360);
        $f = 360 * $f;
        return $f;
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
            array(),
        );
        return $b0;
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
?>