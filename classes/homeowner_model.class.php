<?php

class homeowner_Model
{
    public $file;
    public $array_of_homeowner_raw_data;
    public $array_of_homeowner_data;
    public $array_of_homeowners;

    public function csv_to_array()
    {
        if(file_exists('examples__284_29.csv'))
        {
            $this->file = file('examples__284_29.csv');
            unset($this->file[0]);
            $string = implode(" ", array_values($this->file));
            $this->array_of_homeowner_raw_data = explode(" ",$string);
            return $this->find_multi_ownerships();
        }
    } 

    public function find_multi_ownerships()
    {
        $counter = 1;
        foreach($this->file as $a){
            if((str_contains($a, "and"))||(str_contains($a, "&"))){
                $multi_owner_corrected_array = $this->process_multi_ownership_data($this->file[$counter]);
                $this->array_of_homeowner_data[] = $multi_owner_corrected_array[0];
                $this->array_of_homeowner_data[] = $multi_owner_corrected_array[1];
            }else{
                $this->array_of_homeowner_data[] = $a;
            }
            $counter++;
        }
        return $this->key_value_matcher($this->array_of_homeowner_data);
    }

    public function process_multi_ownership_data($multi_ownership_data)
    {
        // $data = explode(" ",substr(trim($multi_ownership_data), 0, -1));
        $data = explode(" ",$multi_ownership_data);
        $owner_one = [];
        $owner_two = [];
        $counter = 0;
        foreach($data as $d)
        {
            if(($d == "and")||($d == "&")){
                $owner_one_array = array_slice($data,0,$counter);
                $owner_two_array = array_slice($data,$counter+1, count($data));
                if((count($owner_one_array) == 1) && (count($owner_two_array) == 3)){ 
                    $owner_one = implode(" ", array_merge(array_slice($data,0, ($counter)),array_slice($data,$counter+2,$counter),array_slice($data,$counter-2,count($data))));
                    $owner_two = implode(" ", array_merge(array_slice($data,$counter+1,$counter),array_slice($data,$counter-2,count($data))));
                }elseif(count($owner_one_array) == 1){
                    $owner_one = implode(" ", array_merge(array_slice($data,0, ($counter)),array_slice($data,$counter-2,count($data))));
                    $owner_two = implode(" ", array_merge(array_slice($data,$counter+1,$counter),array_slice($data,$counter-2,count($data))));
                }else{
                    $owner_one = implode(" ", array_slice($data,0, ($counter)));
                    $owner_two = implode(" ", array_slice($data,($counter+1),count($data)));  
                }
            }
            $counter++;
        }
        return array($owner_one, $owner_two);
    }

    public function key_value_matcher($homeowner_data){
        foreach($homeowner_data as $d){
            $homeowner_data_array = explode(" ", $d);
            $array_len = sizeof($homeowner_data_array);
            $array_of_keys = array("title", "first_name", "initial", "last_name");
            $first_name = "null";
            $initial = "null";
            switch($array_len)
            {
                case $array_len == 2:
                    $title = $homeowner_data_array[0];
                    $last_name = $homeowner_data_array[1];
                    $homeowner_string = $title." ".$first_name." ".$initial." ". substr(trim($last_name), 0, -1);
                    $homeowner_array_corrected_for_nulls = explode(" ",$homeowner_string);
                    $this->array_of_homeowners[] = array_combine($array_of_keys, $homeowner_array_corrected_for_nulls); 
                    break;

                case $array_len == 3 && (strlen($homeowner_data_array[1]) == 2) && (strpos($homeowner_data_array[1], ".")==true):
                    $title = $homeowner_data_array[0];
                    $initial = $homeowner_data_array[1];
                    $last_name = $homeowner_data_array[2];
                    $homeowner_string = $title." ".$first_name." ".$initial." ". substr(trim($last_name), 0, -1);
                    $homeowner_array_corrected_for_nulls = explode(" ",$homeowner_string);
                    $this->array_of_homeowners[] = array_combine($array_of_keys, $homeowner_array_corrected_for_nulls); 
                    break;
                case $array_len == 3 && (strlen($homeowner_data_array[1]) == 1):
                    $title = $homeowner_data_array[0];
                    $initial = $homeowner_data_array[1];
                    $last_name = $homeowner_data_array[2];
                    $homeowner_string = $title." ".$first_name." ".$initial." ". substr(trim($last_name), 0, -1);
                    $homeowner_array_corrected_for_nulls = explode(" ",$homeowner_string);
                    $this->array_of_homeowners[] = array_combine($array_of_keys, $homeowner_array_corrected_for_nulls); 
                    break;
                case $array_len == 3 && (strlen($homeowner_data_array[1]) >= 2) && (strpos($homeowner_data_array[1], ".")==false):
                    $title = $homeowner_data_array[0];
                    $first_name = $homeowner_data_array[1];
                    $last_name = $homeowner_data_array[2];
                    $homeowner_string = $title." ".$first_name." ".$initial." ". substr(trim($last_name), 0, -1);
                    $homeowner_array_corrected_for_nulls = explode(" ",$homeowner_string);
                    $this->array_of_homeowners[] = array_combine($array_of_keys, $homeowner_array_corrected_for_nulls); 
                    break;
                case $array_len == 4:
                    $this->array_of_homeowners = array_combine($array_of_keys,$homeowner_data_array);
                    break;
                default:
                    echo "Sorry, not enough homeowner data";
            }
        }
        ?><pre><?php
        print_r($this->array_of_homeowners);
        ?></pre><?php

    }
}