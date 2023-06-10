<?php
require 'config.php';
authorize("prebook");
$data = getData("POST", ["user_id", "amount", "begin", "end"]);

prebook($data["user_id"], $data["amount"], $data["begin"], $data["end"]);
function prebook($user_id, $amount, $prebook_begin, $prebook_end)
{
    $date = date("H:i:s", strtotime($prebook_begin));
    global $pdo;
    
    $sql = "SELECT * FROM devices";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $highest_amount = count($sth->fetchAll());
    
    $date = date("Y-m-d", strtotime($prebook_begin));
    
    // is empty: WHYYYY
    $sql = "SELECT * FROM prebook WHERE DATE(prebook_begin) = :date";
    $sth = $pdo->prepare($sql);
    $sth->execute(["date" => $date]);
    $result = $sth->fetchAll();
    
    foreach ($result as $key => $value) {
        // if there are overlapping prebook
        if (date("H:i:s", strtotime($prebook_begin)) < date("H:i:s", strtotime($value["prebook_end"])) && date("H:i:s", strtotime($value["prebook_begin"])) < date("H:i:s", strtotime($prebook_end)))
        {
            $highest_amount -= $value["amount"];
        }
    }

    if ($highest_amount >= $amount)
    {

        // check for user
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["user_id" => $user_id]);
        $user_exists = $sth->fetch();

        if ($user_exists)
        {
            // prebook now
            echo "prebook now! ($highest_amount avaiable)";
            $sql = "INSERT INTO `prebook` (`prebook_id`, `prebook_user_id`, `prebook_amount`, `prebook_begin`, `prebook_end`) VALUES (NULL, :user_id, :amount, :begin, :end)";
            $sth = $pdo->prepare($sql);
            $sth->execute(["user_id" => $user_id, "amount" => $amount, "begin" => $prebook_begin, "end" => $prebook_end]);

        }
        else
        {
            $response["message"] = "user doesn't exist";
            $response["response"] = "1";
        }
        
    }
    else
    {
        // not enough devices avaiable
    }
}

/**
 * - berechne wie viele Geräte in dem Zeitraum frei sind
 * - sind genug Geräte frei?
 * - setze Zeitraum
 * - 
 * 
 * Bei der normalen Ausleihe:
 * - Schaue ob man noch Geräte ausleihen kann, wenn man nicht vorgebucht hat. Wenn für den Tag welche zu einer Späteren Zeit vorgebucht sind, muss man diese spätestens 2 Stunden vorher ausleihen.
 * - Wenn man vorgebucht hat, muss bei Ausleihen erst diese Zahl heruntergehen. Wenn sie bei 0 ist, kann man weitere Ausleihen, diese aber nur mit dem nichtvorgebuchten Verfahren.
 */