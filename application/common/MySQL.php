<?php
class MySQL
{
    
    var $host;
    var $database;
    var $user;
    var $pass;
    var $site_name = "Pickr";
    var $site_root = "Pickr";
    
    public $mysqli;
    var $result;

    public function __construct()
    {
        switch( $_SERVER['SERVER_NAME'] )
        {
            default:
            case "localhost":
                $this->host 	= "localhost";
                $this->database = "pickr";
                $this->user 	= "root";
                $this->pass 	= "";
                $this->site_root = "http://www.pickr.com";
            break;
            
            case "pickr.ht2.co.uk":
                $this->host 	= "localhost";
                $this->database = "pickr";
                $this->user 	= "root";
                $this->pass 	= "";
                $this->site_root = "http://pickr.ht2.co.uk";
                break;
        }

        $this->mysqli = new mysqli( $this->host, $this->user, $this->pass, $this->database );
        $this->checkConnectError();
        
    }
    
    private function checkConnectError(){
        if ($this->mysqli->connect_errno) {
            $this->error("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error );
        }
    }

    public function query($query)
    {
        $this->result = $this->mysqli->query($query);
        if( !$this->result )
            $this->error( $this->mysqli->error);
        return $this->last_id();
    }	

    public function result()
    {
        return $this->result;
    }

    public function results()
    {
        $results = array();
        
        if( $this->result === false ) return array();
        
        while ($obj = $this->result->fetch_object())
            array_push( $results, $obj );
        return $results;
    }	

    public function singleResult(){
        $results = $this->results();
        if( sizeof($results)>0 )
            return $results[0];		
        else
            return false;
    }

    public function num_rows()
    {
        return mysqli_num_rows( $this->result ); 
    }

    public function last_id()
    {
            return $this->mysqli->insert_id;
    }	

    public function close()
    {
        $this->mysqli->close();
    }

    public function error( $error )
    {
        var_dump($error);
        exit();
    }

    // HELPERS
    public function select( $table, $where = NULL )
    {
        $query = ( is_null( $where ) ) ? "SELECT * FROM $table WHERE active='1'" : "SELECT * FROM $table WHERE active='1' and $where";
        $this->query( $query );
    }


    public function select_all( $table, $where = NULL )
    {
            $query = ( is_null( $where ) ) ? "SELECT * FROM $table WHERE 1" : "SELECT * FROM $table WHERE $where";
            $this->query( $query );
    }

    public function insert( $table, $data ) 
    {
        foreach( $data as $field => $value ) 
        {
            $fields[] = '`' . $field . '`';
            $values[] = "'" . $this->mysqli->real_escape_string($value) . "'";
        }
        $field_list = join( ',', $fields );
        $value_list = join( ', ', $values );
        $query = "INSERT INTO `" . $table . "` (" . $field_list . ") VALUES (" . $value_list . ")";
        
        return $this->query( $query );
    }


    public function update($table, $data, $id_field, $id_value) 
    {
        foreach ($data as $field => $value) $fields[] = sprintf("`%s` = '%s'", $field, $this->mysqli->real_escape_string($value));
        $field_list = join(',', $fields);
        $query = sprintf("UPDATE `%s` SET %s WHERE `%s` = %s", $table, $field_list, $id_field, intval($id_value));
        $this->query( $query );
    }

    public function destroy( $table, $id_field, $id_value)
    {
        if( $where != NULL && $where!="1" )
        {
            $query = "DELETE FROM  $table WHERE $id_field='$id_value'";
            $this->query( $query );
        }
    }

    public function delete($table, $id_field, $id_value) 
    {
        $this->update($table, array('active'=>0), $id_field, $id_value);
    }

    public function self_query( $query )
    {
        return $this->mysqli->query( $query );
    }

    public function safe($value)
    {
        return $this->mysqli->real_escape_string($value);
    }
	
}
?>