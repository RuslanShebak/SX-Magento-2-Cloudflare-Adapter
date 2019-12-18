<?php
/**
 * Created by Skynix Team.
 * Date: 27.11.19
 * Time: 16:47
 */
namespace Skynix\SXCloudflareAdapter\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Skynix\SXCloudflareAdapter\Helper\Data;
use Skynix\SXCloudflareAdapter\Helper\Api;

class CleanCloudflare extends Command
{
    /**
     * @var Data
     */
    private $data;

    /**
     * @var Api
     */
    private $api;

    /**
     * CleanCloudflare constructor.
     *
     * @param Data $data
     * @param Api $api
     */
    public function __construct(
        Data $data,
        Api $api
    ) {
        $this->data = $data;
        $this->api = $api;
        parent::__construct();
    }

    /**
     * Set configuration console command "Purge Cache Cloudflare"
     */
    protected function configure()
    {
        $this->setName( 'sx:clean-cloud-flare' )->setDescription( "Purge Cache Cloudflare" );
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        try{

            if( $this->data->getEnable() ) {

                $output->writeln( 'Purge Cache Cloud Flare' );
                $output->writeln( 'Check account' );

                $this->api->getAccounts();

                $output->writeln( 'Get zones' );
                $zones = $this->api->getZones();

                $output->writeln( 'Flush zones' );
                $flush = $this->api->purgeCache($zones);
                $output->writeln( $flush ? 'Success' : 'Error' );

            }

        } catch (\Exception $e) {
            $output->writeln( $e->getMessage() );
        }
    }
}