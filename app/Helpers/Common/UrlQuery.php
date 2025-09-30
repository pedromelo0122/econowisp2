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

namespace App\Helpers\Common;

class UrlQuery
{
	protected ?string $url;
	protected array $parsedUrl;
	protected array $parameters;
	protected array $numericParameters = ['distance'];
	
	/**
	 * @param string|null $url
	 * @param array $parameters
	 * @param null $secure
	 */
	public function __construct(?string $url = null, array $parameters = [], $secure = null)
	{
		// Get URL (Accepts URL & URI|Path)
		$this->url = !empty($url)
			? str_starts_with(mb_strtolower($url), 'http') ? $url : url($url, $parameters, $secure)
			: request()->fullUrl();
		
		// Get parsed URL
		$parsedUrl = mb_parse_url($this->url);
		$this->parsedUrl = is_array($parsedUrl) ? $parsedUrl : [];
		
		// Get query parameters
		$this->parameters = [];
		if (isset($this->parsedUrl['query'])) {
			mb_parse_str($this->parsedUrl['query'], $this->parameters);
		}
		$this->parameters = array_merge($this->parameters, $parameters);
		
		// Remove all empty query parameters
		$this->removeEmptyParameters();
		
		// In addition,
		// Remove the country parameter when the DomainMapping plugin is installed
		if (config('plugins.domainmapping.installed')) {
			$this->removeParameters(['country']);
		}
	}
	
	/* ---------------------------------------------------------------------------
	 *                              PARAMETER METHODS
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Set (add or update) the given query parameters
	 *
	 * @param array<string, string|array> $parameters
	 * @return $this
	 */
	public function setParameters(array $parameters): static
	{
		foreach ($parameters as $key => $value) {
			Arr::set($this->parameters, $key, $value);
		}
		
		// Remove all empty query parameters
		$this->removeEmptyParameters();
		
		return $this;
	}
	
	/**
	 * Remove a single parameter by key.
	 *
	 * @param string $parameterKey
	 * @return $this
	 */
	public function removeParameter(string $parameterKey): static
	{
		return $this->removeParameters([$parameterKey]);
	}
	
	/**
	 * Remove some query parameters
	 *
	 * @param array<int, string> $parameters
	 * @return $this
	 */
	public function removeParameters(array $parameters): static
	{
		// Remove empty elements
		$parameters = array_filter($parameters);
		
		// Remove the parameters
		foreach ($parameters as $parameter) {
			Arr::forget($this->parameters, $parameter);
		}
		
		return $this;
	}
	
	/**
	 * Remove all the query parameters
	 *
	 * @return $this
	 */
	public function removeAllParameters(): static
	{
		$this->parameters = [];
		
		return $this;
	}
	
	/**
	 * Remove all the query parameters which value is empty
	 *
	 * @return void
	 */
	protected function removeEmptyParameters(): void
	{
		$this->parameters = $this->removeEmptyRecursive($this->parameters);
	}
	
	/**
	 * Remove all empty query parameters recursively
	 *
	 * @param array $array
	 * @return array
	 */
	protected function removeEmptyRecursive(array $array): array
	{
		return array_filter($array, function ($value, $key) {
			if (is_array($value)) {
				$value = $this->removeEmptyRecursive($value);
			}
			
			return (in_array($key, $this->numericParameters))
				? !empty($value) || $value == 0
				: !empty($value);
		}, ARRAY_FILTER_USE_BOTH);
	}
	
	/* ---------------------------------------------------------------------------
	 *                           PARAMETER CHECKS/GETTERS
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Check if a single parameter exists.
	 *
	 * @param string $parameterKey
	 * @return bool
	 */
	public function hasParameter(string $parameterKey): bool
	{
		return !empty($this->getParameter($parameterKey));
	}
	
	/**
	 * Check if ALL listed parameters exist.
	 *
	 * @param array $parameterKeys
	 * @return bool
	 */
	public function hasParameters(array $parameterKeys): bool
	{
		return !empty($this->getParameters($parameterKeys));
	}
	
	/**
	 * Throw an error if the parameter is missing; otherwise return its value.
	 *
	 * @param string $parameterKey
	 * @return array|string
	 * @throws \Exception
	 */
	public function requireParameter(string $parameterKey): array|string
	{
		$value = $this->getParameter($parameterKey);
		if (empty($value)) {
			throw new \Exception("Parameter '$parameterKey' is required but missing.");
		}
		
		return $value;
	}
	
