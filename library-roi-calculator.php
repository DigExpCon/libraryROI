<?php
/*
Plugin Name: Library ROI Calculator
Description: This plugin displays a Return on Investment (ROI) calculator for public libraries on your WordPress site. This plugin no longer works. 
Version: 1.0
Author: Steve Gregory, Colorado State Library
Author URI: https://www.colibraries.org
License: Please contact the Colorado State Library for licensing information.
*/


register_activation_hook(__FILE__, 'library_roi_calculator_install');
add_action('admin_menu', 'library_roi_calculator_create_menu');
//add_action('admin_menu', 'library_roi_calculator_create_settings_submenu');
add_filter('the_content', 'library_roi_calculator');
//add_action('admin_init', 'library_roi_calculator_settings_api_init');






function library_roi_calculator_install() {
  global $wp_version;
  if (version_compare($wp_version, '3.0', '<')) { wp_die('This plugin requires WordPress version 3.0 or later.'); }
  
  $library_roi_calculator_options = array(
    'roi_incomepercap'    =>  '27.16',
    'roi_libraryname'  =>  'Ruby M. Sisson Memorial Library, Pagosa Springs, CO',
    'roi_libraryid'    =>  'pagosa',
    'roi_pagetitle'    =>  'Library ROI Calculator for Pagosa Springs',
    'roi_lrsreportyear'    =>  '2012',
  );
  update_option('library_roi_calculator_options', $library_roi_calculator_options);
}



function library_roi_calculator_settings_api_init() {

}




function library_roi_calculator_create_settings_submenu() {
  add_options_page( 'Library ROI Calculator Settings Page', 'Library ROI Calculator Settings', 'manage_options', 'library_roi_calculator_settings_menu', 'library_roi_calculator_settings_page');
  add_action('admin_init', 'library_roi_calculator_register_settings');
}



function library_roi_calculator_create_menu() {
  add_menu_page( 'Library ROI Calculator Settings Page', 'Library ROI Calculator Settings', 'manage_options', 'library_roi_calculator_settings_menu', 'library_roi_calculator_settings_page', plugins_url('/images/wordpress.png', __FILE__));
  add_action('admin_init', 'library_roi_calculator_register_settings');
}



function library_roi_calculator_register_settings() {
//delete_option('library_roi_calculator_options');
  register_setting('library_roi_calculator_settings_group', 'library_roi_calculator_options', 'library_roi_calculator_sanitize_options');
}

function library_roi_calculator_sanitize_options($opt) {
  $opt['roi_incomepercap'] = sanitize_text_field($opt['roi_incomepercap']);
  $opt['roi_libraryname'] = sanitize_text_field($opt['roi_libraryname']);
  $opt['roi_libraryid'] = sanitize_text_field($opt['roi_libraryid']);
  $opt['roi_pagetitle'] = sanitize_text_field($opt['roi_pagetitle']);
  $opt['roi_lrsreportyear'] = sanitize_text_field($opt['roi_lrsreportyear']);
  return $opt;
}



