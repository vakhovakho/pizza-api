<?php

namespace App\Services\Cart;

use Illuminate\Cache\Repository;

class CartRepository
{
	protected $repository;

	protected $key;

	protected $ttl;

	/**
	 * CartRepository constructor.
	 *
	 * @param Repository                                $repository
	 * @param string                                    $key
	 * @param \DateTimeInterface|\DateInterval|int|null $ttl
	 */
	public function __construct(Repository $repository, string $key, $ttl = null)
	{
		$this->repository = $repository;
		$this->key = $key;
		$this->ttl = $ttl;
	}

	/**
	 * @param CartItem[] $items
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	protected function set($items)
	{
		$this->repository->set($this->key, $this->filter($items), $this->ttl);
	}

	/**
	 * @param CartItem[] $items
	 *
	 * @return CartItem[]
	 */
	public function filter($items)
	{
		return array_filter($items, function (CartItem $item) {
			return !$item->empty();
		});
	}

	/**
	 * @param int      $id
	 * @param string   $size small|medium|large
	 * @param int|null $amount
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function add($id, $size, $amount = null)
	{
		$fresh = new CartItem($id, $size, $amount);
		$items = $this->all();
		foreach ($items as $item) {
			if ($fresh->match($item)) {
				$item->addAmount($fresh->amount);
				$fresh = null;
				break;
			}
		}
		if (!is_null($fresh)) {
			$items[] = $fresh;
		}

		$this->set($items);
	}

	/**
	 * @param int      $id
	 * @param string   $size
	 * @param int|null $amount
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function remove($id, $size, $amount = null)
	{
		$target = new CartItem($id, $size, $amount ?? INF);
		$items = $this->all();

		foreach ($items as $item) {
			if ($item->match($target)) {
				$item->subAmount($target->amount);
			}
		}

		$this->set($items);
	}

	/**
	 * @return CartItem[]
	 */
	public function all()
	{
		return (array)$this->repository->get($this->key, []);
	}
}
