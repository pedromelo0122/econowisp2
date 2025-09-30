<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: Mayeul Akpovi (BeDigit - https://bedigit.com)
 *
 * LICENSE
 * -------
 * This software is provided under a license agreement and may only be used or copied
 * in accordance with its terms, including the inclusion of the above copyright notice.
 * As this software is sold exclusively on CodeCanyon,
 * please review the full license details here: https://codecanyon.net/licenses/standard
 */

namespace App\Http\Controllers\Web\Admin\Panel\Traits;

// save_and_back save_and_edit save_and_new
trait SaveActions
{
	/**
	 * Get the save configured save action or the one stored in a session variable.
	 *
	 * @return array[]
	 */
	public function getSaveAction()
	{
		$defaultSaveAction = config('larapen.admin.default_save_action', 'save_and_back');
		$saveAction = session('save_action', $defaultSaveAction);
		$saveOptions = [];
		$saveCurrent = [
			'value' => $saveAction,
			'label' => $this->getSaveActionButtonName($saveAction),
		];
		
		switch ($saveAction) {
			case 'save_and_edit':
				$saveOptions['save_and_back'] = $this->getSaveActionButtonName('save_and_back');
				if ($this->xPanel->hasAccess('create')) {
					$saveOptions['save_and_new'] = $this->getSaveActionButtonName('save_and_new');
				}
				break;
			case 'save_and_new':
				$saveOptions['save_and_back'] = $this->getSaveActionButtonName('save_and_back');
				if ($this->xPanel->hasAccess('update')) {
					$saveOptions['save_and_edit'] = $this->getSaveActionButtonName('save_and_edit');
				}
				break;
			case 'save_and_black':
			default:
				if ($this->xPanel->hasAccess('update')) {
					$saveOptions['save_and_edit'] = $this->getSaveActionButtonName('save_and_edit');
				}
				if ($this->xPanel->hasAccess('create')) {
					$saveOptions['save_and_new'] = $this->getSaveActionButtonName('save_and_new');
				}
				break;
		}
		
		return [
			'active'  => $saveCurrent,
			'options' => $saveOptions,
		];
	}
	
	/**
	 * Change the session variable that remembers what to do after the "Save" action.
	 *
	 * @param [type] $forceSaveAction [description]
	 */
	public function setSaveAction($forceSaveAction = null)
	{
		if ($forceSaveAction) {
			$saveAction = $forceSaveAction;
		} else {
			$defaultSaveAction = config('larapen.admin.default_save_action', 'save_and_back');
			$saveAction = request()->input('save_action', $defaultSaveAction);
		}
		
		if (session('save_action', 'save_and_back') !== $saveAction) {
			$message = trans('admin.save_action_changed_notification');
			notification($message, 'info');
		}
		
		session()->put('save_action', $saveAction);
	}
	
	/**
	 * Redirect to the correct URL, depending on which save action has been selected.
	 *
	 * @param null $itemId
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function performSaveAction($itemId = null)
	{
		$defaultSaveAction = config('larapen.admin.default_save_action', 'save_and_back');
		$saveAction = request()->input('save_action', $defaultSaveAction);
		
		switch ($saveAction) {
			case 'save_and_new':
				$redirectUrl = $this->xPanel->getUrl('create');
				break;
			case 'save_and_edit':
				$itemId = !empty($itemId) ? $itemId : request()->input('id');
				$redirectUrl = $this->xPanel->getUrl($itemId . '/edit');
				
				$locale = request()->input('locale');
				if (!empty($locale)) {
					$redirectUrl = urlQuery($redirectUrl)->setParameters(['locale' => $locale])->toString();
				}
				break;
			case 'save_and_back':
			default:
				$redirectUrl = $this->xPanel->getUrl();
				break;
		}
		
		return redirect()->to($redirectUrl);
	}
	
	/**
	 * Get the translated text for the Save button.
	 *
	 * @param string|null $actionValue
	 * @return string
	 */
	private function getSaveActionButtonName(?string $actionValue = 'save_and_black'): string
	{
		$name = match ($actionValue) {
			'save_and_edit' => trans('admin.save_action_save_and_edit'),
			'save_and_new'  => trans('admin.save_action_save_and_new'),
			default         => trans('admin.save_action_save_and_back'),
		};
		
		return getAsString($name, 'Save');
	}
}
