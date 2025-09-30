<?php

namespace extras\plugins\reviews\app\Http\Controllers\Web\Front;

use App\Http\Controllers\Web\Front\FrontController;
use extras\plugins\reviews\app\Http\Requests\ReviewRequest;
use extras\plugins\reviews\app\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends FrontController
{
	protected ReviewService $reviewService;
	
	public function __construct(ReviewService $reviewService)
	{
		parent::__construct();
		
		$this->reviewService = $reviewService;
	}
	
	/**
	 * Store a new review
	 *
	 * @param $postId
	 * @param \extras\plugins\reviews\app\Http\Requests\ReviewRequest $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store($postId, ReviewRequest $request): RedirectResponse
	{
		// Store the review
		$data = getServiceData($this->reviewService->store($postId, $request));
		
		// Parsing the API response
		$message = data_get($data, 'message', t('unknown_error'));
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
			session()->flash('review_posted');
		} else {
			flash($message)->error();
			
			return redirect()->back()->withErrors(['error' => $message])->withInput();
		}
		
		// Get the Listing
		$post = data_get($data, 'extra.post') ?? [];
		
		$nextUrl = !empty($post)
			? urlGen()->post($post) . '#item-reviews'
			: url('/');
		
		// Redirect
		return redirect()->to($nextUrl);
	}
	
	/**
	 * @param $postId
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy($postId, $id): RedirectResponse
	{
		// Get entries ID(s)
		$ids = getSelectedEntryIds($id, request()->input('entries'), asString: true);
		
		// Delete the review
		$data = getServiceData($this->reviewService->destroy($postId, $ids));
		
		// Parsing the API response
		$message = data_get($data, 'message');
		
		// Notification Message
		if (data_get($data, 'success')) {
			flash($message)->success();
			session()->flash('review_removed');
		} else {
			$message = $message ?? t('unknown_error');
			flash($message)->error();
			
			return redirect()->back()->withErrors(['error' => $message])->withInput();
		}
		
		// Get the Listing
		$post = data_get($data, 'extra.post') ?? [];
		
		$nextUrl = !empty($post)
			? urlGen()->post($post) . '#item-reviews'
			: url('/');
		
		// Redirect
		return redirect()->to($nextUrl);
	}
}
