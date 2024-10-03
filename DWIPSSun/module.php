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
            IPS_SetVariableProfileValues("DWIPS." . $this->Translate("season"), 1, 4, 0);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 1, $this->Translate("spring"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 2, $this->Translate("summer"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 3, $this->Translate("fall"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("season"), 4, $this->Translate("winter"), "", -1);
        }
        if (!IPS_VariableProfileExists("DWIPS." . $this->Translate("compass_rose"))) {
            IPS_CreateVariableProfile("DWIPS." . $this->Translate("compass_rose"), 2);
            IPS_SetVariableProfileValues("DWIPS." . $this->Translate("compass_rose"), 0, 360, 0);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 0, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 22.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 45, $this->Translate("NE"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 67.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 90, $this->Translate("E"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 112.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 135, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 157.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 180, $this->Translate("S"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 202.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 225, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 247.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 270, $this->Translate("W"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 292.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 315, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 337.5, $this->Translate("N"), "", -1);
            IPS_SetVariableProfileAssociation("DWIPS." . $this->Translate("compass_rose"), 360, $this->Translate("W"), "", -1);
        }
        $p = 1;
        $this->MaintainVariable("lastsunrise", $this->Translate("sunrise"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextsunrise", $this->Translate("sunrise"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("lastsunset", $this->Translate("sunset"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextsunset", $this->Translate("sunset"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("laststartastronomicaltwilight", $this->Translate("startastronomicaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextstartastronomicaltwilight", $this->Translate("startastronomicaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("laststartnauticaltwilight", $this->Translate("startnauticaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextstartnauticaltwilight", $this->Translate("startnauticaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("laststartciviltwilight", $this->Translate("startciviltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextstartciviltwilight", $this->Translate("startciviltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("solarnoon", $this->Translate("solarnoon"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("laststopciviltwilight", $this->Translate("stopciviltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextstopciviltwilight", $this->Translate("stopciviltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("laststopnauticaltwilight", $this->Translate("stopnauticaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextstopnauticaltwilight", $this->Translate("stopnauticaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("laststopastronomicaltwilight", $this->Translate("stopastronomicaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("nextstopastronomicaltwilight", $this->Translate("stopastronomicaltwilight"), 1, "~UnixTimestamp", $p, true);
        $p++;
        $this->MaintainVariable("sunlightduration", $this->Translate("sunlightduration"), 1, "", $p, true);
        $p++;
        $this->MaintainVariable("sunlightdurationstr", $this->Translate("sunlightduration"), 3, "", $p, true);
        $p++;
        $this->MaintainVariable("sunazimut", $this->Translate("sunazimut"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("sundirection", $this->Translate("sundirection"), 3, "", $p, true);
        $p++;
        $this->MaintainVariable("sunelevation", $this->Translate("sunelevation"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("sundeclination", $this->Translate("sundeclination"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("sunelevationmin", $this->Translate("sunelevationmin"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("sunelevationmax", $this->Translate("sunelevationmax"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("day", $this->Translate("day"), 0, "", $p, true);
        $p++;
        $this->MaintainVariable("insideCivilTwilight", $this->Translate("insideCivilTwilight"), 0, "", $p, true);
        $p++;
        $this->MaintainVariable("durationOfSunrise", $this->Translate("durationOfSunrise"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("sundistance", $this->Translate("sundistance"), 1, "", $p, true);
        $p++;
        $this->MaintainVariable("season", $this->Translate("season"), 1, "DWIPS." . $this->Translate("season"), $p, false);
        $this->MaintainVariable("season", $this->Translate("season"), 1, "DWIPS." . $this->Translate("season"), $p, true);
        $p++;
        $this->MaintainVariable("shadowLength", $this->Translate("shadowlength"), 2, "", $p, true);
        $p++;
        $this->MaintainVariable("solarirradiancespace", $this->Translate("solarirradiancespace"), 2, "", $p, true);
        $p++; //"Astronomie.Radiant_Power", 26);
        $this->MaintainVariable("solarirradiancerectangular", $this->Translate("solarirradiancerectangular"), 2, "", $p, true);
        $p++; //"Astronomie.Radiant_Power", 27);
        $this->MaintainVariable("solarirradianceground", $this->Translate("solarirradianceground"), 2, "", $p, true);
        $p++; //"Astronomie.Radiant_Power", 28);
        $this->MaintainVariable("solarirradiancepvcollector", $this->Translate("solarirradiancepvcollector"), 2, "", $p, true);
        $p++; //"Astronomie.Radiant_Power", 40);


        $this->MaintainVariable("moonphase", $this->Translate("moonphase"), 3, "", 30, true);


        $this->RegisterPropertyFloat("Latitude", 50.0);
        $this->RegisterPropertyFloat("Longitude", 9);
        $this->RegisterPropertyFloat("Elevation", 1);
        $this->RegisterPropertyFloat("deltaT", 69.184);

        $this->RegisterPropertyInteger("UpdateInterval", 1);

        $this->RegisterAttributeFloat("jd", 0);
        $this->RegisterAttributeFloat("jc", 0);
        $this->RegisterAttributeFloat("jm", 0);
        $this->RegisterAttributeFloat("jde", 0);
        $this->RegisterAttributeFloat("jce", 0);
        $this->RegisterAttributeFloat("jme", 0);
        $this->RegisterAttributeFloat("helioCentLong", 0);
        $this->RegisterAttributeFloat("L0", 0);
        $this->RegisterAttributeFloat("L1", 0);
        $this->RegisterAttributeFloat("L2", 0);
        $this->RegisterAttributeFloat("L3", 0);
        $this->RegisterAttributeFloat("L4", 0);
        $this->RegisterAttributeFloat("L5", 0);
        $this->RegisterAttributeFloat("helioCentLat", 0);
        $this->RegisterAttributeFloat("B0", 0);
        $this->RegisterAttributeFloat("B1", 0);
        $this->RegisterAttributeFloat("earthRadVec", 0);
        $this->RegisterAttributeFloat("geoCentLong", 0);
        $this->RegisterAttributeFloat("geoCentLat", 0);
        $this->RegisterAttributeFloat("nutationLongitude", 0);
        $this->RegisterAttributeFloat("nutationObliquity", 0);
        $this->RegisterAttributeFloat("meanOblEcl", 0);
        $this->RegisterAttributeFloat("trueOblEcl", 0);
        $this->RegisterAttributeFloat("aberCorr", 0);
        $this->RegisterAttributeFloat("appSunLong", 0);
        $this->RegisterAttributeFloat("appSidTimeGreenwich", 0);
        $this->RegisterAttributeFloat("geoSunRAsc", 0);
        $this->RegisterAttributeFloat("geoSunDec", 0);
        $this->RegisterAttributeFloat("locHourAngle", 0);
        $this->RegisterAttributeFloat("topoSunRAsc", 0);
        $this->RegisterAttributeFloat("topoSunDec", 0);
        $this->RegisterAttributeFloat("topoLocHourAngle", 0);
        $this->RegisterAttributeFloat("topoZenAngle", 0);
        $this->RegisterAttributeFloat("topoAziAngle", 0);
        $this->RegisterAttributeFloat("eqOfTime", 0);



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
        
        $this->WriteAttributeFloat("jd", ASTROGEN::JulianDay());
        $this->WriteAttributeFloat("jc", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jd")));
        $this->WriteAttributeFloat("jm", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jc")));
        $this->WriteAttributeFloat("jde", ASTROGEN::JDE($this->ReadAttributeFloat("jd"), $this->ReadPropertyFloat("deltaT")));
        $this->WriteAttributeFloat("jce", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jde")));
        $this->WriteAttributeFloat("jme", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jce")));
        
        $this->WriteAttributeFloat("helioCentLong", ASTROSUN::HeliocentricLongitudeDEG($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("L0", ASTROSUN::L0($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("L1", ASTROSUN::L1($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("L2", ASTROSUN::L2($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("L3", ASTROSUN::L3($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("L4", ASTROSUN::L4($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("L5", ASTROSUN::L5($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("helioCentLat", ASTROSUN::HeliocentricLatitude($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("B0", ASTROSUN::B0($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("B1", ASTROSUN::B1($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("earthRadVec", ASTROSUN::EarthRadiusVector($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("geoCentLong", ASTROSUN::GeocentricLongitude($this->ReadAttributeFloat("helioCentLong")));
        $this->WriteAttributeFloat("geoCentLat", ASTROSUN::GeocentricLatitude($this->ReadAttributeFloat("helioCentLat")));
        
        $this->WriteAttributeFloat("nutationLongitude", ASTROSUN::NutationInLongitude($this->ReadAttributeFloat("jce")));
        $this->WriteAttributeFloat("nutationObliquity", ASTROSUN::NutationInObliquity($this->ReadAttributeFloat("jce")));

        $this->WriteAttributeFloat("meanOblEcl", ASTROSUN::MeanObliquityOfTheEcliptic($this->ReadAttributeFloat("jme")));
        $this->WriteAttributeFloat("trueOblEcl", ASTROSUN::TrueObliquityOfTheEcliptic($this->ReadAttributeFloat("meanOblEcl"), $this->ReadAttributeFloat("nutationObliquity")));
        $this->WriteAttributeFloat("aberCorr", ASTROSUN::AberrationCorrection($this->ReadAttributeFloat("earthRadVec")));
        $this->WriteAttributeFloat("appSunLong", ASTROSUN::ApparentSunLongitude($this->ReadAttributeFloat("geoCentLong"), $this->ReadAttributeFloat("nutationLongitude"), $this->ReadAttributeFloat("aberCorr")));
        $this->WriteAttributeFloat("appSidTimeGreenwich", ASTROSUN::ApparentSiderealTimeAtGreenwich($this->ReadAttributeFloat("jd"), $this->ReadAttributeFloat("jc"), $this->ReadAttributeFloat("nutationLongitude"), $this->ReadAttributeFloat("trueOblEcl")));

        $this->WriteAttributeFloat("geoSunRAsc", ASTROSUN::GeocentricSunRightAscension($this->ReadAttributeFloat("appSunLong"), $this->ReadAttributeFloat("trueOblEcl"), $this->ReadAttributeFloat("geoCentLat")));
        $this->WriteAttributeFloat("geoSunDec", ASTROSUN::GeocentricSunDeclination($this->ReadAttributeFloat("geoCentLong"), $this->ReadAttributeFloat("trueOblEcl"), $this->ReadAttributeFloat("appSunLong")));
        $this->WriteAttributeFloat("locHourAngle", ASTROSUN::LocalHourAngle($this->ReadAttributeFloat("appSidTimeGreenwich"), $this->ReadPropertyFloat("Longitude"), $this->ReadAttributeFloat("geoSunRAsc")));
        $this->WriteAttributeFloat("topoSunRAsc", ASTROSUN::TopocentricSunRightAscension($this->ReadAttributeFloat("earthRadVec"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Elevation"), $this->ReadAttributeFloat("locHourAngle"), $this->ReadAttributeFloat("geoSunDec"), $this->ReadAttributeFloat("geoSunRAsc")));
        $this->WriteAttributeFloat("topoSunDec", ASTROSUN::TopocentricSunDeclination($this->ReadAttributeFloat("earthRadVec"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Elevation"), $this->ReadAttributeFloat("locHourAngle"), $this->ReadAttributeFloat("geoSunDec"), $this->ReadAttributeFloat("geoSunRAsc")));
        $this->WriteAttributeFloat("topoLocHourAngle", ASTROSUN::TopocentricLocalHourAngle($this->ReadAttributeFloat("earthRadVec"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Elevation"), $this->ReadAttributeFloat("locHourAngle"), $this->ReadAttributeFloat("geoSunDec"), $this->ReadAttributeFloat("geoSunRAsc")));
        $this->WriteAttributeFloat("topoZenAngle", ASTROSUN::TopocentricZenithAngle($this->ReadPropertyFloat("Latitude"), $this->ReadAttributeFloat("geoSunDec"), $this->ReadAttributeFloat("topoLocHourAngle"), 820, 10));
        $this->WriteAttributeFloat("topoAziAngle", ASTROSUN::TopocentricAzimuthAngle($this->ReadPropertyFloat("Latitude"), $this->ReadAttributeFloat("topoSunDec"), $this->ReadAttributeFloat("topoLocHourAngle")));
        $this->WriteAttributeFloat("eqOfTime", ASTROSUN::EqOfTime($this->ReadAttributeFloat("jme"), $this->ReadAttributeFloat("geoSunRAsc"), $this->ReadAttributeFloat("nutationLongitude"), $this->ReadAttributeFloat("trueOblEcl")));
   

        $this->UpdateFormField("jd", "value", $this->ReadAttributeFloat("jd"));
        $this->UpdateFormField("jc", "value", $this->ReadAttributeFloat("jc"));
        $this->UpdateFormField("jm", "value", $this->ReadAttributeFloat("jm"));
        $this->UpdateFormField("jde", "value", $this->ReadAttributeFloat("jde"));
        $this->UpdateFormField("jce", "value", $this->ReadAttributeFloat("jce"));
        $this->UpdateFormField("jme", "value", $this->ReadAttributeFloat("jme"));

        $this->UpdateFormField("helioCentLong", "value", $this->ReadAttributeFloat("helioCentLong"));
        $this->UpdateFormField("L0", "value", $this->ReadAttributeFloat("L0"));
        $this->UpdateFormField("L1", "value", $this->ReadAttributeFloat("L1"));
        $this->UpdateFormField("L2", "value", $this->ReadAttributeFloat("L2"));
        $this->UpdateFormField("L3", "value", $this->ReadAttributeFloat("L3"));
        $this->UpdateFormField("L4", "value", $this->ReadAttributeFloat("L4"));
        $this->UpdateFormField("L5", "value", $this->ReadAttributeFloat("L5"));
        $this->UpdateFormField("helioCentLat", "value", $this->ReadAttributeFloat("helioCentLat"));
        $this->UpdateFormField("B0", "value", $this->ReadAttributeFloat("B0"));
        $this->UpdateFormField("B1", "value", $this->ReadAttributeFloat("B1"));
        $this->UpdateFormField("earthRadVec", "value", $this->ReadAttributeFloat("earthRadVec"));
        $this->UpdateFormField("geoCentLong", "value", $this->ReadAttributeFloat("geoCentLong"));
        $this->UpdateFormField("geoCentLat", "value", $this->ReadAttributeFloat("geoCentLat"));
        
        $this->UpdateFormField("nutationLong", "value", $this->ReadAttributeFloat("nutationLongitude"));
        $this->UpdateFormField("nutationObl", "value", $this->ReadAttributeFloat("nutationObliquity"));

        $this->UpdateFormField("meanOblEcl", "value", $this->ReadAttributeFloat("meanOblEcl"));
        $this->UpdateFormField("trueOblEcl", "value", $this->ReadAttributeFloat("trueOblEcl"));
        $this->UpdateFormField("aberCorr", "value", $this->ReadAttributeFloat("aberCorr"));
        $this->UpdateFormField("appSunLong", "value", $this->ReadAttributeFloat("appSunLong"));
        $this->UpdateFormField("appSidTimeGreenwich", "value", $this->ReadAttributeFloat("appSidTimeGreenwich"));

        $this->UpdateFormField("geoSunRAsc", "value", $this->ReadAttributeFloat("geoSunRAsc"));
        $this->UpdateFormField("geoSunDec", "value", $this->ReadAttributeFloat("geoSunDec"));
        $this->UpdateFormField("locHourAngle", "value", $this->ReadAttributeFloat("locHourAngle"));
        $this->UpdateFormField("topoSunRAsc", "value", $this->ReadAttributeFloat("topoSunRAsc"));
        $this->UpdateFormField("topoSunDec", "value", $this->ReadAttributeFloat("topoSunDec"));
        $this->UpdateFormField("topoLocHourAngle", "value", $this->ReadAttributeFloat("topoLocHourAngle"));
        $this->UpdateFormField("topoZenAngle", "value", $this->ReadAttributeFloat("topoZenAngle"));
        $this->UpdateFormField("topoAziAngle", "value", $this->ReadAttributeFloat("topoAziAngle"));
        $this->UpdateFormField("eqOfTime", "value", $this->ReadAttributeFloat("eqOfTime"));

        $now = time();

 

        $this->SetValue("solarnoon", ASTROSUN::SunriseSunsetTransit(idate('Y', $now), idate('m', $now), idate('d', $now), $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333)["T"]);
        $this->SetValue("lastsunrise", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333, "R"));
        $this->SetValue("nextsunrise", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333, "R"));
        $this->SetValue("lastsunset", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333, "S"));
        $this->SetValue("nextsunset", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333, "S"));
        $this->SetValue("laststartciviltwilight", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -6, "R"));
        $this->SetValue("nextstartciviltwilight", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -6, "R"));
        $this->SetValue("laststartnauticaltwilight", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -12, "R"));
        $this->SetValue("nextstartnauticaltwilight", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -12, "R"));
        $this->SetValue("laststartastronomicaltwilight", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -18, "R"));
        $this->SetValue("nextstartastronomicaltwilight", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -18, "R"));
        $this->SetValue("laststopciviltwilight", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -6, "R"));
        $this->SetValue("nextstopciviltwilight", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -6, "R"));
        $this->SetValue("laststopnauticaltwilight", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -12, "R"));
        $this->SetValue("nextstopnauticaltwilight", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -12, "R"));
        $this->SetValue("laststopastronomicaltwilight", ASTROSUN::lastEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -18, "R"));
        $this->SetValue("nextstopastronomicaltwilight", ASTROSUN::nextEl($now, $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -18, "R"));


        $sundura = (ASTROSUN::SunriseSunsetTransit(idate('Y', $now), idate('m', $now), idate('d', $now), $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333)["S"] - ASTROSUN::SunriseSunsetTransit(idate('Y', $now), idate('m', $now), idate('d', $now), $this->ReadPropertyFloat("deltaT"), $this->ReadPropertyFloat("Latitude"), $this->ReadPropertyFloat("Longitude"), -0.8333)["R"]) / 60.0 / 60.0;
        //$this->SetValue("sunlightduration", $sundura);
        //$this->SetValue("sunlightdurationstr", date('H:i:s', ($sunset - $sunrise - intval(date('Z', $sunset - $sunrise)))));

        $solarAzimut = ASTROSUN::TopocentricAzimuthAngle($this->ReadPropertyFloat("Latitude"), $this->ReadAttributeFloat("topoSunDec"), $this->ReadAttributeFloat("topoLocHourAngle"));

        $this->SetValue("sundistance", $this->ReadAttributeFloat("earthRadVec") * ASTROSUN::AU / 1000);
        $this->SetValue("sundeclination", ASTROSUN::DeclinationOfSun($this->ReadAttributeFloat("jd")));


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
        $jdtomorrow = $jd + 1;
        $jctomorrow = ASTROGEN::JulianCentury($jdtomorrow);

        $solarZenith = ASTROSUN::SolarZenith($jc, $localTime, $latitude, $longitude, $timezone);


     
        $sunelevation = ASTROSUN::SolarElevation($jc, $localTime, $latitude, $longitude, $timezone);
        $sundistance = ASTROSUN::SunRadVector($jc) * 149597870.7;
        $solarirradiancespace = 3.845 * pow(10, 26) / (4 * pi() * pow($sundistance * 1000, 2));



        $this->SetValue("sunazimut", $solarAzimut);
        //$this->SetValue("sundeclination", ASTROSUN::Declination($jc));
        $this->SetValue("sunelevation", $sunelevation);
        $this->SetValue("sunelevationmin", -90 + $latitude + ASTROSUN::Declination($jc));
        $this->SetValue("sunelevationmax", 90 - $latitude + ASTROSUN::Declination($jc));

        $this->SetValue("sundirection", ASTROSUN::SolarDirection($solarAzimut));
        $this->SetValue("season", ASTROSUN::Season($jc, $latitude));


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
        //$this->SetValue("durationOfSunrise", ASTROSUN::DurationOfSunrise($latitude, $longitude, $jc));
        /*
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
        }*/

        $this->SetValue("moonphase", ASTROMOON::PhaseStr());
    }


}
?>