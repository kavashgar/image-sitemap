<?PHP

	
	$config = array(
		'host'		=> '#',
		'username' 	=> '#',
		'password' 	=> '#',
		'dbname' 	=> '#'
	);	
		
	// Create a connection.
  $db = null;

    // Connect from App Engine.
    try{
      		
		$db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, 1);

    }catch(PDOException $ex){
	
        die(json_encode(
            array('outcome' => false, 'message' => 'Unable to connect.')
            )
        );
    }
	
  // delete existing image-stemap first
 
 unlink("/var/www/html/#####.com/image_sitemap.xml");
  
$fp = fopen("/var/www/html/#####.com/image_sitemap.xml","wb"); 


$xml="<?xml version='1.0' encoding='UTF-8'?>\n\t\t";
$xml .="<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'
  xmlns:image='http://www.google.com/schemas/sitemap-image/1.1'>
  \n\t\t";
  

				
				
			
			$stmtLanCloud = $db->prepare("select DISTINCT LanguageId from Sticker Where LanguageId IN (Select LanguageId From Sticker GROUP BY LanguageId HAVING COUNT(LanguageId)>0 )");
			$stmtLanCloud->execute();
			
			
while($data = $stmtLanCloud->fetch(PDO::FETCH_ASSOC)) {

	$languageId=$data['LanguageId'];
	
					
    $pageUrl="http://#####.com/index.php?page=facebook-picture-comments&lang=".$data['LanguageId'];
	

	
	
  	$xml .=" <url>\n\t\t";
    $xml .= "<loc><![CDATA[".$pageUrl."]]></loc>\n\t\t";
	  
	  //nested while loop to contruct images for every page
	  

				$sql= "select * from Sticker Where LanguageId=:languageId";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':languageId', $languageId, PDO::PARAM_INT); 
				$stmt->execute();
				
				

		while($data2=$stmt->fetch(PDO::FETCH_ASSOC))
		{
			//$data2['ImagePath']
			
			$imagePath="http://#####.com/index.php?page=picture-creative-comments-facebook-users&pic=".$data2['StickerId'];
			$xml .= "<image:image>\n\t\t";
			$xml .= "<image:loc><![CDATA[".$imagePath."]]></image:loc>\n\t\t";
			
			if (!empty($data2['Description'])) {
				$xml .= "<image:title>".$data2['Description']."</image:title>\n\t\t";
			}
			
		$xml .= "</image:image>\n\t\t";
		} // end child while loop
		
   $xml.="</url>\n\t";
	

	
		

} // end parent while loop

$xml.="</urlset>\n\r";
	
fwrite($fp,$xml);
fclose($fp);

echo "Success: Image site map has been updated and - submitted to Google";

?>