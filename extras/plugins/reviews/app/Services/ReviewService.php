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

namespace extras\plugins\reviews\app\Services;

use App\Http\Resources\EntityCollection;
use App\Http\Resources\PostResource;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use App\Services\BaseService;
use extras\plugins\reviews\app\Http\Requests\ReviewRequest;
use extras\plugins\reviews\app\Http\Resources\ReviewResource;
use extras\plugins\reviews\app\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewService extends BaseService
{
	/**
	 * List reviews
	 *
	 * @param $postId
	 * @param array $params
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getEntries($postId, array $params = []): JsonResponse
	{
		$perPage = getNumberOfItemsPerPage('reviews', $params['perPage'] ?? $this->perPage);
		$embed = getCommaSeparatedStrAsArray($params['embed'] ?? []);
		$sort = $params['sort'] ?? [];
		
		// Get all reviews that are not spam for the product and paginate them
		$reviews = Review::query()->where('post_id', $postId)->approved()->notSpam();
		
		if (in_array('post', $embed)) {
			$reviews->with('post');
		}
		if (in_array('user', $embed)) {
			$reviews->with('user');
		}
		
		// Sorting
		$reviews = $this->applySorting($reviews, ['created_at'], $sort);
		
		$reviews = $reviews->paginate($perPage);
		
		// If the request is made from the app's Web environment,
		// use the Web URL as the pagination's base URL
		$reviews = setPaginationBaseUrl($reviews);
		
		$collection = collect();
		$reviewResourceClass = '\extras\plugins\reviews\app\Http\Resources\ReviewResource';
		if (class_exists($reviewResourceClass)) {
			$collection = new EntityCollection($reviewResourceClass, $reviews, $params);
		}
		
		$message = ($reviews->count() <= 0) ? t('no_reviews_found') : null;
		
		return apiResponse()->withCollection($collection, $message);
	}
	
	/**
	 * Store review
	 *
	 * @param $postId
	 * @param \extras\plugins\reviews\app\Http\Requests\ReviewRequest $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store($postId, ReviewRequest $request): JsonResponse
	{
		// Get Post
		$post = Post::query()->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($postId);
		
		if (empty($post)) {
			return apiResponse()->notFound(t('post_not_found'));
		}
		
		$authUserId = auth(getAuthGuard())->user()?->getAuthIdentifier() ?? '0';
		
		// Instantiate Rating model & Save it
		$review = new Review();
		$review->post_id = $post->id;
		$review->user_id = $authUserId;
		$review->comment = $request->input('comment');
		$review->rating = $request->input('rating');
		$review->save();
		
		// Recalculate ratings for the specified listing
		$post->recalculateRating();
		
		$data = [
			'success' => true,
			'message' => trans('reviews::messages.review_posted'),
			'result'  => (new ReviewResource($review))->toArray($request),
			'extra' => [
				'post' => (new PostResource($post))->toArray($request),
			],
		];
		
		return apiResponse()->json($data);
	}
	
	/**
	 * Delete review(s)
	 *
	 * NOTE: Let's consider that only the reviews of the same listings can be deleted in bulk.
	 *
	 * @param $postId
	 * @param string $ids
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy($postId, string $ids): JsonResponse
	{
		$authUser = auth(getAuthGuard())->user();
		
		if (empty($authUser)) {
			return apiResponse()->unauthorized();
		}
		
		$data = [
			'success' => false,
			'message' => t('no_deletion_is_done'),
			'result'  => null,
		];
		
		$extra = [];
		
		// Get Entries ID (IDs separated by comma accepted)
		$ids = explode(',', $ids);
		
		// Delete
		$res = false;
		foreach ($ids as $reviewId) {
			$review = Review::query()->where('id', $reviewId);
			if (!$authUser->hasAllPermissions(Permission::getStaffPermissions())) {
				$review->where('user_id', $authUser->getAuthIdentifier());
			}
			$review->first();
			
			if (!empty($review)) {
				$res = $review->delete();
			}
		}
		
		// Recalculate ratings for the specified listing
		$extra['post'] = [];
		if (!empty($postId)) {
			$post = Post::query()
				->withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])
				->find($postId);
			
			if (!empty($post)) {
				$post->recalculateRating();
			}
			
			$extra['post'] = (new PostResource($post))->toArray(request());
		}
		
		// Confirmation
		if ($res) {
			$data['success'] = true;
			
			$count = count($ids);
			if ($count > 1) {
				$data['message'] = t('x entities have been deleted successfully', [
					'entities' => mb_strtolower(trans('reviews::messages.Reviews')),
					'count'    => $count,
				]);
			} else {
				$data['message'] = t('1 entity has been deleted successfully', [
					'entity' => mb_strtolower(trans('reviews::messages.Review')),
				]);
			}
		}
		
		$data['extra'] = $extra;
		
		return apiResponse()->json($data);
	}
}
