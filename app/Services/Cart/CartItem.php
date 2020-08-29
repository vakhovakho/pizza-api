<?php

namespace App\Services\Cart;

class CartItem
{
	public $id;
	public $selectedSize;
	public $amount;

	public function __construct($id, $size, $amount = null)
	{
		$this->id = intval($id);
		if (is_nan($this->id)) {
			throw new \InvalidArgumentException('{id} must be positive integer');
		}

		$this->selectedSize = strtolower($size);
		if (!in_array($this->selectedSize, ['small', 'medium', 'large'])) {
			throw new \InvalidArgumentException('{size} must be one of small, medium or large');
		}

		$this->amount = intval($amount ?? 1);
		if (!is_nan($this->amount) && $this->amount < 0) {
			throw new \InvalidArgumentException('{amount} must be positive integer');
		}
	}

	public function getIdentifier()
	{
		return $this->id . '-' . $this->selectedSize;
	}

	public function addAmount($amount = 1)
	{
		return $this->amount = min($this->amount + $amount, 99);
	}

	public function subAmount($amount = 1)
	{
		return $this->amount -= $amount;
	}

	public function empty()
	{
		return $this->amount < 1;
	}

	public function match(CartItem $item)
	{
		return $this->getIdentifier() == $item->getIdentifier();
	}
}
