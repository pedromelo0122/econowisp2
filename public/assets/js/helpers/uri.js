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

/**
 * UrlQuery Helper Function
 *
 * @param {string|null} url
 * @param {object} parameters
 * @param {boolean|null} secure
 * @returns {UrlQuery}
 */
function urlQuery(url = null, parameters = {}, secure = null) {
	return new UrlQuery(url, parameters, secure);
}

class UrlQuery {
	/**
	 * @param {string|null} url        - The full (or partial) URL. If null, defaults to current browser URL.
	 * @param {object}      parameters - Additional query parameters to merge in.
	 * @param {boolean|null} secure    - true => force https; false => force http; null => no change.
	 */
	constructor(url = null, parameters = {}, secure = null) {
		// Parameters that should keep '0' or 0 as valid (i.e., not removed as "empty")
		this.numericParameters = ['distance'];
		
		// Store the original URL, so we can check if it was null or empty
		this.originalUrl = url;
		
		// Default to the current browser URL if none provided
		const baseUrl = url ? url : window.location.href;
		
		// Use the browser's URL API
		this.parsedUrl = new URL(baseUrl);
		
		// If secure is explicitly set, force protocol
		if (secure === true) {
			this.parsedUrl.protocol = 'https:';
		} else if (secure === false) {
			this.parsedUrl.protocol = 'http:';
		}
		
		// Convert existing URL search params to an object
		this.parameters = {};
		for (const [key, value] of this.parsedUrl.searchParams.entries()) {
			this._setDeepValue(this.parameters, key, value);
		}
		
		// Merge additional parameters
		for (const [key, value] of Object.entries(parameters)) {
			this._setDeepValue(this.parameters, key, value);
		}
		
		// Remove empty parameters
		this.removeEmptyParameters();
	}
	
	/* ---------------------------------------------------------------------------
	 *                              PARAMETER METHODS
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Add or update multiple parameters and remove empty values.
	 * @param {object} parameters
	 * @returns {this}
	 */
	setParameters(parameters = {}) {
		for (const [key, value] of Object.entries(parameters)) {
			this._setDeepValue(this.parameters, key, value);
		}
		this.removeEmptyParameters();
		return this;
	}
	
	/**
	 * Remove a single parameter by key (supports dot-notation).
	 * @param {string} parameterKey
	 * @returns {this}
	 */
	removeParameter(parameterKey) {
		return this.removeParameters([parameterKey]);
	}
	
	/**
	 * Remove specific parameters by key (supports dot notation).
	 * @param {string[]} parameterKeys
	 * @returns {this}
	 */
	removeParameters(parameterKeys = []) {
		for (const key of parameterKeys) {
			this._deleteDeepValue(this.parameters, key);
		}
		return this;
	}
	
	/**
	 * Remove all query parameters.
	 * @returns {this}
	 */
	removeAllParameters() {
		this.parameters = {};
		return this;
	}
	
	/**
	 * Removes empty parameters (recursively).
	 * @private
	 */
	removeEmptyParameters() {
		this.parameters = this._removeEmptyRecursive(this.parameters);
	}
	
	/* ---------------------------------------------------------------------------
	 *                           PARAMETER CHECKS/GETTERS
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Check if a single parameter exists (dot notation supported).
	 * @param {string} parameterKey
	 * @returns {boolean}
	 */
	hasParameter(parameterKey) {
		return this._getDeepValue(this.parameters, parameterKey) !== undefined;
	}
	