function library_roi_calculator_settings_page() {
  $library_roi_calculator_options = (array)get_option('library_roi_calculator_options');
  $roi_incomepercap =  esc_attr($library_roi_calculator_options['roi_incomepercap']);
  $roi_libraryname =  esc_attr($library_roi_calculator_options['roi_libraryname']);
  $roi_libraryid =  esc_attr($library_roi_calculator_options['roi_libraryid']);
  $roi_pagetitle = esc_attr($library_roi_calculator_options['roi_pagetitle']);
  $roi_lrsreportyear = esc_attr($library_roi_calculator_options['roi_lrsreportyear']);
  
  
?>
  <div class="wrap">
    <h2>Settings for Library ROI Calculator</h2>
    <form method="post" action="options.php">
      <?php settings_fields('library_roi_calculator_settings_group'); ?>
      
      <dl><dt>Instructions:</dt><dd>Fill in all fields below for your Colorado library.  Be sure to look up
      the Library Income per Capita in the LRS annual survey of public libraries at 
      <a href="http://www.lrs.org/data-tools/public-libraries/annual-statistics/">
      www.lrs.org/data-tools/public-libraries/annual-statistics/</a>.  When ready, make a page
      on your WordPress site including the shortcode &quot;&#91;library-roi-calculator&#93;&quot;</dd></dl>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Page Subitle</th>
          <td><input type="text" size="80" name="library_roi_calculator_options[roi_pagetitle]" value="<?php echo ${roi_pagetitle}; ?>" /></td>
          <td><p>This text will be displayed at the top of the Library ROI Calculator page.</p></td>
        </tr>
        <tr valign="top">
          <th scope="row">Library Name</th>
          <td><input type="text" size="80" name="library_roi_calculator_options[roi_libraryname]" value="<?php echo ${roi_libraryname}; ?>" /></td>
          <td><p>This is the library name for display, e.g.: &quot;Berthoud Community Library District&quot;</p></td>
        </tr>
        <tr valign="top">
          <th scope="row">Library ID</th>
          <td><input type="text" size="80" name="library_roi_calculator_options[roi_libraryid]" value="<?php echo ${roi_libraryid}; ?>" /></td>
          <td><p>This is the short code for use in LRS tracking, e.g. &quot;berthoud&quot;.  Usually these are all lowercase with no spaces.</p></td>
        </tr>
        <tr valign="top">
          <th scope="row">Library Income per Capita</th>
          <td>$<input type="text" size="8" name="library_roi_calculator_options[roi_incomepercap]" value="<?php echo ${roi_incomepercap}; ?>" /></td>
          <td><p>Look this up in the LRS annual survey of public libraries at <a href="http://www.lrs.org/data-tools/public-libraries/annual-statistics/">www.lrs.org/data-tools/public-libraries/annual-statistics/</a>.  A dollar value such as &quot;27.16&quot;.</p></td>
        </tr>
        <tr valign="top">
          <th scope="row">LRS Report Year</th>
          <td><input type="text" size="6" name="library_roi_calculator_options[roi_lrsreportyear]" value="<?php echo ${roi_lrsreportyear}; ?>" /></td>
          <td><p>Be sure to note the year of the LRS survey cited above, e.g. &quot;2012&quot;</p></td>
        </tr>
      </table>
      <p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p>
    </form>
  </div>
<?php

  echo $options_form;
}






