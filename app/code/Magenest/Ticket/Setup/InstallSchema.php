<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *
 * Magenest_Ticket extension
 * NOTICE OF LICENSE
 *
 * @category  Magenest
 * @package   Magenest_Ticket
 * @author ThaoPV <thaopw@gmail.com>
 */
namespace Magenest\Ticket\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Magenest\Ticket\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * install tables
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('magenest_ticket_ticket')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_ticket_ticket')
            )
                ->addColumn(
                    'ticket_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Ticket ID'
                )
                ->addColumn(
                    'event_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'Event Id'
                )
//                ->addColumn(
//                    'product_id',
//                    Table::TYPE_INTEGER,
//                    11,
//                    ['nullable' => false],
//                    'Product Id'
//                )
//                ->addColumn(
//                    'session_id',
//                    Table::TYPE_INTEGER,
//                    11,
//                    ['nullable' => true],
//                    'Session Id'
//                )
//                ->addColumn(
//                    'qty',
//                    Table::TYPE_INTEGER,
//                    11,
//                    ['nullable' => true],
//                    'Qty'
//                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true],
                    'Ticket Type'
                )
                ->addColumn(
                    'code',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Ticket Code'
                )
                ->addColumn(
                    'customer_name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Name'
                )
                ->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Customer Email'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true],
                    'Customer Id'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false],
                    'Ticket Order  Id'
                )
                ->addColumn(
                    'order_increment_id',
                    Table::TYPE_TEXT,
                    32,
                    ['nullable' => true],
                    'Ticket Order Increment Id'
                )
                ->addColumn(
                    'order_item_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Ticket Order Item Id'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'default' => 0],
                    'Ticket Order Item Id'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Ticket Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Ticket Updated At'
                )
                ->addColumn(
                    'note',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Note'
                )
                ->setComment('Ticket Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('magenest_ticket_ticket'),
                $setup->getIdxName(
                    $installer->getTable('magenest_ticket_ticket'),
                    ['customer_name','customer_email'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['customer_name','customer_email'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }

        if (!$installer->tableExists('magenest_ticket_event')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_ticket_event')
            )
            ->addColumn(
                'event_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
                ],
                'Event ID'
            )
            ->addColumn(
                'product_id',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Event Product Id'
            )
            ->addColumn(
                'event_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Event Event Name'
            )
            ->addColumn(
                'location',
                Table::TYPE_TEXT,
                255,
                [],
                'Event Location'
            )
            ->addColumn(
                'start_time',
                Table::TYPE_DATETIME,
                null,
                [],
                'Event Start Time'
            )
            ->addColumn(
                'end_time',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Event End Time'
            )
            ->addColumn(
                'reminder_before_day',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'Event Reminder Before Day'
            )
            ->addColumn(
                'reminder_template',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Event Reminder Template'
            )
            ->addColumn(
                'enable',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Event Status'
            )
            ->addColumn(
                'allow_generate_pdf_ticket',
                Table::TYPE_INTEGER,
                null,
                [],
                'Event Allow Generate PDF Ticket'
            )
            ->addColumn(
                'pdf_coordinates',
                Table::TYPE_TEXT,
                '64K',
                [],
                'PDF Ticket Coordinates'
            )
            ->addColumn(
                'pdf_page_width',
                Table::TYPE_INTEGER,
                null,
                [],
                'PDF Ticket Page Width'
            )->addColumn(
                'pdf_page_height',
                Table::TYPE_INTEGER,
                null,
                [],
                'PDF Ticket Page Height'
            )->addColumn(
                'pdf_background',
                Table::TYPE_TEXT,
                null,
                [],
                'PDF Ticket Background'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Event Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [],
                'Event Updated At'
            )
//                ->addColumn(
//                'email_config',
//                Table::TYPE_TEXT,
//                null,
//                [],
//                'Email Config'
//            )
            ->setComment('Event Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('magenest_ticket_event'),
                $setup->getIdxName(
                    $installer->getTable('magenest_ticket_event'),
                    ['product_id','event_name','location','reminder_template','enable'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['product_id','event_name','location','reminder_template','enable'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        if (!$installer->tableExists('magenest_ticket_eventoption')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_ticket_eventoption')
            )
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'event_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'Event Id'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'Product Id'
                )
                ->addColumn(
                    'option_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true],
                    'Option Id'
                )
                ->addColumn(
                    'option_title',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Option Title'
                )
                ->addColumn(
                    'option_input_type',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Option Input Type'
                )
                ->addColumn(
                    'is_required',
                    Table::TYPE_SMALLINT,
                    2,
                    ['nullable' => true],
                    'Is Required'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => true],
                    'Store Id'
                )
                ->setComment('Event Option Table');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('magenest_ticket_eventoption_type')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('magenest_ticket_eventoption_type')
            )
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )
                ->addColumn(
                    'event_option_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'Event Option Id'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'Product Id'
                )
                ->addColumn(
                    'option_id',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true],
                    'Option Id'
                )
                ->addColumn(
                    'title',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => true],
                    'Tittle'
                )
                ->addColumn(
                    'sku',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => true],
                    'SKU'
                )
                ->addColumn(
                    'price',
                    Table::TYPE_DECIMAL,
                    '12,2',
                    ['nullable' => false, 'default' => '0.00'],
                    'Code Prefix'
                )
                ->addColumn(
                    'price_type',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => true],
                    'Code Prefix'
                )
                ->addColumn(
                    'code_prefix',
                    Table::TYPE_TEXT,
                    64,
                    ['nullable' => true],
                    'Code Prefix'
                )
                ->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Sort Order'
                )
                ->addColumn(
                    'qty',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true, 'default' => '0'],
                    'Qty of Option'
                )
                ->addColumn(
                    'available_qty',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true, 'default' => '0'],
                    'Available Qty'
                )
                ->addColumn(
                    'purcharsed_qty',
                    Table::TYPE_INTEGER,
                    11,
                    ['nullable' => true, 'default' => '0'],
                    'Purcharsed Qty'
                )
                ->addColumn(
                    'revenue',
                    Table::TYPE_DECIMAL,
                    '12,2',
                    ['nullable' => false, 'default' => '0.00'],
                    'Revenue'
                )
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Description'
                )
                ->addColumn(
                    'tax',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Tax'
                )
                ->setComment('Event Option Type Table');
            $installer->getConnection()->createTable($table);
        }
