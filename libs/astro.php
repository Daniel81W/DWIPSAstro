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
    
    function __construct(float $deltaT = 0, float $dut1 = 0, int $timestamp = null)
    {
        if(is_null($timestamp)){
            $date = new DateTime();
            $timestamp = $date->getTimestamp();
        }
        $this->jd = $this->JulianDay(idate('Y', $timestamp), idate('m', $timestamp), idate('d', $timestamp), idate('H', $timestamp), idate('i', $timestamp), idate('s', $timestamp));
        $this->deltaT = $deltaT;
        $this->dut1 = $dut1;
    }

    public function set_DeltaT($deltaT){
        $this->deltaT=$deltaT;
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
    const atmosRefract = 0.5667;

    private float $timestamp;
    private JulianDay $julianDay;
    private JulianDay $julianDayZero;

    private float $latitude;
    private float $longitude;
    private float $elevation;
    private float $pressure;
    private float $temperature;

    private $deltaT;
    private $dut1;
    
    private Sun $sunJDZeroDT;
    private Sun $sunJDZero;
    private Sun $sunJDZeroM;
    private Sun $sunJDZeroP;




    function __construct(float $deltaT, float $dut1, int $timestamp, float $latitude, float $longitude, float $elevation, float $pressure, float $temperature)
    {
        if ($timestamp < 0) {
            $date = new DateTime();
            $timestamp = time();$date->getTimestamp();
        }
        $this->timestamp = $timestamp;
        $this->julianDay = new JulianDay($deltaT, $dut1, $timestamp);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->elevation = $elevation;
        $this->pressure = $pressure;
        $this->temperature = $temperature;
        $this->deltaT = $deltaT;
        $this->dut1 = $dut1;
    }

    public function set_DeltaT(float $deltaT){
        $this->deltaT = $deltaT;
        $this->julianDay->set_DeltaT($deltaT);
    }

    /*
    public function CalculateEotAndSunRiseTransitSet(): array
    {
        $h0_prime = -1 * (Sun::radius + Sun::atmosRefract);

        $nu = 0;
        $m = 0;
        $h0 = 0;
        $n = 0;
        $m_rts = array();
        $nu_rts = array();
        $h_rts = array();
        $alpha_prime = array();
        $delta_prime = array();
        $h_prime = array();


        $m = $this->SunMeanLongitude();
        $eot = $this->EOT();

        $nu = $this->sunJDZeroDT->GreenwichSiderealTime();

        for (i = 0; i < JD_COUNT; i++) {
            calculate_geocentric_sun_right_ascension_and_declination(&sun_rts);
            alpha[i] = sun_rts.alpha;
            delta[i] = sun_rts.delta;
            sun_rts.jd++;
        }
        $alpha = array($this->sunJDZeroM->GeocentricRightAscension(), $this->sunJDZero->GeocentricRightAscension(), $this->sunJDZeroP->GeocentricRightAscension());
        $delta = array($this->sunJDZeroM->GeocentricDeclination(), $this->sunJDZero->GeocentricDeclination(), $this->sunJDZeroP->GeocentricDeclination());


        //m_rts[0] = $this->sunJDZero->ApproxSunTransitTime();
        //$h0 = $this->sunJDZero->SunHourAngleAtRiseSet();

        if ($h0 >= 0) {

            $m_rts = $this->sunJDZero->ApproxSunRiseAndSet();

            for ($i = 0; $i < 3; $i++) {

                $nu_rts[$i] = $nu + 360.985647 * $m_rts[$i];

                $n = $m_rts[$i] + $this->deltaT / 86400.0;
                $alpha_prime[$i] = $this->RtsAlphaDeltaPrime($alpha, $n);
                $delta_prime[$i] = $this->RtsAlphaDeltaPrime($delta, $n);

                $h_prime[$i] = ASTROMISC::LimitTo180DegPM($nu_rts[$i] + $this->longitude - $alpha_prime[$i]);

                $h_rts[$i] = $this->RtsSunAltitude($delta_prime[$i], $h_prime[$i]);
            }
            $ret = array(
                'srha' => $h_prime[0],
                'ssha' => $h_prime[2],
                'sta' => $h_rts[1],

                'suntransit' => ASTROMISC::DayFracToHr($m_rts[1] - $h_prime[1] / 360.0),

                'sunrise' => ASTROMISC::DayFracToHr(
                    SunRiseAndSet(
                        $m_rts,
                        $h_rts,
                        $delta_prime,
                        $h_prime,
                        $h0_prime,
                        1
                    )
                ),

                'sunset' => ASTROMISC::DayFracToHr(
                    SunRiseAndSet(
                        $m_rts,
                        $h_rts,
                        $delta_prime,
                        $h_prime,
                        $h0_prime,
                        2
                    )
                )
            );

        } else {
            $ret = array(
                'srha' => -99999,
                'ssha' => -99999,
                'sta' => -99999,

                'suntransit' => -99999,

                'sunrise' => -99999,

                'sunset' => -99999
            );
        }
        return $ret;
    }

    public function SunRiseAndSet(        $m_rts,        $h_rts,        $delta_prime,        $h_prime,        $h0_prime,        $sun    ): float {
        return $m_rts[$sun] + ($h_rts[$sun] - $h0_prime) /
            (360.0 * cos(deg2rad($delta_prime[$sun])) * cos(deg2rad($this->latitude)) * sin(deg2rad($h_prime[$sun])));
    }





    public function RtsAlphaDeltaPrime(array $ad, $n)
    {
        $ret = array();
        $a = $ad[1] - $ad[0];
        $b = $ad[2] - $ad[1];

        if (abs($a) >= 2.0) {
            $a = ASTROMISC::LimitZeroToOne($a);
        }
        if (abs($b) >= 2.0) {
            $b = ASTROMISC::LimitZeroToOne($b);
        }

        return $ad[1] + $n * ($a + $b + ($b - $a) * $n) / 2.0;
    }

    public function RtsSunAltitude(float $delta_prime, float $h_prime)
    {
        $latitude_rad = deg2rad($this->latitude);
        $delta_prime_rad = deg2rad($delta_prime);

        return rad2deg(asin(sin($latitude_rad) * sin($delta_prime_rad) +
            cos($latitude_rad) * cos($delta_prime_rad) * cos(deg2rad($h_prime))));
    }

    public function ApproxSunRiseAndSet(): array
    {
        $h0_dfrac = $this->SunHourAngleAtRiseSet() / 360.0;
        $sunTrans = $this->ApproxSunTransitTime();

        $ret = array();
        $ret[0] = ASTROMISC::LimitZeroToOne($sunTrans - $h0_dfrac);
        $ret[2] = ASTROMISC::LimitZeroToOne($sunTrans + $h0_dfrac);
        $ret[1] = ASTROMISC::LimitZeroToOne($sunTrans);
        $ret[0] = ($sunTrans - $h0_dfrac);
        $ret[2] = ($sunTrans + $h0_dfrac);
        $ret[1] = ($sunTrans);

        return $ret;
    }

    public function SunHourAngleAtRiseSet(): float
    {
        $h0 = -99999;
        $latitude_rad = deg2rad($this->latitude);
        $delta_zero_rad = deg2rad($this->sunJDZero->GeocentricDeclination());// TODO ÄÄndern zu Delto JD ZERO
        $h0_prime_rad = deg2rad(-1 * (Sun::radius + Sun::atmosRefract));

        $argument = (sin(deg2rad($h0_prime_rad)) - sin($latitude_rad) * sin($delta_zero_rad)) /
            (cos($latitude_rad) * cos($delta_zero_rad));

        if (abs($argument) <= 1) {
            $h0 = ASTROMISC::LimitTo180Deg(rad2deg(acos($argument)));
        }

        return $h0;
    }

    public function ApproxSunTransitTime(): float
    {
        // TODO Right Ascension für JD ZERO
        return ($this->sunJDZero->GeocentricRightAscension() - $this->longitude - $this->GreenwichSiderealTime()) / 360.0;
    }*/


    public function calculate_eot_and_sun_rise_transit_set(array &$spa)
    {
        $nu = 0.0;
        $m = 0.0;
        $h0 = 0.0;
        $n = 0.0;
        //$alpha[JD_COUNT]=array(); $delta[JD_COUNT]=array();
        $alpha = array();
        $delta = array();
        $m_rts = array();
        $nu_rts = array();
        $h_rts = array();
        $alpha_prime = array();
        $delta_prime = array();
        $h_prime = array();
        $h0_prime = -1 * (Sun::radius + Sun::atmosRefract);

        //sun_rts  = $spa;
        $m = ASTRO_SUN_FORMULA::sun_mean_longitude($this->julianDay->get_JME());
        $spa['eot'] = ASTRO_SUN_FORMULA::eot($m, $this->GeocentricRightAscension(), $this->NutationLongitude(), $this->EclipticTrueObliquity());
        $tsM = mktime(0, 0, 0, idate('m', $this->timestamp), idate('d', $this->timestamp) - 1, idate('Y', $this->timestamp));
        $ts0 = mktime(0, 0, 0, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
        $tsP = mktime(0, 0, 0, idate('m', $this->timestamp), idate('d', $this->timestamp) + 1, idate('Y', $this->timestamp));

        $sunArr = array(
            new Sun($this->deltaT, 0, $tsM, $this->latitude, $this->longitude, $this->elevation, $this->pressure, $this->temperature),
            new Sun($this->deltaT, 0, $ts0, $this->latitude, $this->longitude, $this->elevation, $this->pressure, $this->temperature),
            new Sun($this->deltaT, 0, $tsP, $this->latitude, $this->longitude, $this->elevation, $this->pressure, $this->temperature)
        );


        $nu = $sunArr[1]->GreenwichSiderealTime();

        for ($i = 0; $i < 3; $i++) {
            $sunArr[$i]->set_DeltaT(0);
            $alpha[$i] = $sunArr[$i]->GeocentricRightAscension();
            $delta[$i] = $sunArr[$i]->GeocentricDeclination();
            //calculate_geocentric_sun_right_ascension_and_declination(&sun_rts);
            //alpha[i] = sun_rts.alpha;
            //delta[i] = sun_rts.delta;
            //sun_rts.jd++;
        }

        $m_rts[0] = ASTRO_SUN_FORMULA::approx_sun_transit_time($alpha[1], $this->longitude, $nu);
        $h0 = ASTRO_SUN_FORMULA::sun_hour_angle_at_rise_set($this->latitude, $delta[1], $h0_prime);
        if ($h0 >= 0) {

            ASTRO_SUN_FORMULA::approx_sun_rise_and_set($m_rts, $h0);

            for ($i = 0; $i < 3; $i++) {

                $nu_rts[$i] = $nu + 360.985647 * $m_rts[$i];

                $n = $m_rts[$i] + $this->deltaT / 86400.0;
                $alpha_prime[$i] = ASTRO_SUN_FORMULA::rts_alpha_delta_prime($alpha, $n);
                $delta_prime[$i] = ASTRO_SUN_FORMULA::rts_alpha_delta_prime($delta, $n);

                $h_prime[$i] = ASTROMISC::LimitTo180DegPM($nu_rts[$i] + $this->longitude - $alpha_prime[$i]);

                $h_rts[$i] = ASTRO_SUN_FORMULA::rts_sun_altitude($this->latitude, $delta_prime[$i], $h_prime[$i]);
            }

            $spa['srha'] = $h_prime[1];
            $spa['ssha'] = $h_prime[2];
            $spa['sta'] = $h_rts[0];

            $spa['suntransit'] = ASTROMISC::DayFracToHr($m_rts[0] - $h_prime[0] / 360.0);

            $spa['sunrise'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    $h0_prime,
                    1
                )
            );

            $spa['sunset'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    $h0_prime,
                    2
                )
            );
            $spa['srCT'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    -6,
                    1
                )
            );
            $spa['ssCT'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    -6,
                    2
                )
            );
            $spa['srNT'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    -12,
                    1
                )
            );
            $spa['ssNT'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    -12,
                    2
                )
            );
            $spa['srAT'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    -18,
                    1
                )
            );
            $spa['ssAT'] = ASTROMISC::DayFracToHr(
                ASTRO_SUN_FORMULA::sun_rise_and_set(
                    $m_rts,
                    $h_rts,
                    $delta_prime,
                    $this->latitude,
                    $h_prime,
                    -18,
                    2
                )
            );
            $spa['suntransitUNIX'] = gmmktime(0, 0, $spa['suntransit'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['sunriseUNIX'] = gmmktime(0, 0, $spa['sunrise'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['sunsetUNIX'] = gmmktime(0, 0, $spa['sunset'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['srCTUNIX'] = gmmktime(0, 0, $spa['srCT'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['ssCTUNIX'] = gmmktime(0, 0, $spa['ssCT'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['srNTUNIX'] = gmmktime(0, 0, $spa['srNT'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['ssNTUNIX'] = gmmktime(0, 0, $spa['ssNT'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['srATUNIX'] = gmmktime(0, 0, $spa['srAT'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['ssATUNIX'] = gmmktime(0, 0, $spa['ssAT'] * 60 * 60, idate('m', $this->timestamp), idate('d', $this->timestamp), idate('Y', $this->timestamp));
            $spa['sunlightduration'] = $spa['sunsetUNIX'] - $spa['sunriseUNIX'] - (new DateTimeImmutable())->setTimestamp(0)->getOffset();
            $spa['azimuth'] = $this->TopocentricAzimuthAngle();
            $spa['elevationAngle'] = $this->TopocentricElevationAngleCorrected();
            $spa['declination'] = $this->TopocentricDeclination();
            $spa['shadow'] = 1/tan(deg2rad($spa['elevationAngle']));

        } else {
            $spa['srha'] = -9999;
            $spa['ssha'] = -9999;
            $spa['sta'] = -9999;

            $spa['suntransit'] = -9999;

            $spa['sunrise'] = -9999;

            $spa['sunset'] = -9999;
        }

    }

    public function EOT(): float
    {
        return ASTRO_SUN_FORMULA::eot($this->SunMeanLongitude(),$this->GeocentricRightAscension(),$this->NutationLongitude(),$this->EclipticTrueObliquity());
    }

    public function SunMeanLongitude(): float
    {
        return ASTRO_SUN_FORMULA::sun_mean_longitude($this->julianDay->get_JME());
    }

    public function SurfaceIncidenceAngle(float $orientation, float $slope):float
    {
        return ASTRO_SUN_FORMULA::surface_incidence_angle($this->TopocentricZenithAngle(),$this->TopocentricAzimuthAngleAstro(),$orientation,$slope);
    }

    public function TopocentricAzimuthAngle(): float
    {
        return ASTRO_SUN_FORMULA::topocentric_azimuth_angle(
            ASTRO_SUN_FORMULA::topocentric_azimuth_angle_astro(
                $this->TopocentricLocalHourAngle(),
                $this->latitude,
                $this->TopocentricDeclination()
            )
        );
    }

    private function TopocentricAzimuthAngleAstro(): float
    {
        $h_prime_rad = deg2rad($this->TopocentricLocalHourAngle());
        $lat_rad = deg2rad($this->latitude);

        return ASTROMISC::LimitTo360Deg(rad2deg(
            atan2(
                sin($h_prime_rad),
                cos($h_prime_rad) * sin($lat_rad) - tan(deg2rad($this->TopocentricDeclination())) * cos($lat_rad)
            )
        ));
    }

    public function TopocentricZenithAngle(): float
    {
        return ASTRO_SUN_FORMULA::topocentric_zenith_angle($this->TopocentricElevationAngleCorrected());
    }
    
    public function TopocentricElevationAngleCorrected(): float
    {
        return ASTRO_SUN_FORMULA::topocentric_elevation_angle_corrected($this->TopocentricElevationAngle(), $this->AtmosphericRefractionCorrection());
    }

    public function AtmosphericRefractionCorrection(): float
    {
        return ASTRO_SUN_FORMULA::atmospheric_refraction_correction($this->pressure,$this->temperature,Sun::atmosRefract, $this->TopocentricElevationAngle());
    }

    public function TopocentricElevationAngle(): float
    {
        return ASTRO_SUN_FORMULA::topocentric_elevation_angle($this->latitude, $this->TopocentricDeclination(), $this->TopocentricLocalHourAngle());
    }

    public function TopocentricLocalHourAngle(): float
    {
        return ASTRO_SUN_FORMULA::topocentric_local_hour_angle($this->ObserverHourAngle(), $this->RightAscensionParallax());
    }

    public function TopocentricRightAscension(): float
    {
        return ASTRO_SUN_FORMULA::topocentric_right_ascension($this->GeocentricRightAscension(), $this->RightAscensionParallax());
    }

    public function TopocentricDeclination(): float
    {
        $delta_alpha = 0.0;
        $delta_prime = 0.0;
        ASTRO_SUN_FORMULA::right_ascension_parallax_and_topocentric_dec($this->latitude, $this->elevation, $this->SunEquatorialHorizontalParallax(), $this->ObserverHourAngle(), $this->GeocentricDeclination(), $delta_alpha, $delta_prime);
        return $delta_prime;
    }

    public function RightAscensionParallax(): float
    {
        $delta_alpha = 0.0;
        $delta_prime = 0.0;
        ASTRO_SUN_FORMULA::right_ascension_parallax_and_topocentric_dec($this->latitude, $this->elevation, $this->SunEquatorialHorizontalParallax(), $this->ObserverHourAngle(), $this->GeocentricDeclination(), $delta_alpha, $delta_prime);
        return $delta_alpha;
    }

    public function SunEquatorialHorizontalParallax(): float
    {
        return ASTRO_SUN_FORMULA::sun_equatorial_horizontal_parallax($this->EarthRadiusVector());
    }

    public function ObserverHourAngle(): float
    {
        return ASTRO_SUN_FORMULA::observer_hour_angle($this->GreenwichSiderealTime(),$this->longitude,$this->GeocentricRightAscension());
    }

    public function GeocentricDeclination(): float
    {
        return ASTRO_SUN_FORMULA::geocentric_declination($this->GeocentricLatitude(), $this->EclipticTrueObliquity(), $this->ApparentSunLongitude());
    }

    public function GeocentricRightAscension(): float
    {
        return ASTRO_SUN_FORMULA::geocentric_right_ascension($this->ApparentSunLongitude(),$this->EclipticTrueObliquity(),$this->GeocentricLatitude());
    }

    public function GreenwichSiderealTime(): float
    {
        return ASTRO_SUN_FORMULA::greenwich_sidereal_time($this->GreenwichMeanSiderealTime(),$this->NutationLongitude(),$this->EclipticTrueObliquity());
    }

    public function GreenwichMeanSiderealTime():float
    {
        return ASTRO_SUN_FORMULA::greenwich_mean_sidereal_time($this->julianDay->get_JD(),$this->julianDay->get_JC());
    }

    public function ApparentSunLongitude():float
    {
        return ASTRO_SUN_FORMULA::apparent_sun_longitude($this->GeocentricLongitude(),$this->NutationLongitude(),$this->AberrationCorrection());
    }

    public function AberrationCorrection():float
    {
        return ASTRO_SUN_FORMULA::aberration_correction($this->EarthRadiusVector());
    }

    public function EclipticTrueObliquity():float
    {
        return ASTRO_SUN_FORMULA::ecliptic_true_obliquity($this->NutationObliquity(), $this->EclipticMeanObliquity());
    }

    public function EclipticMeanObliquity():float
    {
        return ASTRO_SUN_FORMULA::ecliptic_mean_obliquity($this->julianDay->get_JME());
    }

    public function NutationObliquity(): float
    {
        $x = $this->xTerms();
        $del_psi=0;
        $del_epsilon = 0;
        $this->NutationLongitudeAndObliquity($x, $del_psi, $del_epsilon);
        return $del_epsilon;
    }

    public function NutationLongitude():float
    {
        $x = $this->xTerms();
        $del_psi=0;
        $del_epsilon = 0;
        $this->NutationLongitudeAndObliquity($x,$del_psi, $del_epsilon);
        return $del_psi;
    }

    private function NutationLongitudeAndObliquity(array $x,float &$del_psi, float &$del_epsilon)
    {
        return ASTRO_SUN_FORMULA::nutation_longitude_and_obliquity($this->julianDay->get_JCE(), $x, $del_psi, $del_epsilon);
    }

    private function xTerms():array{
        
        return ASTRO_SUN_FORMULA::xTerms($this->julianDay->get_JCE());
    }

    public function AscendingLongitudeMoon(): float
    {
        return ASTRO_SUN_FORMULA::ascending_longitude_moon($this->julianDay->get_JCE());
    }

    public function ArgumentLatitudeMoon(): float
    {
        return ASTRO_SUN_FORMULA::argument_latitude_moon($this->julianDay->get_JCE());
    }

    public function MeanAnomalyMoon(): float
    {
        return ASTRO_SUN_FORMULA::mean_anomaly_moon($this->julianDay->get_JCE());
    }

    public function MeanAnomalySun(): float
    {
        return ASTRO_SUN_FORMULA::mean_anomaly_sun($this->julianDay->get_JCE());
    }

    public function MeanElongationMoonSun(): float
    {
        return ASTRO_SUN_FORMULA::mean_elongation_moon_sun($this->julianDay->get_JCE());
    }

    public function GeocentricLatitude(): float
    {
        return ASTRO_SUN_FORMULA::geocentric_latitude($this->EarthHeliocentricLatitude());
    }

    public function GeocentricLongitude(): float
    {
        return ASTRO_SUN_FORMULA::geocentric_longitude($this->EarthHeliocentricLongitude());
    }

    public function EarthRadiusVector(): float
    {
        return ASTRO_SUN_FORMULA::earth_radius_vector($this->julianDay->get_JME());
    }

    public function EarthHeliocentricLatitude():float
    {
        return ASTRO_SUN_FORMULA::earth_heliocentric_latitude($this->julianDay->get_JME());
    }

    public function EarthHeliocentricLongitude(): float
    {
        return ASTRO_SUN_FORMULA::earth_heliocentric_longitude($this->julianDay->get_JME());
    }
}

class ASTRO_SUN_FORMULA{
    
    const AU = 149597870700;
    const sun_radius = 0.26667;

    public static function earth_periodic_term_summation(array $terms, int $count, float $jme): float
    {
        $sum = 0;

        for ($i = 0; $i < $count; $i++) {
            //$sum += $terms[$i][TERM_ABC::TERM_A->value] * cos($terms[$i][TERM_ABC::TERM_B->value] + $terms[$i][TERM_ABC::TERM_C->value] * $jme);
            $sum += $terms[$i][0] * cos($terms[$i][1] + $terms[$i][2] * $jme);
        }
        return $sum;
    }

    public static function earth_values(array $term_sum, int $count, float $jme): float
    {
        $sum = 0;

        for ($i = 0; $i < $count; $i++)
            $sum += $term_sum[$i] * pow($jme, $i);

        $sum /= pow(10, 8);

        return $sum;
    }

    public static function earth_heliocentric_longitude(float $jme): float
    {
        $sum = array();

        for ($i = 0; $i < ASTROTERMS::l_count; $i++)
            $sum[$i] = ASTRO_SUN_FORMULA::earth_periodic_term_summation(ASTROTERMS::l_terms[$i], ASTROTERMS::l_subcount[$i], $jme);

        return ASTROMISC::LimitTo360Deg(rad2deg(ASTRO_SUN_FORMULA::earth_values($sum, ASTROTERMS::l_count, $jme)));

    }

    public static function earth_heliocentric_latitude(float $jme): float
    {
        $sum = array();

        for ($i = 0; $i < ASTROTERMS::b_count; $i++)
            $sum[$i] = ASTRO_SUN_FORMULA::earth_periodic_term_summation(ASTROTERMS::b_terms[$i], ASTROTERMS::b_subcount[$i], $jme);

        return rad2deg(ASTRO_SUN_FORMULA::earth_values($sum, ASTROTERMS::b_count, $jme));

    }

    public static function earth_radius_vector(float $jme): float
    {
        $sum = array();

        for ($i = 0; $i < ASTROTERMS::r_count; $i++)
            $sum[$i] = ASTRO_SUN_FORMULA::earth_periodic_term_summation(ASTROTERMS::r_terms[$i], ASTROTERMS::r_subcount[$i], $jme);

        return ASTRO_SUN_FORMULA::earth_values($sum, ASTROTERMS::r_count, $jme);

    }

    public static function geocentric_longitude(float $l): float
    {
        $theta = $l + 180.0;

        if ($theta >= 360.0) {
            $theta -= 360.0;
        }

        return $theta;
    }

    public static function geocentric_latitude(float $b): float
    {
        return -1 * $b;
    }

    public static function mean_elongation_moon_sun(float $jce): float
    {
        return ASTROMISC::ThirdOrderPolynomial(1.0 / 189474.0, -0.0019142, 445267.11148, 297.85036, $jce);
    }

    public static function mean_anomaly_sun(float $jce): float
    {
        return ASTROMISC::ThirdOrderPolynomial(-1.0 / 300000.0, -0.0001603, 35999.05034, 357.52772, $jce);
    }

    public static function mean_anomaly_moon(float $jce): float
    {
        return ASTROMISC::ThirdOrderPolynomial(1.0 / 56250.0, 0.0086972, 477198.867398, 134.96298, $jce);
    }

    public static function argument_latitude_moon(float $jce): float
    {
        return ASTROMISC::ThirdOrderPolynomial(1.0 / 327270.0, -0.0036825, 483202.017538, 93.27191, $jce);
    }

    public static function ascending_longitude_moon(float $jce): float
    {
        return ASTROMISC::ThirdOrderPolynomial(1.0 / 450000.0, 0.0020708, -1934.136261, 125.04452, $jce);
    }
    
    public static function xTerms(float $jce):array{
        $x = array();
        $x[0] = ASTRO_SUN_FORMULA::mean_elongation_moon_sun($jce);
        $x[1] = ASTRO_SUN_FORMULA::mean_anomaly_sun($jce);
        $x[2] = ASTRO_SUN_FORMULA::mean_anomaly_moon($jce);
        $x[3] = ASTRO_SUN_FORMULA::argument_latitude_moon($jce);
        $x[4] = ASTRO_SUN_FORMULA::ascending_longitude_moon($jce);

        return $x;
    }

    public static function xy_term_summation(int $i, array $x):float
    {
        $sum=0;

        for ($j = 0; $j < ASTROTERMS::y_term_count; $j++)
            $sum += $x[$j]*ASTROTERMS::y_terms[$i][$j];

        return $sum;
    }

    public static function nutation_longitude_and_obliquity(float $jce, array $x, float &$del_psi, float &$del_epsilon)
    {
        $xy_term_sum = 0.0; 
            $sum_psi=0.0; 
            $sum_epsilon=0.0;

        for ($i = 0; $i < ASTROTERMS::y_count; $i++) {
            $xy_term_sum  = deg2rad(ASTRO_SUN_FORMULA::xy_term_summation($i, $x));
            $sum_psi     += (ASTROTERMS::pe_terms[$i][0] + $jce*ASTROTERMS::pe_terms[$i][1])*sin($xy_term_sum);
            $sum_epsilon += (ASTROTERMS::pe_terms[$i][2] + $jce*ASTROTERMS::pe_terms[$i][3])*cos($xy_term_sum);
            //$sum_psi     += (ASTROTERMS::pe_terms[$i][TERM_PSI_A] + $jce*ASTROTERMS::pe_terms[$i][TERM_PSI_B])*sin($xy_term_sum);
            //$sum_epsilon += (ASTROTERMS::pe_terms[$i][TERM_EPS_C] + $jce*ASTROTERMS::pe_terms[$i][TERM_EPS_D])*cos($xy_term_sum);
        }

        $del_psi     = $sum_psi     / 36000000.0;
        $del_epsilon = $sum_epsilon / 36000000.0;
    }
    
    public static function ecliptic_mean_obliquity(float $jme):float
    {
        $u = $jme/10.0;

        return 84381.448 + $u*(-4680.93 + $u*(-1.55 + $u*(1999.25 + $u*(-51.38 + $u*(-249.67 +
                           $u*(  -39.05 + $u*( 7.12 + $u*(  27.87 + $u*(  5.79 + $u*2.45)))))))));
    }

    public static function ecliptic_true_obliquity(float $delta_epsilon, float $epsilon0):float
    {
        return $delta_epsilon + $epsilon0/3600.0;
    }
    
    public static function aberration_correction(float $r): float
    {
        return -20.4898 / (3600.0*$r);
    }

    public static function apparent_sun_longitude(float $theta, float $delta_psi, float $delta_tau): float
    {
        return $theta + $delta_psi + $delta_tau;
    }

    public static function greenwich_mean_sidereal_time (float $jd, float $jc): float
    {
        return ASTROMISC::LimitTo360Deg(280.46061837 + 360.98564736629 * ($jd - 2451545.0) +
            $jc* $jc*(0.000387933 - $jc/38710000.0));
    }
    
    public static function greenwich_sidereal_time (float $nu0, float $delta_psi, float $epsilon): float
    {
        return $nu0 + $delta_psi*cos(deg2rad($epsilon));
    }

    public static function geocentric_right_ascension(float $lamda, float $epsilon, float $beta): float
    {
        $lamda_rad   = deg2rad($lamda);
        $epsilon_rad = deg2rad($epsilon);

        return ASTROMISC::LimitTo360Deg(rad2deg(atan2(sin($lamda_rad)*cos($epsilon_rad) -
                                           tan(deg2rad($beta))*sin($epsilon_rad), cos($lamda_rad))));
    }

    public static function geocentric_declination(float $beta, float $epsilon, float $lamda): float
    {
        $beta_rad    = deg2rad($beta);
        $epsilon_rad = deg2rad($epsilon);

        return rad2deg(asin(sin($beta_rad)*cos($epsilon_rad) +
                            cos($beta_rad)*sin($epsilon_rad)*sin(deg2rad($lamda))));
    }

    public static function observer_hour_angle(float $nu, float $longitude, float $alpha_deg): float
    {
        return ASTROMISC::LimitTo360Deg($nu + $longitude - $alpha_deg);
    }

    public static function sun_equatorial_horizontal_parallax(float $r): float
    {
        return 8.794 / (3600.0 * $r);
    }

    public static function right_ascension_parallax_and_topocentric_dec(float $latitude, float $elevation, float $xi, float $h, float $delta, float &$delta_alpha, float &$delta_prime)
    {
        $delta_alpha_rad = 0.0;
        $lat_rad = deg2rad($latitude);
        $xi_rad = deg2rad($xi);
        $h_rad = deg2rad($h);
        $delta_rad = deg2rad($delta);
        $u = atan(0.99664719 * tan($lat_rad));
        $y = 0.99664719 * sin($u) + $elevation * sin($lat_rad) / 6378140.0;
        $x = cos($u) + $elevation * cos($lat_rad) / 6378140.0;

        $delta_alpha_rad = atan2(
            -$x * sin($xi_rad) * sin($h_rad),
            cos($delta_rad) - $x * sin($xi_rad) * cos($h_rad)
        );

        $delta_prime = rad2deg(
            atan2(
                (sin($delta_rad) - $y * sin($xi_rad)) * cos($delta_alpha_rad),
                cos($delta_rad) - $x * sin($xi_rad) * cos($h_rad)
            )
        );

        $delta_alpha = rad2deg($delta_alpha_rad);
    }

    public static function topocentric_right_ascension(float $alpha_deg, float $delta_alpha): float
    {
        return $alpha_deg + $delta_alpha;
    }

    public static function topocentric_local_hour_angle(float $h, float $delta_alpha): float
    {
        return $h - $delta_alpha;
    }

    public static function topocentric_elevation_angle(float $latitude, float $delta_prime, float $h_prime): float
    {
        $lat_rad = deg2rad($latitude);
        $delta_prime_rad = deg2rad($delta_prime);

        return rad2deg(asin(sin($lat_rad) * sin($delta_prime_rad) +
            cos($lat_rad) * cos($delta_prime_rad) * cos(deg2rad($h_prime))));
    }

    public static function atmospheric_refraction_correction(float $pressure, float $temperature, float $atmos_refract, float $e0): float
    {
        $del_e = 0;

        if ($e0 >= -1 * (ASTRO_SUN_FORMULA::sun_radius + $atmos_refract))
            $del_e = ($pressure / 1010.0) * (283.0 / (273.0 + $temperature)) *
                1.02 / (60.0 * tan(deg2rad($e0 + 10.3 / ($e0 + 5.11))));

        return $del_e;
    }

    public static function topocentric_elevation_angle_corrected(float $e0, float $delta_e): float
    {
        return $e0 + $delta_e;
    }

    public static function topocentric_zenith_angle(float $e): float
    {
        return 90.0 - $e;
    }
    
    public static function topocentric_azimuth_angle_astro(float $h_prime, float $latitude, float $delta_prime):float
    {
        $h_prime_rad = deg2rad($h_prime);
        $lat_rad     = deg2rad($latitude);

        return ASTROMISC::LimitTo360Deg(rad2deg(atan2(sin($h_prime_rad),
                             cos($h_prime_rad)*sin($lat_rad) - tan(deg2rad($delta_prime))*cos($lat_rad))));
    }

    public static function topocentric_azimuth_angle(float $azimuth_astro):float
    {
        return ASTROMISC::LimitTo360Deg($azimuth_astro + 180.0);
    }

    public static function surface_incidence_angle(float $zenith, float $azimuth_astro, float $azm_rotation, float $slope): float
    {
        $zenith_rad = deg2rad($zenith);
        $slope_rad = deg2rad($slope);

        return rad2deg(acos(cos($zenith_rad) * cos($slope_rad) +
            sin($slope_rad) * sin($zenith_rad) * cos(deg2rad($azimuth_astro - $azm_rotation))));
    }

    public static function sun_mean_longitude(float $jme): float
    {
        return ASTROMISC::LimitTo360Deg(280.4664567 + $jme * (360007.6982779 + $jme * (0.03032028 +
            $jme * (1 / 49931.0 + $jme * (-1 / 15300.0 + $jme * (-1 / 2000000.0))))));
    }

    public static function eot(float $m, float $alpha, float $del_psi, float $epsilon): float
    {
        return ASTROMISC::LimitTo20Minutes(4.0 * ($m - 0.0057183 - $alpha + $del_psi * cos(deg2rad($epsilon))));
    }

    public static function approx_sun_transit_time(float $alpha_zero, float $longitude, float $nu): float
    {
        return ($alpha_zero - $longitude - $nu) / 360.0;
    }

    public static function sun_hour_angle_at_rise_set(float $latitude, float $delta_zero, float $h0_prime): float
    {
        $h0 = -99999;
        $latitude_rad = deg2rad($latitude);
        $delta_zero_rad = deg2rad($delta_zero);
        $argument = (sin(deg2rad($h0_prime)) - sin($latitude_rad) * sin($delta_zero_rad)) /
            (cos($latitude_rad) * cos($delta_zero_rad));

        if (abs($argument) <= 1) {
            $h0 = ASTROMISC::LimitTo180Deg(rad2deg(acos($argument)));
        }

        return $h0;
    }

    public static function approx_sun_rise_and_set(array &$m_rts, float $h0)
    {
        $h0_dfrac = $h0 / 360.0;

        $m_rts[1] = ASTROMISC::LimitZeroToOne($m_rts[0] - $h0_dfrac);
        $m_rts[2] = ASTROMISC::LimitZeroToOne($m_rts[0] + $h0_dfrac);
        $m_rts[0] = ASTROMISC::LimitZeroToOne($m_rts[0]);
        /*$m_rts[SUN_RISE]    = ASTROMISC::LimitZeroToOne(m_rts[SUN_TRANSIT] - $h0_dfrac);
        $m_rts[SUN_SET]     = ASTROMISC::LimitZeroToOne(m_rts[SUN_TRANSIT] + $h0_dfrac);
        $m_rts[SUN_TRANSIT] = ASTROMISC::LimitZeroToOne(m_rts[SUN_TRANSIT]);*/
    }

    public static function rts_alpha_delta_prime(array &$ad, float $n): float
    {
        $a = $ad[1] - $ad[0];
        $b = $ad[2] - $ad[1];
        //$a = $ad[JD_ZERO] - $ad[JD_MINUS];
        //$b = $ad[JD_PLUS] - $ad[JD_ZERO];

        if (abs($a) >= 2.0)
            $a = ASTROMISC::LimitZeroToOne($a);
        if (abs($b) >= 2.0)
            $b = ASTROMISC::LimitZeroToOne($b);

        return $ad[1] + $n * ($a + $b + ($b - $a) * $n) / 2.0;
        //return $ad[JD_ZERO] + $n * ($a + $b + ($b-$a)*$n)/2.0;
    }

    public static function rts_sun_altitude(float $latitude, float $delta_prime, float $h_prime): float
    {
        $latitude_rad = deg2rad($latitude);
        $delta_prime_rad = deg2rad($delta_prime);

        return rad2deg(asin(sin($latitude_rad) * sin($delta_prime_rad) +
            cos($latitude_rad) * cos($delta_prime_rad) * cos(deg2rad($h_prime))));
    }

    public static function sun_rise_and_set(array &$m_rts, array &$h_rts, array &$delta_prime, float $latitude, array &$h_prime, float $h0_prime, int $sun): float
    {
        return $m_rts[$sun] + ($h_rts[$sun] - $h0_prime) /
            (360.0 * cos(deg2rad($delta_prime[$sun])) * cos(deg2rad($latitude)) * sin(deg2rad($h_prime[$sun])));
    }

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
        $m = 0;//ASTROSUN::MeanAnomalyOfTheSun($jce);
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
    const y_term_count = 5;
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

/*enum TERM_ABC:int{
    case TERM_A = 0;
    case TERM_B = 1;
    case TERM_C = 2;
    case TERM_COUNT = 3;
}*/
?>