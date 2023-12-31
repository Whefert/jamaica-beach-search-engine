<?php 
require "Beach.php";

class Beaches{
    private $allBeachesQuery = "SELECT beach.beach_id, beach.beach_name, parish.parish, 
    beach.licensed, beach.entrance_fee, beach.opening_hour, beach.closing_hour, beach.image, 
    beach.owner_1, beach.owner_2 FROM beach 
    LEFT JOIN parish ON beach.parish_id = parish.parish_id";

    public function getBeaches() {
        global $conn;
        $result = 0;

        if($_GET["search"]===""){
            $result = mysqli_query($conn, $this->allBeachesQuery. ";");
        }else{        
            $searchTermQuery = $this->allBeachesQuery . 
            " WHERE beach.beach_name LIKE " . "'%". $_GET["search"]. "%'" . 
            " OR parish.parish LIKE " . "'%". $_GET["search"]. "%'" . ";";
            $result = mysqli_query($conn, $searchTermQuery);
        }

        
        if (mysqli_num_rows($result) > 0) {
        // output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            if($row["image"] == null){
                $row["image"] = "./images/pexels-asad-photo-maldives-1450353.jpg";
            }

            $beach = new Beach($row["beach_id"], $row["beach_name"], 
            $row["parish"], $row["licensed"], $row["entrance_fee"], $row["opening_hour"],
             $row["closing_hour"], $row["image"]);
            $beach->showBeach();
        }        
        } else {
        echo "<p class='display-3'>No beaches found</p>";
        }

    }




    public static function importBeaches(){
        //Source website: https://websitearchive2020.nepa.gov.jm/new/services_products/subsites/beach_guide/beach_list.php

        //import beaches to database
        if (($handle = fopen("jamaican_beaches.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            //create fields for new Beach
            $name = $data[0];
            //search for corresponding parish and return parish id based on iput
            $parish_id = Parish::findParishID($data[1]);
            $owner_1 = $data[2];
            $owner_2 = $data[3];
            $licensed = $data[4];
            $entrance_fee = $data[5];
            $opening_hour = $data[6];
            $closing_hour = $data[7];
            $image = $data[8];
            //insert Beach
            Beach::insertBeach($name, $parish_id, $owner_1, $owner_2, $licensed, $entrance_fee, $opening_hour, $closing_hour, $image);
            }
            fclose($handle);
        }
            }
    }
?>