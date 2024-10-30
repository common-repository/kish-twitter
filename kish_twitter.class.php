<?php
class kish_twitter {  	
   private $id, $url, $uname, $pw, $title, $desc, $xmlrpcurl, $blogurl, $version, $blogtitle, $blogdescription, $errHandler;
	// create a new object $test=new kish_multi_wp('url', 'uname', 'pass', 'title', 'desc' );
   	public function __construct() {   
		//error_reporting(0);		
   		define('KMW_ID', 'kish_multi_wp_id', TRUE);
    	define('KMW_URL', 'kish_multi_wp_blog_url', TRUE);
		define('KMW_UNAME', 'kish_multi_wp_username', TRUE);
		define('KMW_PW', 'kish_multi_wp_password', TRUE);
		define('KMW_TITLE', 'kish_multi_wp_blog_name', TRUE);
		define('KMW_DESC', 'kish_multi_blog_desc', TRUE);
		$args = func_get_args();
		if($args) {
			$this->url=$args[0];
			$this->uname=$args[1];
			$this->pw= $args[2];
			$this->title=$args[3];
			$this->desc=$args[4];
			$this->setXmlRpcUrl($url);		
		}
   	}
	public function getId() {return $this->id;}
	public function Id($id) {
		$this->id=$id;
		$this->getSiteFromDb($id);
	}
	public function setXmlRpcUrl(){
		$xmlrpcurl=$this->getUrl();
		substr($xmlrpcurl, -1)=='/' ? $xmlrpcurl .="xmlrpc.php" : $xmlrpcurl .="/xmlrpc.php";
		$this->xmlrpcurl= $xmlrpcurl;
	}
	public function getXmlRpcUrl(){return $this->xmlrpcurl;}
	public function getUrl() {return $this->url;}
	public function setUrl($url) {$this->url=$url;}
	public function getBlogUrl(){return $this->blogurl;}
	public function setBlogUrl(){$this->blogurl=$blogurl;}
	public function getBlogDescription(){return $this->blogdescriptions;}
	public function setBlogDescription(){$this->blogdescriptions=$blogdescriptions;}
	public function getVersion(){return $this->version;}
	public function setVersion(){$this->version=$version;}
	public function getUname() {return $this->uname;}
	public function setUname($uname) {$this->uname=$uname;}
	public function getPassWord() {return $this->pw;}
	public function setPassWord($pw) {$this->pw=$pw;}
	public function getTitle() {return $this->title;}
	public function setTitle($title) {$this->title=$title;}
	public function getDesc() {	return $this->desc;}
	public function setDesc($desc) {$this->desc=$desc;}
	protected function checkNew($siteurl){
		global $wpdb;
		$sql = "SELECT kish_multi_wp_id FROM ".$wpdb->prefix."kish_multi_wp WHERE kish_multi_wp_blog_url = '".$siteurl."' LIMIT 1";
		$results=$wpdb->get_results($sql, OBJECT);
		if ($results){
			return true;	
		}
		else {return false;}
	}
	public function getIdFromUrl($siteurl){
		global $wpdb;
		$sql = "SELECT kish_multi_wp_id FROM ".$wpdb->prefix."kish_multi_wp WHERE kish_multi_wp_blog_url = '".$siteurl."' LIMIT 1";
		$results=$wpdb->get_results($sql, OBJECT);
		if ($results){
			print_r($results);
			return $results->kish_multi_wp_id;	
		}
		else {return false;}
	}
	public function load($id) {
		$this->id=$id;
		$this->getSiteDb();
		$this->setXmlRpcUrl();
	}
	public function update() {
		global $wpdb;
		if(!$this->id){echo "I am going out"; return;}
		//if(!$this->getSite($this->getId())) {echo "Sorry Wronge Site Id"; return;}
		$sql = "UPDATE ".$wpdb->prefix."kish_multi_wp SET ";
		if(strlen($this->url)) {$sql .= KMW_URL ."='".$this->url."', ";}
		if(strlen($this->uname)) {$sql .= KMW_UNAME ."='".$this->uname."', ";}
		if(strlen($this->pw)) {$sql .= KMW_PW ."='".$this->pw."', ";}
		if(strlen($this->title)) {$sql .= KMW_TITLE ."='".$this->title."', ";}
		if(strlen($this->desc)) {$sql .= KMW_DESC ."='".$this->desc."', ";}
		$sql=substr(trim($sql), 0, -1);
		$sql .=" WHERE kish_multi_wp_id = ".$this->id." LIMIT 1";
		//echo $sql;
		if(mysql_query($sql, $wpdb->dbh)) {
			echo "Site Updated" ;
			return true;
		}
		else {
			echo "Error Updating site - ".$this->getTitle();
			return false;
		}
	}
	public function save() {
		if($this->getBlogInfoNew()) {
			global $wpdb;
			if($this->checkNew($this->url)) {echo "Site Already Entered, You can edit it "; return;}
			$sql = "INSERT INTO ".$wpdb->prefix."kish_multi_wp(".KMW_URL.", ".KMW_UNAME.",".KMW_PW.",".KMW_TITLE.",".KMW_DESC.")";
			$sql .="VALUES('".trim($this->url)."','".trim(addslashes($this->uname))."','".trim(addslashes($this->pw))."', '".addslashes(trim($this->title))."', '".trim(addslashes($this->desc))."')";
			//echo $sql;
			if(mysql_query($sql, $wpdb->dbh)) {
				echo "Site Saved" ;
				return true;
			}
			else {
				echo "Error Saving site - ".$this->getTitle();
				return false;
			}
		}
	}
	public function getBlogInfo() {
		include_once('class-IXR.php');
		if(!$this->getId()){return;}
		if($kish_multi_wp_client = new IXR_Client($this->xmlrpcurl)) {
			if($kish_multi_wp_client->query('wp.getOptions', 0, $this->uname, $this->pw)) {
				$options = $kish_multi_wp_client->getResponse();
				$this->title=$options['blog_title']['value'];
				$this->desc=$options['blog_tagline']['value'];
				return true;
			}
			else{echo "Error Login - Please check the username and password for  - ".$this->getTitle(); return false;}
		}
		else {echo "Error, the xmlrpc url is haveing some problem  - ".$this->getTitle(); return false;}	
	}
	public function getBlogInfoNew() {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->xmlrpcurl)) {
			if($kish_multi_wp_client->query('wp.getOptions', 0, $this->uname, $this->pw)) {
				$options = $kish_multi_wp_client->getResponse();
				$this->title=$options['blog_title']['value'];
				$this->desc=$options['blog_tagline']['value'];
				return true;
			}
			else{echo "Error Login - Please check the username and password for  - ".$this->getTitle(); return false;}
		}
		else {echo "Error, the xmlrpc url is haveing some problem  - ".$this->getTitle(); return false;}	
	}
	public function getLatestPosts($num) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if($kish_multi_wp_client->query('metaWeblog.getRecentPosts', 0, $this->getUname(), $this->getPassword(), $num)) {
				return $kish_multi_wp_client->getResponse();
			}
			else{echo "Error Login - Please check the username and password"; return false;}
			echo "This xml rl".$this->getXmlRpcUrl();
		}
		else {echo "Error, the xmlrpc url is haveing some problem  - ".$this->getTitle(); return false;}	
	}
	public function delPost($postId) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('metaWeblog.deletePost','', $postId, $this->getUname(), $this->getPassword(), false)) {
				die('Something went wrong - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {echo "Post DEleted - ".$this->getTitle();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_get_post($postid){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('metaWeblog.getPost', $postid, $this->getUname(), $this->getPassword())) {
				die('Something went wrong-1 - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_get_recent_posts_titles($num){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('mt.getRecentPostTitles', 0, $this->getUname(), $this->getPassword(), $num)) {
				die('Something went wrong-1 - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_edit_post($postid, $editval){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('metaWeblog.editPost', $postid, $this->getUname(), $this->getPassword(), $editval, false)) {
				 die('Something went wrong-2 - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return true;}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_new_post($postinfo){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('metaWeblog.newPost', 0, $this->getUname(), $this->getPassword(), $postinfo, false)) {
				 die('Something went wrong-2 - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_upload_image($imageinfo){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('metaWeblog.newMediaObject', 0, $this->getUname(), $this->getPassword(), $imageinfo)) {
					 die('Something went wrong-2 - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
				}
				else {return $kish_multi_wp_client->getResponse(); }
			}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_get_categories(){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('mt.getCategoryList', 0, $this->getUname(), $this->getPassword())) {
				 die('Something went wrong-2 - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_get_comments($status="", $num=10) {
		$info=array('status'=>$status,'number'=>$num);
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('wp.getComments', 0, $this->getUname(), $this->getPassword(), $info)) {
				die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_get_comment($comid) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('wp.getComment', 0, $this->getUname(), $this->getPassword(), $comid)) {
				die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_get_comment_for_post($info) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('wp.getComments', 0, $this->getUname(), $this->getPassword(), $info)) {
				die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return $kish_multi_wp_client->getResponse();}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_new_comment($postid, $cominfo) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('wp.newComment', 0, $this->getUname(), $this->getPassword(), $postid, $cominfo)) {
				die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return true;}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_delete_comment($comid) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('wp.deleteComment', 0, $this->getUname(), $this->getPassword(), $comid)) {
				die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return true;}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function wp_edit_comment($comid, $cominfo) {
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if(!$kish_multi_wp_client->query('wp.editComment', 0, $this->getUname(), $this->getPassword(), $comid, $cominfo)) {
				die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
			}
			else {return true;}
		}
		else {echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function get_tags() {
		include_once('class-IXR.php');
			if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
				if(!$kish_multi_wp_client->query('wp.getTags', 0, $this->getUname(), $this->getPassword())) {
					die('Something went wrong-getting the comments - '.$kish_multi_wp_client->getErrorCode().' : '.$kish_multi_wp_client->getErrorMessage());
				}
				else {return $kish_multi_wp_client->getResponse();}
			}
		else { echo "Error, the xmlrpc url is haveing some problem - ".$this->getTitle(); return false;}
	}
	public function getSavedSites() {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."kish_multi_wp";
		$results=$wpdb->get_results($sql, OBJECT);
		if($results) {
			return $results;
		}
		else return false;
	}
	public function getSiteFromDb($id) {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."kish_multi_wp WHERE kish_multi_wp_id = ".$id." LIMIT 1";
		$result=$wpdb->get_results($sql, OBJECT);
		if($result) {
			foreach($result as $results):
				$this->setUrl($results->kish_multi_wp_blog_url);
				$this->setUname($results->kish_multi_wp_username);
				$this->setPassword($results->kish_multi_wp_password);
				$this->setTitle($results->kish_multi_wp_blog_name);
				$this->setDesc($results->kish_multi_blog_desc);
			endforeach;	
			return $result;		
		}
		else return false;
	}
	private function getSiteDb() {
		$id=$this->getId();
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."kish_multi_wp WHERE kish_multi_wp_id = ".$id." LIMIT 1";
		$result=$wpdb->get_results($sql, OBJECT);
		if($result) {
			foreach($result as $results):
				$this->setUrl($results->kish_multi_wp_blog_url);
				$this->setUname($results->kish_multi_wp_username);
				$this->setPassword($results->kish_multi_wp_password);
				$this->setTitle($results->kish_multi_wp_blog_name);
				$this->setDesc($results->kish_multi_blog_desc);
			endforeach;	
			return true;
		}
		else return false;
	}
	public function getSiteInfoFromUrl($url) {
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."kish_multi_wp WHERE kish_multi_wp_blog_url = '".$url."' LIMIT 1";
		$result=$wpdb->get_results($sql, OBJECT);
		if($result) {
			foreach($result as $results):
				$this->id=$results->kish_multi_wp_id;
				$this->uname=$results->kish_multi_wp_username;
				$this->pw=$results->kish_multi_wp_password;
				$this->title=$results->kish_multi_wp_blog_name;
				$this->desc=$results->kish_multi_blog_desc;
				$this->url=$results->kish_multi_wp_blog_url;
			endforeach;	
			return true;		
		}
		else return false;
	}
	public function deleteSite($id) {
		global $wpdb;	
		$sql = "DELETE FROM ".$wpdb->prefix."kish_multi_wp WHERE kish_multi_wp_id = ".$id." LIMIT 1";
		if(mysql_query($sql, $wpdb->dbh)) {
				echo "Site Deleted - ";
		}
		else {
			echo "Error Deleting Site";
		}
	}
	public function __tostring(){
		$siteinfo .="<p><img style=\"float:right\" src=\"http://images.websnapr.com/?size=s&url=".$this->getUrl()."\"><strong>Blog Name :</strong><a href=\"".$this->getUrl()."\">".$this->getTitle()."</a><br>";
		$siteinfo .="<strong>Blog Description :</strong>".$this->getDesc()."</p>";
		return $siteinfo;
	}
	public function setBlogOptions(){
		include_once('class-IXR.php');
		if($kish_multi_wp_client = new IXR_Client($this->getXmlRpcUrl())) {
			if($kish_multi_wp_client->query('wp.getOptions', 0, $this->uname, $this->pw)) {
				$options = $kish_multi_wp_client->getResponse();
				$this->blogurl=$options['blog_url']['value'];
				$this->blogtitle=$options['blog_title']['value'];
				$this->version=$options['software_version']['value'];
				$this->blogdescriptions=$options['blog_tagline']['value'];
			}
			else{echo "Error Login - Please check the username and passwordsss"; return false;}
		}
	}
}
class errorHandler {
	var $debug_level=0;
	function errors($debug_level=0) {
		$this->debug_level=$debug_level;
		set_error_handler(array($this, 'handle_error'));
	}
	function handle_error($type, $string, $file, $line, $vars) {
     // Decide which type of error it is, and handle appropriately
		switch ($type) {
	    	// Error type
	    	case FATAL:
		    // Select debug level
		    switch ($this->debug_level) {
	         	default:
	            case 0:
	            echo 'Error: '.$string.' in '.$file.' on line'. $line.'<br />';
	            print_r($var);
	            // Stop application
	            exit;
	            case 1:
	            echo 'There has been an error. Sorry for the inconvenience.';
	            // Stop application
	            exit;
	      	}
	        case ERROR:
	        echo '<pre><b>ERROR</b> ['.$type.'] '.$string.'<br />'."</pre>n";
	        break;
	        case WARNING:
	       	echo '<pre><b>WARNING</b> ['.$type.'] '.$string.'<br />'."</pre>n";
	        break;
	    }
	}
}
?>