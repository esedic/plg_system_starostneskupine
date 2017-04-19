<?php
/**
 * Joomla! System plugin - Starostne skupine
 *
 * @author    Spletodrom (info@spletodrom.com)
 * @copyright Copyright 2015
 * @license   GNU Public License
 * @link      http://www.spletodrom.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Import the parent class
jimport('joomla.plugin.plugin');

/**
 * Starostne skupine System Plugin
 */
class PlgSystemStarostneSkupine extends JPlugin
{


	/**
	 * Event OnAfterRender
	 *
	 * @param null
	 *
	 * @return bool
	 */
	public function OnAfterRender()
	{
		// Only continue in the frontend
		$application = JFactory::getApplication();

		if (!$application->isSite())
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;
		$option = $jinput->getCmd('option', '');
		$view = $jinput->getCmd('view', '');
		$layout = $jinput->getCmd('layout', '');
		$task = $jinput->getCmd('task', '');
		$itemid = $jinput->getCmd('Itemid', '');
		$k2Id = $jinput->getInt('id', '');
		$content = JFactory::getApplication()->getBody();

		/* OTROCI */
		// itemid-ji za strani, ki niso K2 članek/kategorija
		$otroci_param_val = $this->params->get("otroci_nonk2_item_ids");
		// pripravi array
		$otroci_itemids = array("('".str_replace(",", "','", $otroci_param_val)."')");
		// id k2 kategorije
		$otroci_k2_category = trim($this->params->get('otroci_k2_category', '74'));
		// body class
		$otroci_body_class = trim($this->params->get('otroci_body_class', 'otroci'));

		/* OTROCI */		
		// itemid-ji za strani, ki niso K2 članek/kategorija		
		$mladi_param_val = $this->params->get("mladi_nonk2_item_ids");
		// pripravi array
		$mladi_itemids = array("('".str_replace(",", "','", $mladi_param_val)."')");
		// id k2 kategorije
		$mladi_k2_category = trim($this->params->get('mladi_k2_category', '81'));
		// body class
		$mladi_body_class = trim($this->params->get('mladi_body_class', 'mladi'));

		/* ODRASLI */		
		// body class
		$odrasli_body_class = trim($this->params->get('odrasli_body_class', 'odrasli'));
		// id k2 kategorije
		$odrasli_k2_category = trim($this->params->get('odrasli_k2_category', '83'));

		/* STAREJŠI */		
		// body class 
		$starejsi_body_class = trim($this->params->get('starejsi_body_class', 'starejsi'));
		// id k2 kategorije
		$starejsi_k2_category = trim($this->params->get('starejsi_k2_category', '86'));
		
		/**
		* Preverjamo URL parametre in določimo body class za posamezno starostno skupino, ker template assignment 
		* vedno ne deluje ali pa K2 članek/kategorija nima menija, na katerega bi vezali template style
		**/

		/* OTROCI */
		// poglej, če smo na podstrani z itemid-ji, ki so vnešeni v polje "otroci_nonk2_item_ids" za otroke in če smo, dodaj page class otroci
		if(in_array($itemid, $otroci_itemids)) {
			if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
				$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$otroci_body_class.'$4$5', $content);
			} elseif (preg_match('/(<body.*?)(>)/', $content)) {
				$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$otroci_body_class.'">', $content);
			}
			JFactory::getApplication()->setBody($content);
		}
		
		// nismo v k2 članku, ampak v npr. kategoriji
		if($option == 'com_k2' && $k2Id && $view !== 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$otroci_k2_category."' OR parent = '".$otroci_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			// poglej, če smo smo v kategoriji otroci ali v children kategoriji
			if(in_array($k2Id, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$otroci_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$otroci_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}
		// smo v k2 članku
		elseif($option == 'com_k2' && $k2Id && $view == 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$otroci_k2_category."' OR parent = '".$otroci_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2CatId, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$otroci_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$otroci_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}

		
		/* MLADI */
		// če je joomla menu itemid v navedeni vrednosti/arrayu (itemidji, ki niso k2 članek ali nimajo page class-a mladi), dodaj page mladi
		if(in_array($itemid, $mladi_itemids)) {
			if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
				$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$mladi_body_class.'$4$5', $content);
			} elseif (preg_match('/(<body.*?)(>)/', $content)) {
				$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$mladi_body_class.'">', $content);
			}
			JFactory::getApplication()->setBody($content);
		}

		// nismo v k2 članku, ampak v npr. kategoriji
		if($option == 'com_k2' && $k2Id && $view !== 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$mladi_k2_category."' OR parent = '".$mladi_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2Id, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$mladi_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$mladi_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}
		// smo v k2 članku
		elseif($option == 'com_k2' && $k2Id && $view == 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$mladi_k2_category."' OR parent = '".$mladi_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2CatId, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$mladi_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$mladi_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}
		
		
		/* ODRASLI */
		// nismo v k2 članku, ampak v npr. kategoriji
		if($option == 'com_k2' && $k2Id && $view !== 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$odrasli_k2_category."' OR parent = '".$odrasli_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2Id, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$odrasli_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$odrasli_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}
		// smo v k2 članku
		elseif($option == 'com_k2' && $k2Id && $view == 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$odrasli_k2_category."' OR parent = '".$odrasli_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2CatId, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$odrasli_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$odrasli_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}


		/* STAREJŠI */
		// nismo v k2 članku, ampak v npr. kategoriji
		if($option == 'com_k2' && $k2Id && $view !== 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$starejsi_k2_category."' OR parent = '".$starejsi_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2Id, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$starejsi_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$starejsi_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}
		// smo v k2 članku
		elseif($option == 'com_k2' && $k2Id && $view == 'item') {
			$db = JFactory::getDBO();
			$db->setQuery("SELECT catid FROM #__k2_items WHERE id = ".$k2Id );
			$k2CatId = $db->loadResult();
					
			$db->setQuery("SELECT id FROM #__k2_categories WHERE id = '".$starejsi_k2_category."' OR parent = '".$starejsi_k2_category."'" );
			$k2CatIdArray = $db->loadColumn();
			
			if(in_array($k2CatId, $k2CatIdArray)) {
				if (preg_match('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', $content)) {
					$content = preg_replace('/(<body.*?)(class *= *"|\')(.*)("|\')(.*>)/', '$1$2$3 '.$starejsi_body_class.'$4$5', $content);
				} elseif (preg_match('/(<body.*?)(>)/', $content)) {
					$content = preg_replace('/(<body.*?)(>)/', '$1 class ="'.$starejsi_body_class.'">', $content);
				}
				JFactory::getApplication()->setBody($content);
			}
		}

	}
}