<?php
declare(strict_types=1);

namespace GildedRose;

/*
 * Refactorización Post-Feedback
 * 
 * Tras el feedback recibido sobre Single Responsibility Principle, se implementó
 * una arquitectura basada en Strategy Pattern y Factory Pattern.
 * 
 * Problemas identificados en la versión anterior:
 * - Toda la lógica concentrada en GildedRose, antes era muy dificil de escalar y violaba SRP.
 * Solución implementada:
 * - Cada tipo de item tiene su propia clase (Strategy Pattern)
 * - Factory centraliza la selección del updater apropiado
 */

interface ItemUpdater
{
    public function canHandle(Item $item): bool;
    
    public function update(Item $item): void;
}

abstract class BaseItemUpdater implements ItemUpdater
{
    protected function increaseQuality(Item $item, int $amount = 1): void
    {
        $item->quality = min(50, $item->quality + $amount);
    }

    protected function decreaseQuality(Item $item, int $amount = 1): void
    {
        $item->quality = max(0, $item->quality - $amount);
    }
}

class NormalItemUpdater extends BaseItemUpdater
{
    public function canHandle(Item $item): bool
    {
        return true;
    }

    public function update(Item $item): void
    {
        $item->sellIn--;

        if ($item->sellIn < 0) {
            $this->decreaseQuality($item, 2);
        } else {
            $this->decreaseQuality($item, 1);
        }
    }
}

class AgedBrieUpdater extends BaseItemUpdater
{
    public function canHandle(Item $item): bool
    {
        return $item->name === 'Aged Brie';
    }

    public function update(Item $item): void
    {
        $item->sellIn--;

        if ($item->sellIn < 0) {
            $this->increaseQuality($item, 2);
        } else {
            $this->increaseQuality($item, 1);
        }
    }
}

class SulfurasUpdater extends BaseItemUpdater
{
    public function canHandle(Item $item): bool
    {
        return $item->name === 'Sulfuras, Hand of Ragnaros';
    }

    public function update(Item $item): void
    {
    }
}

class BackstagePassUpdater extends BaseItemUpdater
{
    public function canHandle(Item $item): bool
    {
        return $item->name === 'Backstage passes to a TAFKAL80ETC concert';
    }

    public function update(Item $item): void
    {
        $item->sellIn--;

        if ($item->sellIn < 0) {
            $item->quality = 0;
            return;
        }

        $increase = 1;
        if ($item->sellIn < 5) {
            $increase = 3;
        } elseif ($item->sellIn < 10) {
            $increase = 2;
        }

        $this->increaseQuality($item, $increase);
    }
}

class ConjuredItemUpdater extends BaseItemUpdater
{
    public function canHandle(Item $item): bool
    {
        return str_starts_with($item->name, 'Conjured');
    }

    public function update(Item $item): void
    {
        $item->sellIn--;

        if ($item->sellIn < 0) {
            $this->decreaseQuality($item, 4); 
        } else {
            $this->decreaseQuality($item, 2);
        }
    }
}

class ItemUpdaterFactory
{
    private array $updaters;
    private ItemUpdater $defaultUpdater;

    public function __construct()
    {
        $this->updaters = [
            new SulfurasUpdater(),
            new AgedBrieUpdater(),
            new BackstagePassUpdater(),
            new ConjuredItemUpdater(),
        ];

        $this->defaultUpdater = new NormalItemUpdater();
    }

    public function getUpdaterFor(Item $item): ItemUpdater
    {
        foreach ($this->updaters as $updater) {
            if ($updater->canHandle($item)) {
                return $updater;
            }
        }

        return $this->defaultUpdater;
    }
}

final class GildedRose
{
    private ItemUpdaterFactory $factory;

    /**
     * @param Item[] $items
     */
    public function __construct(
        private array $items
    ) {
        $this->factory = new ItemUpdaterFactory();
    }

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $updater = $this->factory->getUpdaterFor($item);
            $updater->update($item);
        }
    }
}
