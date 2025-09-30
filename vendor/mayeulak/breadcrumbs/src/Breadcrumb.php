<?php

namespace Bedigit\Breadcrumbs;

class Breadcrumb
{
	protected array $items = [];
	protected mixed $config;
	protected int $homeIndex = -1; // Track home item's position
	
	public function __construct()
	{
		$this->config = config('breadcrumbs');
		if ($this->config['home']['enabled']) {
			$this->items[] = [
				'title' => $this->config['home']['title'],
				'url'   => $this->config['home']['url'],
			];
			$this->homeIndex = 0; // Home is the first item
		}
	}
	
	public function add(string $title, ?string $url = null): self
	{
		$this->items[] = [
			'title' => $title,
			'url'   => $url,
		];
		
		return $this;
	}
	
	public function setHome(string $title, ?string $url = null): self
	{
		$home = [
			'title' => $title,
			'url'   => $url,
		];
		
		if ($this->homeIndex >= 0) {
			// Update existing home
			$this->items[$this->homeIndex] = $home;
		} else {
			// Add home if it wasn't enabled initially
			array_unshift($this->items, $home);
			$this->homeIndex = 0;
		}
		
		return $this; // Allow chaining
	}
	
	public function clear(): self
	{
		$this->items = [];
		$this->homeIndex = -1;
		
		return $this; // Allow chaining
	}
	
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function hasItems(): bool
	{
		return !empty($this->items);
	}
	
	public function render(?string $view = null): string
	{
		$view = $view ?? $this->config['view'];
		
		return view($view, ['breadcrumbs' => $this->items])->render();
	}
}
