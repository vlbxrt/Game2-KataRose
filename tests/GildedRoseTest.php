<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function testFoo(): void
    {
        $items = [new Item('foo', 0, 0)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame('foo', $items[0]->name);
        $this->assertSame(-1, $items[0]->sellIn);
        $this->assertSame(0, $items[0]->quality);
    }

    // tests items 

    public function testNormalItemDecreasesQuality(): void
    {
        $items = [new Item('Normal Item', 10, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(9, $items[0]->sellIn);
        $this->assertSame(19, $items[0]->quality);
    }

    public function testNormalItemDegradesTwiceAsFastAfterSellDate(): void
    {
        $items = [new Item('Normal Item', 0, 10)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(-1, $items[0]->sellIn);
        $this->assertSame(8, $items[0]->quality);
    }

    public function testQualityNeverNegative(): void
    {
        $items = [new Item('Normal Item', 5, 0)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(0, $items[0]->quality);
    }

    // tests aged y brie

    public function testAgedBrieIncreasesQuality(): void
    {
        $items = [new Item('Aged Brie', 10, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(21, $items[0]->quality);
    }

    public function testAgedBrieQualityNeverAbove50(): void
    {
        $items = [new Item('Aged Brie', 10, 50)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(50, $items[0]->quality);
    }

    public function testSulfurasNeverChanges(): void
    {
        $items = [new Item('Sulfuras, Hand of Ragnaros', 10, 80)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(80, $items[0]->quality);
        $this->assertSame(10, $items[0]->sellIn);
    }

    public function testBackstagePassesIncreaseBy1WhenMoreThan10Days(): void
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 15, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(21, $items[0]->quality);
    }

    public function testBackstagePassesIncreaseBy2When10DaysOrLess(): void
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 10, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(22, $items[0]->quality);
    }

    public function testBackstagePassesIncreaseBy3When5DaysOrLess(): void
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 5, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(23, $items[0]->quality);
    }

    public function testBackstagePassesDropToZeroAfterConcert(): void
    {
        $items = [new Item('Backstage passes to a TAFKAL80ETC concert', 0, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(0, $items[0]->quality);
    }

    public function testConjuredItemDegradesTwiceAsFast(): void
    {
        $items = [new Item('Conjured Mana Cake', 10, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(9, $items[0]->sellIn);
        $this->assertSame(18, $items[0]->quality);
    }

    public function testConjuredItemDegradesFourTimesAsFastAfterSellDate(): void
    {
        $items = [new Item('Conjured Mana Cake', 0, 20)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(-1, $items[0]->sellIn);
        $this->assertSame(16, $items[0]->quality);
    }

    public function testConjuredItemQualityNeverNegative(): void
    {
        $items = [new Item('Conjured Mana Cake', 5, 1)];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame(0, $items[0]->quality);
    }
}
