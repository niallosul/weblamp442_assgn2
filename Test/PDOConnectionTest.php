<?php
include '../Src/PDOConnection.php';
include '../../vendor/autoload.php';

class PDOConnectionTest extends PHPUnit_Framework_TestCase
{	
    public function setUp ( ) {
        $this->db = new db\PDOConnection( array(
  	    'dsn'     => 'mysql:dbname=integration_mgr;host=127.0.0.1',
        'username' => 'guest',
        'password' => 'guest',
        ) );
		$this->db->connect();
     }
     
    public function tearDown ( ) {
        $this->db->disconnect();
     }


    /**
	 * verify that the Insert function returns
	 * a integer of zero or greater matching the new row's id
	 * Had to force the id into the insert to allow for multiple tests
     */
    public function testInsert( ) {
		$insertSql = "INSERT INTO member (id, first_name, last_name, role) VALUES ('1', 'Frank', 'Newguy', 'admin')";
		$this->assertEquals(1,$this->db->insert($insertSql));

		$insertSql = "INSERT INTO member (id, first_name, last_name, role) VALUES ('2', 'Bill', 'Twoguy', 'sysadmin')";
		$this->assertEquals(2,$this->db->insert($insertSql));

		$insertSql = "INSERT INTO member (id, first_name, last_name, role) VALUES ('3', 'Ed', 'Threeguy', 'engineer')";
		$this->assertEquals(3,$this->db->insert($insertSql));
		
		try {
		   $insertSql = "INSERT INTO members (id, first_name, last_name, role) VALUES ('4', 'Bad', 'Testguy', 'engineer')";//bad table name
		   $this->assertEquals(4,$this->db->insert($insertSql));		
		} 
		catch(\Exception $e) {
		   return;
		}
        $this->fail('An expected exception has not been raised on a bad select.');
	}
	
	
	/**
	 * verify that the Select function returns a specific row
	 * for a specific key, or multiple rows for a range of values
     */
    public function testSelect( ) {
    
    	$querysql = "SELECT * FROM `member` WHERE id =1";
		$result = $this->db->select($querysql);
		$this->assertEquals(1, count($result));//Assert that 1 row is returned 
		$this->assertObjectHasAttribute("id",$result[0]);//Assert that the id column is returned as an attribute 
		$this->assertObjectHasAttribute("first_name",$result[0]);//Assert that the first_name column is returned as an attribute
		$this->assertObjectHasAttribute("last_name",$result[0]);//Assert that the last_name column is returned as an attribute
		$this->assertSame("1", $result[0]->id);
		
		$querysql = "SELECT * FROM `member` WHERE id >0";
		$result = $this->db->select($querysql);
		$this->assertEquals(3, count($result));//Assert that 3 rows are returned

		$querysql = "SELECT * FROM `members` WHERE id >0";//bad table name
		try {
		   $result = $this->db->select($querysql);
		} 
		catch(\Exception $e) {
		  return;
		}
        $this->fail('An expected exception has not been raised on a bad select.');
	}
		


	/**
	 * verify that the Update function returns 1 for a specific update
	 * zero when there's nothing to update and > 1 for multiples
     */
    public function testUpdate( ) {
  
		$updatesql = "UPDATE `member` SET first_name ='BILLY', last_name='UPDATED' WHERE id=1";
		$this->assertSame(1,$this->db->update($updatesql));//There should be one id = 7
		
		$updatesql = "UPDATE `member` SET first_name ='Bigger', last_name='BigUpdate' WHERE id>1";
		$this->assertGreaterThan(1,$this->db->update($updatesql));

		$updatesql = "UPDATE `member` SET first_name ='New', last_name='Guy2' WHERE id=4000";
		$this->assertSame(0,$this->db->update($updatesql));//There should be zero
		
		try {
            $updatesql = "UPDATE `member` SET non_name ='New', last_name='Guy2' WHERE id=4000";
			$this->db->update($updatesql);//This should fail
        }
 
        catch (Exception $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
	}
		
	/**
	 * verify that the Delete function returns 1 for a specific delete
	 * after seeding the database with 1 row earlier
     */
    public function testDelete( ) {
		$deletesql = "DELETE FROM `member` WHERE id =1";
		$this->assertSame(1,$this->db->delete($deletesql));
		
		$deletesql = "DELETE FROM `member` WHERE id >0";
		$this->assertGreaterThan(1,$this->db->delete($deletesql));
		
		try {
            $deletesql = "DELETE FROM `member` WHERE notacolumn =notanumber";
			$this->db->update($deletesql);//This should fail
        }
 
        catch (Exception $expected) {
            return;
        }
 
        $this->fail('An expected exception has not been raised by the delete.');
	}
	

}	

?>