function library_roi_calculator($content) 
{
	if(!strstr($content,"[library-roi-calculator]")) return $content;
	
  $library_roi_calculator_options = (array)get_option('library_roi_calculator_options');
  $roi_incomepercap =  esc_attr($library_roi_calculator_options['roi_incomepercap']);
  $roi_libraryname =  esc_attr($library_roi_calculator_options['roi_libraryname']);
  $roi_libraryid =  esc_attr($library_roi_calculator_options['roi_libraryid']);
  $roi_pagetitle = esc_attr($library_roi_calculator_options['roi_pagetitle']);
  $roi_lrsreportyear = esc_attr($library_roi_calculator_options['roi_lrsreportyear']);



  $libcalc = <<<EOT



	<div id="library_roi_calculator">
	<h2>${roi_pagetitle}</h2>	 
	</div>
	



        <form action="" method="post" name="calculator">



<script type="text/javascript">
  /*          Fill in these constants for each new site         */
	var incpercap =  ${roi_incomepercap};        /* Fill in Per Capita Income Figure from LRS Annual Report Here  */
  var lrsreportyear = "${roi_lrsreportyear}";    /* Year of LRS Annual Report */
  var libraryname = "${roi_libraryname}";       /* Name of library as it appears on web site */
	var library = "${roi_libraryid}";	/* Fill in machine-readable Library Name Here  */
</script>
        



        <input type="hidden" name="roi" id="roi" value=0 />
        <input type="hidden" name="totalvalue" id="totalvalue" value=0 />
        <input type="hidden" name="key" id="key" value="none" />
	


        <div id="rightside"></div>					
	<div style="clear: both;"> </div>
	<h4 style="margin-top: 35px;">Please enter the number of times you use the following library services each <u>month</u></h4>





	<table width="100%">
	<tr style="text-align: left;">
          <th>Your Use</th>
          <th>Library Services</th>
          <th nowrap style="width: 11em;">Value of Services</th>
        </tr>
	<tr>

	<td><input type="text" name="books" id="books" size="5" onchange="calculate()" tabindex="1" /></td>
	<td><label for="books">Books Borrowed per Month</label></td>
	<td><label for="booksResult">$
	<input type="text" id="booksResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>					
	<tr>
	<td><input type="text" name="magazine" id="magazine" size="5" onchange="calculate()" tabindex="2" /></td>
	<td><label for="magazine">Magazines Borrowed per Month</label></td>
	<td><label for="magazineResult">$
	<input type="text" id="magazineResult" size="6" value="0.00" style="text-align: right;" /></label></td>

	</tr>			
	<tr>
	<td><input type="text" name="video" id="video" size="5" onchange="calculate()" tabindex="3" /></td>
	<td><label for="video">Videos Borrowed per Month</label></td>
	<td><label for="videoResult">$
	<input type="text" id="videoResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>
	<td><input type="text" name="audio" id="audio" size="5" onchange="calculate()" tabindex="4" /></td>
	<td><label for="audio">Audio Books Borrowed per Month</label></td>

	<td><label for="audioResult">$
	<input type="text" id="audioResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>
	<td><input type="text" name="libmag" id="libmag" size="5" onchange="calculate()" tabindex="5" /></td>
	<td><label for="libmag">In-Library Magazine Use per Month</label></td>
	<td><label for="libmagResult">$
	<input type="text" id="libmagResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>

	<td><input type="text" name="ill" id="ill" size="5" onchange="calculate()" tabindex="6" /></td>
	<td><label for="ill">Interlibrary Loans per Month</label></td>
	<td><label for="illResult">$
	<input type="text" id="illResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>
	<td><input type="text" name="meeting" id="meeting" size="5" onchange="calculate()" tabindex="7" /></td>
	<td><label for="meeting">Meeting Rooms Use (Hours per Month)</label></td>
	<td><label for="meetingResult">$
	<input type="text" id="meetingResult" size="6" value="0.00" style="text-align: right;" /></label></td>

	</tr>			
	<tr>
	<td><input type="text" name="program" id="program" size="5" onchange="calculate()" tabindex="8" /></td>
	<td><label for="program">Program/Class Attended per Month - Adult</label></td>
	<td><label for="programResult">$
	<input type="text" id="programResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>
	<td><input type="text" name="program2" id="program2" size="5" onchange="calculate()" tabindex="9" /></td>
	<td><label for="program2">Program/Class Attended per Month - Child</label></td>

	<td><label for="program2Result">$
	<input type="text" id="program2Result" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>
	<td><input type="text" name="computer" id="computer" size="5" onchange="calculate()" tabindex="10" /></td>
	<td><label for="computer">Computer Use (Hours per Month)</label></td>
	<td><label for="computerResult">$
	<input type="text" id="computerResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>

	<td><input type="text" name="database" id="database" size="5" onchange="calculate()" tabindex="11" /></td>
	<td><label for="database">Databases Used per Month (Number of Separate Databases)</label></td>
	<td><label for="databaseResult">$
	<input type="text" id="databaseResult" size="6" value="0.00" style="text-align: right;" /></label></td>
	</tr>			
	<tr>
	<td><input type="text" name="reference" id="reference" size="5" onchange="calculate()" tabindex="12" /></td>
	<td><label for="reference">Reference Questions Asked per Month</label></td>
	<td><label for="referenceResult">$
	<input type="text" id="referenceResult" size="6" value="0.00" style="text-align: right;" /></label></td>

	</tr>			
	<tr class="boldthis" style="font-size: 120%;">
	<td align="left">&nbsp;</td>
	<td align="right"><div style="font-weight: bold; color: #000030;">
	Value you receive monthly from <span id="librarychoice">your library</span>:
	</div></td>
	<td style="font-weight: bold; color: #000030; font-size: larger; text-align: left;">$<span id="totalResult">____</span></td>
	</tr>			
	</table>		
	
<br />

        <div style="font-weight: bold; font-size: 120%;">
          For every <span class="red" style="color: #600020;">$1.00</span> in taxes you invest in your library, you receive 
		<span class="red" style="color: #600020;">$</span><span id="personalvalue" style="color: #600020;">_____</span> of value in return*
        </div>

<br />

        <table width="100%">
	<tr>
	<td align="left"><input type="button" value="Click for Total" onclick="calculate()" /></td>
	<td  nowrap style="width: 11em;" align="left"><input type="reset" value="Reset Form" /></td>
	</tr>
	</table>		




	<p class="small" style="margin-top: 40px;">
		<strong>Where did these numbers come from?</strong><br />
		Typical taxpayer contributions are determined from the library's <script type="text/javascript">document.write(lrsreportyear);</script> local income per capita. For libraries in Colorado, this can be found at 
			<a href="http://www.lrs.org/pub_stats.php">http://www.lrs.org/pub_stats.php</a>.  
		We borrowed value of library service figures from Maine State Library's <a href="http://www.maine.gov/msl/services/calculator.htm">
			Library Use Value Calculator</a>. 
		For more information, see their <a href="http://www.maine.gov/msl/services/calexplantion.htm">explanation</a>.
	</p>							
	<div class="small" id="yourresult">
		<p>*Your personal return on investment is based on your responses and the typical annual tax contribution for <span class="boldthis">
		<script type="text/javascript">document.write(libraryname);</script></span>. You see a returned value of $<span id="yourvalue">____</span> for every one dollar invested.
		 Visit <a href="http://www.lrs.org/pub_stats.php">http://www.lrs.org/pub_stats.php</a> to find Local Income per Capita for Colorado's public 
		 libraries.</p>
	</div>

        <div class="small" id="credits">
                <p>This calculator is a modification of the Maine State Library calculator, developed by the
                   <a href="http://www.lrs.org/">Library Research Service</a>.
                </p>
        </div>
	</form>


<iframe src="http://www.lrs.org/public/roi/usercalculator.php" height=0 width=0 id="communicate" name="communicate" frameborder=0 >
In an effort to better understand how people use this site, and the
value they are deriving from our public libraries, this iframe is being
used to help us compile 
data about the answers given here. No personal information is being
collected. Please direct questions or concerns to LRS -
lrs.lrs.org.
</iframe>




<script type="text/javascript">
function calculate() {	  


	
	var monthinc = incpercap / 12;
	
	var booksValue = document.calculator.books.value * 15;
	document.getElementById("booksResult").value = booksValue.toFixed(2);
	
	var magazineValue = document.calculator.magazine.value * 2;
	document.getElementById("magazineResult").value = magazineValue.toFixed(2);
	
	var videoValue = document.calculator.video.value * 4;
	document.getElementById("videoResult").value = videoValue.toFixed(2);
	
	var audioValue = document.calculator.audio.value * 10;
	document.getElementById("audioResult").value = audioValue.toFixed(2);
	
	var libmagValue = document.calculator.libmag.value * 2;
	document.getElementById("libmagResult").value = libmagValue.toFixed(2);
	
	var illValue = document.calculator.ill.value * 25;
	document.getElementById("illResult").value = illValue.toFixed(2);
	
	var meetingValue = document.calculator.meeting.value * 50;
	document.getElementById("meetingResult").value = meetingValue.toFixed(2);
	
	var computerValue = document.calculator.computer.value * 12;
	document.getElementById("computerResult").value = computerValue.toFixed(2);
	
	var databaseValue = document.calculator.database.value * 20;
	document.getElementById("databaseResult").value = databaseValue.toFixed(2);
	
	var referenceValue = document.calculator.reference.value * 7;
	document.getElementById("referenceResult").value = referenceValue.toFixed(2);
	
	var programValue = document.calculator.program.value * 10;
	document.getElementById("programResult").value = programValue.toFixed(2);
	
	var program2Value = document.calculator.program2.value * 6;
	document.getElementById("program2Result").value = program2Value.toFixed(2);
	
						 
	var totalresult = booksValue+magazineValue+videoValue+audioValue+libmagValue+illValue+meetingValue+computerValue+databaseValue+referenceValue+programValue+program2Value;
	document.getElementById("totalResult").innerHTML = totalresult.toFixed(2);
	
	var personalvalue = totalresult / monthinc;
	document.getElementById("yourvalue").innerHTML = personalvalue.toFixed(2);	
	document.getElementById("personalvalue").innerHTML = personalvalue.toFixed(2);	
	
	/*
	document.getElementById("rightside").innerHTML = "<h3>Your Personal ROI<\/h3><h2>$"+personalvalue.toFixed(2)+"<\/h2><p>For every <span class='red'>$1.00<\/span> in taxes you invest in your library, you receive <span class='red'>$"+personalvalue.toFixed(2)+"<\/span> of value in return*<\/p>";
	document.getElementById("rightside").style.border = "2px dashed #003366";
	document.getElementById("rightside").style.padding = "1em";
	*/
	
	if(isNaN(booksValue)) {alert("Your response for number of books borrowed contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(magazineValue)) {alert("Your response for number of magazines borrowed contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(videoValue)) {alert("Your response for number of videos borrowed contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(audioValue)) {alert("Your response for number of audio materials borrowed contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(libmagValue)) {alert("Your response for number of magazines read in the library contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(illValue)) {alert("Your response for number of interlibrary loans contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(meetingValue)) {alert("Your response for number of hours of meeting room use contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(computerValue)) {alert("Your response for number of hours using the computer contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(databaseValue)) {alert("Your response for number of databases used contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(referenceValue)) {alert("Your response for number of reference questions asked contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(programValue)) {alert("Your response for number of adult programs attended contains a non-numeric character. Please re-enter your response.");}
	if(isNaN(program2Value)) {alert("Your response for number of children's programs attended contains a non-numeric character. Please re-enter your response.");}






	var totalvalue=totalresult.toFixed(2);
	var roi=personalvalue.toFixed(2);
	var uniqueid = document.getElementById("key").value;
	if (uniqueid == "none") {
		var currentTime = new Date();
		var month = currentTime.getMonth() + 1;
		var day = currentTime.getDate();
		var year = currentTime.getFullYear();
		var hr = currentTime.getHours();
		var min = currentTime.getMinutes();
		var sec = currentTime.getSeconds();
		var randomnumber=Math.floor(Math.random()*101);
		uniqueid = year+"-"+month+"-"+day+"_"+hr+"_"+min+"_"+sec+"--"+library+"--"+randomnumber;
		document.getElementById("key").value = uniqueid;
	}
	
	var url = "http://www.lrs.org/public/roi/process/communicate.php?key="+uniqueid+"& from="+library+"&library="+library+"&total_value="+totalvalue+"&roi="+roi+"&books="+booksValue+"&magazine="+magazineValue+"&video="+videoValue+"&audio="+audioValue+"&libmag="+libmagValue+"&ill="+illValue+"&meeting="+meetingValue+"&program="+programValue+"&program2="+program2Value+"&computer="+computerValue+"&database="+databaseValue+"&reference="+referenceValue+"";
	document.getElementById("communicate").src = url;
	
}
</script>




EOT;

	$content=str_replace("[library-roi-calculator]",$libcalc,$content);
	return $content;
}



?>