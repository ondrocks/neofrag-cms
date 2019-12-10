<?php
/**
 * https://neofr.ag
 * @author: Michaël BILCOT <michael.bilcot@neofr.ag>
 */

namespace NF\Modules\Addons\Controllers\Addons;

use NF\NeoFrag\Loadables\Controller;

class Language extends Controller
{
	public $__label = ['Langues', 'Langue', 'fa-flag-o', 'danger'];

	public function __actions()
	{
		return $this->array
					->set('enable', ['Activer', 'fa-check', 'success', TRUE, function($addon){
						return !$addon->is_enabled();
					}])
					->set('disable', ['Désactiver', 'fa-times', 'muted', TRUE, function($addon){
						return count($this->config->langs) > 1 && $addon->is_enabled();
					}])
					->set('order', ['Ordre', 'fa-sort', 'info', TRUE]);
	}

	public function enable($addon)
	{
		$addon->__addon->set('data', $addon->__addon->data->set('enabled', TRUE))->update();

		notify($this->lang('<b>%s</b> activé', $addon->info()->title));

		refresh();
	}

	public function disable($addon)
	{
		$addon->__addon->set('data', $addon->__addon->data->set('enabled', FALSE))->update();

		notify($this->lang('<b>%s</b> désactivé', $addon->info()->title));

		refresh();
	}

	public function order($addon)
	{
		$langs = $this->array($this->config->langs);
		
		if (($post = post_check('position')) && (list($position) = array_values($post)))
		{
			
			$addon->__addon->set('data', $addon->__addon->data->set('order', $position))->update();
			
			return $this->output->json(['success' => 'refresh']);
			
		}
		return $this->modal('Préférence des langues', 'fa-flag-o')
					->body($this->view('language_order', ['langs' => $langs]));
	}
}
