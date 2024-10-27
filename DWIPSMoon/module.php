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


        $this->RegisterAttributeString("TestCalc_DateTime", '{"year":2024,"month":7,"day":1,"hour":12,"minute":0,"second":0}');
        $this->RegisterAttributeFloat("TestCalc_Lat", 0);
        $this->RegisterAttributeFloat("TestCalc_Long", 0);
        $this->RegisterAttributeFloat("TestCalc_Elevation", 0);
        $this->RegisterAttributeFloat("TestCalc_DeltaT", 0);

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
    
    public function GetConfigurationForm()
    {
        $this->Update();
        //load form from file
        $jsonForm = json_decode(file_get_contents(__DIR__ . "/form.json"), true);
        /*
        $jsonForm["actions"][0]["items"][0]["value"] = $this->ReadAttributeFloat("jd");
        $jsonForm["actions"][0]["items"][1]["value"] = $this->ReadAttributeFloat("jc");
        $jsonForm["actions"][0]["items"][2]["value"] = $this->ReadAttributeFloat("jm");

        $jsonForm["actions"][1]["items"][0]["value"] = $this->ReadAttributeFloat("jde");
        $jsonForm["actions"][1]["items"][1]["value"] = $this->ReadAttributeFloat("jce");
        $jsonForm["actions"][1]["items"][2]["value"] = $this->ReadAttributeFloat("jme");

        $jsonForm["actions"][2]["items"][0]["value"] = $this->ReadAttributeFloat("helioCentLong");
        $jsonForm["actions"][2]["items"][1]["value"] = $this->ReadAttributeFloat("L0");
        $jsonForm["actions"][2]["items"][2]["value"] = $this->ReadAttributeFloat("L1");
        $jsonForm["actions"][2]["items"][3]["value"] = $this->ReadAttributeFloat("L2");
        $jsonForm["actions"][2]["items"][4]["value"] = $this->ReadAttributeFloat("L3");
        $jsonForm["actions"][2]["items"][5]["value"] = $this->ReadAttributeFloat("L4");
        $jsonForm["actions"][2]["items"][6]["value"] = $this->ReadAttributeFloat("L5");

        $jsonForm["actions"][3]["items"][0]["value"] = $this->ReadAttributeFloat("helioCentLat");
        $jsonForm["actions"][3]["items"][1]["value"] = $this->ReadAttributeFloat("B0");
        $jsonForm["actions"][3]["items"][2]["value"] = $this->ReadAttributeFloat("B1");
        $jsonForm["actions"][3]["items"][3]["value"] = $this->ReadAttributeFloat("earthRadVec");

        $jsonForm["actions"][4]["items"][0]["value"] = $this->ReadAttributeFloat("geoCentLong");
        $jsonForm["actions"][4]["items"][1]["value"] = $this->ReadAttributeFloat("geoCentLat");
        
        $jsonForm["actions"][5]["items"][0]["value"] = $this->ReadAttributeFloat("nutationLongitude");
        $jsonForm["actions"][5]["items"][1]["value"] = $this->ReadAttributeFloat("nutationObliquity");

        $jsonForm["actions"][6]["items"][0]["value"] = $this->ReadAttributeFloat("meanOblEcl");
        $jsonForm["actions"][6]["items"][1]["value"] = $this->ReadAttributeFloat("trueOblEcl");
        $jsonForm["actions"][6]["items"][2]["value"] = $this->ReadAttributeFloat("aberCorr");
        $jsonForm["actions"][6]["items"][3]["value"] = $this->ReadAttributeFloat("appSunLong");
        $jsonForm["actions"][6]["items"][4]["value"] = $this->ReadAttributeFloat("appSidTimeGreenwich");

        $jsonForm["actions"][7]["items"][0]["value"] = $this->ReadAttributeFloat("geoSunRAsc");
        $jsonForm["actions"][7]["items"][1]["value"] = $this->ReadAttributeFloat("geoSunDec");
        $jsonForm["actions"][7]["items"][2]["value"] = $this->ReadAttributeFloat("locHourAngle");
        $jsonForm["actions"][7]["items"][3]["value"] = $this->ReadAttributeFloat("topoSunRAsc");
        $jsonForm["actions"][7]["items"][4]["value"] = $this->ReadAttributeFloat("topoSunDec");
        $jsonForm["actions"][7]["items"][5]["value"] = $this->ReadAttributeFloat("topoLocHourAngle");
        $jsonForm["actions"][7]["items"][6]["value"] = $this->ReadAttributeFloat("topoZenAngle");
        $jsonForm["actions"][7]["items"][7]["value"] = $this->ReadAttributeFloat("topoAziAngle");
        $jsonForm["actions"][7]["items"][8]["value"] = $this->ReadAttributeFloat("eqOfTime");
        */

        $jsonForm["actions"][1]["popup"]["items"][0]["items"][0]["value"] = $this->ReadAttributeString("TestCalc_DateTime");
        $jsonForm["actions"][1]["popup"]["items"][1]["items"][0]["value"] = $this->ReadAttributeFloat("TestCalc_Lat");
        $jsonForm["actions"][1]["popup"]["items"][1]["items"][1]["value"] = $this->ReadAttributeFloat("TestCalc_Long");
        $jsonForm["actions"][1]["popup"]["items"][1]["items"][2]["value"] = $this->ReadAttributeFloat("TestCalc_Elevation");
        $jsonForm["actions"][1]["popup"]["items"][1]["items"][3]["value"] = $this->ReadAttributeFloat("TestCalc_DeltaT");
        
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
        $this->SetValue("moonelevation", $moonDat['e']);


        $this->UpdateFormField("Current_JCE", "value", $moonDat['spa']['jce']);

        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("Current_moon_mean_elongation", "value", $moonDat['d']);
        $this->UpdateFormField("Current_sun_mean_anomaly", "value", $moonDat['m']);
        $this->UpdateFormField("Current_moon_mean_anomaly", "value", $moonDat['m_prime']);
        $this->UpdateFormField("Current_moon_latitude_argument", "value", $moonDat['f']);

        $this->UpdateFormField("Current_l", "value", $moonDat['l']);
        $this->UpdateFormField("Current_r", "value", $moonDat['r']);
        $this->UpdateFormField("Current_b", "value", $moonDat['b']);

        
    }

    public function CalcTestValues(int $date, float $deltaT, float $lat, float $long, float $elev, float $pressure, float $temperature): void{

        $jd = new JulianDay($deltaT, 0, $date);
        $sun = new Sun($deltaT, 0, $date, $lat, $long, $elev, $pressure, $temperature);

        $moon = new Moon($deltaT, 0, $date, $lat, $long, $elev, $pressure, $temperature);

        $moonDat = array();
        $moon->sampa_calculate($moonDat);

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

        $this->UpdateFormField("TestCalc_l", "value", $moonDat['l']);
        $this->UpdateFormField("TestCalc_r", "value", $moonDat['r']);
        $this->UpdateFormField("TestCalc_b", "value", $moonDat['b']);

        $this->UpdateFormField("TestCalc_lamda_prime", "value", $moonDat['lamda_prime']);
        $this->UpdateFormField("TestCalc_beta", "value", $moonDat['beta']);
        $this->UpdateFormField("TestCalc_cap_delta", "value", $moonDat['cap_delta']);
        $this->UpdateFormField("TestCalc_pi", "value", $moonDat['pi']);
        $this->UpdateFormField("TestCalc_lamda", "value", $moonDat['lamda']);
        $this->UpdateFormField("TestCalc_alpha", "value", $moonDat['alpha']);
        $this->UpdateFormField("TestCalc_delta", "value", $moonDat['delta']);
        $this->UpdateFormField("TestCalc_h", "value", $moonDat['h']);
        $this->UpdateFormField("TestCalc_del_alpha", "value", $moonDat['del_alpha']);
        $this->UpdateFormField("TestCalc_alpha_prime", "value", $moonDat['alpha_prime']);
        $this->UpdateFormField("TestCalc_delta_prime", "value", $moonDat['delta_prime']);
        $this->UpdateFormField("TestCalc_h_prime", "value", $moonDat['h_prime']);
        $this->UpdateFormField("TestCalc_e0", "value", $moonDat['e0']);
        $this->UpdateFormField("TestCalc_zenith", "value", $moonDat['zenith']);
        $this->UpdateFormField("TestCalc_azimuth", "value", $moonDat['azimuth']);

        $this->UpdateFormField("TestCalc_topocentric_zenith_angle", "value", $moonDat['zenith']);
        $this->UpdateFormField("TestCalc_topocentric_azimuth_angle", "value", $moonDat['azimuth']);
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

    public function WriteDebugMessage($Message){
        $this->SendDebug("Moon", $Message, 0);
    }


    public function WriteFloatAttribute(string $att, float $val)
    {

        $this->WriteAttributeFloat($att, $val);
    }

    public function WriteStringAttribute(string $att, string $val)
    {

        $this->WriteAttributeString($att, $val);
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