<?php
namespace App\Core;
class Kafka {
    
    protected $kafkaBrokerList = '';
    public function __construct() {
        $this->kafkaBrokerList      = env('KAFKA_BROKER_LIST');
    }
    function producer($topicName, $value){
        try {
            $config = \Kafka\ProducerConfig::getInstance();
            $config->setMetadataRefreshIntervalMs(10000);
            $config->setMetadataBrokerList($this->kafkaBrokerList);
            $config->setBrokerVersion('1.0.0');
            $config->setRequiredAck(1);
            $config->setIsAsyn(false);
            $config->setProduceInterval(500);
            $producer = new \Kafka\Producer();
            return $producer->send([
                [
                    'topic' => $topicName,
                    'value' => $value,
                    'key'   => '',
                ],
            ]);
        } catch (\Exception $ex) {
            return null;
        }
    }
    function saveLogAll($value){
        return $this->producer(env('KAFKA_TOPIC_NAME_LOG_ALL'), $value);
    }
}
