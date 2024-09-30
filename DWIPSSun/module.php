<?php

include_once ("/var/lib/symcon/modules/DWIPSAstro/libs/astro.php");
class DWIPSSun extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        
        if (!IPS_VariableProfileExists("DWIPS." . $this->Translate("season"))) {
            IPS_CreateVariableProfile("DWIPS." . $this->Translate("season"), 1);
            IPS_SetVariableProfileValues("DWIPS." . $this->Translate("season"), 1, 4, 1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 1, $this->Translate("spring"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 2, $this->Translate("summer"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 3, $this->Translate("fall"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 4, $this->Translate("winter"), "", -1);
        }

        $jdid = $this->RegisterVariableFloat("juliandate", $this->Translate("juliandate"), "", 1);
        $this->RegisterVariableFloat("juliancentury", $this->Translate("juliancentury"), "", 2);
        $this->RegisterVariableInteger("startastronomicaltwilight", $this->Translate("startastronomicaltwilight"), "~UnixTimestamp", 3);
        $this->RegisterVariableInteger("startnauticaltwilight", $this->Translate("startnauticaltwilight"), "~UnixTimestamp", 4);
        $this->RegisterVariableInteger("startciviltwilight", $this->Translate("startciviltwilight"), "~UnixTimestamp", 5);
        $this->RegisterVariableInteger("sunrise", $this->Translate("sunrise"), "~UnixTimestamp", 6);
        $this->RegisterVariableInteger("solarnoon", $this->Translate("solarnoon"), "~UnixTimestamp", 7);
        $this->RegisterVariableInteger("sunset", $this->Translate("sunset"), "~UnixTimestamp", 8);
        $this->RegisterVariableInteger("stopciviltwilight", $this->Translate("stopciviltwilight"), "~UnixTimestamp", 9);
        $this->RegisterVariableInteger("stopnauticaltwilight", $this->Translate("stopnauticaltwilight"), "~UnixTimestamp", 10);
        $this->RegisterVariableInteger("stopastronomicaltwilight", $this->Translate("stopastronomicaltwilight"), "~UnixTimestamp", 11);
        $this->RegisterVariableFloat("sunlightduration", $this->Translate("sunlightduration"), "", 12);
        $this->RegisterVariableString("sunlightdurationstr", $this->Translate("sunlightduration"), "", 12);
        $this->RegisterVariableFloat("sunazimut", $this->Translate("sunazimut"), "", 13);
        $this->RegisterVariableString("sundirection", $this->Translate("sundirection"), "", 14);
        $this->RegisterVariableFloat("sunelevation", $this->Translate("sunelevation"), "", 15);
        $this->RegisterVariableFloat("sunelevationmin", $this->Translate("sunelevationmin"), "", 16);
        $this->RegisterVariableFloat("sunelevationmax", $this->Translate("sunelevationmax"), "", 17);
        $this->RegisterVariableFloat("sundeclination", $this->Translate("sundeclination"), "", 18);
        $this->RegisterVariableInteger("sundistance", $this->Translate("sundistance"), "", 19);
        $this->RegisterVariableFloat("equationOfTime", $this->Translate("equationOfTime"), "", 20);
        $this->RegisterVariableFloat("durationOfSunrise", $this->Translate("durationOfSunrise"), "", 21);
        $this->RegisterVariableInteger("season", $this->Translate("season"), "DWIPS." . $this->Translate("season"), 22);
        $this->RegisterVariableBoolean("day", $this->Translate("day"), "", 23);
        $this->RegisterVariableBoolean("insideCivilTwilight", $this->Translate("insideCivilTwilight"), "", 24);
        $this->RegisterVariableFloat("shadowLength", $this->Translate("shadowlength"), "", 25);
        $this->RegisterVariableFloat("solarirradiancespace", $this->Translate("solarirradiancespace"), "", 26); //"Astronomie.Radiant_Power", 26);
        $this->RegisterVariableFloat("solarirradiancerectangular", $this->Translate("solarirradiancerectangular"), "", 27); //"Astronomie.Radiant_Power", 27);
        $this->RegisterVariableFloat("solarirradianceground", $this->Translate("solarirradianceground"), "", 28); //"Astronomie.Radiant_Power", 28);
        $this->RegisterVariableFloat("solarirradiancepvcollector", $this->Translate("solarirradiancepvcollector"), "", 40); //"Astronomie.Radiant_Power", 40);


        $this->RegisterVariableString("moonphase", $this->Translate("moonphase"), "", 30);


        $this->RegisterPropertyFloat("Latitude", 50.0);
        $this->RegisterPropertyFloat("Longitude", 9);
        $this->RegisterPropertyFloat("deltaT", 69.184);

        $this->RegisterPropertyInteger("UpdateInterval", 1);

        $this->RegisterAttributeFloat("jd", 0);
        $this->RegisterAttributeFloat("jc", 0);
        $this->RegisterAttributeFloat("jm", 0);
        $this->RegisterAttributeFloat("jde", 0);
        $this->RegisterAttributeFloat("jce", 0);
        $this->RegisterAttributeFloat("jme", 0);
        $this->RegisterAttributeFloat("helioCentLong", 0);
        $this->RegisterAttributeFloat("helioCentLat", 0);
        $this->RegisterAttributeFloat("earthRadVec", 0);
        $this->RegisterAttributeFloat("geoCentLong", 0);
        $this->RegisterAttributeFloat("geoCentLat", 0);
        $this->RegisterAttributeFloat("nutationLongitude", 0);
        $this->RegisterAttributeFloat("nutationObliquity", 0);



        $this->RegisterTimer("Update", 60000, "DWIPSSUN_Update($this->InstanceID);");
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->SetTimerInterval("Update", $this->ReadPropertyInteger("UpdateInterval") * 60 * 1000);

        DWIPSSUN_Update($this->InstanceID);
    }

    public function GetConfigurationForm()
    {
        //load form from file
        $jsonForm = json_decode(file_get_contents(__DIR__ . "/form.json"), true);

        
        $jsonForm["actions"][0]["items"][0]["value"] = $this->ReadAttributeFloat("jd");
        $jsonForm["actions"][0]["items"][1]["value"] = $this->ReadAttributeFloat("jc");
        $jsonForm["actions"][0]["items"][2]["value"] = $this->ReadAttributeFloat("jm");

        $jsonForm["actions"][1]["items"][0]["value"] = $this->ReadAttributeFloat("jde");
        $jsonForm["actions"][1]["items"][1]["value"] = $this->ReadAttributeFloat("jce");
        $jsonForm["actions"][1]["items"][2]["value"] = $this->ReadAttributeFloat("jme");

        $jsonForm["actions"][2]["items"][0]["value"] = $this->ReadAttributeFloat("helioCentLong");
        $jsonForm["actions"][2]["items"][1]["value"] = $this->ReadAttributeFloat("helioCentLat");
        $jsonForm["actions"][2]["items"][2]["value"] = $this->ReadAttributeFloat("earthRadVec");

        $jsonForm["actions"][3]["items"][0]["value"] = $this->ReadAttributeFloat("geoCentLong");
        $jsonForm["actions"][3]["items"][1]["value"] = $this->ReadAttributeFloat("geoCentLat");
        
        $jsonForm["actions"][4]["items"][0]["value"] = $this->ReadAttributeFloat("nutationLongitude");
        $jsonForm["actions"][4]["items"][1]["value"] = $this->ReadAttributeFloat("nutationObliquity");

        return json_encode($jsonForm);
    }

    /**
     * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
     *
     * DWIPSASTRO_UpdateSunrise($id);
     *
     */
    public function Update()
    {
        /*
        $this->WriteAttributeFloat("jd", ASTROGEN::JulianDay());
        $this->WriteAttributeFloat("jc", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jd")));
        $this->WriteAttributeFloat("jm", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jc")));
        $this->WriteAttributeFloat("jde", ASTROGEN::JDE($this->ReadAttributeFloat("jd"), $this->ReadPropertyFloat("deltaT")));
        $this->WriteAttributeFloat("jce", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jde")));
        $this->WriteAttributeFloat("jme", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jce")));
        
        $this->WriteAttributeFloat("helioCentLong", ASTROSUN::HeliocentricLongitudeDEG($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("helioCentLat", ASTROSUN::HeliocentricLatitude($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("earthRadVec", ASTROSUN::EarthRadiusVector($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("geoCentLong", ASTROSUN::GeocentricLongitude($this->ReadAttributeFloat("helioCentLong")));
        $this->WriteAttributeFloat("geoCentLat", ASTROSUN::GeocentricLatitude($this->ReadAttributeFloat("helioCentLat")));
        */
        //$this->WriteAttributeFloat("nutationLongitude", ASTROSUN::NutationInLongitude($this->ReadAttributeFloat("jce")));
        //$this->WriteAttributeFloat("nutationObliquity", ASTROSUN::NutationInObliquity($this->ReadAttributeFloat("jce")));
        /*
        $this->UpdateFormField("jd", "value", $this->ReadAttributeFloat("jd"));
        $this->UpdateFormField("jc", "value", $this->ReadAttributeFloat("jc"));
        $this->UpdateFormField("jm", "value", $this->ReadAttributeFloat("jm"));
        $this->UpdateFormField("jde", "value", $this->ReadAttributeFloat("jde"));
        $this->UpdateFormField("jce", "value", $this->ReadAttributeFloat("jce"));
        $this->UpdateFormField("jme", "value", $this->ReadAttributeFloat("jme"));

        $this->UpdateFormField("helioCentLong", "value", $this->ReadAttributeFloat("helioCentLong"));
        $this->UpdateFormField("helioCentLat", "value", $this->ReadAttributeFloat("helioCentLat"));
        $this->UpdateFormField("earthRadVec", "value", $this->ReadAttributeFloat("earthRadVec"));
        $this->UpdateFormField("geoCentLong", "value", $this->ReadAttributeFloat("geoCentLong"));
        $this->UpdateFormField("geoCentLat", "value", $this->ReadAttributeFloat("geoCentLat"));
        */
        //$this->UpdateFormField("nutationLong", "value", $this->ReadAttributeFloat("nutationLongitude"));
        //$this->UpdateFormField("nutationObl", "value", $this->ReadAttributeFloat("nutationObliquity"));
        /*
        $timezone = 1;
        if (date('I')) {
            $timezone = 2;
        }
        $localTime = intval(date("G")) / 24 + intval(date("i")) / 1440 + intval(date("s") / 86400);

        $latitude = $this->ReadPropertyFloat("Latitude");
        $longitude = $this->ReadPropertyFloat("Longitude");

        $jd = ASTROGEN::JulianDay();
        $jc = ASTROGEN::JulianCentury($jd);
        $jm = ASTROGEN::JulianMillennium($jc);
        $this->SendDebug('TZ', date_default_timezone_get(), 0);
        $jdtomorrow = $jd + 1;
        $jctomorrow = ASTROGEN::JulianCentury($jdtomorrow);

        $solarZenith = ASTROSUN::SolarZenith($jc, $localTime, $latitude, $longitude, $timezone);
        $sunrise = mktime(0, 0, ASTROSUN::TimeForElevation(-0.833, $latitude, $longitude, $timezone, $jc, true) * 24 * 60 * 60);
        $sunrisetoday = $sunrise;
        if ($sunrise < time()) {
            $sunrise = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-0.833, $latitude, $longitude, $timezone, $jctomorrow, true)) * 24 * 60 * 60);
        }
        $sunset = mktime(0, 0, ASTROSUN::TimeForElevation(-0.833, $latitude, $longitude, $timezone, $jc, false) * 24 * 60 * 60);
        $sunsettoday = $sunset;
        if ($sunset < time()) {
            $sunset = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-0.833, $latitude, $longitude, $timezone, $jctomorrow, false)) * 24 * 60 * 60);
        }
        $solarAzimut = ASTROSUN::SolarAzimut($jc, $localTime, $latitude, $longitude, $timezone);
        $beginCivilTwilight = mktime(0, 0, ASTROSUN::TimeForElevation(-6, $latitude, $longitude, $timezone, $jc, true) * 24 * 60 * 60);
        $beginCivilTwilighttoday = $beginCivilTwilight;
        if ($beginCivilTwilight < time()) {
            $beginCivilTwilight = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-6, $latitude, $longitude, $timezone, $jctomorrow, true)) * 24 * 60 * 60);
        }
        $endCivilTwilight = mktime(0, 0, ASTROSUN::TimeForElevation(-6, $latitude, $longitude, $timezone, $jc, false) * 24 * 60 * 60);
        $endCivilTwilightToday = $endCivilTwilight;
        if ($endCivilTwilight < time()) {
            $endCivilTwilight = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-6, $latitude, $longitude, $timezone, $jctomorrow, false)) * 24 * 60 * 60);
        }
        $sunelevation = ASTROSUN::SolarElevation($jc, $localTime, $latitude, $longitude, $timezone);
        $sundistance = ASTROSUN::SunRadVector($jc) * 149597870.7;
        $solarirradiancespace = 3.845 * pow(10, 26) / (4 * pi() * pow($sundistance * 1000, 2));

        $this->SetValue("juliandate", $jd);
        $this->SetValue("juliancentury", $jc);

        $solarnoon = mktime(0, 0, ASTROSUN::SolarNoon($timezone, $longitude, $jc) * 24 * 60 * 60);
        if ($solarnoon < time()) {
            $solarnoon = mktime(0, 0, (1 + ASTROSUN::SolarNoon($timezone, $longitude, $jctomorrow)) * 24 * 60 * 60);
        }
        $this->SetValue("solarnoon", $solarnoon);
        $this->SetValue("sunazimut", $solarAzimut);
        $this->SetValue("sundeclination", ASTROSUN::Declination($jc));
        $this->SetValue("sunelevation", $sunelevation);
        $this->SetValue("sunelevationmin", -90 + $latitude + ASTROSUN::Declination($jc));
        $this->SetValue("sunelevationmax", 90 - $latitude + ASTROSUN::Declination($jc));
        $this->SetValue("sundistance", $this->ReadAttributeFloat("earthRadVec") * ASTROSUN::AU / 1000);
        $this->SetValue("equationOfTime", ASTROSUN::EquationOfTime($jc));
        $this->SetValue("sundirection", ASTROSUN::SolarDirection($solarAzimut));
        $sundura = ($sunset - $sunrise) / 60.0 / 60.0;
        $this->SetValue("sunlightduration", $sundura);
        $this->SetValue("sunlightdurationstr", date('H:i:s', ($sunset - $sunrise - intval(date('Z', $sunset - $sunrise)))));
        $this->SetValue("season", ASTROSUN::Season($jc, $latitude));


        $this->SetValue("sunrise", $sunrise);
        $this->SetValue("sunset", $sunset);
        $this->SetValue("startciviltwilight", $beginCivilTwilight);
        $this->SetValue("stopciviltwilight", $endCivilTwilight);
        try {
            $beginNauticalTwilight = mktime(0, 0, ASTROSUN::TimeForElevation(-12, $latitude, $longitude, $timezone, $jc, true) * 24 * 60 * 60);
            if ($beginNauticalTwilight < time()) {
                $beginNauticalTwilight = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-12, $latitude, $longitude, $timezone, $jctomorrow, true)) * 24 * 60 * 60);
            }
            $this->SetValue("startnauticaltwilight", $beginNauticalTwilight);
        } catch (Exception $e) {
        }
        try {
            $endNauticalTwilight = mktime(0, 0, ASTROSUN::TimeForElevation(-12, $latitude, $longitude, $timezone, $jc, false) * 24 * 60 * 60);
            if ($endNauticalTwilight < time()) {
                $endNauticalTwilight = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-12, $latitude, $longitude, $timezone, $jctomorrow, false)) * 24 * 60 * 60);
            }
            $this->SetValue("stopnauticaltwilight", $endNauticalTwilight);
        } catch (Exception $e) {
        }
        try {
            $beginAstronomicalTwilight = mktime(0, 0, ASTROSUN::TimeForElevation(-18, $latitude, $longitude, $timezone, $jc, true) * 24 * 60 * 60);
            if ($beginAstronomicalTwilight < time()) {
                $beginAstronomicalTwilight = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-18, $latitude, $longitude, $timezone, $jctomorrow, true)) * 24 * 60 * 60);
            }
            $this->SetValue("startastronomicaltwilight", $beginAstronomicalTwilight);
        } catch (Exception $e) {
        }
        try {
            $endAstronomicalTwilight = mktime(0, 0, ASTROSUN::TimeForElevation(-18, $latitude, $longitude, $timezone, $jc, false) * 24 * 60 * 60);
            if ($endAstronomicalTwilight < time()) {
                $endAstronomicalTwilight = mktime(0, 0, (1 + ASTROSUN::TimeForElevation(-18, $latitude, $longitude, $timezone, $jctomorrow, false)) * 24 * 60 * 60);
            }
            $this->SetValue("stopastronomicaltwilight", $endAstronomicalTwilight);
        } catch (Exception $e) {
        }
        $shadowlen = 1 / tan(deg2rad($sunelevation));
        if ($shadowlen > 0) {
            $this->SetValue("shadowLength", $shadowlen);
        } else {

            $this->SetValue("shadowLength", 0);
        }
        $this->SetValue("solarirradiancespace", $solarirradiancespace);
        $this->SetValue("solarirradiancerectangular", $solarirradiancespace * 0.75);
        $this->SetValue("solarirradianceground", $solarirradiancespace * 0.75 * sin(deg2rad($sunelevation)));
        $this->SetValue("solarirradiancepvcollector", $solarirradiancespace * 0.75 * (cos(deg2rad($sunelevation)) * cos(deg2rad($solarAzimut - 183)) * sin(deg2rad(39)) + sin(deg2rad($sunelevation)) * cos(deg2rad(39))));
        $this->SetValue("durationOfSunrise", ASTROSUN::DurationOfSunrise($latitude, $longitude, $jc));

        $ts = time();
        if ($sunrisetoday <= $ts and $ts <= $sunsettoday) {
            $this->SetValue("day", true);
        } else {
            $this->SetValue("day", false);
        }
        if ($beginCivilTwilighttoday <= $ts and $ts <= $endCivilTwilightToday) {
            $this->SetValue("insideCivilTwilight", true);
        } else {
            $this->SetValue("insideCivilTwilight", false);
        }

        $this->SetValue("moonphase", ASTROMOON::PhaseStr());*/
    }


}
?>