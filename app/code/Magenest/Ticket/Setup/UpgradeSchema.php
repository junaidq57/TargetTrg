<?php
namespace Magenest\Ticket\Setup;

use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package Magenest\Ticket\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrade database when run bin/magento setup:upgrade from command line
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '101.0.5') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_ticket_ticket'),
                'product_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => false,
                    'comment' => 'Product Id'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_ticket_ticket'),
                'information',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Information'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_ticket_ticket'),
                'qty',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'comment' => 'Qty'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_ticket_event'),
                'email_config',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Email Config'
                ]
            );

            $this->createTicketLocationTable($installer);

            $this->createTicketDateTable($installer);

            $this->createTicketSessionTable($installer);
        }

        if (version_compare($context->getVersion(), '101.0.5') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_ticket_event'),
                'enable_date_time',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Enabled Date Time'
                ]
            );
        }

        $installer->endSetup();
    }

    /**
     * Create the table name magenest_ticket_event_location
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createTicketLocationTable($installer)
    {
        $tableName = 'magenest_ticket_event_location';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
                'unsigned' => true,
            ],
            'Location ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product Id'
        )->addColumn(
            'location_title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Location Title'
        )->addColumn(
            'location_detail',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Location Detail'
        )->addColumn(
            'location_is_enabled',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => true],
            'Location Is Enabled'
        )->setComment('Event Location Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create the table name magenest_ticket_event_date
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createTicketDateTable($installer)
    {
        $tableName = 'magenest_ticket_event_date';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'date_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
                'unsigned' => true,
            ],
            'Date ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product Id'
        )->addColumn(
            'event_location_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Event Location Id'
        )->addColumn(
            'date_start',
            Table::TYPE_DATETIME,
            null,
            [],
            'Date Start'
        )->addColumn(
            'date_end',
            Table::TYPE_DATETIME,
            null,
            [],
            'Date End'
        )->addColumn(
            'date_is_enabled',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => true],
            'Date Is Enabled'
        )->setComment('Event Date Table');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create the table name magenest_ticket_event_session
     *
     * @param SetupInterface $installer
     * @return void
     */
    private function createTicketSessionTable($installer)
    {
        $tableName = 'magenest_ticket_event_session';
        if ($installer->tableExists($tableName)) {
            return;
        }
        $table = $installer->getConnection()->newTable(
            $installer->getTable($tableName)
        )->addColumn(
            'session_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
                'unsigned' => true,
            ],
            'Session ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Product Id'
        )->addColumn(
            'event_date_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Event Date Id'
        )->addColumn(
            'start_time',
            Table::TYPE_TEXT,
            null,
            [],
            'Start Time'
        )->addColumn(
            'end_time',
            Table::TYPE_TEXT,
            null,
            [],
            'End Time'
        )->addColumn(
            'session_is_enabled',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => true],
            'Session Is Enabled'
        )->setComment('Event Session Table');

        $installer->getConnection()->createTable($table);
    }
}
