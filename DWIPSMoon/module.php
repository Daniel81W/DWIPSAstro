<?php

include_once (__DIR__ . "/../libs/astro.php");

class DWIPSMoon extends IPSModule
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


     

        $this->RegisterVariableString("moonphase", $this->Translate("moonphase"), "", 30);


        $p = 1;
        $this->MaintainVariable("moonrise", $this->Translate("moonrise"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("moonset", $this->Translate("moonset"), 1, "~UnixTimestamp", $p, true);
        //$p++;
        //$this->MaintainVariable("moonnoon", $this->Translate("moonnoon"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("moonlightduration", $this->Translate("moonlightduration"), 1, "~UnixTimestampTime", $p, true);
        $p++;
        $this->MaintainVariable("moonazimuth", $this->Translate("moonazimuth"), 2, "~WindDirection.F", $p, true);
        $p++;
        $this->MaintainVariable("moondirection", $this->Translate("moondirection"), 2, "DWIPS." . $this->Translate("compass_rose"), $p, true);
        $p++;
        $this->MaintainVariable("moonelevation", $this->Translate("moonelevation"), 2, "~WindDirection.F", $p, true);
        $p++;
        $this->MaintainVariable("moonelevationmin", $this->Translate("moonelevationmin"), 2, "~WindDirection.F", $p, true);
        $p++;
        $this->MaintainVariable("moonelevationmax", $this->Translate("moonelevationmax"), 2, "~WindDirection.F", $p, true);
        $p++;
        $this->MaintainVariable("moonazimutAtSunrise", $this->Translate("moonazimutSunrise"), 2, "~WindDirection.F", $p, false);
        $this->MaintainVariable("moonazimutAtSunrise", $this->Translate("moonazimutSunrise"), 2, "~WindDirection.F", $p, true);
        $p++;
        $this->MaintainVariable("moonazimutAtSunset", $this->Translate("moonazimutSunset"), 2, "~WindDirection.F", $p, false);
        $this->MaintainVariable("moonazimutAtSunset", $this->Translate("moonazimutSunset"), 2, "~WindDirection.F", $p, true);
        //$p++;
        //$this->MaintainVariable("day", $this->Translate("day"), 0, "DWIPS." . $this->Translate("DayNight"), $p, true);
        $p++;
        $this->MaintainVariable("moondistance", $this->Translate("moondistance"), 1, "DWIPS." . $this->Translate("distance.km"), $p, true);
        $p++;


        $this->RegisterPropertyFloat("Latitude", 50.0);
        $this->RegisterPropertyFloat("Longitude", 9);
        $this->RegisterPropertyFloat("Elevation", 1);
        $this->RegisterPropertyFloat("deltaT", 69.184);


        $this->RegisterAttributeFloat("jd", 0);
        $this->RegisterAttributeFloat("jc", 0);
        $this->RegisterAttributeFloat("jm", 0);
        $this->RegisterAttributeFloat("jde", 0);
        $this->RegisterAttributeFloat("jce", 0);
        $this->RegisterAttributeFloat("jme", 0);

        $this->RegisterPropertyInteger("MoonUpdateInterval", 1);


        $this->RegisterTimer("Update", 60000, "DWIPSMOON_Update($this->InstanceID);");
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
        $this->SetTimerInterval("Update", $this->ReadPropertyInteger("MoonUpdateInterval") * 60 * 1000);

        DWIPSMOON_Update($this->InstanceID);
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
        $jd = new JulianDay($this->ReadPropertyFloat("deltaT"));
        $moon = new Moon($this->ReadPropertyFloat("deltaT"), 0, -1, $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), $this->ReadPropertyFloat("Elevation"), 835, 10);

        $moonDat = array();
        $moon->sampa_calculate($moonDat);

        //$this->WriteAttributeFloat("jd", );
        //$this->WriteAttributeFloat("jc", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jd")));
        //$this->WriteAttributeFloat("jm", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jc")));
        //$this->WriteAttributeFloat("jde", ASTROGEN::JDE($this->ReadAttributeFloat("jd"), $this->ReadPropertyFloat("deltaT")));
        $this->WriteAttributeFloat("jce", $moonDat['spa']['jce']);
        //$this->WriteAttributeFloat("jme", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jce")));


        $this->SetValue("moonazimuth", $moonDat['azimuth']);
        $this->SetValue("moonelevation", $moonDat['zenith']);


        $this->UpdateFormField("Current_JCE", "value", $moonDat['spa']['jce']);

        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("Current_moon_mean_elongation", "value", $moonDat['d']);
        $this->UpdateFormField("Current_sun_mean_anomaly", "value", $moonDat['m']);
        $this->UpdateFormField("Current_moon_mean_anomaly", "value", $moonDat['m_prime']);
        $this->UpdateFormField("Current_moon_latitude_argument", "value", $moonDat['f']);

        
    }

    public function CalcTestValues(int $date, float $deltaT, float $lat, float $long, float $elev, float $pressure, float $temperature): void{

        $jd = new JulianDay($deltaT, 0, $date);
        $sun = new Sun($deltaT, 0, $date, $lat, $long, $elev, $pressure, $temperature);

        $this->UpdateFormField("TestCalc_JD", "value", $jd->get_JD());
        $this->UpdateFormField("TestCalc_JC", "value", $jd->get_JC());
        $this->UpdateFormField("TestCalc_JM", "value", $jd->get_JM());
        $this->UpdateFormField("TestCalc_JDE", "value", $jd->get_JDE());
        $this->UpdateFormField("TestCalc_JCE", "value", $jd->get_JCE());
        $this->UpdateFormField("TestCalc_JME", "value", $jd->get_JME());


        $this->UpdateFormField("TestCalc_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("TestCalc_moon_mean_elongation", "value", $moonDat['d']);
        $this->UpdateFormField("TestCalc_sun_mean_anomaly", "value", $moonDat['m']);
        $this->UpdateFormField("TestCalc_moon_mean_anomaly", "value", $moonDat['m_prime']);
        $this->UpdateFormField("TestCalc_moon_latitude_argument", "value", $moonDat['f']);

        $this->UpdateFormField("TestCalc_MeanElongationMoonSun", "value", $sun->MeanElongationMoonSun());
        $this->UpdateFormField("TestCalc_MeanAnomalySun", "value", $sun->MeanAnomalySun());
        $this->UpdateFormField("TestCalc_MeanAnomalyMoon", "value", $sun->MeanAnomalyMoon());
        $this->UpdateFormField("TestCalc_ArgumentLatitudeMoon", "value", $sun->ArgumentLatitudeMoon());
        $this->UpdateFormField("TestCalc_AscendingLongitudeMoon", "value", $sun->AscendingLongitudeMoon());

        $this->UpdateFormField("TestCalc_NutationLongitude", "value", $sun->NutationLongitude());
        $this->UpdateFormField("TestCalc_NutationObliquity", "value", $sun->NutationObliquity());
        $this->UpdateFormField("TestCalc_EclipticMeanObliquity", "value", $sun->EclipticMeanObliquity());
        $this->UpdateFormField("TestCalc_EclipticTrueObliquity", "value", $sun->EclipticTrueObliquity());
        $this->UpdateFormField("TestCalc_AberrationCorrection", "value", $sun->AberrationCorrection());
        $this->UpdateFormField("TestCalc_ApparentSunLongitude", "value", $sun->ApparentSunLongitude());
        $this->UpdateFormField("TestCalc_GreenwichMeanSiderealTime", "value", $sun->GreenwichMeanSiderealTime());
        $this->UpdateFormField("TestCalc_GreenwichSiderealTime", "value", $sun->GreenwichSiderealTime());

        $this->UpdateFormField("TestCalc_GeocentricRightAscension", "value", $sun->GeocentricRightAscension());
        $this->UpdateFormField("TestCalc_GeocentricDeclination", "value", $sun->GeocentricDeclination());
        $this->UpdateFormField("TestCalc_ObserverHourAngle", "value", $sun->ObserverHourAngle());
        $this->UpdateFormField("TestCalc_SunEquatorialHorizontalParallax", "value", $sun->SunEquatorialHorizontalParallax());
        $this->UpdateFormField("TestCalc_RightAscensionParallax", "value", $sun->RightAscensionParallax());
        $this->UpdateFormField("TestCalc_TopocentricDeclination", "value", $sun->TopocentricDeclination());
        $this->UpdateFormField("TestCalc_TopocentricRightAscension", "value", $sun->TopocentricRightAscension());
        $this->UpdateFormField("TestCalc_TopocentricLocalHourAngle", "value", $sun->TopocentricLocalHourAngle());
        $this->UpdateFormField("TestCalc_TopocentricElevationAngle", "value", $sun->TopocentricElevationAngle());
        $this->UpdateFormField("TestCalc_AtmosphericRefractionCorrection", "value", $sun->AtmosphericRefractionCorrection());
        $this->UpdateFormField("TestCalc_TopocentricElevationAngleCorrected", "value", $sun->TopocentricElevationAngleCorrected());
        $this->UpdateFormField("TestCalc_TopocentricZenithAngle", "value", $sun->TopocentricZenithAngle());
        $this->UpdateFormField("TestCalc_TopocentricAzimuthAngle", "value", $sun->TopocentricAzimuthAngle());
        $this->UpdateFormField("TestCalc_SurfaceIncidenceAngle", "value", $sun->SurfaceIncidenceAngle(180,0));
        $this->UpdateFormField("TestCalc_SunMeanLongitude", "value", $sun->SunMeanLongitude());

        $sunDat = array();
        $sun->calculate_eot_and_sun_rise_transit_set($sunDat);
        $this->UpdateFormField("TestCalc_EOT", "value", $sunDat['eot']);
        $this->UpdateFormField("TestCalc_SunRiseHourAngle", "value", $sunDat['srha']);
        $this->UpdateFormField("TestCalc_SunSetHourAngle", "value", $sunDat['ssha']);
        $this->UpdateFormField("TestCalc_SunTransitAltitude", "value", $sunDat['sta']);
        $this->UpdateFormField("TestCalc_ApproxSunTransitTime", "value", $sunDat['suntransit']);
        $this->UpdateFormField("TestCalc_SunRiseTime", "value", $sunDat['sunrise']);
        $this->UpdateFormField("TestCalc_SunSetTime", "value", $sunDat['sunset']);

    }


    public function LoadSetupFromSun(){
        $guid = "{8FEB8771-2E4C-CB78-EA91-52546AE77A79}";
        $mods = IPS_GetInstanceListByModuleID($guid);
        if(count($mods)==1){
            $sett = DWIPSSUN_GetSettings($mods[0]);
            print_r($sett);


            $this->UpdateFormField("Latitude", "value", $sett["Latitude"]);
            $this->UpdateFormField("Longitude", "value", $sett["Longitude"]);
            $this->UpdateFormField("Elevation", "value", $sett["Elevation"]);
            $this->UpdateFormField("UpdateInterval", "value", $sett["UpdateInterval"]);
            $this->UpdateFormField("deltaT", "value", $sett["deltaT"]);
        }

    }

}
?>