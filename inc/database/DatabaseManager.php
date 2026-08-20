<?php

declare(strict_types=1);

namespace HKS\Database;


use HKS\Database\Migrations\CreateUsersTable;
use HKS\Database\Migrations\CreateProductSeriesTable;
use HKS\Database\Migrations\CreateProductsTable;
use HKS\Database\Migrations\AddPreviewPdfToProducts;            
use HKS\Database\Migrations\AddSoftwareProductType;
use HKS\Database\Migrations\AddPublicationProductTypes;
use HKS\Database\Migrations\AddSoftwareVersionToProducts;
use HKS\Database\Migrations\CreateSalesOptionsTable;
use HKS\Database\Migrations\CreateSalesChannelsTable;
use HKS\Database\Migrations\CreateShippingMethodsTable;
use HKS\Database\Migrations\CreateOrdersTable;
use HKS\Database\Migrations\CreateOrderItemsTable;
use HKS\Database\Migrations\CreateSubscriptionsTable;
use HKS\Database\Migrations\CreateShipmentsTable;
use HKS\Database\Migrations\CreateInventoryTable;
use HKS\Database\Migrations\CreateInventoryLogsTable;
use HKS\Database\Migrations\CreateLicensesTable;
use HKS\Database\Migrations\CreateProductDownloadsTable;


/**
 * Framework Database Manager
 *
 * Frameworkで使用する独自DBテーブルの
 * Migration実行を管理する。
 */
final class DatabaseManager
{
    /**
     * Database Migrationを実行する。
     */
    public static function migrate(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Migration List
        |--------------------------------------------------------------------------
        |
        | Frameworkで必要となるMigrationを
        | 実行順に登録する。
        |
        | 今後テーブルを追加する場合は、
        | この配列へMigrationクラスを追加する。
        |
        */


        $migrations = [
            CreateUsersTable::class,
            CreateProductSeriesTable::class,
            CreateProductsTable::class,
            AddPreviewPdfToProducts::class,
            AddSoftwareProductType::class,
            AddPublicationProductTypes::class,
            AddSoftwareVersionToProducts::class,
            CreateSalesOptionsTable::class,
            CreateSalesChannelsTable::class,
            CreateShippingMethodsTable::class,
            CreateOrdersTable::class,
            CreateOrderItemsTable::class,
            CreateSubscriptionsTable::class,
            CreateShipmentsTable::class,
            CreateInventoryTable::class,
            CreateInventoryLogsTable::class,
            CreateLicensesTable::class,
            CreateProductDownloadsTable::class,
        ];


        /*
        |--------------------------------------------------------------------------
        | Run Migrations
        |--------------------------------------------------------------------------
        */


        foreach ($migrations as $migration) {


            /*
            |--------------------------------------------------------------------------
            | Migration Class確認
            |--------------------------------------------------------------------------
            */


            if (!class_exists($migration)) {
                throw new \RuntimeException(
                    sprintf(
                        'Migrationクラスが見つかりません: %s',
                        $migration
                    )
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Migration実行
            |--------------------------------------------------------------------------
            */


            $migration::up();
        }
    }
}