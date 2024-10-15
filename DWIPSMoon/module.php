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


        $l = ASTROMOON::MoonEarthDistance($this->ReadAttributeFloat("jce"));
    }

    public function LoadSetupFromSun(){
        $guid = "{8FEB8771-2E4C-CB78-EA91-52546AE77A79}";
        $mods = IPS_GetInstanceListByModuleID($guid);
        if(count($mods1)==1){
            $sett = IPS_GetInstance($mods[0])->GetSettings();
            print_r($sett);
        }
    }

}
?>