//        if (!$installer->tableExists('magenest_ticket_event_location')) {
//            $table = $installer->getConnection()->newTable(
//                $installer->getTable('magenest_ticket_event_location')
//            )->addColumn(
//                'location_id',
//                Table::TYPE_INTEGER,
//                null,
//                [
//                    'identity' => true,
//                    'nullable' => false,
//                    'primary'  => true,
//                    'unsigned' => true,
//                ],
//                'Location ID'
//            )->addColumn(
//                'product_id',
//                Table::TYPE_INTEGER,
//                null,
//                ['nullable' => false],
//                'Product Id'
//            )->addColumn(
//                'location_title',
//                Table::TYPE_TEXT,
//                255,
//                ['nullable' => true],
//                'Location Title'
//            )->addColumn(
//                'location_detail',
//                Table::TYPE_TEXT,
//                null,
//                ['nullable' => true],
//                'Location Detail'
//            )->addColumn(
//                'location_is_enabled',
//                Table::TYPE_SMALLINT,
//                2,
//                ['nullable' => true],
//                'Location Is Enabled'
//            )->setComment('Event Location Table');
//            $installer->getConnection()->createTable($table);
//        }

//        if (!$installer->tableExists('magenest_ticket_event_date')) {
//            $table = $installer->getConnection()->newTable(
//                $installer->getTable('magenest_ticket_event_date')
//            )->addColumn(
//                'date_id',
//                Table::TYPE_INTEGER,
//                null,
//                [
//                    'identity' => true,
//                    'nullable' => false,
//                    'primary'  => true,
//                    'unsigned' => true,
//                ],
//                'Date ID'
//            )->addColumn(
//                'product_id',
//                Table::TYPE_INTEGER,
//                null,
//                ['nullable' => false],
//                'Product Id'
//            )->addColumn(
//                'event_location_id',
//                Table::TYPE_INTEGER,
//                null,
//                ['nullable' => false],
//                'Event Location Id'
//            )->addColumn(
//                'date_start',
//                Table::TYPE_DATETIME,
//                null,
//                [],
//                'Date Start'
//            )->addColumn(
//                'date_end',
//                Table::TYPE_DATETIME,
//                null,
//                [],
//                'Date End'
//            )->addColumn(
//                'date_is_enabled',
//                Table::TYPE_SMALLINT,
//                2,
//                ['nullable' => true],
//                'Date Is Enabled'
//            )->setComment('Event Date Table');
//            $installer->getConnection()->createTable($table);
//        }
//        if (!$installer->tableExists('magenest_ticket_event_session')) {
//            $table = $installer->getConnection()->newTable(
//                $installer->getTable('magenest_ticket_event_session')
//            )->addColumn(
//                'session_id',
//                Table::TYPE_INTEGER,
//                null,
//                [
//                    'identity' => true,
//                    'nullable' => false,
//                    'primary'  => true,
//                    'unsigned' => true,
//                ],
//                'Session ID'
//            )->addColumn(
//                'product_id',
//                Table::TYPE_INTEGER,
//                null,
//                ['nullable' => false],
//                'Product Id'
//            )->addColumn(
//                'event_date_id',
//                Table::TYPE_INTEGER,
//                null,
//                ['nullable' => false],
//                'Event Date Id'
//            )->addColumn(
//                'start_time',
//                Table::TYPE_TEXT,
//                null,
//                [],
//                'Start Time'
//            )->addColumn(
//                'end_time',
//                Table::TYPE_TEXT,
//                null,
//                [],
//                'End Time'
//            )->addColumn(
//                'session_is_enabled',
//                Table::TYPE_SMALLINT,
//                2,
//                ['nullable' => true],
//                'Session Is Enabled'
//            )->setComment('Event Session Table');
//            $installer->getConnection()->createTable($table);
//        }
        $installer->endSetup();
    }
}
