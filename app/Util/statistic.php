<?php

function monthStatistic($date)
{
        $daysInMonth = $date->daysInMonth;
        
            //pgsql
    //$statistic = DB::select("select count(*) as count, day from (select date_part('year', procedure_at) as year, date_part('month', procedure_at) as month, date_part('day', procedure_at) as day from sessions where procedure_at >= ? and procedure_at < ? and last_message_id > 5) as date_part_sessions group by date_part_sessions.year, date_part_sessions.month, date_part_sessions.day", [
    //mysql
            $statistic = DB::select("select count(*) as count, day from (select year(FROM_UNIXTIME(procedure_at)) as year, MONTH(FROM_UNIXTIME(procedure_at)) as month, MONTH(FROM_UNIXTIME(procedure_at)) as day from sessions where procedure_at >= ? and procedure_at < ? and last_message_id > 5) as date_part_sessions group by date_part_sessions.year, date_part_sessions.month, date_part_sessions.day", [
        $date->copy()->startOfMonth(),
        $date->copy()->addMonths(1)->startOfMonth()
    ]);
    $statistic = array_column($statistic, 'count', 'day');
    for($i = 1; $i <= $daysInMonth; $i++) {
        if(!array_key_exists($i, $statistic)) {
        $statistic[$i] = 0;    
        }//if not exists
    }//for i
    ksort($statistic);
    return $statistic;
}

function totalDrugStatistic()
{
    return DB::select("select drugs.name, drug_statistic.count from drugs inner join (select drug_id, count(*) as count from sessions where drug_id is not null group by drug_id) as drug_statistic on drugs.id = drug_statistic.drug_id", []);
    }
