<?php

class Database {

    public function Insert($adress, $url, $description, $date_of_change) {
        $sql = 'INSERT INTO bitAccounts(Adress, Url, Description, Change) VALUES(:adress,:url,:description,:date_of_change)';
        $dbconn2 = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=2050Love*");

        // prepare statement for insert
        $stmt = $this->pdo->prepare($sql);

        // pass values to the statement
        $stmt->bindValue(':adress', $symbol);
        $stmt->bindValue(':url', $company);
        $stmt->bindValue(':description', $symbol);
        $stmt->bindValue(':date_of_change', $company);

        // execute the insert statement
        $stmt->execute();
    }

}