	/**
	 * Check if ALL listed parameters exist (dot notation).
	 * @param {string[]} parameterKeys
	 * @returns {boolean}
	 */
	hasParameters(parameterKeys = []) {
		for (const key of parameterKeys) {
			if (!this.hasParameter(key)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Throw an error if the parameter is missing; otherwise return its value.
	 * @param {string} parameterKey
	 * @returns {*}
	 * @throws {Error}
	 */
	requireParameter(parameterKey) {
		const val = this.getParameter(parameterKey);
		if (val === null) {
			throw new Error(`Parameter "${parameterKey}" is required but missing.`);
		}
		return val;
	}
	
	/**
	 * Get a single parameter's value or null if not found (dot notation).
	 * @param {string} parameterKey
	 * @returns {*|null}
	 */
	getParameter(parameterKey) {
		const val = this._getDeepValue(this.parameters, parameterKey);
		return val !== undefined ? val : null;
	}
	
	/**
	 * Get only the specified parameters (dot notation), ignoring missing ones.
	 * @param {string[]} parameterKeys
	 * @returns {object}
	 */
	getParameters(parameterKeys = []) {
		const result = {};
		for (const key of parameterKeys) {
			const val = this._getDeepValue(this.parameters, key);
			if (val !== undefined) {
				this._setDeepValue(result, key, val);
			}
		}
		return result;
	}
	
	/**
	 * Return a copy of current parameters, excluding specified keys (dot notation).
	 * @param {string[]} parameterKeys
	 * @returns {object}
	 */
	getParametersExcluding(parameterKeys = []) {
		const filtered = this._deepClone(this.parameters);
		for (const key of parameterKeys) {
			this._deleteDeepValue(filtered, key);
		}
		return filtered;
	}
	
	/**
	 * Get all current parameters as an object.
	 * @returns {object}
	 */
	getAllParameters() {
		return this.parameters;
	}
	
	/* ---------------------------------------------------------------------------
	 *                         URL BUILDING AND MANIPULATION
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Build and return the absolute URL with current query parameters.
	 * @returns {string}
	 */
	buildUrl() {
		let newUrl = `${this.parsedUrl.protocol}//${this.parsedUrl.hostname}`;
		
		if (this.parsedUrl.port) {
			newUrl += `:${this.parsedUrl.port}`;
		}
		newUrl += this.parsedUrl.pathname;
		
		const query = new URLSearchParams();
		this._objectToSearchParams(this.parameters, query);
		
		const queryString = query.toString();
		if (queryString) {
			newUrl += `?${queryString}`;
		}
		
		if (this.parsedUrl.hash) {
			newUrl += this.parsedUrl.hash;
		}
		
		return newUrl;
	}
	
	/**
	 * Return the absolute URL as a string. Optionally return null if the original URL was null or an empty string.
	 * @param {boolean} allowNullIfEmptyUrl - If true and the original URL was null/empty, return null.
	 * @returns {string|null}
	 */
	toString(allowNullIfEmptyUrl = false) {
		if (allowNullIfEmptyUrl && (this.originalUrl === null || this.originalUrl === '')) {
			return null;
		}
		return this.buildUrl();
	}
	
	/**
	 * Build and return a relative URL (pathname + ?query + #fragment).
	 * @returns {string}
	 */
	buildRelativeUrl() {
		const path = this.parsedUrl.pathname;
		const query = new URLSearchParams();
		this._objectToSearchParams(this.parameters, query);
		
		let relativeUrl = path;
		const queryString = query.toString();
		if (queryString) {
			relativeUrl += `?${queryString}`;
		}
		if (this.parsedUrl.hash) {
			relativeUrl += this.parsedUrl.hash;
		}
		return relativeUrl;
	}
	
	/**
	 * Get the URL path (pathname).
	 * @returns {string}
	 */
	getPath() {
		return this.parsedUrl.pathname;
	}
	
	/**
	 * Set the URL path (pathname).
	 * @param {string} newPath
	 * @returns {this}
	 */
	setPath(newPath) {
		this.parsedUrl.pathname = newPath;
		return this;
	}
	
	/**
	 * Get the URL host (hostname).
	 * @returns {string}
	 */
	getHost() {
		return this.parsedUrl.hostname;
	}
	
	/**
	 * Set the URL host (hostname).
	 * @param {string} newHost
	 * @returns {this}
	 */
	setHost(newHost) {
		this.parsedUrl.hostname = newHost;
		return this;
	}
	
	/**
	 * Set the URL hash/fragment (excluding the '#').
	 * @param {string} fragment
	 * @returns {this}
	 */
	setFragment(fragment = '') {
		this.parsedUrl.hash = fragment ? `#${fragment}` : '';
		return this;
	}
	
	/**
	 * Remove the URL hash/fragment entirely.
	 * @returns {this}
	 */
	removeFragment() {
		this.parsedUrl.hash = '';
		return this;
	}
	
	/**
	 * Clone this UrlQuery instance by re-parsing its absolute URL.
	 * @returns {UrlQuery}
	 */
	clone() {
		return new UrlQuery(this.buildUrl());
	}
	
	/* ---------------------------------------------------------------------------
	 *                              PRIVATE HELPERS
	 * -------------------------------------------------------------------------*/
	
	/**
	 * Recursively remove empty values (e.g., '', null, undefined) from an object/array.
	 * Numeric parameters can keep "0" or 0.
	 * @private
	 */
	_removeEmptyRecursive(data) {
		if (Array.isArray(data)) {
			return data
			.map((item) => {
				if (item && typeof item === 'object') {
					return this._removeEmptyRecursive(item);
				}
				return item;
			})
			.filter((item) => item !== '' && item !== null && item !== undefined);
		} else if (data && typeof data === 'object') {
			const result = {};
			for (const [key, val] of Object.entries(data)) {
				let cleanedValue = val;
				if (val && typeof val === 'object') {
					cleanedValue = this._removeEmptyRecursive(val);
				}
				if (this.numericParameters.includes(key)) {
					if (cleanedValue !== '' && cleanedValue !== null && cleanedValue !== undefined) {
						result[key] = cleanedValue;
					} else if (cleanedValue === 0 || cleanedValue === '0') {
						result[key] = cleanedValue;
					}
				} else {
					if (
						cleanedValue !== ''
						&& cleanedValue !== null
						&& cleanedValue !== undefined
						&& !(Array.isArray(cleanedValue) && cleanedValue.length === 0)
					) {
						result[key] = cleanedValue;
					}
				}
			}
			return result;
		}
		return data;
	}
	
	/**
	 * Convert a nested object into URLSearchParams (like Laravel's Arr::query).
	 * @private
	 */
	_objectToSearchParams(obj, searchParams, parentKey = '') {
		if (obj && typeof obj === 'object' && !Array.isArray(obj)) {
			for (const [key, val] of Object.entries(obj)) {
				const newKey = parentKey ? `${parentKey}[${key}]` : key;
				this._objectToSearchParams(val, searchParams, newKey);
			}
		} else if (Array.isArray(obj)) {
			obj.forEach((val, index) => {
				const newKey = `${parentKey}[${index}]`;
				this._objectToSearchParams(val, searchParams, newKey);
			});
		} else {
			searchParams.append(parentKey, obj);
		}
	}
	
	/**
	 * Dot-notation get. E.g. "user.name" => obj.user.name
	 * @private
	 */
	_getDeepValue(obj, keyPath) {
		const keys = keyPath.split('.');
		let current = obj;
		for (const k of keys) {
			if (current === undefined || current === null || typeof current !== 'object') {
				return undefined;
			}
			current = current[k];
		}
		return current;
	}
	
	/**
	 * Dot-notation set. E.g. ("user.name", "Alice") => obj.user.name = "Alice"
	 * @private
	 */
	_setDeepValue(obj, keyPath, value) {
		const keys = keyPath.split('.');
		let current = obj;
		while (keys.length > 1) {
			const k = keys.shift();
			if (current[k] === undefined || current[k] === null || typeof current[k] !== 'object') {
				current[k] = {};
			}
			current = current[k];
		}
		current[keys[0]] = value;
	}
	
	/**
	 * Dot-notation delete. E.g. "user.name" => delete obj.user.name
	 * @private
	 */
	_deleteDeepValue(obj, keyPath) {
		const keys = keyPath.split('.');
		let current = obj;
		while (keys.length > 1) {
			const k = keys.shift();
			if (current[k] === undefined || current[k] === null || typeof current[k] !== 'object') {
				return;
			}
			current = current[k];
		}
		delete current[keys[0]];
	}
	
	/**
	 * Simple deep clone via JSON (not suitable for functions, Dates, etc.).
	 * @private
	 */
	_deepClone(value) {
		return JSON.parse(JSON.stringify(value));
	}
}

/*
 ========================================================================================
 Usage Examples
 ----------------------------------------------------------------------------------------
 Example 1: Basic Usage
 ----------------------------------------------------------------------------------------
 // Current page URL: https://example.com?distance=0&foo=bar
 const urlQ = new UrlQuery();
 
 // Check if a parameter exists
 console.log(urlQ.hasParameter('foo'));         // true
 console.log(urlQ.getParameter('foo')); // "bar"
 
 // Remove a parameter
 urlQ.removeParameters(['foo']);
 console.log(urlQ.buildUrl());
 // => "https://example.com?distance=0" (assuming hash/fragment was empty)
 
 ----------------------------------------------------------------------------------------
 Example 2: Dot Notation
 ----------------------------------------------------------------------------------------
 // Suppose the current URL is: https://example.com?user[name]=Alice&user[role]=admin
 const urlQ = new UrlQuery();
 
 // Dot notation used for read/update
 console.log(urlQ.getParameter('user.name')); // "Alice"
 urlQ.setParameters({ 'user.name': 'Bob' });
 console.log(urlQ.getParameter('user.name')); // "Bob"
 
 ----------------------------------------------------------------------------------------
 Example 3: Forcing HTTPS, Relative URL, and Cloning
 ----------------------------------------------------------------------------------------
 // Start with an http URL
 const secureUrl = new UrlQuery('http://example.org?foo=bar', {}, true);
 console.log(secureUrl.toString());
 // => "https://example.org?foo=bar"
 
 // Using toString with allowEmpty
 const emptyUrl = new UrlQuery(null);
 console.log(emptyUrl.toString(true)); // => null
 console.log(emptyUrl.toString());     // => current browser URL
 
 // Build a relative URL (pathname + ?query + #fragment)
 secureUrl.setPath('/products');
 secureUrl.setFragment('details');
 console.log(secureUrl.buildRelativeUrl());
 // => "/products?foo=bar#details"
 
 // Clone and modify the clone
 const cloneUrl = secureUrl.clone();
 cloneUrl.removeParameters(['foo']);
 
 or
 
 // Remove a single parameter by name
 urlQuery.removeParameter('foo');
 
 console.log(cloneUrl.buildUrl());
 // => "https://example.org/products#details"
 // (original "secureUrl" still has ?foo=bar)
 
 // If you remove an unused or nonexistent parameter, it simply does nothing
 urlQuery.removeParameter('unknownParam');
 
 ----------------------------------------------------------------------------------------
 Example 4: requireParameter
 ----------------------------------------------------------------------------------------
 // If the query is missing "token", throw an error:
 try {
 const token = urlQ.requireParameter('token');
 console.log('Token is:', token);
 } catch (err) {
 console.error(err.message);
 // => "Parameter "token" is required but missing."
 }
 ========================================================================================
 */
