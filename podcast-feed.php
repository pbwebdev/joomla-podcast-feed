<?php
date_default_timezone_set('Australia/Sydney');
mysql_connect("localhost", "user", "password") or die(mysql_error());
mysql_select_db("database_name") or die(mysql_error());

header("Content-type: text/xml");

//This is the ID of the K2 category where the podcast items reside for the feed
// can replace this code with Joomla category id and replace db selection
$podcastID = 9;//$_GET['id'];

//Outputting standard variable information on the podcasts.
$output = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<rss xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" version=\"2.0\">
    <channel>
    <title>Joomla Beat Podcast - Design, Development, Management, Marketing</title>
    <link>
    	http://joomlabe.at
    </link>
    <language>en-au</language>
    <itunes:subtitle>Joomla podcast about web design, web development, managing and online marketing for your Joomla website!</itunes:subtitle>
    <itunes:summary>
    Weekly Joomla podcast helping web site owners design, develop, manage and market their Joomla website. Interviews, reviews and news from the Joomla industry. Get the most out of the open source content management system Joomla!
    </itunes:summary>
    <description>
	<![CDATA[
<p>
A weekly Joomla podcast helping website owners design, develop, manage and market their Joomla website and take it to a whole new level. Aimed at Joomla beginners, experts and addicts!
</p>
<p>
Get the most out of your Joomla website to make it work for you. Filled with great interviews from Joomla experts, Joomla entrepreneurs from around the world and in the Joomla community, hints and tips on how to better manage and build Joomla websites, news and reviews of what's hot to do with the ever so popular content management system, Joomla!</p>

<p>Make sure you check out the show notes at http://JoomlaBe.at and follow us on http://fb.com/joomlabeat and twitter.com/joomlabeat.</p> 

<p>Produced by the expert team of Joomla service providers from PB Web Development. Bringing years of Joomla experience from design, development, training and marketing to podcast listeners around the world. </p> 

<p>Listen to hints and tips, expert interviews and reviews every week!</p>
]]>
    </description>
	<itunes:keywords>Joomla,web,design,web,development,social,media,marketing,Joomla,news,Joomla,development,Joomla,design,Joomla,help,Joomla,tutorials,online marketing, entrepreneur</itunes:keywords>
	<itunes:category text=\"Technology\">
	<itunes:category text=\"Software How-To\" /></itunes:category>
	<itunes:category text=\"Technology\">
	<itunes:category text=\"Podcasting\" /></itunes:category>
	<itunes:category text=\"Technology\">
	<itunes:category text=\"Tech News\" /></itunes:category>
	<itunes:category text=\"Technology\" />
    <itunes:explicit>no</itunes:explicit>
    <itunes:image href=\"http://joomlabe.at/images/joomla-beat-podcast-logo.png\"/>
	<atom10:link xmlns:atom10=\"http://www.w3.org/2005/Atom\" rel=\"self\" type=\"application/rss+xml\" href=\"http://joomlabe.at/podcast-feed.php\" />
	<itunes:owner>
		<itunes:name>Peter Bui | Joomla consultant and addict!</itunes:name>
		<itunes:email>mail@joomlabe.at</itunes:email>
	</itunes:owner>";
echo $output;
?>    

<?php
//Query to pull in the podcast data from entries in a particular K2 category
//$podcastID is the ID of the category in which the K2 items for the podcasts reside

$date = date('Y-m-d H:i:s');

$query = "SELECT `title`, `introtext`, `created`, `state`, `images`, `urls`, `catid`, `publish_up` FROM  `__content` WHERE `catid` = ".$podcastID." AND `state` = 1 AND date(publish_up) < '".$date."' ORDER BY `created` DESC";
$results = mysql_query($query) or die(mysql_error());

while($row = mysql_fetch_array($results)){
	if($row['urls']){
	
	$mp3URL = $row['urls']; //gets media URL from K2
	
	$urls = preg_split('/[,]/', $mp3URL);
	$mp3URL = str_replace('{"urla":"', '', $urls[0]); //Remotes K2 auto mp3 code
	$mp3URL = str_replace('"', '', $mp3URL); //Remotes K2 auto mp3 code
	$mp3URL = str_replace('\\', '', $mp3URL); //Remotes K2 auto mp3 code
	$mp3URL = str_replace('http://joomlabe.at/', '', $mp3URL); //Remotes K2 auto mp3 code
	$filesize = '';
	
	if(strstr($mp3URL, "http://")){
		$mp3URL = $mp3URL; //Inserts the domain URL to the mp3 URL with trailing /
	} else {
		$filesize = filesize($mp3URL); //Get file size of mp3 file. Must be done without %20 replacment chars
		$mp3URL = "http://joomlabe.at/".$mp3URL; //Inserts the domain URL to the mp3 URL with trailing /
	}
	
	$imageURL = $row['images']; //gets media URL from K2
	
	$imageURL = preg_split('/[,]/', $imageURL);
	$imageURL = str_replace('{"image_intro":"', '', $imageURL[0]); //Remotes K2 auto mp3 code
	$imageURL = str_replace('"', '', $imageURL); //Remotes K2 auto mp3 code
	$imageURL = str_replace('\\', '', $imageURL); //Remotes K2 auto mp3 code
	if ($imageURL==null) { $imageURL = 'images/joomla-beat-podcast-logo.png'; }
	$imageURL = 'http://joomlabe.at/'.$imageURL;
	$imageURL = 'http://joomlabe.at/images/joomla-beat-podcast-logo.png';
	
	$date = date("D, d M Y G:i:s", strtotime(str_replace('-', '/', $row['created']))); //Insert the date from K2 item create date into iTunes standard time date format
	
	$introtext = $row['introtext'];
	//$introtext = str_replace("<p>", "", $introtext);
	//$introtext = str_replace("</p>", "", $introtext);
	$introtext = str_replace("<h2>", "", $introtext);
	$introtext = str_replace("</h2>", "", $introtext);
	$introtext = str_replace("<strong>", "", $introtext);
	$introtext = str_replace("</strong>", "", $introtext);
	
	$title = str_replace("&", "and", $row['title']);
	
	$chars = preg_split('/[.?!]/', $introtext);
	
	//Output formatting code
	$item = '<item>
	';
	$item .= '<title>'.$title.'</title>
	';
	$item .= '<itunes:author>PB Web Development</itunes:author>
	';
	$item .= '<itunes:subtitle>'.$title.'</itunes:subtitle>
	';
	$item .= '<itunes:summary>       
	<![CDATA[
'.$introtext.'
	   ]]>
</itunes:summary>
	';
	$item .= '<description>       
	<![CDATA[
'.$introtext.'
	   ]]>
</description>
	';
	$item .= '<itunes:image href="'.$imageURL.'" />
	';	
	$item .= '<enclosure url="'.$mp3URL.'" length="'.$filesize.'" type="audio/mpeg"/>
	';
	$item .= '<guid>
	';
	$item .= '	'.$mp3URL.'
	';
	$item .= '</guid>
	';
	$item .= '<itunes:explicit>no</itunes:explicit>
	';
	$item .= '<pubDate>'.$date.' +1000</pubDate>
	';
	$item .= '<itunes:keywords>Joomla, joomla, web design, web development, open source, online marketing, social media</itunes:keywords>
	';
	$item .= '</item>
	';
	
	echo $item;	
}
	}
?>
	</channel>
    
</rss>
