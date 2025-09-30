<?php

namespace extras\plugins\reviews\app\Http\Requests;

use App\Http\Requests\Admin\Request;

class ReviewRequest extends Request
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		if (isFromAdminPanel()) {
			$guard = getAuthGuard();
			
			return auth($guard)->check();
		} else {
			return true;
		}
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		$rules = [
			'comment' => ['required', 'min:10', 'max:1000'],
		];
		
		// Rating is required outside the Admin Panel
		if (!isFromAdminPanel()) {
			$rules['rating'] = ['required', 'integer', 'between:1,5'];
		}
		
		return $rules;
	}
	
	/**
	 * @return array
	 */
	public function messages(): array
	{
		return [
			'comment.required' => trans('reviews::messages.validation.comment.required'),
			'comment.min'      => trans('reviews::messages.validation.comment.min'),
			'comment.max'      => trans('reviews::messages.validation.comment.max'),
			'rating.required'  => trans('reviews::messages.validation.rating.required'),
			'rating.integer'   => trans('reviews::messages.validation.rating.integer'),
			'rating.between'   => trans('reviews::messages.validation.rating.between'),
		];
	}
}