	/**
	 * Get a single parameter's value or null if not found.
	 *
	 * @param string $parameterKey
	 * @return array|string|null
	 */
	public function getParameter(string $parameterKey): array|string|null
	{
		$value = $this->getParameters([$parameterKey]);
		
		return $value[$parameterKey] ?? null;
	}
	
	/**
	 * Get specific query parameters (if they exist)
	 *
	 * @param array<int, string> $parameterKeys
	 * @return array<string, string|array>
	 */
	public function getParameters(array $parameterKeys): array
	{
		$result = [];
		foreach ($parameterKeys as $key) {
			$value = Arr::get($this->parameters, $key);
			if ($value !== null) {
				Arr::set($result, $key, $value);
			}
		}
		
		return $result;
	}
	
	/**
	 * Get query parameters by excluding some ones
	 *
	 * @param array<int, string> $parameterKeys
	 * @return array<string, string|array>
	 */
	public function getParametersExcluding(array $parameterKeys): array
	{
		$filteredParameters = $this->parameters;
		
		foreach ($parameterKeys as $key) {
			Arr::forget($filteredParameters, $key);
		}
		
		return $filteredParameters;
	}
	
	/**
	 * Get all the query parameters
	 *
	 * @return array<string, string|array>
	 */
	public function getAllParameters(): array
	{
		return $this->parameters;
	}
	
	/* ---------------------------------------------------------------------------
	 *                         URL BUILDING AND MANIPULATION
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Build new URL with the updated query parameters
	 *
	 * @return string
	 */
	public function buildUrl(): string
	{
		$newQueryString = Arr::query($this->parameters);
		$modifiedUrl = $this->parsedUrl['scheme'] . '://' . $this->parsedUrl['host'];
		
		if (isset($this->parsedUrl['port'])) {
			$modifiedUrl .= ':' . $this->parsedUrl['port'];
		}
		
		if (isset($this->parsedUrl['path'])) {
			$modifiedUrl .= $this->parsedUrl['path'];
		}
		
		if ($newQueryString) {
			$modifiedUrl .= '?' . $newQueryString;
		}
		
		if (isset($this->parsedUrl['fragment'])) {
			$modifiedUrl .= '#' . $this->parsedUrl['fragment'];
		}
		
		return $modifiedUrl;
	}
	
	/**
	 * @return string
	 */
	public function toString(): string
	{
		return $this->buildUrl();
	}
	
	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->buildUrl();
	}
	
	/**
	 * Build a relative URL (path + query + fragment).
	 *
	 * @return string
	 */
	public function buildRelativeUrl(): string
	{
		$path = $this->parsedUrl['path'] ?? '';
		$newQueryString = Arr::query($this->parameters);
		
		$relativeUrl = $path;
		
		if (!empty($newQueryString)) {
			$relativeUrl .= '?' . $newQueryString;
		}
		
		if (isset($this->parsedUrl['fragment'])) {
			$relativeUrl .= '#' . $this->parsedUrl['fragment'];
		}
		
		return $relativeUrl;
	}
	
	/**
	 * Get the current path component of the URL.
	 *
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->parsedUrl['path'] ?? '';
	}
	
	/**
	 * Set the path component of the URL.
	 *
	 * @param string $path
	 * @return static
	 */
	public function setPath(string $path): static
	{
		$this->parsedUrl['path'] = $path;
		
		return $this;
	}
	
	/**
	 * Get the host component of the URL.
	 *
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->parsedUrl['host'] ?? '';
	}
	
	/**
	 * Set the host component of the URL.
	 *
	 * @param string $host
	 * @return static
	 */
	public function setHost(string $host): static
	{
		$this->parsedUrl['host'] = $host;
		
		return $this;
	}
	
	/**
	 * Set the fragment/hash (without '#').
	 *
	 * @param string $fragment
	 * @return static
	 */
	public function setFragment(string $fragment): static
	{
		// Just in case, strip any leading '#' characters
		$fragment = ltrim($fragment, '#');
		$this->parsedUrl['fragment'] = $fragment;
		
		return $this;
	}
	
	/**
	 * Remove the fragment/hash from the URL.
	 *
	 * @return static
	 */
	public function removeFragment(): static
	{
		unset($this->parsedUrl['fragment']);
		
		return $this;
	}
	
	/**
	 * Clone the current UrlQuery instance as a new object with the same data.
	 *
	 * @return static
	 */
	public function clone(): static
	{
		// Re-instantiate with the same URL (including current parameters)
		return new static($this->buildUrl());
	}
}
