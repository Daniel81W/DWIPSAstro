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

        $this->WriteAttributeFloat("jd", ASTROGEN::oldJulianDay());
        $this->WriteAttributeFloat("jc", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jd")));
        $this->WriteAttributeFloat("jm", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jc")));
        $this->WriteAttributeFloat("jde", ASTROGEN::JDE($this->ReadAttributeFloat("jd"), $this->ReadPropertyFloat("deltaT")));
        $this->WriteAttributeFloat("jce", ASTROGEN::JulianCentury($this->ReadAttributeFloat("jde")));
        $this->WriteAttributeFloat("jme", ASTROGEN::JulianMillennium($this->ReadAttributeFloat("jce")));


        $this->SetValue("moonazimuth", $moonDat['azimuth']);
        $this->SetValue("moonelevation", $moonDat['zenith']);


        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
        $this->UpdateFormField("Current_moon_mean_longitude", "value", $moonDat['l_prime']);
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