<?php

namespace StripeIntegration\Payments\Model\Stripe;

use StripeIntegration\Payments\Helper\Logger;

abstract class StripeObject
{
    public $lastError = null;

    protected $objectSpace = null;
    protected  $object = null;
    protected $objectManager;
    protected $subscriptionsHelper;
    protected $config;
    protected $helper;
    protected $dataHelper;

    public function __construct(
        \StripeIntegration\Payments\Model\Config $config,
        \StripeIntegration\Payments\Helper\Generic $helper,
        \StripeIntegration\Payments\Helper\Data $dataHelper,
        \StripeIntegration\Payments\Helper\Subscriptions $subscriptionsHelper
    )
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->dataHelper = $dataHelper;
        $this->subscriptionsHelper = $subscriptionsHelper;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function getStripeObject()
    {
        return $this->object;
    }

    public function lookupSingle($key)
    {
        $items = $this->config->getStripeClient()->{$this->objectSpace}->all(['lookup_keys' => [$key], 'limit' => 1]);
        $this->object = $items->first();
        return $this->object;
    }

    public function destroy()
    {
        if (!$this->object || empty($this->object->id))
            return;

        $this->config->getStripeClient()->{$this->objectSpace}->delete($this->object->id, []);
    }

    public function getType()
    {
        return $this->objectSpace;
    }

    public function getId()
    {
        if (empty($this->object->id))
            return null;

        return $this->object->id;
    }

    public function load($id)
    {
        $this->object = $this->getObject($id);
        return $this;
    }

    public function getStripeUrl()
    {
        if (empty($this->object))
            return null;

        if ($this->object->livemode)
            return "https://dashboard.stripe.com/{$this->objectSpace}/{$this->object->id}";
        else
            return "https://dashboard.stripe.com/test/{$this->objectSpace}/{$this->object->id}";
    }

    protected function upsert($id, $data)
    {
        $this->object = $this->getObject($id);

        if (!$this->object)
        {
            $data["id"] = $id;
            return $this->createObject($data);
        }
        else
            return $this->updateObject($id, $data);
    }

    protected function getObject($id)
    {
        try
        {
            return $this->object = $this->config->getStripeClient()->{$this->objectSpace}->retrieve($id, []);
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    protected function setObject($object)
    {
        if (empty($object))
            throw new \Exception("Invalid Stripe object specified");

        $this->object = $object;

        return $this;
    }

    protected function createObject($data)
    {
        try
        {
            $this->lastError = null;
            $this->object = $this->config->getStripeClient()->{$this->objectSpace}->create($data);
            return $this->object;
        }
        catch (\Exception $e)
        {
            $this->lastError = $e->getMessage();
            Logger::log($e->getMessage());
            Logger::log($e->getTraceAsString());
            return $this->object = null;
        }
    }

    protected function updateObject($id, $data)
    {
        try
        {
            return $this->object = $this->config->getStripeClient()->{$this->objectSpace}->update($id, $data);
        }
        catch (\Exception $e)
        {
            Logger::log($e->getMessage());
            Logger::log($e->getTraceAsString());
            return $this->object = null;
        }
    }
}
