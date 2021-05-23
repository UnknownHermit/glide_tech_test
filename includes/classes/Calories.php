<?php

class Calories{
    private $db;

    public function __construct(){
        $database = new Database();
        $this->db = $database->connect();
    }

    public function importData(){
        // Step 1 - Do a post curl to the website to fetch all the data 
        $csvData = $this->curlAPI();
        
        // Step 2 - Create a CSV from the data
        $file = ROOTDIR."/includes/resources/calories.csv";
        $this->createCSV($file, $csvData);
        unset($csvData);

        // Step 3 - Use the CSV data to populate the tables
        $this->populateTables($file);
    }
    
    public function curlAPI(){
        $curlUrl = "http://mip-prd-web.azurewebsites.net/DataItemViewer/DownloadFile";
        $postData = Array(
            "LatestValue" => true,
            "PublicationObjectIds" => "408:28, 408:5328, 408:5320, 408:5291, 408:5366, 408:5312, 408:5346, 408:5324, 408:5316, 408:5308, 408:5336, 408:5333, 408:5342, 408:5354, 408:82, 408:70, 408:59, 408:38, 408:49",
            "PublicationObjectStagingIds" => "PUBOBJ1660,PUBOB4507,PUBOB4508,PUBOB4510,PUBOB4509,PUBOB4511,PUBOB4512,PUBOB4513,PUBOB4514,PUBOB4515,PUBOB4516,PUBOB4517,PUBOB4518,PUBOB4519,PUBOB4521,PUBOB4520,PUBOB4522,PUBOBJ1661,PUBOBJ1662",
            "Applicable" => "applicableAt",
            "PublicationObjectCount" => 19,
            "FromUtcDateTime" => "2021-01-01T00:00:00.000Z",
            "ToUtcDateTime" => "2021-05-22T23:00:00.000Z",
            "FileType" => "Csv",
        );
        $curler = new Curler($curlUrl);
        $csvData = $curler->doPost($postData);

        return $csvData;
    }

    public function createCSV($file, $csvData){
        file_put_contents($file, $csvData);
    }

    public function populateTables($file){
        if( ($handle = fopen($file, "r")) !== FALSE){
            $skipFirst = true;
            $dataItems = Array();
            while( ($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                if($skipFirst){
                    $skipFirst = false;
                    continue;
                }
                // Needed CSV Columns are [1] Applicable for (date), [2] Data item, [3] Value
                $dateFor = strtotime(str_replace("/","-",$data[1])); 
                $dataItem = $data[2];
                $dataValue = $data[3];
                if(!isset($dataItems[$dataItem])){
                    $cleanDataItem = $this->db->real_escape_string($dataItem);
                    $sqlIns = "INSERT INTO data_items (data_item)
                               VALUES('$cleanDataItem');";
                    $this->db->query($sqlIns);
                    $dataItems[$dataItem]["id"] = $this->db->insert_id;
                    $dataItems[$dataItem]["calorie_data"] = Array();
                }
                $dataItems[$dataItem]["calorie_data"][] = Array(
                    "dateFor" => $dateFor,
                    "dataValue" => $dataValue
                ); 
            }
            fclose($handle);
            foreach($dataItems as $data){
                $dataItem = $data["id"];
                $sqlIns = "INSERT INTO calorie_data (data_item, date_for, calorific_value) VALUES";
                foreach($data["calorie_data"] as $calorie){
                    $sqlIns .= "('$dataItem', '$calorie[dateFor]', '$calorie[dataValue]'),";
                }
                $sqlIns = rtrim($sqlIns,",").";";
                $this->db->query($sqlIns);
            }
        }
    }

    public function wipeData(){
        // Step 1 - Delete data from the tables
        $sqlDelDataItems = "DELETE FROM data_items";
        $this->db->query($sqlDelDataItems);
        $sqlDelCalorieData = "DELETE FROM calorie_data";
        $this->db->query($sqlDelCalorieData);
        
        // Step 2 - Reset the auto-incrementation
        $sqlAltDataItems = "ALTER TABLE data_items AUTO_INCREMENT = 1";
        $this->db->query($sqlAltDataItems);
        $sqlAltCalorieData = "ALTER TABLE calorie_data AUTO_INCREMENT = 1";
        $this->db->query($sqlAltCalorieData);
    }

    public function createCalorieTableHTML(){
        $data = $this->fetchCalorieData();
        if($data["status"] == "ok"){
            $html = "<table>";
                $html .= "<tr>";
                    $html .= "<th>Data Item</th>";
                    $html .= "<th>Date</th>";
                    $html .= "<th>Calorific Value</th>";
                $html .= "</tr>";
            foreach($data["data"] as $calorieData){
                $date = date("d-m-Y",$calorieData["date_for"]);
                $html .= "<tr>";
                    $html .= "<td>$calorieData[data_item]</td>";
                    $html .= "<td>$date</td>";
                    $html .= "<td>$calorieData[calorific_value]</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        }
        else{
            $html = "<p>$data[error]</p>";
        }
        return $html;
    }

    public function fetchCalorieData($filters=Array()){
        $status = Array();
        $sqlSel = "SELECT a.data_item, b.date_for, b.calorific_value
                    FROM data_items AS a
                    INNER JOIN calorie_data AS b
                    ON a.id = b.data_item
                    ORDER BY b.date_for DESC;";
        $sqlRes = $this->db->query($sqlSel);
        if($sqlRes->num_rows > 0){
            $calorieData = Array();
            while($data = $sqlRes->fetch_assoc()){
                $calorieData[] = Array(
                    "data_item" => $data["data_item"],
                    "date_for" => $data["date_for"],
                    "calorific_value" => $data["calorific_value"]
                );
            }
            $status = Array(
                "status" => "ok",
                "data" => $calorieData
            );
        }
        else{
            $status = Array(
                "status" => "error",
                "error" => "No calorie data found"
            );
        }
        return $status;
    }
}


?>