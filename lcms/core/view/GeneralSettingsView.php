<?php
include("core/lib/table.php");

class GeneralSettingsView extends View{
	
	public function GeneralSettingsView(){
		//Meta is usually not setup yet, so we manually do this before loading css file
		$this->meta = new Meta();
		
		//This class requires an extra css file
		$this->getMeta()->addExtra('<link href="core/fragments/css/admin.css" rel="stylesheet" type="text/css" />');
	}	

	public function createEditor($data){
		$this->setState('LOADING_EDITOR');
		
		//Change the content into a form
		$content = $this->singleForm($data[0], $data[1]);
		
		//Tabs above settings
		$tabs = $this->openFile("core/fragments/settings/SettingsDash.phtml");	
		
		//Activate Tab
		$tabs = $this->setTabActive(1, $tabs);
		
		//Localise
		$tabs = str_replace("%GENERAL_SETTINGS_LOCALE%", $this->localize("General Settings"), $tabs);
		$tabs = str_replace("%SEO_SETTINGS_LOCALE%", $this->localize("SEO Settings"), $tabs);
		$tabs = str_replace("%TEMPLATE_SETTINGS_LOCALE%", $this->localize("Template Settings"), $tabs);
		$tabs = str_replace("%CLEAR_CACHE_LOCALE%", $this->localize("Clear Cache"), $tabs);
		
		//Print this dashboard
		$this->setContent($tabs.$content);	
	}
	
	/**
	 * Redirects after successfully saving the data
	 */
	public function setWebsiteRedirect(){
		$this->setState('REDIRECTING');

		//Show success message on redirected to page
		$_SESSION['ERROR_TYPE'] = "success";
		$_SESSION['ERROR_MESSAGE'] = "Website Data Saved Successfully";
		
		//Go Redirect
		$this->setRedirect("index.php?system=GeneralSettings&page=edit");	
	}
	
	/**
	 * Create a form for the page.
	 */
	protected function singleForm($version, $title){
		$this->setState('CREATING_SINGLE_FORM');

		$listLocale = $this->getController()->getModel()->getListOfLocale();
		$activeLocale = $this->getLocale();
		
		//Get the form
		$out = $this->openFile("core/fragments/settings/GeneralSettingsForm.phtml");
		
		//Replace Title in File
		$out = str_replace("%TITLE%", $title, $out);
		
		//Replace current version in File
		$out = str_replace("%CURRENTVERSION%", $version, $out);
		
		//Put in update notice
		$out = str_replace("%UPDATE_NOTICE%", "<p class='msg error'>".$this->getInputString('versionCheck', "", "S")."</p>", $out);
		
		$localeOptions = "";
		$fID = 0;
		
		//Find the ID of the active locale
		for($i = 0; $i < count($listLocale[0]); $i++){
			if($activeLocale==str_replace(".txt", "", $listLocale[0][$i])){
				$fID = $i;
				break;
			}
		}
		
		$localeOptions .= "<option>";
		$localeOptions .= $listLocale[1][$fID]." [".str_replace(".txt", "", $listLocale[0][$fID])."]";
		$localeOptions .= "</option>";
		
		//Create the list
		for($i = 0; $i < count($listLocale[0]); $i++){
			if($i!=$fID){
				$localeOptions .= "<option>";
				$localeOptions .= $listLocale[1][$i]." [".str_replace(".txt", "", $listLocale[0][$i])."]";
				$localeOptions .= "</option>";
			}
		}
		
		//Enter Locale List
		$out = str_replace("%LOCALE_LIST%", $localeOptions, $out);
		
		//Locale
		$out = str_replace("%WEBSITE_TITLE_LOCALE%", $this->localize("Website Title"), $out);
		$out = str_replace("%WEBSITE_VERSION_LOCALE%", $this->localize("Website Version"), $out);
		$out = str_replace("%CLEAR_CACHE_MESSAGE_LOCALE%", $this->localize("You may wish to clear the CMS cache after this, as cached pages will contain old title."), $out);
		$out = str_replace("%WEBSITE_DISABLE_LOCALE%", $this->localize("Disable Website"), $out);
		$out = str_replace("%UNDER_CONSTRUCTION_LOCALE%", $this->localize("This feature is yet to be implemented"), $out);
		$out = str_replace("%YOUR_VERSION_LOCALE%", $this->localize("Your Version"), $out);
		$out = str_replace("%LATEST_VERSION_LOCALE%", $this->localize("Latest Version"), $out);
		$out = str_replace("%SAVE_LOCALE%", $this->localize("Save"), $out);
		$out = str_replace("%WEBSITE_LOCALE_LOCALE%", $this->localize("Change Website Locale"), $out);
			
		$data = $this->getController()->getModel()->getInputString("vnumber", "", "S");
		//Include and load the latest version
		if(empty($data)){
			include("core/lib/RemoteFiles.php");
			$rf = new RemoteFiles();

			$data = $rf->getURL("http://update.lotuscms.org/lcms-3-series/latestVersion.dat");
		}
		
		//Reduce load by dumping the retrieval system.
		unset($rf);
		
		//Replace Unix in File
		$out = str_replace("%LATESTVERSION%", $data, $out);
		
		
		//Return the out data
		return $out;
	}
}

?>