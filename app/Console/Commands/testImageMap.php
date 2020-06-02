<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Model\LineOfficialAccount;

use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;

use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;

use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\ExternalLinkBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\VideoBuilder;

use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;

class testImageMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imagemap:test_send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$line_account = LineOfficialAccount::where('line_basic_id', '442nvild')->first();

		$bot = new LINEBot(
				new LINEBot\HTTPClient\CurlHTTPClient($line_account->line_token),
				['channelSecret' => $line_account->line_channel_secret]
		);

		$basesize = new BaseSizeBuilder(780, 1040);

		$imagemap_area1 = new AreaBuilder(10, 10, 30, 30);
		$imagemap_area2 = new AreaBuilder(50, 50, 30, 30);
		$imagemap_area3 = new AreaBuilder(100, 100, 30, 30);

		$imagemap_uri_act1 = new ImagemapUriActionBuilder('http://yahoo.co.jp', $imagemap_area1);
		$imagemap_uri_act2 = new ImagemapUriActionBuilder('http://google.co.jp', $imagemap_area2);
		$imagemap_uri_act3 = new ImagemapUriActionBuilder('http://tantora.jp', $imagemap_area3);

		$listImageMapAct = [$imagemap_uri_act1, $imagemap_uri_act2, $imagemap_uri_act3];

        $imagemap = new ImagemapMessageBuilder(
			'https://dev.line.ad-scope.net/php/imagemap/442nvild/imagemap',
			'alt text',
			$basesize,
			$listImageMapAct
		);

		$messageBuilder = new MultiMessageBuilder();
		$messageBuilder->add($imagemap);

		$response = $bot->multicast(['Ub0f5b61bde8379b913a52bb3299c3123'], $messageBuilder, true);

		if( !$response->isSucceeded() ){
			error_log('Failed! '. $response->getRawBody(),3,"/data/www/line/storage/logs/nishi_log.txt");
		}

    }
}
