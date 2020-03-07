<?php
namespace DiSarli\WhiteRabbit;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * WhiteRabbit - Simple Library to push/pull RabbitMQ in PHP with PHPAMQPLib PHP Version 5
 *
 * @category Class
 * @package WhiteRabbit
 * @author Di Sarli <leonardo@disarli.com.br>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://github.com/leodisarli/white-rabbit
 */
class WhiteRabbit
{
    protected $config;
    private $connection;
    private $channel;

    /**
     * Construct
     *
     * @param array $config - Configuration
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = [];
        if (!empty($config)) {
            $this->config = $config;
        }
        $this->connect($this->config);
    }

    /**
     * Connect to RabbitMq
     *
     * @param array $config - Configuration
     * @return void
     */
    private function connect(array $config = [])
    {
        if (!empty($config)) {
            $this->config = $config;
            $this->connection = new AMQPStreamConnection(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['pass'],
                $this->config['vhost']
            );
            $this->channel = $this->connection->channel();
        } else {
            $this->outputMessage('Invalid configuration file', 'error');
        }
    }

    /**
     * Push in the specified queue
     *
     * @param string $queue - Queue
     * @param mixed (string/array) $data - Data
     * @param boolean $permanent - Permanent mode
     * @param array $params - Parameters
     * @param bool $output - Show output
     * @return bool
     */
    public function push(
        $queue = null,
        $data = null,
        $permanent = false,
        array $params = [],
        $output = false
    ) {
        if (empty($queue)) {
            if ($output) {
                $this->outputMessage('Missing Queue', 'error');
            }
            return false;
        }

        if (is_array($data)) {
            $data = json_encode($data);
        }

        $item = new AMQPMessage($data, $params);
        $this->channel->queue_declare($queue, false, $permanent, false, false, false, null, null);
        $this->channel->basic_publish($item, '', $queue);

        if ($output) {
            $this->outputMessage('Pushing "'.$item->body.'" to "'.$queue.'" queue -> OK', null);
        }
        return true;
    }

    /**
     * Get items from the specified queue
     *
     * @param string $queue - Queue
     * @param bool $permanent - Permanent mode
     * @param array $callback - Callback
     * @return void
     */
    public function pull(
        $queue = null,
        $permanent = false,
        array $callback = []
    ) {
        if (!empty($queue)) {
            $this->channel->queue_declare($queue, false, $permanent, false, false, false, null, null);
            $this->channel->basic_qos(null, 1, null);
            $this->channel->basic_consume($queue, '', false, false, false, false, $callback);
            $total = count($this->channel->callbacks);
            while ($total) {
                $this->channel->wait();
            }
        } else {
            $this->outputMessage('Queue parameter is mandatory', 'error');
        }
    }
    
    /**
     * Lock a message
     *
     * @param AMQPMessage $message
     * @return void
     */
    public function lock($message)
    {
        $this->channel->basic_reject($message->delivery_info['delivery_tag'], true);
    }

    /**
     * Release a message
     *
     * @param  AMQPMessage $message
     * @return void
     */
    public function unlock($message)
    {
        $this->channel->basic_ack($message->delivery_info['delivery_tag']);
    }

    /**
     * Output defined message in Browser or Console
     *
     * @param  string $message - Output message
     * @param  string $type    - Message type
     * @return void
     */
    private function outputMessage($message, $type = null)
    {
        if (php_sapi_name() == 'cli') {
            switch ($type) {
                case 'error':
                    echo '[x] RabbitMQ Library Error : '.$message . PHP_EOL;
                    break;
                default:
                    echo '[v] '.$message . PHP_EOL;
                    break;
            }
        } else {
            switch ($type) {
                case 'error':
                    show_error($message, null, 'RabbitMQ Library Error');
                    break;
                default:
                    echo $message . '<br>';
                    break;
            }
        }
    }

    /**
     * Close the channel and the connection
     */
    public function __destruct()
    {
        if (!empty($this->channel)) {
            $this->channel->close();
        }
        if (!empty($this->connection)) {
            $this->connection->close();
        }
    }
}
